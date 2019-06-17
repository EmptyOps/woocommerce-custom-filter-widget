<?php
class WOO_CUSTOME_FILTER_Action{

	public function __construct() {
        
		if ( wp_verify_nonce( $_POST['_wpnonce'],'woo_custome_filter_action_add') ) {

            $this->wcfw_sanitize();            
            $this->add_filter();       
        }            
        elseif ( wp_verify_nonce( $_POST['_wpnonce'],'woo_custome_filter_action_remove' ) ) {                
            
            $this->wcfw_sanitize();
        	$this->del_filter();
		}
        elseif( wp_verify_nonce( $_POST['_wpnonce'],'woo_custom_filter-config_opt') ){

            $this->wcfw_sanitize();
            $this->config();            
        }
        elseif(wp_verify_nonce( $_POST['_wpnonce'],'woo_custome_filter_target' )){

            $this->wcfw_sanitize();
            $this->filter_target();
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

	private function add_filter() {

        $filter_name=$_POST["filter_name"]; //required

        $filter_type=$_POST["filter_type"]; //required
        
        $filter_label=(!empty($_POST["filter_label"])?$_POST["filter_label"]:'');

        $filter_advanced=(!empty($_POST["filter_advanced"])?"1":"0");
                
        $filter_input=$_POST["filter_input"]; //required
        
        $filter_data=unserialize(get_option('woo_custome_filter_widget',"a:0:{}"));
        
        $filter_data[]=array(
                        'name'=>$filter_name,  
                        'type'=>$filter_type,                      
                        'label'=>$filter_label,
                        'advance'=>$filter_advanced,                        
                        'input'=>$filter_input,
                    );            
        update_option('woo_custome_filter_widget',serialize($filter_data));             
	}

	private function del_filter() {

        $filter_data=unserialize(get_option('woo_custome_filter_widget',"a:0:{}"));                
        foreach ($filter_data as $key=>$item) {
            
            if ($item['name']==$_POST['del_filter_name']) {                 
                unset($filter_data[$key]);
            }
        }
        update_option('woo_custome_filter_widget',serialize($filter_data));                
	}

    private function config(){
        if (
                isset($_POST['wcfw_config_opt_submit_text']) &&
                isset($_POST['wcfw_config_opt_submit_bg_color']) &&
                isset($_POST['wcfw_config_opt_submit_border_color']) &&
                isset($_POST['wcfw_config_opt_submit_font_color']) &&
                isset($_POST['wcfw_config_opt_submit_font_size']) &&
                isset($_POST['wcfw_config_opt_submit_padding']) &&
                isset($_POST['wcfw_config_opt_submit_inline_css']) &&
                isset($_POST['wcfw_config_opt_submit_additional_css']) &&
                isset($_POST['wcfw_config_opt_submit_url']) &&

                

                isset($_POST['wcfw_config_opt_dropdown_bg_color']) &&
                isset($_POST['wcfw_config_opt_dropdown_border_color']) &&
                isset($_POST['wcfw_config_opt_dropdown_font_color']) &&
                isset($_POST['wcfw_config_opt_dropdown_font_size']) &&
                isset($_POST['wcfw_config_opt_dropdown_padding']) &&
                isset($_POST['wcfw_config_opt_dropdown_inline_css']) &&
                isset($_POST['wcfw_config_opt_dropdown_additional_css'])

        ) {

            $config_data=array(
                'submit_text'=>$_POST['wcfw_config_opt_submit_text'],
                'submit_back_color'=>$_POST['wcfw_config_opt_submit_bg_color'],
                'submit_border_color'=>$_POST['wcfw_config_opt_submit_border_color'],
                'submit_font_color'=>$_POST['wcfw_config_opt_submit_font_color'],
                'submit_font_size'=>$_POST['wcfw_config_opt_submit_font_size'],
                'submit_padding'=>$_POST['wcfw_config_opt_submit_padding'],
                'submit_inline_css'=>$_POST['wcfw_config_opt_submit_inline_css'],
                'submit_add_css'=>$_POST['wcfw_config_opt_submit_additional_css'],
                'submit_url'=>$_POST['wcfw_config_opt_submit_url'],

                'dropdown_back_color'=>$_POST['wcfw_config_opt_dropdown_bg_color'],
                'dropdown_border_color'=>$_POST['wcfw_config_opt_dropdown_border_color'],
                'dropdown_font_color'=>$_POST['wcfw_config_opt_dropdown_font_color'],
                'dropdown_font_size'=>$_POST['wcfw_config_opt_dropdown_font_size'],
                'dropdown_padding'=>$_POST['wcfw_config_opt_dropdown_padding'],
                'dropdown_inline_css'=>$_POST['wcfw_config_opt_dropdown_inline_css'],
                'dropdown_add_css'=>$_POST['wcfw_config_opt_dropdown_additional_css']                
            );
            
            update_option('woo_custome_filter_widget_config',serialize($config_data));
        }
    }

    private function filter_target() {
        if (
                isset($_POST['filter_target']) &&
                isset($_POST['filter_target_cat']) &&
                isset($_POST['wcfw_shop_opt_submit_additional_css'])
            ){

            $config_data=array(
                'page'=>$_POST['filter_target'],
                'cat_id'=>$_POST['filter_target_cat'],
                'add_css'=>$_POST['wcfw_shop_opt_submit_additional_css']
            );

            update_option('woo_custome_filter_target',serialize($config_data));   
        }
    }
}
?>