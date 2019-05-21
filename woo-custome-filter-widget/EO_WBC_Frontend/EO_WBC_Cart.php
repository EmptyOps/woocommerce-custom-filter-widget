<?php
class EO_WBC_Cart{

    public function __construct(){
        
        if(isset($_GET['EO_WBC_REMOVE'])){
            $this->eo_wbc_remove();
        }     

        if(WC()->session->get('EO_WBC_SETS'))//Destroy EO_WBC_SETS data if session is available
        {
            WC()->session->set('EO_WBC_SETS',NULL);
        }

        if(isset($_GET['empty_cart']) && sanitize_text_field($_GET['empty_cart'])==1){
            $this->eo_wbc_empty_cart();
        }        
        
        $this->eo_wbc_cart_service();
        $this->eo_wbc_add_css();
        $this->eo_wbc_render();       
    }
   
    private function eo_wbc_remove(){
    
        $eo_wbc_maps=WC()->session->get('EO_WBC_MAPS',array());   
        if(isset($eo_wbc_maps[$_GET['EO_WBC_REMOVE']])){
    		unset($eo_wbc_maps[sanitize_text_field($_GET['EO_WBC_REMOVE'])]);
	        WC()->session->set('EO_WBC_MAPS',$eo_wbc_maps);
	        	        
	        //Reload cart data
	        WC()->cart->empty_cart();	        
	        foreach ($eo_wbc_maps as $index=>$set)
	        {	 	        
	        	if($set["FIRST"]){       	
		        	wc()->cart->add_to_cart(
	                	$set["FIRST"][0],
	                	$set["FIRST"][1],
	                	($set["FIRST"][2]=='0'?NULL:$set["FIRST"][2]),
	                    ($set["FIRST"][2]=='0'?NULL:EO_WBC_Support::eo_wbc_get_product_variation_attributes($set["FIRST"][2]))
	                  );
	        	}

        		if($set["SECOND"])
	            {
	                wc()->cart->add_to_cart(
	                	$set["SECOND"][0],
	                	$set["SECOND"][1],
	                	($set["SECOND"][2]=='0'?NULL:$set["SECOND"][2]),
	                    ($set["SECOND"][2]=='0'?NULL:EO_WBC_Support::eo_wbc_get_product_variation_attributes($set["SECOND"][2]))
	                  );
	            }
	    	}	
		}                
    } 
        
    
    
    private function eo_wbc_empty_cart(){
        //empty cart on user request
        WC()->session->set('EO_WBC_SETS',NULL);
        WC()->session->set('EO_WBC_MAPS',NULL);
        WC()->session->set('EO_WBC_CART',NULL);
        WC()->cart->empty_cart();
        exit(wp_redirect(EO_WBC_Support::eo_wbc_get_cart_url()));
    }
    
    private function eo_wbc_add_css()
    {
        //Adding JQuery Library....
        add_action( 'wp_enqueue_scripts',function(){
            wp_enqueue_script('JQuery');
            wp_register_script('eo_wbc_cart_js',plugins_url('/js/eo_wbc_cart.js',__FILE__));
            wp_enqueue_script('eo_wbc_cart_js');
        });
    }
    
    private function eo_wbc_cart_service()
    {    	
        $eo_wbc_maps=WC()->session->get('EO_WBC_MAPS',array());
        foreach (wc()->cart->cart_contents as $cart_key=>$cart_item)
        {
            $product_count=0;
            $single_count=0;
            foreach ($eo_wbc_maps as $map)
            {
                if($map["FIRST"][0]==$cart_item["product_id"] && $map["FIRST"][2]==$cart_item["variation_id"]){
                    $product_count+=$map["FIRST"][1];
                    if (!$map["SECOND"]){
                        $single_count+=$map["FIRST"][1];
                    }
                }    
                if ($map["SECOND"] && $map["SECOND"][0]==$cart_item["product_id"] && $map["SECOND"][2]==$cart_item["variation_id"])
                {
                    $product_count+=$map["SECOND"][1];
                }
            }
            
            if ($product_count>0)
            {
                if ($product_count<$cart_item["quantity"])
                {
                    if($single_count>0)
                    {
                        foreach ($eo_wbc_maps as $map_key=>$map)
                        {
                            if($map["FIRST"][0]==$cart_item["product_id"] && $map["FIRST"][2]==$cart_item["variation_id"])
                            {
                                unset($eo_wbc_maps[$map_key]);
                            }
                        }                       
                        $eo_wbc_maps[]=array(
                            "FIRST"=>array(
                                (string)$cart_item["product_id"],
                                (string)($cart_item["quantity"]-$product_count)+$single_count,
                                (string)$cart_item["variation_id"]
                            ),
                            "SECOND"=>FALSE
                        );
                    }
                    else
                    {
                        $eo_wbc_maps[]=array(
                            "FIRST"=>array(
                                (string)$cart_item["product_id"],
                                (string)($cart_item["quantity"]-$product_count),
                                (string)$cart_item["variation_id"]
                            ),
                            "SECOND"=>FALSE
                        );
                    }
                }
            }
            else
            {
                $eo_wbc_maps[]=array(
                    "FIRST"=>array(
                        (string)$cart_item["product_id"],
                        (string)$cart_item["quantity"],
                        (string)$cart_item["variation_id"]
                    ),
                    "SECOND"=>FALSE
                );
            }
        }
        WC()->session->set('EO_WBC_MAPS',$eo_wbc_maps);            
    }
    
    private function eo_wbc_render()
    {
        //Removing Cart Table data.....
        //Adding Custome Cart Table Data.......        
        add_action('woocommerce_before_cart_contents',function(){
            ?>
            	<style>
                    tr.cart_item
                    {
                        display: none;
                    }
                    
                    [name="update_cart"]
                    {
                        display: none !important;   
                    }

                    table.cart img{
                        width: 150px !important;
                        height: auto !important;
                    }
                    .column {
                      float: left;
                      width: 40% !important;
                      padding: 5px;
                    }
                    .row{
                        padding: 2px !important;
                    }
                    .row::after {
                      content: "";
                      clear: both;
                      display: table;                      
                    }
                    .shop_table{
                        font-size: medium; 
                        text-align: left !important;                                             
                    }
                    .woocommerce table.shop_table th
                    {                        
                        padding-right: 2em !important;                        
                    }
                    #eo_wbc_extra_btn a{
                        margin-bottom: 2em;
                    }
                    #eo_wbc_extra_btn::after{
                        content: '\A';
                        white-space: pre;                         
                    }
                    @media screen and (max-width: 720px) {
                        td[data-title="Thumbnail"] {
                            display: flex !important;
                        }
                        span.column::before{
                            content: '\A\A';
                            white-space: pre;
                        }
                        #eo_wbc_extra_btn{
                            display: grid;
                        }                                             
                    }                    
                </style>
            <?php 
            $maps=(WC()->session->get('EO_WBC_MAPS'));            
            foreach ($maps as $index=>$map){
            	
            	$this->eo_wbc_cart_ui($index,$map);               
            }
        });
            
            // Adding Buttons
            // 1 Continue Shopping
            // 2 Empty Cart
          /*  add_action('woocommerce_after_cart_table',function(){
                echo '<div style="float:right;" id="eo_wbc_extra_btn"><a href="'.get_bloginfo('url').'" class="checkout-button button alt wc-backword">Continue Shopping</a><br style="display:none;" />
              <a href="./?EO_WBC=1&empty_cart=1" class="checkout-button button alt wc-backword">Empty Cart</a></div><div style="clear:both;"></div>';
            });*/
    }
    private function eo_wbc_cart_ui($index,$cart)
    {  
        $first=EO_WBC_Support::eo_wbc_get_product($cart['FIRST'][0]);
        $second=$cart['SECOND']?EO_WBC_Support::eo_wbc_get_product($cart['SECOND'][0]):FALSE;
        ?>
		<tr>
			<td data-title="">
				<a href="?EO_WBC=1&EO_WBC_REMOVE=<?php echo $index;?>" class="remove" aria-label="Remove this item" >&times;</a> 									
			</td>
			<td class="row" data-title="Thumbnail">
                <div style="display:grid;grid-template-columns: auto auto;">
    				<span><?php echo $first->get_image('thumbnail'); ?></span>
    				<?php if($cart['SECOND']):?>						
    				<span><?php echo $second->get_image('thumbnail'); ?></span>
				    <?php endif; ?>
                </div>
			</td>
			<td data-title="Product">			
				<p><?php _e($first->get_title().
				    ($cart['FIRST'][2]  ? "<br/>&nbsp; - &nbsp;".implode(',',EO_WBC_Support::eo_wbc_get_product_variation_attributes($cart['FIRST'][2])) :'')); ?></p>			
			
				<?php if($cart['SECOND']):?>
				<p><?php _e($second->get_title().
				       ($cart['SECOND'][2] ? "<br/>&nbsp; - &nbsp;".implode(',',EO_WBC_Support::eo_wbc_get_product_variation_attributes($cart['SECOND'][2])):'')); ?></p>
				<?php endif; ?>                               	
			</td>
			<td data-title="Price">				
				<p><?php _e(get_woocommerce_currency_symbol(get_option('woocommerce_currency'))." ".get_post_meta($cart['FIRST'][2]?$cart['FIRST'][2]:$cart['FIRST'][0],'_price',TRUE));?></p>
				<?php $price=(get_post_meta($cart['FIRST'][2]?$cart['FIRST'][2]:$cart['FIRST'][0],'_price',TRUE)*$cart['FIRST'][1]); ?>
			
			
			 	<?php if($cart['SECOND']):?>
				<p><?php _e(get_woocommerce_currency_symbol(get_option('woocommerce_currency'))." ".get_post_meta($cart['SECOND'][2]?$cart['SECOND'][2]:$cart['SECOND'][0],'_price',TRUE));?></p>
					<?php $price+=(get_post_meta($cart['SECOND'][2]?$cart['SECOND'][2]:$cart['SECOND'][0],'_price',TRUE)*$cart['SECOND'][1]); ?>
				<?php endif; ?>				
			</td>
			<td data-title="Quantity">
				<p><?php _e($cart['FIRST'][1]); ?></p>
				<?php if($cart['SECOND']):?>
				<p><?php _e($cart['SECOND'][1]); ?></p>
				<?php endif; ?>
			</td>
			<td data-title="Cost">
				<p><?php _e(get_woocommerce_currency_symbol(get_option('woocommerce_currency'))." ".$price); ?></p>
			</td>								
		</tr>
		<?php               
    }
    
}
?>

  									
  									