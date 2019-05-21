<?php   
class EO_WBC_Order_Recived{
    public function __construct()
    {

        add_action('woocommerce_thankyou',function($order_id){            
            $sets=WC()->session->get('EO_WBC_MAPS');
            $maps=array();
            if(!is_null($sets))
            {
                foreach ($sets as $set)
                {
                    $map=array();                    
                        $map[]=array($set["FIRST"][0],$set["FIRST"][1],$set["FIRST"][2]);
                        if($set["SECOND"])
                        {
                            $map[]=array($set["SECOND"][0],$set["SECOND"][1],$set["SECOND"][2]);
                        }
                        else 
                        {
                            $map[]=NULL;
                        }
                    $maps[]=$map;
                }
                
                global $wpdb;                
                $wpdb->insert($wpdb->prefix.'eo_wbc_order_maps',array('order_id'=>$order_id,'order_map'=>json_encode($maps)),array("%s","%s"));                
                
                //Clearing Plugin Session data                
                WC()->session->set('EO_WBC_SETS',NULL);
                WC()->session->set('EO_WBC_MAPS',NULL);
                WC()->cart->empty_cart();                 
            }
        });
    } 
}    
?>