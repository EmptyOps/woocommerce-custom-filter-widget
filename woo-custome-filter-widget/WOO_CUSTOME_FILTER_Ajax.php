<?php
class WOO_CUSTOME_FILTER_Ajax
{

    function __construct(){

        $this->filter();
    }

	private function attribute_range($term,$min,$max,$numeric_range=false) {

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
                var_dump(get_terms(array('taxonomy'=>$term,'hide_empty'=>false)));
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
        var_dump($list);
        return $list;
    }

    private function filter() {

        if(isset($_GET['woo_custome_filter'])) {    

            var_dump($_GET);

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
                                        'terms' => $this->attribute_range($attr,$_GET['min_'.$attr],$_GET['max_'.$attr],true)
                                    );
                                }
                                else {

                                    $tax_query[]=array(
                                        'taxonomy' => $attr,
                                        'field' => 'slug',
                                        'terms' => $this->attribute_range($attr,$_GET['min_'.$attr],$_GET['max_'.$attr])                    
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
                            elseif (isset($_GET['dropdown_'.$attr]) && !empty($_GET['dropdown_'.$attr])) {
                                $tax_query[]=array(
                                    'taxonomy' => $attr,
                                    'field' => 'slug',
                                    'terms' => explode(',',$_GET['dropdown_'.$attr])                    
                                );     
                            } 
                        }
                    }                                                   
                    $query->set( 'tax_query',$tax_query);  
                    return $query;                                             
                }           
            });
        }
    }			
}