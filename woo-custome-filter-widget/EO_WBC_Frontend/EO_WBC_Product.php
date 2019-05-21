<?php
class EO_WBC_Product
{
    public function __construct()
    {       
        $this->eo_wbc_config();            //Disable 'Add to Cart Button' and Set 'Sold Individually'
        $this->eo_wbc_add_breadcrumb();    //Add Breadcrumb        
        $this->eo_wbc_render();            //Render View and Routings                                       
    }
    
    private function eo_wbc_config()
    {        
        //Remove add to cart button
        remove_action( 
            'woocommerce_after_shop_loop_item',
            'woocommerce_template_loop_add_to_cart'
        );
        add_filter('woocommerce_product_single_add_to_cart_text',function(){
            return __('Continue','woocommerce');
        });
    }

    private function eo_wbc_add_breadcrumb()
    {   
        //Adding Breadcrumb
        add_action( 'woocommerce_before_single_product',function(){

            echo EO_WBC_Breadcrumb::eo_wbc_add_breadcrumb(
                                            sanitize_text_field($_GET['STEP']),
                                            sanitize_text_field($_GET['BEGIN'])
                                        ).'<br/><br/>';
        }, 15 );
    }
    
    private function eo_wbc_render()
    {
        //Registering Scripts : JavaScript
        add_action( 'wp_enqueue_scripts',function(){

            global $post;
            wp_register_script(
                'eo_wbc_add_to_cart_js',
                plugins_url(
                    'js/eo_wbc_single_add_to_cart.js',
                    __FILE__
                ),
                array('jquery')
            );
            
            wp_localize_script(
                'eo_wbc_add_to_cart_js',
                'eo_wbc_object',
                array('url'=>$this->eo_wbc_product_route())
            );            
            wp_enqueue_script('eo_wbc_add_to_cart_js');
        });
          
        //Adding own ADD_TO_CART_BUTTON
        add_action('wp_footer',function(){            
        ?>
       	<script type="text/javascript">
    		jQuery(".single_add_to_cart_button.button.alt").ready(function(){
                jQuery('form.cart').prepend("<input type='hidden' name='eo_wbc_target' value='<?php echo $this->eo_wbc_get_category(); ?>'/><input type='hidden' name='eo_wbc_product_id' value='<?php global $post; echo $post->ID; ?>'/>")
    			jQuery(".single_add_to_cart_button.button.alt:not(.disabled)").replaceWith(
    			     "<a href='#' id='eo_wbc_add_to_cart' class='single_add_to_cart_button button alt'>"
                     +"<?php _e('Continue'); ?>"
                     +"</a>"
                    );
    			});
    	</script>
       <?php    
       });
    }
    
    private function eo_wbc_product_route(){

        global $post;
        $url=null;        
        $category=$this->eo_wbc_get_category();        
        if(sanitize_text_field($_GET['STEP'])==1)
        {   

            if(!empty($_GET['CART']) && !empty($_GET['REDIRECT']) && sanitize_text_field($_GET['REDIRECT'])==1)
            {    
                //if redirec signal is set and cart data are ready then
                //relocate user to target path.
                if($category==get_option('eo_wbc_first_slug')){
                    $category_link=$this->eo_wbc_category_link();
                    $url=get_bloginfo('url').'/product-category/'.$category_link
                        .'EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=2&FIRST='.$post->ID.'&SECOND='.sanitize_text_field($_GET['SECOND'])
                        ."&CART=".sanitize_text_field($_GET['CART']).'&CAT_LINK='.substr($category_link,0,strpos($category_link,'/'));
                }
                elseif($category==get_option('eo_wbc_second_slug')){
                    $category_link=$this->eo_wbc_category_link();
                    $url=get_bloginfo('url').'/product-category/'.$category_link
                        .'EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=2&FIRST='.sanitize_text_field($_GET['FIRST']).'&SECOND='.$post->ID
                        ."&CART=".sanitize_text_field($_GET['CART']).'&CAT_LINK='.substr($category_link,0,strpos($category_link,'/'));
                }                                
                header("Location: {$url}");
            }
            else
            {
                if($category==get_option('eo_wbc_first_slug')){
                    $url=get_permalink($post->ID)
                        .'?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=1&FIRST='.$post->ID.'&SECOND='.sanitize_text_field($_GET['SECOND'])."&REDIRECT=1";
                }
                elseif($category==get_option('eo_wbc_second_slug')){
                    $url=get_permalink($post->ID)
                        .'?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=1&FIRST='.sanitize_text_field($_GET['FIRST']).'&SECOND='.$post->ID."&REDIRECT=1";
                }                          
            }            
        }
        
        elseif(sanitize_text_field($_GET['STEP'])==2)
        {
            if(sanitize_text_field($_GET['FIRST'])==='' OR $category==get_option('eo_wbc_first_slug'))
            {
                $url=get_bloginfo('url').get_option('eo_wbc_review_page')
                    .'?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                    .'&STEP=3&FIRST='.$post->ID.'&SECOND='.sanitize_text_field($_GET['SECOND']);
            }
            elseif (sanitize_text_field($_GET['SECOND'])==='' OR $category==get_option('eo_wbc_second_slug'))
            {
                $url=get_bloginfo('url').get_option('eo_wbc_review_page')
                    .'?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                    .'&STEP=3&FIRST='.sanitize_text_field($_GET['FIRST']).'&SECOND='.$post->ID;
            }
            else
            {
                $url='';
            }
        }        
        return $url;
    }
    
    /**
     * @return string
     *  string of mapped category to current category item
     */
    private function eo_wbc_category_link($variable_status=FALSE){

        global $post;

        $variable_status=FALSE;//status if product is varaible in nature.
        $cart=NULL;//storage variable for cart data if redirected from 'Add to cart' action.

        if(isset($_GET['CART']))
        {
            $cart=str_replace("\\",'',base64_decode(sanitize_text_field($_GET['CART']),TRUE));
            $cart=(array)json_decode($cart);
         
            if(!empty($cart['variation_id']))
            {
                $variable_status=TRUE;
            }    
        }
                
        $terms=wp_get_post_terms($post->ID,get_taxonomies(),array('fields'=>'ids'));                            
        if($variable_status)
        {   
            $new_terms=array();
            foreach ($terms as $term_id) {                
                $term_object=get_term_by('term_taxonomy_id',$term_id,'category');                
                if($term_object->taxonomy=='product_cat' 
                    or
                    in_array(
                        $term_object->slug,
                        array_values(wc_get_product_variation_attributes($cart['variation_id']))) 
                ){
                    $new_terms[]=$term_id;
                }          
            }
            $terms=$new_terms;
        }

        $category=array();        
        foreach ($terms as $term)
        {
            global $wpdb;
            if($this->eo_wbc_get_category()==get_option('eo_wbc_first_slug')){
                $query="select * from `{$wpdb->prefix}eo_wbc_cat_maps` "."where first_cat_id={$term}";
            }
            elseif($this->eo_wbc_get_category()==get_option('eo_wbc_second_slug')){
                $query="select * from `{$wpdb->prefix}eo_wbc_cat_maps` "."where second_cat_id={$term}";
            }            
            
            $maps=$wpdb->get_results($query,'ARRAY_N');                
            foreach ($maps as $map){

                if($map[0]==$term){
                   
                    $category[]=$map[1];
                }
                else{
                   
                    $category[]=$map[0];
                }
            }
        }                        
        //remove empty array space and duplicate values
        $category=array_unique(array_filter($category));                
        $cat=array();//array to hold category slugs
        $tax=array();//array to hold taxonomy slugs
        foreach ($category as $term_id)
        {
            $term_object=get_term_by('term_taxonomy_id',$term_id,'category');            

            if(!empty($term_object)){
                if($term_object->taxonomy=='product_cat'){
                    $cat[]=$term_object->term_id;
                }
                else
                {
                    $tax[]=$term_object->term_id;                                   
                }
            }                        
        }
        $link='';        
        //if category maping is available then make url filter to category
        //else add default category to the link.
        if(is_array($cat) && count($cat)>0){
            foreach ($cat as $term_id){
                $link.=get_term_by('term_taxonomy_id',$term_id,'category')->slug.',';    
            }                        
            $link=substr($link,0,-1);//remove ',' at the end of category filter.
        }
        else
        {
            $link.=($this->eo_wbc_get_category()==get_option('eo_wbc_first_slug'))
                        ?
                    get_option('eo_wbc_second_slug')
                        :
                    get_option('eo_wbc_first_slug');                    
        }

        $link.="/?";        

        if(is_array($tax) && count($tax)>0){            
            
            $filter_query=array();
            foreach ($tax as $tax_id) {
                $term_object=get_term_by('term_taxonomy_id',$tax_id,'category');  
                if(!empty($term_object)){
                    $filter_query[str_replace('pa_','',$term_object->taxonomy)][]=$term_object->slug;    
                }                             
            }            

            foreach ($filter_query as $filter_name => $filters) {                
                $link.="query_type_{$filter_name}=or&filter_{$filter_name}=".implode(',',$filters)."&";
            }       
        }        
        return $link;
    }

    /**
     * @method Returns Current-Product's top level catgory
     * @return string
     */
    private function eo_wbc_get_category()
    {
        global $post;
        $terms = get_the_terms( $post->ID, 'product_cat' );        
        $term_slug=[];
        foreach ($terms as $term) {
            $term_slug[]=$term->slug;
        }        
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