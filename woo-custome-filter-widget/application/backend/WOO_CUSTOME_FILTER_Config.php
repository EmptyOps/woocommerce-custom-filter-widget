<?php
class WOO_CUSTOME_FILTER_Config
{   
    public function __construct()
    {
        $this->menu();        
    }
    public function menu()
    {           
        add_action('admin_menu',function(){
            
            add_menu_page('Custome Filters','Custom Filters','administrator','woo-custome-filter-config',function(){                

                require_once apply_filters('woo_custome_filter_backview','view/WOO_CUSTOME_FILTER_BackView.php');
            });                            
            
        },11);        
    }
}