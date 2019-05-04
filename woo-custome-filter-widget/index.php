<?php

/** 
* Plugin Name: Woocommerce Custome Filter Widget
* Plugin URI: https://wordpress.org/plugins/woo-custome-filter-widget/
* Description: A powerfull and easy tool to enable horizontal product filter bar at your e-commerce website.
* Version: 0.0.1
* Author: emptyopssphere
* Author URI: https://profiles.wordpress.org/emptyopssphere
* Requires at least: 3.5
* Tested up to: 5.1
* License: GPLv3+
* License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/


/////////////////////////////////////////////////////////////////////////////////////
//***********************************************************************************
/////////////////////////////////////////////////////////////////////////////////////
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
/////////////////////////////////////////////////////////////////////////////////////
//***********************************************************************************
/////////////////////////////////////////////////////////////////////////////////////

if(isset($_GET) && isset($_GET['woo_custome_filter']) )  {
	
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

?>