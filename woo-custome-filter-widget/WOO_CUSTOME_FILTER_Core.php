<?php
class WOO_CUSTOME_FILTER_Core   {   

    public function __construct(){

        if (class_exists( 'WooCommerce' )){
            //if request from Admin
            if(is_admin()){	
                //Go to eo_wbc_admin method
                $this->backend();                 
            }
            //if request from User
            else{				
                //Go to eo_wbc_frontend method
                $this->frontend();
            }            
        }        
    }

    private function backend() { 
        
        if($_POST && isset($_POST['_wpnonce'])) {
            

            require_once apply_filters('woo_custome_filter_action','application/backend/WOO_CUSTOME_FILTER_Action.php',30);
            
            if(class_exists('WOO_CUSTOME_FILTER_Action')){                
                new WOO_CUSTOME_FILTER_Action();
            }
        }
        

        require_once apply_filters('woo_custome_filter_config','application/backend/WOO_CUSTOME_FILTER_Config.php',30);
        
        if(class_exists('WOO_CUSTOME_FILTER_Config')){
            
            new WOO_CUSTOME_FILTER_Config();
        }
        
    }

    private function frontend() {

        add_action('template_redirect',function() {

            if(is_product_category() OR is_shop()) { 
                
                require_once apply_filters('woo_custome_filter_widget','application/frontend/WOO_CUSTOME_FILTER_Widget.php',30);

                add_action( 'woocommerce_before_shop_loop',function(){  

                    if (class_exists('WOO_CUSTOME_FILTER_Widget')) {

                        new WOO_CUSTOME_FILTER_Widget();                                
                    }

                },30);                
            }   
        });
    }
}

?>