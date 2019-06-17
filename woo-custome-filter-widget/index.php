<?php

/** 
* Plugin Name: Woocommerce Custom Filter Widget
* Plugin URI: https://wordpress.org/plugins/woo-custom-filter-widget/
* Description: A powerfull and easy tool to enable horizontal product filter bar at your e-commerce website.
* Version: 0.0.3
* Author: emptyopssphere
* Author URI: https://profiles.wordpress.org/emptyopssphere
* Requires at least: 3.5
* Tested up to: 5.2.1
* License: GPLv3+
* License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/


/////////////////////////////////////////////////////////////////////////////////////
//***********************************************************************************
/////////////////////////////////////////////////////////////////////////////////////
function wcfw_sanitize(){
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
/////////////////////////////////////////////////////////////////////////////////////
//***********************************************************************************
/////////////////////////////////////////////////////////////////////////////////////

if(isset($_GET) && isset($_GET['woo_custome_filter']) )  {
	wcfw_sanitize();
	/////////////////////////////////////////////////////////////////////////////////////
	//***********************************************************************************
	/////////////////////////////////////////////////////////////////////////////////////
	/*
	*	Ajax filter request handling section
	*/

	require_once apply_filters('woo_custome_filter_ajax','WOO_CUSTOME_FILTER_Ajax.php');

	if(class_exists('WOO_CUSTOME_FILTER_Ajax')){

		new WOO_CUSTOME_FILTER_Ajax();		
	}	
	/////////////////////////////////////////////////////////////////////////////////////
	//***********************************************************************************
	/////////////////////////////////////////////////////////////////////////////////////
}
else{
	
	/////////////////////////////////////////////////////////////////////////////////////
	//***********************************************************************************
	/////////////////////////////////////////////////////////////////////////////////////
	/*
	*	Plugin core section
	*/	
	add_action('plugins_loaded',function() {

		require_once apply_filters('woo_custome_filter_core','WOO_CUSTOME_FILTER_Core.php');	

		if(class_exists('WOO_CUSTOME_FILTER_Core')) {
		
			new WOO_CUSTOME_FILTER_Core();
		}
	},20);
	/////////////////////////////////////////////////////////////////////////////////////
	//***********************************************************************************
	/////////////////////////////////////////////////////////////////////////////////////
}

 add_action('wp_ajax_eo_custom_filter','get_controls'); 
 add_action('wp_ajax_nopriv_eo_custom_filter','get_controls');

 function get_controls(){

 	wcfw_sanitize();

 	require_once apply_filters('woo_custome_filter_widget','application/frontend/WOO_CUSTOME_FILTER_Widget.php',30);

    if (class_exists('WOO_CUSTOME_FILTER_Widget') && isset($_POST['slug']) && isset($_POST['type']) ) {                        
        
        $widget=new WOO_CUSTOME_FILTER_Widget(); 
        
        $slug = sanitize_text_field($_POST['slug']);

        $term=get_term_by('slug',$slug,'product_cat');

        $id=$term->term_id;
        $label=sanitize_text_field($_POST['title']);
        $type=sanitize_text_field($_POST['type']);        

        $filter=$widget->range_steps($id,$label,$type);                                                     
        $widget->input_dropdown($filter['slug'],
                array_column($filter['list'],'name'),
                array_column($filter['list'],'slug'),
                $id,
                $type,
                $label
        );
    }
    else{
    	echo '';
    } 	
 	exit();
 }

?>