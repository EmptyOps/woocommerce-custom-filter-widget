<?php
class EO_WBC_Config
{   
    public function __construct()
    {
        $this->menu();        
    }
    public function menu()
    {
        /**
         * Adding menu to admin panel with name : 'Your Choice'
         * and includes three other sub menu
         * 1. Home page
         * 2. Config Page
         * 3. Mapping Page
         */
        
        //Main Menu and home page
        require_once 'EO_WBC_View/EO_WBC_View_Head_Banner.php';
        add_action('admin_menu',function(){
            add_menu_page('Configuration','Bundle Choice','administrator','eo-wbc-home',function(){                
                $this->eo_wbc_home();
            });                
            //Configuration Page
            add_submenu_page('eo-wbc-home', 'Configuration', 'Configuration', 'administrator', 'eo-wbc-setting',function(){
                $this->eo_wbc_config();                
            });                    
            //Mapping Page
            add_submenu_page('eo-wbc-home', 'Mapping', 'Mapping', 'administrator', 'eo-wbc-map',function(){                
                $this->eo_wbc_map();
            });
            //Advance Filters
            add_submenu_page('eo-wbc-home', 'Filters', 'Filters', 'administrator', 'eo-wbc-filter',function(){                
                $this->eo_wbc_filter();
            });
        },11);        
    }    
    
    private function eo_wbc_home()
    {           
        require_once apply_filters('eo_wbc_admin_config_home_page','EO_WBC_View/EO_WBC_View_Home.php');
    }
    
    private function eo_wbc_config()
    {   
        require_once apply_filters('eo_wbc_admin_config_config_page','EO_WBC_View/EO_WBC_View_Config.php');
    }
    
    private function eo_wbc_map()
    {
        require_once apply_filters('eo_wbc_admin_config_map_page','EO_WBC_View/EO_WBC_View_Mapping.php');
    }   

    private function eo_wbc_filter() {
        require_once apply_filters('eo_wbc_admin_config_filter_page','EO_WBC_View/EO_WBC_View_Filter.php');
    }
}