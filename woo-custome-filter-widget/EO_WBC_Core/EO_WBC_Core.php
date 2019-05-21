<?php
class EO_WBC_Core{
    public $_eo_wbc_;
    public function __construct($file)
    {
        $this->_eo_wbc_=$file;
        register_activation_hook($this->_eo_wbc_,array(__CLASS__,'eo_wbc_activate'));
        register_deactivation_hook($this->_eo_wbc_,array(__CLASS__,'eo_wbc_deactivate'));
        register_uninstall_hook($this->_eo_wbc_,array(__CLASS__,'eo_wbc_uninstall'));      
        $this->update_manager();  
    }
    public static function eo_wbc_activate(){
        #Plugin Activation Code
        
        //add category count to hopp on.
        if(!get_option('eo_wbc_category_count')){
            add_option('eo_wbc_category_count',"2");
        }
        
        //Name of first product
        if(!get_option('eo_wbc_first_name')){
            add_option('eo_wbc_first_name',"");
        }
        //Slug of first product
        if(!get_option('eo_wbc_first_slug')){
            add_option('eo_wbc_first_slug',"");
        }
        //URL of first product
        if(!get_option('eo_wbc_first_url')){
            add_option('eo_wbc_first_url',"");
        }
        
        //Name of second product
        if(!get_option('eo_wbc_second_name')){
            add_option('eo_wbc_second_name',"");
        }
        //Slug of second product
        if(!get_option('eo_wbc_second_slug')){
            add_option('eo_wbc_second_slug',"");
        }
        //URL of second product
        if(!get_option('eo_wbc_second_url')){
            add_option('eo_wbc_second_url',"");
        }
        
        //Name to final collaction
        if(!get_option('eo_wbc_collection_name')){
            add_option('eo_wbc_collection_name',"Preview");
        }
        
        //URL to product review page
        if(!get_option('eo_wbc_review_page')){
            add_option('eo_wbc_review_page',"/eo-wbc-product-review/");
        }
        
        //Configuration status -- if categories are selected 
        if(!get_option('eo_wbc_config_category')){
            add_option('eo_wbc_config_category',"0");
        }
        //Configuration status -- if maps are created
        if(!get_option('eo_wbc_config_map')){
            add_option('eo_wbc_config_map',"0");
        }
                
        $page_check = get_page_by_title('Product Review');
        $new_page_template='';
        $new_page = array(
            'post_type' => 'page',
            'post_title' => 'Product Review',
            'post_name'=>'eo-wbc-product-review',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
        );
        if(!isset($page_check->ID)){
            $new_page_id = wp_insert_post($new_page);
            if(!empty($new_page_template)){
                update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
            }
        }
        
        /**
         * create table to store orders in a SETS form that are recived from customers
         */
        global $wpdb;
        $eo_wbc_order_map= $wpdb->prefix."eo_wbc_order_maps";
        if($wpdb->get_var( "SHOW TABLES LIKE '$eo_wbc_order_map'" ) != $eo_wbc_order_map)
        {
            $sql = "CREATE TABLE `$eo_wbc_order_map`( ";
            $sql .= "  `order_id`  int(11) NOT NULL, ";
            $sql .= "  `order_map` text NOT NULL, ";
            $sql .= "  PRIMARY KEY(`order_id`)";
            $sql .= ") ".$wpdb->get_charset_collate().";";
            require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
            dbDelta($sql);
        }
        
        /**
         * create table to store maps between product that is created by admin
         */
        $eo_wbc_cat_map= $wpdb->prefix."eo_wbc_cat_maps";
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        
        if($wpdb->get_var( "show tables like '$eo_wbc_cat_map'" ) != $eo_wbc_cat_map)
        {
            $sql='';
            $sql = "CREATE TABLE `$eo_wbc_cat_map` ( ";
            $sql .= " `first_cat_id` VARCHAR(125) NOT NULL , `second_cat_id` VARCHAR(125) NOT NULL, `discount` VARCHAR(20) not null DEFAULT '0%', PRIMARY KEY (`first_cat_id`, `second_cat_id`)";
            $sql .= ") ".$wpdb->get_charset_collate().";";                        
            dbDelta($sql);            
        }
        
        update_option('eo_wbc_active',"1");
        add_action( 'activated_plugin',function(){
            if(!
                (  (get_option('eo_wbc_first_name')    &&
                    get_option('eo_wbc_first_slug')    &&
                    get_option('eo_wbc_first_url')     &&
                    get_option('eo_wbc_second_name')   &&
                    get_option('eo_wbc_second_slug')   &&
                    get_option('eo_wbc_second_url'))
                    )){
                        //Plugin Activated                        
                        exit( wp_redirect( admin_url( 'admin.php?page=eo-wbc-home')));
            }
        });
            
    }
    public static function eo_wbc_deactivate(){
        #Plugin Deactivation Code
        //Plugin Activated
        update_option('eo_wbc_active',"0");
    }
    public static function eo_wbc_uninstall(){
        #Plugin Uninstall
        
        //Remove category count options.
        if(get_option('eo_wbc_category_count')){
            delete_option('eo_wbc_category_count');
        }
        
        //Name of first product
        if(get_option('eo_wbc_first_name')){
            delete_option('eo_wbc_first_name');
        }
        
        //Slug of first product
        if(get_option('eo_wbc_first_slug')){
            delete_option('eo_wbc_first_slug');
        }
        
        //URL of first product
        if(get_option('eo_wbc_first_url')){
            delete_option('eo_wbc_first_url');
        }
        
        //Name of second product
        if(get_option('eo_wbc_second_name')){
            delete_option('eo_wbc_second_name');
        }
        //Slug of second product
        if(get_option('eo_wbc_second_slug')){
            delete_option('eo_wbc_second_slug');
        }
        //URL of second product
        if(get_option('eo_wbc_second_url')){
            delete_option('eo_wbc_second_url');
        }
        
        //Remove name to final collaction
        if(get_option('eo_wbc_collection_name')){
            delete_option('eo_wbc_collection_name');
        }
        
        //URL to product review page
        if(get_option('eo_wbc_review_page')){
            delete_option('eo_wbc_review_page');
        }
               
        //Configuration status -- if categories are selected
        if(get_option('eo_wbc_config_category')){
            delete_option('eo_wbc_config_category');
        }
        //Configuration status -- if maps are created
        if(get_option('eo_wbc_config_map')){
            delete_option('eo_wbc_config_map');
        }
        
        //Remove table... order_maps
        global $wpdb;
        $eo_wbc_order_map= $wpdb->prefix."eo_wbc_order_maps";
        if($wpdb->get_var( "SHOW TABLES LIKE '$eo_wbc_order_map'" ) == $eo_wbc_order_map)
        {
            $sql='';
            $sql = "DROP TABLE `$eo_wbc_order_map`;";
            require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
            dbDelta($sql);
        }
        
        //Remove table... cat_maps
        $eo_wbc_cat_map= $wpdb->prefix."eo_wbc_cat_maps";
        if($wpdb->get_var( "SHOW TABLES LIKE '$eo_wbc_cat_map'" ) == $eo_wbc_cat_map)
        {
            $sql='';
            $sql = "DROP TABLE `$eo_wbc_cat_map`;";
            require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
            dbDelta($sql);
        }        
    }

    private function update_manager()
    {
        /**
        * This section of code is intended to update necessary elements of plugin
        * such as database
        * @param none
        * @desc method of updating databases.
        **/
        global $wpdb;
        $eo_wbc_cat_map= $wpdb->prefix."eo_wbc_cat_maps";
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

        if(version_compare(EO_WBC_PLUGIN_VERSION,get_option('eo_wbc_version'),'>') )
        {
            if($wpdb->get_var("SHOW COLUMNS FROM `$eo_wbc_cat_map` LIKE 'discount'" ) != 'discount')
            {
                $sql="alter TABLE `".$eo_wbc_cat_map."` ADD `discount` VARCHAR(20) not null DEFAULT '0%' AFTER `second_cat_id` ";   
                $wpdb->query($sql);
            }            
            update_option('eo_wbc_version',EO_WBC_PLUGIN_VERSION);
        }                   
    }
}
?>