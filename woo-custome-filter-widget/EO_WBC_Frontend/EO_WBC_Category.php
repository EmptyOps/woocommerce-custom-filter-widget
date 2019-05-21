<?php
class EO_WBC_Category
{
    public function __construct()
    {                   
        //If add to cart triggred
        // Detection : only one category item get length > 0 
        //   i.e. using XOR check if only one of two have been set.
        if( !empty($_GET['CART']) && empty($_GET['EO_CHANGE']) && ( empty($_GET['FIRST']) XOR empty($_GET['SECOND']) ) ) {
            //Iff condition is mutual exclusive, store it to  the session.
            $this->eo_wbc_add_to_cart();            
        } 

        //if Current-Category is either belongs to FIRST OR SECOND Category then initiate application        
        if(
    		$this->eo_wbc_get_category()==get_option('eo_wbc_first_slug') 
    		  OR
    		$this->eo_wbc_get_category()==get_option('eo_wbc_second_slug')
        ){
            if(get_option('eo_wbc_filter_enable')=='1'){
                $this->eo_wbc_add_filters();      
            }            
            $this->eo_wbc_add_breadcrumb();                  	
            $this->eo_wbc_render(); 
        }
    }

    private function eo_wbc_add_to_cart() {
        
        $cart=base64_decode(sanitize_text_field($_GET['CART']),TRUE);        
        if(!empty($cart)){

            $cart=str_replace("\\",'',$cart);
            $cart=(array)json_decode($cart);
            
            if(is_array($cart) OR is_object($cart)) {
                   
                //if product belongs to first target;
                if (get_option('eo_wbc_first_slug')==$cart['eo_wbc_target']) {

                    WC()->session->set('EO_WBC_SETS',
                        array(
                            'FIRST'=>array(
                                            $cart['eo_wbc_product_id'],
                                            $cart['quantity'],
                                            (isset($cart['variation_id'])?$cart['variation_id']:NULL)
                                        ),
                            'SECOND'=>NULL
                                                
                    ));
                }
                //if product belongs to second target;
                elseif (get_option('eo_wbc_second_slug')==$cart['eo_wbc_target']) {

                    WC()->session->set('EO_WBC_SETS',
                        array(
                            'FIRST'=>NULL,
                            'SECOND'=>array(
                                            $cart['eo_wbc_product_id'],
                                            $cart['quantity'],
                                            (isset($cart['variation_id'])?$cart['variation_id']:NULL)
                                        )
                    ));
                }                                              
            }                        
        }
    }

    private function eo_wbc_add_filters() {
        //Add product filter widget...
        
        add_action( 'woocommerce_before_shop_loop',function(){            
            if (class_exists('EO_WBC_Filter_Widget')) {
                new EO_WBC_Filter_Widget();                                
            }
        },30);         
        
    }

    private function eo_wbc_add_breadcrumb()
    {	        
    	//Add Breadcumb at top....		
        add_action( 'woocommerce_before_shop_loop',function(){            
            echo EO_WBC_Breadcrumb::eo_wbc_add_breadcrumb(sanitize_text_field($_GET['STEP']),sanitize_text_field($_GET['BEGIN'])).'<br/><br/>';
        }, 20);
    }

    private function eo_wbc_render()
    {           
        //Hide Add to cart in Shop and product_category page
        remove_action( 'woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart');
                
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );    



        //Add information to end of pemalink of product
        add_filter( 'post_type_link',function($url){
            return  $url.'?EO_WBC=1'.
                            '&BEGIN='.sanitize_text_field($_GET['BEGIN']).
                            '&STEP='.sanitize_text_field($_GET['STEP']).                            
                            '&FIRST='.
                            (
                                $this->eo_wbc_get_category()==get_option('eo_wbc_first_slug') 
                                    ?
                                ''
                                    :
                                (
                                    !empty($_GET['FIRST'])
                                        ? 
                                    sanitize_text_field( $_GET['FIRST'])
                                        :
                                    ''
                                )
                            ).
                            '&SECOND='.
                            (
                                $this->eo_wbc_get_category()==get_option('eo_wbc_second_slug')
                                    ?
                                ''
                                    :
                                (
                                    !empty($_GET['SECOND'])
                                        ?
                                    sanitize_text_field($_GET['SECOND'])
                                        :
                                    ''
                                )
                            );
        });
    }
    
    private function eo_wbc_id_2_slug($id)
    {
        return get_term_by('id',$id,'product_cat')->slug;
    }
    
    /**
     * Function to find current page's product super category
     * @param null
     * @return string
     */
    public function eo_wbc_get_category()
    {        
        global $wp_query;        
        
        //get list of slug which are ancestors of current page item's category
        $term_slug=array_map(array('self',"eo_wbc_id_2_slug"),get_ancestors($wp_query->get_queried_object()->term_id, 'product_cat'));


        //append current page's slug so that create complete list of terms including current term even if it is parent.
        $term_slug[]=$wp_query->get_queried_object()->slug;                 

        if(in_array(get_option('eo_wbc_first_slug'),$term_slug))
        {
            return get_option('eo_wbc_first_slug');
        }
        elseif(in_array(get_option('eo_wbc_second_slug'),$term_slug))
        {
            return get_option('eo_wbc_second_slug');
        }
    }
}
?>