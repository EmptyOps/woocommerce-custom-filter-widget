<?php
class EO_WBC_Filter
{
	function eo_wbc_attribute_range($term,$min,$max,$numeric_range=false) {

        $list=array();
        if($min==$max) {
            $list[]=$min;
        }
        else {
            if($numeric_range)
            {
                foreach(get_terms(array('taxonomy'=>$term,'hide_empty'=>false)) as $term_obj) {                
                    if(($min<=$term_obj->name) && ($max>=$term_obj->name))
                    {
                        $list[]=$term_obj->slug;
                    }
                }
            }
            else
            {
                $show_flag=FALSE;
                foreach(get_terms(array('taxonomy'=>$term,'hide_empty'=>false)) as $term_obj) {
                    
                    if($term_obj->slug==$min && $show_flag==FALSE)
                    {
                        $list[]=$term_obj->slug;
                        $show_flag=TRUE;
                    }
                    elseif($show_flag==TRUE)
                    {
                        if( $term_obj->slug == $max)
                        {
                            $list[]=$term_obj->slug;
                            $show_flag=FALSE;                    
                            break;
                        }
                        else
                        {
                            $list[]=$term_obj->slug;
                        }
                    }            
                }
            }                
        }        
        return $list;
    }
	
	///////////////////////////////////////////////////////
	///////////////////////////////////////////////////////
	function eo_wbc_filter() {

        $__post=$_POST;

        $args = array(          
            'posts_per_page' => apply_filters('loop_shop_per_page',12),                  
            'post_type' => ['product','product_variation'],
            'orderby' => 'title',
            'post_status'=>'publish',
            'paged' => ((!empty($__post['_paged_']))?$__post['_paged_']:1),
        );

        
        if(!empty($__post))
        {
            // price range query
            if(!empty($__post['min_price']) && !empty($__post['max_price'])) {
                
                $args['meta_query'] = array(                    
                    array(
                            'key'     => '_price',
                            'value'   => array(
                                            str_replace('$','',$__post['min_price']),
                                            str_replace('$','',$__post['max_price'])
                                        ),
                            'type'    => 'numeric',
                            'compare' => 'BETWEEN',
                    )                    
                );
                unset($_POST['min_price']);     //remove _POST `min_price` param
                unset($_POST['max_price']);     //remove _POST `max_price` param
            }

            $tax_query=array();                
            
            $tax_query['relation']='AND';

            if( isset($__post['_category']) OR isset($__post['_current_category']) ) {

                $_category_added=false;
                if(!empty($__post['_category'])) {

                    foreach(explode(',', $__post['_category']) as $_category){

                        if( isset($__post['cat_filter_'.$_category]) && !empty($__post['cat_filter_'.$_category]) ) {                           
                            $tax_query[]=array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' =>explode(',',$__post['cat_filter_'.$_category]),
                                'operator'=>'IN'
                            );    
                            $_category_added=true;                
                        }
                    }                                
                }
                if(!empty($__post['_current_category']) && $_category_added==false) {

                    $tax_query[]=array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => explode(',',$__post['_current_category'])
                    );
                }
            }
            
            if(!empty($__post['_attribute'])) {

                $attribute=explode(',', $__post['_attribute']);

                foreach ($attribute as $attr) {

                    if(isset($__post['min_'.$attr]) && isset($__post['max_'.$attr])){
                        
                        if ( is_numeric($__post['min_'.$attr]) && is_numeric($__post['max_'.$attr]) ) {
                            
                                $tax_query[]=array(
                                    'taxonomy' => $attr,
                                    'field' => 'slug',
                                    'terms' => $this->eo_wbc_attribute_range($attr,$__post['min_'.$attr],$__post['max_'.$attr],true),
                                    'operator'=>'IN'
                                );
                        }
                        else {
                            $tax_query[]=array(
                                    'taxonomy' => $attr,
                                    'field' => 'slug',
                                    'terms' => $this->eo_wbc_attribute_range($attr,$__post['min_'.$attr],$__post['max_'.$attr]),
                                    'operator'=>'IN'
                                );
                        }                   
                    }
                    elseif (isset($__post['checklist_'.$attr]) && !empty($__post['checklist_'.$attr])) {
                        $tax_query[]=array(
                            'taxonomy' => $attr,
                            'field' => 'slug',
                            'terms' => explode(',',$__post['checklist_'.$attr]),
                            'operator'=>'IN'
                        );     
                    } 
                }
            }            

            $args['tax_query'] =$tax_query;            
        }
     
        //print_r($args);
        $query = new WP_Query( $args );
        //echo $query->request;                
        $res=array();
        if( $query->have_posts() ) :
            $res=array();
            while( $query->have_posts() ): $query->the_post();
                $post=$query->post;    

                //$post_attr=array_keys((new WC_Product( '2385'))->get_attributes());                

                $res[]=array(
                    'ID'=>$post->ID,
                    'post_title'=>$post->post_title,
                    'post_name'=>$post->post_name,
                    'image'=>(new WC_Product($post->ID))->get_image('thumbnail'),
                    'shape'=>implode(',',wc_get_product_terms($post->ID, 'pa_shape', array( 'fields' => 'names' ) )),
                    'caret'=>implode(',',wc_get_product_terms($post->ID, 'pa_carat', array( 'fields' => 'names' ) )),
                    'color'=>implode(',',wc_get_product_terms($post->ID, 'pa_color', array( 'fields' => 'names' ) )),
                    'clarity'=>implode(',',wc_get_product_terms($post->ID, 'pa_clarity', array( 'fields' => 'names'))),
                    'cut'=>implode(',',wc_get_product_terms($post->ID,'pa_cut', array( 'fields' => 'names'))),
                    'lab'=>implode(',',wc_get_product_terms($post->ID,'pa_lab_report', array( 'fields' => 'names'))),
                    'price'=>(new WC_Product($post->ID))->get_price_html()//get_woocommerce_currency_symbol().((new WC_Product($post->ID))->price)
                );                
            endwhile;

            $res=array('items'=>$res,'count'=>$query->found_posts,'page'=>((isset($__post['_paged_']))?$__post['_paged_']:'1'));

            echo json_encode($res);
            ///echo json_encode($_POST);
            wp_reset_postdata();
        else :
            //echo json_encode($_POST);
            $res=array('items'=>$res,'count'=>$query->found_posts,'page'=>((isset($__post['_paged_']))?$__post['_paged_']:'1'));
            echo json_encode($res);
        endif;   
        
        die();
    }
	

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//  Enable non table based filter that loads whole page at front :)
	//////////////////////////////////////////////////////////////////////////////////////////////////
	function __construct(){

		if(isset($_GET['eo_wbc_filter'])) {    

		    add_filter('pre_get_posts',function($query ) {
		    
		        if( $query->is_main_query() ) {

		            $tax_query=array('relation' => 'AND');

		            if( isset($_GET['_category']) OR isset($_GET['_current_category']) ){

		                if(!empty($_GET['_category'])) {

		                    foreach(explode(',', $_GET['_category']) as $_category){

		                        if(isset($_GET['cat_filter_'.$_category]) && (!empty($_GET['cat_filter_'.$_category])) ) {                           
		                            $tax_query[]=array(
		                                'taxonomy' => 'product_cat',
		                                'field' => 'slug',
		                                'terms' =>explode(',',$_GET['cat_filter_'.$_category]),
		                                'operator'=>'IN'
		                            );                    
		                        }
		                    }                                
		                }
		                elseif(!empty($_GET['_current_category'])) {

		                    $tax_query[]=array(
		                        'taxonomy' => 'product_cat',
		                        'field' => 'slug',
		                        'terms' => explode(',',$_GET['_current_category'])
		                    );
		                }
		            }

		            if(!empty($_GET['_attribute'])) {

		                foreach (explode(',', $_GET['_attribute']) as $attr) {

		                    if(isset($_GET['min_'.$attr]) && isset($_GET['max_'.$attr])){
		                        
		                        if ( is_numeric($_GET['min_'.$attr]) && is_numeric($_GET['max_'.$attr]) ) {
		                            
		                            $tax_query[]=array(
		                                'taxonomy' => $attr,
		                                'field' => 'slug',
		                                'terms' => $this->eo_wbc_attribute_range($attr,$_GET['min_'.$attr],$_GET['max_'.$attr],true)
		                            );
		                        }
		                        else {

		                            $tax_query[]=array(
		                                'taxonomy' => $attr,
		                                'field' => 'slug',
		                                'terms' => $this->eo_wbc_attribute_range($attr,$_GET['min_'.$attr],$_GET['max_'.$attr])                    
		                            );
		                        }                   
		                    }
		                    elseif (isset($_GET['checklist_'.$attr]) && !empty($_GET['checklist_'.$attr])) {
		                        $tax_query[]=array(
		                            'taxonomy' => $attr,
		                            'field' => 'slug',
		                            'terms' => explode(',',$_GET['checklist_'.$attr])                    
		                        );     
		                    } 
		                }
		            }           
                    $query->set( 'paged' ,( !empty($__post['_paged_']) ? $__post['_paged_']:1 ) );
		            $query->set( 'tax_query',$tax_query);                               

		        }
		/*        echo json_encode($tax_query);
		        exit(); */          
		    });
		}

	}	
}