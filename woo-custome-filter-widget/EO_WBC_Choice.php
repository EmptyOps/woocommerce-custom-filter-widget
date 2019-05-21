<?php
class EO_WBC_Choice{   

    public function __construct(){

        if (class_exists( 'WooCommerce' )){
            //if request from Admin
            if(is_admin()){	
                //Go to eo_wbc_admin method
                $this->eo_wbc_admin();                 
            }
            //if request from User
            else{				
                //Go to eo_wbc_frontend method
                $this->eo_wbc_frontend();
            }            
        }        
    }
    
    private function eo_wbc_admin_sanitize(){

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

    public function eo_wbc_admin(){
        
        //Supporting function for older versions of worpress and woocommerce.
        require_once apply_filters('eo_wbc_admin_support','EO_WBC_Admin/EO_WBC_Support.php',30);
        //Load view for orders recived from customers.
        require_once apply_filters('eo_wbc_admin_order_view','EO_WBC_Admin/EO_WBC_Orders/EO_WBC_Orders.php',30);
        //Lad view for plugin configuration.
        require_once 'EO_WBC_Admin/EO_WBC_Config/EO_WBC_Config.php';                

        //perform action routines only if occured in our pages.
        if($_POST && isset($_POST['_wpnonce']) && isset($_POST['eo_wbc_action'])){
            //sanitize all user input before usage.
            $this->eo_wbc_admin_sanitize();
            //Initiate Actions.
            require_once apply_filters('eo_wbc_admin_config_actions','EO_WBC_Admin/EO_WBC_Config/EO_WBC_Actions.php',30);
            new EO_WBC_Actions();
        }
        
        //Show error message if application is not configured.
        if(get_option('eo_wbc_config_category')=="0" OR get_option('eo_wbc_config_map')=="0"){
            
            add_action('admin_notices',function (){
                
                echo "<div class='notice notice-error'><p>".__( '<strong>WooCommerce Bundle Choice</strong> hasn`t configured or partially configured yet.', 'woocommerce' )."</p></div>";
            },30);
        }                     
        
        //Initiate Configuration Menu        
        new EO_WBC_Config();       
        
        //Initiate Orders Page
        new EO_WBC_Orders();        
    }
    
    public function eo_wbc_frontend()
    {          
        //Perform plugin's task only if both configuration and mapping are done.
        if(
            get_option('eo_wbc_config_category')=="1"
                &&
            get_option('eo_wbc_config_map')=="1"
        ){
            //Frontend service method
            $this->eo_wbc_front_service();

            //Act on when whole page with template is loaded for effect.
            add_action('template_redirect',function(){
                
                //Start session by saving cookie if not available.                
                if(/*!isset( $_COOKIE[ $this->_cookie ] ) || $this->_has_cookie || */!is_user_logged_in())
                {
                    wc()->session->set_customer_session_cookie(TRUE);
                }

                //Supporting function for older versions of worpress and woocommerce.
                if(!class_exists('EO_WBC_Support'))       
                {
                    require_once apply_filters('eo_wbc_front_support','EO_WBC_Frontend/EO_WBC_Support.php',33);
                }
                //Front Page to provide LINK to Select BEGIN WITH PRODUCT
                if(is_front_page())                    
                {   
                    require_once apply_filters('eo_wbc_front_home','EO_WBC_Frontend/EO_WBC_Home.php',35);
                    new EO_WBC_Home();                    
                }

                //Condition check for alteration of flow if not a Home or Cart page.
                if(isset($_GET['EO_WBC']) && sanitize_text_field($_GET['EO_WBC'])==1){
                    
                    //Product Category Page ie: First / Second Product
                    if(is_product_category()){
                           
                            require_once apply_filters('eo_wbc_front_breadcrumb','EO_WBC_Frontend/EO_WBC_Breadcrumb.php',35);
                            EO_WBC_Breadcrumb::eo_wbc_add_css();                            

                            require_once apply_filters('eo_wbc_front_filter_widget','EO_WBC_Frontend/EO_WBC_Filter_Widget.php',35);
                            
                            require_once apply_filters('eo_wbc_front_category','EO_WBC_Frontend/EO_WBC_Category.php',35);
                            new EO_WBC_Category();
                    }
                    
                    //Single Product description page
                    if(is_product()){

                        require_once apply_filters('eo_wbc_front_breadcrumb','EO_WBC_Frontend/EO_WBC_Breadcrumb.php',35);
                        EO_WBC_Breadcrumb::eo_wbc_add_css();
                        
                        require_once apply_filters('eo_wbc_front_product','EO_WBC_Frontend/EO_WBC_Product.php',35);
                        new EO_WBC_Product();                    
                    }
                    
                    //Review page to confirm adding SET to cart
                    if(is_page('Product Review'))
                    {   
                        require_once apply_filters('eo_wbc_front_breadcrumb','EO_WBC_Frontend/EO_WBC_Breadcrumb.php',35);
                        EO_WBC_Breadcrumb::eo_wbc_add_css();
                        
                        //ob_start();//Avoid sending header information                            
                            require_once apply_filters('eo_wbc_front_review','EO_WBC_Frontend/EO_WBC_Review.php',35);
                            new EO_WBC_Review(); 
                          //  ob_clean();                           
                        //ob_flush();//Flush and send header information
                    }
                }
                
                //Cart Page to enlist SETS added to the cart
                if(is_cart()){                
                    if(wc()->session->get("EO_WBC_MAPS",FALSE)){
                        
                        require_once apply_filters('eo_wbc_front_cart','EO_WBC_Frontend/EO_WBC_Cart.php',35);
                        new EO_WBC_Cart();
                    }                    
                }
                
                //Checkout page to make payment and confirm order
                if(is_checkout()){
                    require_once apply_filters('eo_wpc_front_checkout','EO_WBC_Frontend/EO_WBC_Checkout.php',35);
                    new EO_WBC_Checkout();
                }
                
                //Page shown after completion of checkout ie: formated bill & shipping detaiils
                if(is_order_received_page()){
                    require_once apply_filters('eo_wbc_front_order_recived','EO_WBC_Frontend/EO_WBC_Order_Recived.php',35);
                    new EO_WBC_Order_Recived();
                }
                
                //Customer on view-orders detail page....
                if(function_exists('is_wc_endpoint_url')){
                    if(is_wc_endpoint_url('view-order'))
                    {
                        require_once apply_filters('eo_wbc_front_view_order','EO_WBC_Frontend/EO_WBC_View_Order.php',35);
                        new EO_WBC_View_Order();
                    }
                }
                else{
                    if($this->eo_wbc_is_endpoint_url('view-order'))
                    {
                        require_once apply_filters('eo_wbc_front_view_order','EO_WBC_Frontend/EO_WBC_View_Order.php',35);
                        new EO_WBC_View_Order();
                    }
                }                                
                
            },30);
        }
    }
    //Frontend sercive method, to add shortcode functinality and save FG_COLOR to session.
    private function eo_wbc_front_service(){ 
        //Perform shortcode routine if required.
        add_shortcode('woo-bundle-choice-btn',function(){
            require_once apply_filters('eo_wbc_front_home','EO_WBC_Frontend/EO_WBC_Home.php');               
            if(!class_exists('EO_WBC_Home'))
            {
                require_once apply_filters('eo_wbc_front_home','EO_WBC_Frontend/EO_WBC_Home.php',35);
            } 
            return EO_WBC_Home::eo_wbc_do_shortcode();
        });                     
    }
    
    //Method to perform endpoint check.
    private function eo_wbc_is_endpoint_url( $endpoint = false ) {
        
        global $wp;
        $wc_endpoints = WC()->query->query_vars;
        
        if ( false !== $endpoint ) {
            if ( ! isset( $wc_endpoints[ $endpoint ] ) ) {
                return false;
            } else {
                $endpoint_var = $wc_endpoints[ $endpoint ];
            }            
            return isset( $wp->query_vars[ $endpoint_var ] );
        } else {
            foreach ( $wc_endpoints as $key => $value ) {
                if ( isset( $wp->query_vars[ $key ] ) ) {
                    return true;
                }
            }            
            return false;
        }
    }
}
?>