<?php
class WOO_CUSTOME_FILTER_Action{

	public function __construct() {
        
		if ( wp_verify_nonce( $_POST['_wpnonce'],'woo_custome_filter_action_add') ) {
            
            $this->add_filter();       
        }            
        elseif ( wp_verify_nonce( $_POST['_wpnonce'],'woo_custome_filter_action_remove' ) ) {                
            
        	$this->del_filter();
		}
	}

	private function add_filter() {



        $filter_name=$_POST["filter_name"]; //required

        $filter_type=$_POST["filter_type"]; //required
        
        $filter_label=(!empty($_POST["filter_label"])?$_POST["filter_label"]:'');

        $filter_advanced=(!empty($_POST["filter_advanced"])?"1":"0");
                
        $filter_input=$_POST["filter_input"]; //required
        
        $filter_data=unserialize(get_option('woo_custome_filter_widget',"a:0:{}"));
        
        $filter_data[]=array(
                        'name'=>$filter_name,  
                        'type'=>$filter_type,                      
                        'label'=>$filter_label,
                        'advance'=>$filter_advanced,                        
                        'input'=>$filter_input,
                    );            
        update_option('woo_custome_filter_widget',serialize($filter_data));             
	}

	private function del_filter() {

        $filter_data=unserialize(get_option('woo_custome_filter_widget',"a:0:{}"));                
        foreach ($filter_data as $key=>$item) {
            
            if ($item['name']==$_POST['del_filter_name']) {                 
                unset($filter_data[$key]);
            }
        }
        update_option('woo_custome_filter_widget',serialize($filter_data));                
	}
}
?>