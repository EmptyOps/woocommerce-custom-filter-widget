<?php
class WOO_CUSTOME_FILTER_Core   {   

    public function __construct(){

        if (class_exists( 'WooCommerce' )){
            //if request from Admin
            if(is_admin()){	
                //Go to eo_wbc_admin method
                $this->backend();                 
            }
            //if request from User
            else{				
                //Go to eo_wbc_frontend method
                $this->frontend();                
            }            
        }        
    }

    private function backend() { 
        
        if($_POST && isset($_POST['_wpnonce'])) {
            

            require_once apply_filters('woo_custome_filter_action','application/backend/WOO_CUSTOME_FILTER_Action.php',30);
            
            if(class_exists('WOO_CUSTOME_FILTER_Action')){                
                new WOO_CUSTOME_FILTER_Action();
            }
        }        

        require_once apply_filters('woo_custome_filter_config','application/backend/WOO_CUSTOME_FILTER_Config.php',30);
        
        if(class_exists('WOO_CUSTOME_FILTER_Config')){
            
            new WOO_CUSTOME_FILTER_Config();
        }
        
    }

    private function frontend() {
        $this->wcfw_sanitize();
        require_once apply_filters('woo_custome_filter_widget','application/frontend/WOO_CUSTOME_FILTER_Widget.php',30);

        if (class_exists('WOO_CUSTOME_FILTER_Widget')) {                        
            
            $widget=new WOO_CUSTOME_FILTER_Widget();           

            $_category=array();
            $_attribute=array();

            add_shortcode('woo_custome_filter_begin',function() use(&$widget){
                ob_start();
                    ?>
                    
                        <form method="GET" class="woo_custome_filter_short_form">
                            <input type="hidden" name="woo_custome_filter" value="1"/>
                            <div id="filter_container">
                    <?php
                return ob_get_clean();
            },10);

            add_shortcode('woo_custome_filter_end',function($config) use(&$widget,&$_category,&$_attribute){
                ob_start();
                    extract( shortcode_atts( array(
                        'filter_size'=>2
                    ), $config,'woo_custome_filter_end') );

                    $config_data=unserialize(get_option('woo_custome_filter_widget_config'));                    

                    ?>
                                <a href="#" class="" id="woo-custome-filter-redirect"><?php echo isset($config_data['submit_text'])?$config_data['submit_text']:'OK'; ?></a>
                            </div>                            
                            <input type="hidden" name="_category" value="<?php echo implode(',',$_category) ?>"/>
                            <input type="hidden" name="_attribute" value="<?php echo implode(',',$_attribute) ?>"/>
                        </form>      
                        <?php if(count($config_data)>0): ?>                    
                            <style type="text/css">
                                #filter_container select{
                                    border: 1px solid <?php echo $config_data['dropdown_border_color'];?>;
                                    color: <?php echo $config_data['dropdown_font_color'];?>;
                                    background-color: <?php echo $config_data['dropdown_back_color'];?>;
                                    padding: <?php echo $config_data['dropdown_padding'];?>px;
                                    font-size: <?php echo $config_data['dropdown_font_size'];?>;
                                    <?php echo $config_data['dropdown_inline_css'];?>;
                                }
                                #filter_container{
                                    display: grid;
                                    grid-template-columns: <?php for ($i=0; $i < $filter_size ; $i++) { echo 'auto ';} ?> max-content;
                                }
                                #woo-custome-filter-redirect{                                                                        
                                    text-decoration: none;
                                    align-self: center;                                    
                                    color: <?php echo $config_data['submit_font_color'];?>;
                                    background-color: <?php echo $config_data['submit_back_color'];?>;
                                    border: 1px solid <?php echo $config_data['submit_border_color'];?>;
                                    padding: <?php echo $config_data['submit_padding'];?>px !important;
                                    font-size:<?php echo $config_data['submit_font_size'];?>px !important;
                                    <?php echo $config_data['submit_inline_css'];?>;
                                }
                                @media only screen and (max-width: 600px) {
                                  #woo-custome-filter-redirect{
                                    width: 100%;
                                  }
                                  #filter_container{
                                    grid-template-columns: auto;
                                  }
                                }
                                <?php echo $config_data['submit_add_css']; ?>
                                <?php echo $config_data['dropdown_add_css']; ?>
                            </style>
                        <?php endif; ?>                        
                    <?php
                    wp_register_script('woo_custome_filter_shortcode_js',plugin_dir_url( __FILE__ ).'/application/frontend/js/shortcode.js');
                    wp_enqueue_script('woo_custome_filter_shortcode_js');
                    wp_localize_script('woo_custome_filter_shortcode_js','filter_ob',array(
                        'ajaxurl' => admin_url('admin-ajax.php'),
                        'cat_url'=> get_option('siteurl').'/product-category/',
                        'shop_url'=>get_permalink(wc_get_page_id('shop')) 
                        )
                    ) ;
                return ob_get_clean();
            },10);

            add_shortcode('woo_custome_filter',function($config) use(&$widget,&$_category,&$_attribute){                    
                ob_start();                        
                    extract( shortcode_atts( array(
                        'input'=>'dropdown',
                        'id'=>'0',
                        'label'=>'Title',
                        'type'=>'0',
                        'node_type'=>'parent',
                        'parent_node'=>'',
                        'node_name'=>''
                    ), $config,'woo_custome_filter') );

                    $widget->enque_asset();
                    $term=null;

                    if($type=='0'){
                        $term=get_term_by('id',$id,'product_cat');
                        if(@$term->slug){
                            $_category[]=@$term->slug;
                        }
                    }
                    elseif($type=='1'){
                        $term=wc_get_attribute($id);                                                
                        if(@$term->slug){
                            $_attribute[]=@$term->slug;    
                        }                       
                    }

                    /*switch ($input) {
                        case 'text_slider':                            
                            $filter=$widget->range_steps($id,$label,$type);                                                     
                            $widget->input_text_slider(                                   
                                    $filter['slug'],
                                    array_column($filter['list'],'name'),
                                    array_column($filter['list'],'slug'),
                                    $item['type']
                            );
                            break;
                        case 'step_slider':
                            $widget->input_step_slider($id,$label,$type);
                            break;

                        case 'checkbox':

                            $filter=$widget->range_steps($id,$label,$type);                                                     
                            $widget->input_checkbox(                                   
                                    $filter['slug'],
                                    array_column($filter['list'],'name'),
                                    array_column($filter['list'],'slug'),
                                    $item['type']
                            );
                            break;                      
                        default:*/
                        ?><div data-node-name="<?php echo $node_name; ?>" data-node-id="<?php echo $id; ?>"> <?php
                            $filter=$widget->range_steps($id,$label,$type);                                                     
                            $widget->input_dropdown(
                                    $filter['slug'],
                                    array_column($filter['list'],'name'),
                                    array_column($filter['list'],'slug'),
                                    $id,
                                    $type,
                                    $label
                                );                                     
                        ?></div> <?php  
                    /*}                    */
                    echo "<script>console.log('".$parent_node." ".$node_type." ".$node_name."');</script>";

                    if(!empty($parent_node) && $node_type=='Child'){
                        ?>
                            <script>
                                jQuery(document).ready(function($){
                                    bind_dependency("<?php echo $parent_node ?>","<?php echo $node_name; ?>","change");
                                });
                            </script>
                        <?php
                    }
                return ob_get_clean();
            },10);

           

            add_action('template_redirect',function() use(&$widget){

                $filter_target_data=unserialize(get_option('woo_custome_filter_target',"a:0:{}"));
                global $wp_query;

                if('shop'==$filter_target_data['page'] && is_shop() ){

                    add_action( 'woocommerce_before_shop_loop',function() use(&$widget){                              
                            $widget->get_widget();                                    
                    },30);                
                }
                elseif( 'category'==$filter_target_data['page'] ){

                    if(is_product_category() && $filter_target_data['cat_id']==get_queried_object()->term_id ) { 

                        add_action( 'woocommerce_before_shop_loop',function() use(&$widget){                              
                                $widget->get_widget();                                    
                        },30);                
                    }
                }

                /*if(is_product_category() OR is_shop()) { 

                    add_action( 'woocommerce_before_shop_loop',function() use(&$widget){                              
                            $widget->get_widget();                                    
                    },30);                
                }*/
            });
        }        
    }

    private function wcfw_sanitize(){
        //Sanitize $_GET global variable
        foreach ($_GET as $key => $value){

            $_GET[$key]=sanitize_text_field($value);
        }

        //Sanitize $_POST global variable
        foreach ($_POST as $key => $value){

            $_POST[$key]=sanitize_text_field($value);
        }

        //Sanitize $_REQUEST global variable
        foreach ($_REQUEST as $key => $value){

            $_REQUEST[$key]=sanitize_text_field($value);
        }
    }
}

?>