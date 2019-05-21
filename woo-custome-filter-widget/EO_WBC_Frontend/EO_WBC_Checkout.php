<?php
class EO_WBC_Checkout{
    public function __construct()
    {        
        $this->eo_wps_add_js();//Load Jquery and product info contents...     
/*
        add_action('woocommerce_cart_calculate_fees',function($cart){
            wc()->cart->set_discount_total("1000");
            var_dump(wc()->cart->get_total_discount( ));
            var_dump(wc()->cart->get_cart_total());
            $cart=WC()->cart;
            
            $discount = $cart->subtotal * 0.1;
            $cart->add_fee( 'You saved 10% on bundled product.', -$discount);
        });*/

/*        wc()->cart->add_fee('Handling',500);

        var_dump(wc()->cart->get_fees());

    */ 



    }

    private function eo_wps_add_js()
    {
        //Adding JQuery Library....
        add_action( 'wp_enqueue_scripts',function(){
            wp_enqueue_script('JQuery');
        });
            
        //Add cutomization on load....
        add_action('wp_footer',function(){
            echo "<script>
                jQuery(document).ready(function(){
                    jQuery('.cart_item').remove();
                    jQuery('table.shop_table>tbody').append(\"".$this->eo_wbc_render()."\");
                        
                    jQuery(document).on('updated_checkout',function(){
                        jQuery('.cart_item').remove();
                        jQuery('table.shop_table>tbody').append(\"".$this->eo_wbc_render()."\");
                    });
                });
            </script>";
        });                
    }
        
    private function eo_wbc_render()
    {        
        $res="";
        if(WC()->session->get('EO_WBC_MAPS')){
            
            foreach (WC()->session->get('EO_WBC_MAPS') as $map){
                
                $res.=$this->checkout_rows($map);
            }
            return $res;
        }
        else{
            
            return NULL;
        }
    }
    
    private function checkout_rows($map)
    {
            $price=0;
            $html="<tr><td><div><strong>".
                __(EO_WBC_Support::eo_wbc_get_product($map['FIRST'][0])->get_title().($map['FIRST'][2]  ? "&nbsp; -&nbsp;".implode(',',EO_WBC_Support::eo_wbc_get_product_variation_attributes($map['FIRST'][2])):''))."</strong>&nbsp;X&nbsp;{$map["FIRST"][1]}</div>";
                $price+=get_post_meta($map['FIRST'][2]?$map['FIRST'][2]:$map['FIRST'][0],'_price',TRUE)*$map["FIRST"][1];
            if($map["SECOND"])
            {
                $html.="<strong style='color: red;'>+</strong><div><strong>".
                    __(EO_WBC_Support::eo_wbc_get_product($map['SECOND'][0])->get_title().($map['SECOND'][2]  ? "&nbsp; -&nbsp;".implode(',',EO_WBC_Support::eo_wbc_get_product_variation_attributes($map['SECOND'][2])):''))."</strong>&nbsp;X&nbsp;{$map["SECOND"][1]}</div></td>";
                    $price+=get_post_meta($map['SECOND'][2]?$map['SECOND'][2]:$map['SECOND'][0],'_price',TRUE)*$map["SECOND"][1];
            }
            $html.="<td><h6>".__(get_woocommerce_currency_symbol(get_option('woocommerce_currency'))." ".$price)."</h6></td></tr>";
            return $html;
    }
}
?>