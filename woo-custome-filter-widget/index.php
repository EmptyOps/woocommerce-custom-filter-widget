<?php
/** 
* Plugin Name: Woocommerce Bundle Choice
* Plugin URI: https://wordpress.org/plugins/woocommerce-bundle-choice/
* Description: An E-Commerce tool that let your customer's buy product in a set and create map that relates between your product categories.
* Version: 0.3.1
* Author: emptyopssphere
* Author URI: https://profiles.wordpress.org/emptyopssphere
* Requires at least: 3.5
* Tested up to: 5.1
* License: GPLv3+
* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
* Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=hi0001234d1%40gmail.com&item_name=+Favorite+WooCommerce+Product+Bundle+Choice++Download+WooCommerce+Product+Bundle+Choice+Plugin+Donation&currency_code=USD&source=url
*/


//Block direct access intrusion.
if (!defined('ABSPATH')) exit;

//load plugin.php to get access to get_plugin_data method.
require_once ABSPATH.'wp-admin/includes/plugin.php';

//Define plugin specification which is available in this file.
define('EO_WBC_PLUGIN_NAME',get_plugin_data(__FILE__)['Name']);
define('EO_WBC_PLUGIN_VERSION',get_plugin_data(__FILE__)['Version']);
define('EO_WBC_PLUGIN_DIR',plugin_dir_path( __FILE__ ));

//Load the core module to perform actvation/deactivation/uninstall.
require_once 'EO_WBC_Core/EO_WBC_Core.php';
//Load the file with main functionality.
require_once 'EO_WBC_Choice.php';

//Powering up the core which activate/deactivate and uninstall plugin.
//Inject Activation, Deactivation and Uninstall hooks
new EO_WBC_Core(__FILE__);

//Begin action only if all plugins are loaded.
add_action('plugins_loaded',function(){
    new EO_WBC_Choice();
},15);

//Add Setting Link adjecent to Plugin Name in Admin's Plugin Panel
add_filter('plugin_action_links_'.plugin_basename(__FILE__),function($links){
    
    $links[] = '<a href="' .
        admin_url( 'admin.php?page=eo-wbc-setting' ) .
        '">' . __('Settings') . '</a>';

        return $links;
},30);

/**
*	--------------------------------------------------------------
*	adding action hook to fees calculation so that we can apply 
*	discount on specific combinations only.
*	
*	we wrote this code here because it wasn't working inside 
*	any other class, it might be fault of woocommerce.
*	--------------------------------------------------------------
*/
add_action( 'woocommerce_cart_calculate_fees',function($cart) {      
       
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

    $total_discount=0;

    foreach (wc()->session->get('EO_WBC_MAPS',array()) as $set) {

        if(count($set)==2){
            get_set_discount($set,$total_discount);
        }
    }

    $cart->add_fee( 'Discount', -$total_discount, true, 'standard' );     
});

/**
*	--------------------------------------------------------------
*	function to calculate discount and return them to action 
*	hook 'woocommerce_cart_calculate_fees'.
*	
*	we wrote this code here because it wasn't working inside 
*	any other class, it might be fault of woocommerce.
*	--------------------------------------------------------------
*/
function get_set_discount($set,&$discount)
{   
    if(empty($set['FIRST']) || empty($set['SECOND'])) return $discount;

    global $wpdb; 
    if(!class_exists('EO_WBC_Support'))       
    {
        require_once apply_filters('eo_wbc_front_support','EO_WBC_Frontend/EO_WBC_Support.php',33);
    }    
        
    $first_cat_tax=(implode(',',wp_get_post_terms($set['FIRST'][0],get_taxonomies(),array('fields'=>'ids'))));
    $second_cat_tax=(implode(',',wp_get_post_terms($set['SECOND'][0],get_taxonomies(),array('fields'=>'ids'))));

    $query="SELECT `discount` FROM `".$wpdb->prefix."eo_wbc_cat_maps` WHERE  `first_cat_id` in({$first_cat_tax}) and `second_cat_id` in({$second_cat_tax}) or `first_cat_id` in({$second_cat_tax}) and `second_cat_id` in({$first_cat_tax})";

    $discount_rates=$wpdb->get_results($query,'ARRAY_N');

    $set_total= EO_WBC_Support::eo_wbc_get_product(empty($set['FIRST'][2])?$set['FIRST'][0]:$set['FIRST'][2])->get_price() *  $set['FIRST'][1]
                    +
                EO_WBC_Support::eo_wbc_get_product(empty($set['SECOND'][2])?$set['SECOND'][0]:$set['SECOND'][2])->get_price() * $set['SECOND'][1];

    foreach ($discount_rates as $rate) {
        
        $discount_value=($set_total * str_replace('%','',$rate[0]))/100;

        $set_total-=$discount_value;
        $discount+=$discount_value;

    }           

    return $discount;
}

add_filter( 'widget_text', 'do_shortcode' );


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//AJAX FILTER .............................
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

require_once apply_filters('eo_wbc_filter','EO_WBC_Filter.php');
$eo_wbc_filter=new EO_WBC_Filter();

add_action('wp_ajax_eo_wbc_filter',array($eo_wbc_filter,'eo_wbc_filter')); // wp_ajax_{ACTION HERE} 
add_action('wp_ajax_nopriv_eo_wbc_filter', array($eo_wbc_filter,'eo_wbc_filter'));

// if(!function_exists('eo_wbc_attribute_range'))
// {
//     function eo_wbc_attribute_range($term,$min,$max,$numeric_range=false) {
//         $list=array();
//         if($min==$max) {
//             $list[]=$min;
//         }
//         else {
//             if($numeric_range)
//             {
//                 foreach(get_terms(array('taxonomy'=>$term,'hide_empty'=>false)) as $term_obj) {                
//                     if(($min<=$term_obj->name) && ($max>=$term_obj->name))
//                     {
//                         $list[]=$term_obj->slug;
//                     }
//                 }
//             }
//             else
//             {
//                 $show_flag=FALSE;
//                 foreach(get_terms(array('taxonomy'=>$term,'hide_empty'=>false)) as $term_obj) {
                    
//                     if($term_obj->slug==$min && $show_flag==FALSE)
//                     {
//                         $list[]=$term_obj->slug;
//                         $show_flag=TRUE;
//                     }
//                     elseif($show_flag==TRUE)
//                     {
//                         if( $term_obj->slug == $max)
//                         {
//                             $list[]=$term_obj->slug;
//                             $show_flag=FALSE;                    
//                             break;
//                         }
//                         else
//                         {
//                             $list[]=$term_obj->slug;
//                         }
//                     }            
//                 }
//             }                
//         }        
//         return $list;
//     }
// }
// ///////////////////////////////////////////////////////
// ///////////////////////////////////////////////////////
// if(!function_exists('eo_wbc_filter'))
// {

//     function eo_wbc_filter() {
//         $__post=$_POST;

//         $args = array(          
//             'posts_per_page' => apply_filters('loop_shop_per_page',12),                  
//             'post_type' => ['product','product_variation'],
//             'orderby' => 'title',
//             'post_status'=>'publish',
//             'paged' => ((!empty($__post['paged']))?$__post['paged']:1),
//         );

        
//         if(!empty($__post))
//         {
//             // price range query
//             if(!empty($__post['min_price']) && !empty(['max_price'])) {
                
//                 $args['meta_query'] = array(                    
//                     array(
//                             'key'     => '_price',
//                             'value'   => array(
//                                             str_replace('$','',$__post['min_price']),
//                                             str_replace('$','',$__post['max_price'])
//                                         ),
//                             'type'    => 'numeric',
//                             'compare' => 'BETWEEN',
//                     )                    
//                 );
//                 unset($_POST['min_price']);     //remove _POST `min_price` param
//                 unset($_POST['max_price']);     //remove _POST `max_price` param
//             }

//             $tax_query=array();                
            
//             $tax_query['relation']='AND';

//             if( isset($__post['_category']) OR isset($__post['_current_category']) ) {

//                 $_category_added=false;
//                 if(!empty($__post['_category'])) {

//                     foreach(explode(',', $__post['_category']) as $_category){

//                         if(isset($__post['cat_filter_'.$_category]) && (!empty(($__post['cat_filter_'.$_category])))) {                           
//                             $tax_query[]=array(
//                                 'taxonomy' => 'product_cat',
//                                 'field' => 'slug',
//                                 'terms' =>explode(',',$__post['cat_filter_'.$_category]),
//                                 'operator'=>'IN'
//                             );    
//                             $_category_added=true;                
//                         }
//                     }                                
//                 }
//                 if(!empty($__post['_current_category']) && $_category_added==false) {

//                     $tax_query[]=array(
//                         'taxonomy' => 'product_cat',
//                         'field' => 'slug',
//                         'terms' => explode(',',$__post['_current_category'])
//                     );
//                 }
//             }
            
//             if(!empty($__post['_attribute'])) {

//                 $attribute=explode(',', $__post['_attribute']);

//                 foreach ($attribute as $attr) {

//                     if(isset($__post['min_'.$attr]) && isset($__post['max_'.$attr])){
                        
//                         if ( is_numeric($__post['min_'.$attr]) && is_numeric($__post['max_'.$attr]) ) {
                            
//                                 $tax_query[]=array(
//                                     'taxonomy' => $attr,
//                                     'field' => 'slug',
//                                     'terms' => eo_wbc_attribute_range($attr,$__post['min_'.$attr],$__post['max_'.$attr],true),
//                                     'operator'=>'IN'
//                                 );
//                         }
//                         else {
//                             $tax_query[]=array(
//                                     'taxonomy' => $attr,
//                                     'field' => 'slug',
//                                     'terms' => eo_wbc_attribute_range($attr,$__post['min_'.$attr],$__post['max_'.$attr]),
//                                     'operator'=>'IN'
//                                 );
//                         }                   
//                     }
//                     elseif (isset($__post['checklist_'.$attr]) && !empty($__post['checklist_'.$attr])) {
//                         $tax_query[]=array(
//                             'taxonomy' => $attr,
//                             'field' => 'slug',
//                             'terms' => explode(',',$__post['checklist_'.$attr]),
//                             'operator'=>'IN'
//                         );     
//                     } 
//                 }
//             }            

//             $args['tax_query'] =$tax_query;            
//         }
     
//         //print_r($args);
//         $query = new WP_Query( $args );
//         //echo $query->request;                

//         if( $query->have_posts() ) :
//             $res=array();
//             while( $query->have_posts() ): $query->the_post();
//                 $post=$query->post;    

//                 //$post_attr=array_keys((new WC_Product( '2385'))->get_attributes());                

//                 $res[]=array(
//                     'ID'=>$post->ID,
//                     'post_title'=>$post->post_title,
//                     'post_name'=>$post->post_name,
//                     'image'=>(new WC_Product($post->ID))->get_image('thumbnail'),
//                     'shape'=>implode(',',wc_get_product_terms($post->ID, 'pa_shape', array( 'fields' => 'names' ) )),
//                     'caret'=>implode(',',wc_get_product_terms($post->ID, 'pa_carat', array( 'fields' => 'names' ) )),
//                     'color'=>implode(',',wc_get_product_terms($post->ID, 'pa_color', array( 'fields' => 'names' ) )),
//                     'clarity'=>implode(',',wc_get_product_terms($post->ID, 'pa_clarity', array( 'fields' => 'names'))),
//                     'cut'=>implode(',',wc_get_product_terms($post->ID,'pa_cut', array( 'fields' => 'names'))),
//                     'lab'=>implode(',',wc_get_product_terms($post->ID,'pa_lab_report', array( 'fields' => 'names'))),
//                     'price'=>(new WC_Product($post->ID))->get_price_html()//get_woocommerce_currency_symbol().((new WC_Product($post->ID))->price)
//                 );                
//             endwhile;

//             $res=array('items'=>$res,'count'=>$query->found_posts);

//             echo json_encode($res);
//             ///echo json_encode($_POST);
//             wp_reset_postdata();
//         else :
//             //echo json_encode($_POST);
//             echo json_encode(array());
//         endif;   
        
//         die();
//     }
// }

// //////////////////////////////////////////////////////////////////////////////////////////////////
// //  Enable non table based filter that loads whole page at front :)
// //////////////////////////////////////////////////////////////////////////////////////////////////
// if(isset($_GET['eo_wbc_filter'])) {    
//     add_filter('pre_get_posts',function($query ) {
    
//         if( $query->is_main_query() ) {

//             $tax_query=array('relation' => 'AND');

//             if( isset($_GET['_category']) OR isset($_GET['_current_category']) ){

//                 if(!empty($_GET['_category'])) {

//                     foreach(explode(',', $_GET['_category']) as $_category){

//                         if(isset($_GET['cat_filter_'.$_category]) && (!empty(($_GET['cat_filter_'.$_category])))) {                           
//                             $tax_query[]=array(
//                                 'taxonomy' => 'product_cat',
//                                 'field' => 'slug',
//                                 'terms' =>explode(',',$_GET['cat_filter_'.$_category]),
//                                 'operator'=>'IN'
//                             );                    
//                         }
//                     }                                
//                 }
//                 elseif(!empty($_GET['_current_category'])) {

//                     $tax_query[]=array(
//                         'taxonomy' => 'product_cat',
//                         'field' => 'slug',
//                         'terms' => explode(',',$_GET['_current_category'])
//                     );
//                 }
//             }

//             if(!empty($_GET['_attribute'])) {

//                 foreach (explode(',', $_GET['_attribute']) as $attr) {

//                     if(isset($_GET['min_'.$attr]) && isset($_GET['max_'.$attr])){
                        
//                         if ( is_numeric($_GET['min_'.$attr]) && is_numeric($_GET['max_'.$attr]) ) {
                            
//                             $tax_query[]=array(
//                                 'taxonomy' => $attr,
//                                 'field' => 'slug',
//                                 'terms' => eo_wbc_attribute_range($attr,$_GET['min_'.$attr],$_GET['max_'.$attr],true)
//                             );
//                         }
//                         else {

//                             $tax_query[]=array(
//                                 'taxonomy' => $attr,
//                                 'field' => 'slug',
//                                 'terms' => eo_wbc_attribute_range($attr,$_GET['min_'.$attr],$_GET['max_'.$attr])                    
//                             );
//                         }                   
//                     }
//                     elseif (isset($_GET['checklist_'.$attr]) && !empty($_GET['checklist_'.$attr])) {
//                         $tax_query[]=array(
//                             'taxonomy' => $attr,
//                             'field' => 'slug',
//                             'terms' => explode(',',$_GET['checklist_'.$attr])                    
//                         );     
//                     } 
//                 }
//             }           

//             $query->set( 'tax_query',$tax_query);                               

//         }
// /*        echo json_encode($tax_query);
//         exit(); */          
//     });
// }
 ?>