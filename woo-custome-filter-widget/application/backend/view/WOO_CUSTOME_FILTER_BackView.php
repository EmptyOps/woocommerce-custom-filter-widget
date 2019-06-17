<style type="text/css">
    .wcfw_help{
        color: grey;
        font-size:smaller;
    }
    select,input{
        min-width: 250px;
    }
</style>

<?php

if(!function_exists('__categories__')){

	function __categories__($prefix='',$slug='')
    {
        $map_base = get_categories(array(
            'hierarchical' => 1,
            'show_option_none' => '',
            'hide_empty' => 0,
            'parent' => @get_term_by('slug',$slug,'product_cat')->term_id,
            'taxonomy' => 'product_cat'
        ));
            
        $category_option_list='';
        foreach ($map_base as $base) {
            $category_option_list.= "<option data-type='0' data-slug='{$base->slug}' value='".$base->term_id."'>".$prefix.$base->name."</option>".__categories__($prefix.'-',$base->slug);
        }
        return $category_option_list;
    }
}
	
if(!function_exists('__attributes__')){

	function __attributes__()
    {    
        $attributes='';
        foreach (wc_get_attribute_taxonomies() as $item) {                     
        	$attributes .= "<option data-type='1' data-slug='{$item->attribute_name}' value='{$item->attribute_id}'>{$item->attribute_label}</option>";            
        }
        return $attributes;
    }
}

function wcfw_sanitize(){
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

?>
<div class="wrap">
    <div>
		<h1>Custom Filter configuration</h1>
	</div>
    
        wcfw_sanitize();

    <?php $config_page=(isset($_GET['tab']))?$_GET['tab']:'config_opt'; ?>    

    <h2 class="nav-tab-wrapper">

        <a href="?page=woo-custome-filter-config&tab=config_opt" 
            class="nav-tab <?php echo $config_page=='config_opt'?'nav-tab-active':''; ?>">
            Configuration
        </a>
        <a href="?page=woo-custome-filter-config&tab=shop_cat_opt" 
            class="nav-tab <?php echo $config_page=='shop_cat_opt'?'nav-tab-active':''; ?>">
            Shop | Category Filter
        </a>
        <a href="?page=woo-custome-filter-config&tab=shortcode_opt" 
            class="nav-tab <?php echo $config_page=='shortcode_opt'?'nav-tab-active':''; ?>">
            Shortcode Filter
        </a>
    </h2>
	
	<?php
        
        switch ($config_page) {

            case 'config_opt':                                                
                require_once 'config_pages/config_opt.php';                
                break;

            case 'shop_cat_opt':
                require_once 'config_pages/shop_cat_opt.php';
                break;            

            case 'shortcode_opt':
                require_once 'config_pages/shortcode_opt.php';
                break;

            default:
                require_once 'config_pages/config_opt.php';
                break;
        }        
    ?>

</div>

