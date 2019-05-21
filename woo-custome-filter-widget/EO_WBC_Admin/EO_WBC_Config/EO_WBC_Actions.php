<?php
class EO_WBC_Actions
{
    public function __construct()
    {
            //Add Map Request
            if(    (isset($_POST['eo_wbc_first_category']) && isset($_POST['eo_wbc_second_category']) )
                && $_POST['eo_wbc_first_category']
                && $_POST['eo_wbc_second_category']
                && wp_verify_nonce($_POST['_wpnonce'],'eo_wbc_nonce_add_map')
                && $_POST['eo_wbc_action']==='eo_wbc_add_map'
            ){                
                $this->map_add();                
            }
            
            //Remove Map Request
            elseif(
                $_POST['eo_wbc_action']==='eo_wbc_remove_map' 
                    &&
                wp_verify_nonce($_POST['_wpnonce'],'eo_wbc_nonce_remove_map')
             )
            {
                $this->map_remove();
            }           
            
            //Save Configurations
            elseif(
                wp_verify_nonce($_POST['_wpnonce'],'eo_wbc_nonce_config')                
                && $_POST['eo_wbc_action']==='eo_wbc_save_config'
                && $_POST['eo_wbc_first_name']
                && $_POST['eo_wbc_first_slug']
                && $_POST['eo_wbc_second_name']
                && $_POST['eo_wbc_second_slug']
                && $_POST['eo_wbc_collection_title']
                && strlen($_POST['eo_wbc_btn_setting'])                
                && !empty($_POST['eo_wbc_btn_position'])                                
                ){
                    $this->config_save();
            }
            elseif (
                wp_verify_nonce($_POST['_wpnonce'],'eo_wbc_nonce_add_filter')
                && !empty($_POST['filter_name'])                
                && !empty($_POST['eo_wbc_action'])
                && ( $_POST['eo_wbc_action']=='eo_wbc_add_filter_first' OR $_POST['eo_wbc_action']=='eo_wbc_add_filter_second')
            ) {
                
                $filter_action=$_POST["eo_wbc_action"];

                $filter_name=$_POST["filter_name"];
                $filter_type=$_POST["filter_type"];
                $filter_label=(!empty($_POST["filter_label"])?$_POST["filter_label"]:"");
                $filter_advanced=(!empty($_POST["filter_advanced"])?"1":"0");
                $filter_dependent=(!empty($_POST["filter_dependent"])?"1":"0");
                $filter_input=$_POST["filter_input"];
                
                $filter_data=unserialize(get_option($filter_action,"a:0:{}"));
                
                $filter_data[]=array(
                                'name'=>$filter_name,
                                'type'=>$filter_type,
                                'label'=>$filter_label,
                                'advance'=>$filter_advanced,
                                'dependent'=>$filter_dependent,
                                'input'=>$filter_input,
                            );    

                update_option($filter_action,serialize($filter_data));                
            }            
            elseif (
                wp_verify_nonce($_POST['_wpnonce'],'eo_wbc_nonce_remove_filter')
                && !empty($_POST['eo_wbc_filter_name'])
                && !empty($_POST['eo_wbc_filter_action'])   
                && $_POST['eo_wbc_action']=='eo_wbc_remove_filter'             
            ) {                

                $filter_data=unserialize(get_option($_POST['eo_wbc_filter_action'],"a:0:{}"));                
                foreach ($filter_data as $key=>$item) {
                    
                    if ($item['name']==$_POST['eo_wbc_filter_name']) {                 
                        unset($filter_data[$key]);
                    }
                }
                update_option($_POST['eo_wbc_filter_action'],serialize($filter_data));                
            }           
    }
    
    private function config_save()
    {          
        //Button setting style,(Default/Shortcode)        
        update_option('eo_wbc_btn_setting',$_POST['eo_wbc_btn_setting']); 
        if ($_POST['eo_wbc_btn_setting']=="0") { //if default is choosen

            //position of button (Top/Bottom/Middle/Custom)
            update_option('eo_wbc_btn_position',$_POST['eo_wbc_btn_position']);           
        }        

        update_option('eo_wbc_first_name',$_POST['eo_wbc_first_name']);//FIRST : NAME
        update_option('eo_wbc_first_slug',$_POST['eo_wbc_first_slug']);//FIRST : SLUG
        
        update_option('eo_wbc_first_url',
            $_POST['eo_wbc_first_url']
                ?
            $_POST['eo_wbc_first_url']
                :
            ('/product-category/'.$_POST['eo_wbc_first_slug'].'/'));//FIRST : URL
        
        update_option('eo_wbc_second_name',$_POST['eo_wbc_second_name']);//SECOND : NAME
        update_option('eo_wbc_second_slug',$_POST['eo_wbc_second_slug']);//SECOND : SLUG
        
        update_option('eo_wbc_second_url',
            $_POST['eo_wbc_second_url']
                ?
            $_POST['eo_wbc_second_url']
                :
            ('/product-category/'.$_POST['eo_wbc_second_slug'].'/'));//SECOND : URL        
        
        update_option('eo_wbc_collection_title',$_POST['eo_wbc_collection_title']);//FINAL : NAME
          
        //update filter bar settings :)
        if(isset($_POST['eo_wbc_filter_enable'])) {

            update_option('eo_wbc_filter_enable','1');
        }
        else{

            update_option('eo_wbc_filter_enable','0');
        }
        
        update_option('eo_wbc_config_category',"1");//SAVE CONFIGURATION        
        
        add_action( 'admin_notices',function (){
            echo "<div class='notice notice-success is-dismissible'><p>".__( '<strong>WooCommerce Bundle Choice</strong> Configured Successfully.', 'woocommerce' )."</p></div>";
        });
        
        if(isset($_GET['callback']) && $_GET['callback']==1)//RETURN TO HOME IF CAME THROUGH HOME
        {
            wp_redirect(admin_url('admin.php?page=eo-wbc-home'));
            exit;
        }                
    }
    
    private function map_add()
    {
        global $wpdb;
        
        $eo_wbc_first_category=$_POST['eo_wbc_first_category'];
        $eo_wbc_second_category=$_POST['eo_wbc_second_category'];
        $eo_wbc_add_discount=$_POST['eo_wbc_add_discount']?$_POST['eo_wbc_add_discount']:0;
        
        if($wpdb->get_var("select * from {$wpdb->prefix}eo_wbc_cat_maps where first_cat_id in ('{$eo_wbc_first_category}','{$eo_wbc_second_category}') and second_cat_id in ('{$eo_wbc_first_category}','{$eo_wbc_second_category}')"))
        {
            add_action( 'admin_notices',function (){
                echo "<div class='notice notice-warning is-dismissible'><p>".__( '<strong>Map Already Exists.</strong>', 'woocommerce' )."</p></div>";
            });
        }
        else
        {
            if($wpdb->insert($wpdb->prefix.'eo_wbc_cat_maps',array('first_cat_id'=>$eo_wbc_first_category,'second_cat_id'=>$eo_wbc_second_category,'discount'=>$eo_wbc_add_discount.'%'),array("%s","%s","%s")))
            {
                update_option('eo_wbc_config_map',"1");
                add_action( 'admin_notices',function (){
                    echo "<div class='notice notice-success is-dismissible'><p>".__( '<strong>New Map Added Successfully.</strong>', 'woocommerce' )."</p></div>";
                });            
                if(isset($_GET['callback']) && $_GET['callback']==1)
                {                
                    //return back to admin home if we have arrived from there.
                    echo "<script>window.location.href = '".admin_url('admin.php?page=eo-wbc-home')."';</script>";
                    exit();
                }            
            }  
            else{
                add_action( 'admin_notices',function (){
                    echo "<div class='notice notice-error is-dismissible'><p>".__( '<strong>Error! Failed to add new map. please contact our developers for help.</strong>', 'woocommerce' )."</p></div>";
                });        
            }
        }
    }
    
    private function map_remove()
    {       
        global $wpdb;
        $wpdb->delete($wpdb->prefix.'eo_wbc_cat_maps',array('first_cat_id'=>$_POST['eo_wbc_source'],'second_cat_id'=>$_POST['eo_wbc_target']),array('%s','%s'));
        
        $map_count = "select count(*) from ".$wpdb->prefix."eo_wbc_cat_maps";
        $map_count = $wpdb->get_var($map_count);
        if($map_count==0)
        {
            update_option('eo_wbc_config_map',"0");
        }        
        add_action( 'admin_notices',function (){
            echo "<div class='notice notice-success is-dismissible'><p>".__( '<strong>Map Removed Successfully.</strong>', 'woocommerce' )."</p></div>";
        });
    }   

}
