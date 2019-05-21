<?php    
    //Footer Rating bar :)
    add_filter( 'admin_footer_text',function($footer_text){
    return __("<p id='footer-left' class='alignleft'>
        If you like <strong>WooCommerce Bundle Choice</strong> please leave us a <a href='https://wordpress.org/support/plugin/woo-bundle-choice/reviews?rate=5#new-post' target='_blank' class='wc-rating-link' data-rated='Thanks :)'>★★★★★</a> rating. A huge thanks in advance! </p>");
    });

    function eo_wbc_prime_category($slug,$prefix)
    {
        $map_base = get_categories(array(
            'hierarchical' => 1,
            'show_option_none' => '',
            'hide_empty' => 0,
            'parent' => get_term_by('slug',$slug,'product_cat')->term_id,
            'taxonomy' => 'product_cat'
        ));
        
        $category_option_list='';
        
        foreach ($map_base as $base) {
            $category_option_list.= "<option value='".$base->term_id."'>".$prefix.$base->name."</option>".eo_wbc_prime_category($base->slug, $prefix.'-');
        }
        return $category_option_list;
    }   

    function eo_wbc_attributes()
    {
        $taxonomies="";
        foreach (wc_get_attribute_taxonomies() as $attribute) {                     
            $taxonomies.="<option disabled='disabled' value='".$attribute->attribute_id."'>".$attribute->attribute_label."</option>";
            foreach (get_terms(wc_attribute_taxonomy_name($attribute->attribute_name)) as $term){
                $taxonomies.="<option value='".$term->term_id."'>-- ".$term->name."</option>";   
            }
        }
        return $taxonomies;
    }
?>
<form name="eo_wbc_remove_frm" action="<?php echo admin_url('admin.php?page=eo-wbc-map'); ?>" method="post">
	<?php wp_nonce_field('eo_wbc_nonce_remove_map'); ?>
	<input type="hidden" name="eo_wbc_source" value="">
	<input type="hidden" name="eo_wbc_target" value="">
	<input type="hidden" name="eo_wbc_action" value="eo_wbc_remove_map">
</form>

<style>
    .info{
        color:grey !important;
        font-style: italic;
    }

    @media only screen and (max-width: 782px) {
        #add_maps_row{            
            display: grid !important;        
        }
        #add_maps_row th:nth-child(3){            
            display: none !important;
        }
        #add_maps_row th:nth-child(2)::before{            
            content: "First Product Category \A\A";            
            display: grid;
            font-size: medium;
            font-weight: 600;
            white-space: pre;
        }
        #add_maps_row th:nth-child(4)::before{            
            content: "Second Product Category \A\A";
            display: grid;            
            font-size: medium;
            font-weight: 600;
            white-space: pre;
        }
        tfoot{
            display: none;
        }
    }
</style>
<div class="wrap woocommerce">
<h1></h1>
	<?php	EO_WBC_Head_Banner::get_head_banner(); ?>
    <br/>
        <p><a href="https://wordpress.org/support/plugin/woo-bundle-choice" target="_blank">If you are facing any issue, please write to us immediately.</a></p>
	<br/>
	<hr/>

    <br/>

    <form action="<?php echo admin_url('admin.php?page=eo-wbc-map'); ?>" method="post">
    			<?php wp_nonce_field('eo_wbc_nonce_add_map'); ?>
    			<input type="hidden" name="eo_wbc_action" value="eo_wbc_add_map">
              <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr>                                   
                    <th class="check-column"></th>
                    <th class="manage-column column-columnname num" scope="col">First Product Category</th>                        
                    <th class="manage-column column-columnname num" scope="col"></th>
                    <th class="manage-column column-columnname num" scope="col">Second Product Category</th>                        
                    <th class="manage-column column-columnname num" scope="col">Discount</th>
                    <th class="manage-column column-columnname num" scope="col">Action</th>
                </tr>
                </thead>
            
                <tbody>                    
                    <?php 
                        global $wpdb;
                        $query='select * from `'.$wpdb->prefix.'eo_wbc_cat_maps`';
                        $maps=$wpdb->get_results($query,'ARRAY_A');
                        if(count($maps)>0):
                        foreach ($maps as $map):                            
                    ?>                        
                    	<tr class="alternate">
                            <td class="check-column"></td>                        
                            <td class="column-columnname num">
                                <?php 
                                    $first_term=get_term_by('term_taxonomy_id',$map['first_cat_id'],'category');
                                    echo 
                                        (empty($first_term)
                                            ?
                                        get_term_by('term_taxonomy_id',$map['first_cat_id'],'product_cat')->name
                                            :
                                        get_term_by('term_taxonomy_id',$map['first_cat_id'],'category')->name); 
                                ?>
                            </td>
                            <td class="column-columnname num"><------------></td>
                            <td class="column-columnname num">
                                <?php 
                                    $second_term=get_term_by('term_taxonomy_id',$map['second_cat_id'],'category');
                                    echo 
                                        (empty($second_term)
                                            ?
                                        get_term_by('term_taxonomy_id',$map['second_cat_id'],'product_cat')->name
                                            :
                                        get_term_by('term_taxonomy_id',$map['second_cat_id'],'category')->name);
                                ?>                                
                            </td>
                            <td class="column-columnname num"><?php echo $map['discount']?></td>
                            <td class="column-columnname num"><a href="#" onclick="eo_wbc_remove_map('<?php echo $map['first_cat_id']; ?>','<?php echo $map['second_cat_id'] ?>')">Remove</a></td>                       
                    	</tr>                    
                    <?php  endforeach; else: ?>
                    			<tr>
                    			<td class="check-column"></td>
                        	    <td colspan=3 class="column-columnname num" style="color: red;font-weight: bold;">No Maps is available</td>
                        	    <td class="column-columnname num"></td>
                        	    </tr>
                        <?php 
                        endif;
                    
                    ?>
                    
                </tbody>
                
                <tfoot>                
                    <tr>                            
                            <th class="check-column"></th>
                            <th class="manage-column column-columnname num" scope="col">First Product Category</th>                            
                            <th class="manage-column column-columnname num" scope="col"></th>
                            <th class="manage-column column-columnname num" scope="col">Second Product Category</th>
                            <th class="manage-column column-columnname num" scope="col">Discount</th>
                            <th class="manage-column column-columnname num" scope="col">Action</th>            
                    </tr>
                </tfoot>
            </table>    
            <br/>
            <table class="widefat fixed">
            	<tbody>
                    <tr>
                        <th class="check-column">&nbsp;</th>
                        <td colspan="3" style="text-align: center;font-size: larger;font-weight: bold;">Add New Maps</td>
                        <th class="manage-column column-columnname num">&nbsp;</th>
                    </tr>
                	<tr id="add_maps_row">
                        <th class="check-column"></th>                            
                        <th class="manage-column column-columnname num" scope="col">
                                <select name="eo_wbc_first_category" id="eo_wbc_first_category">
                                    <option disabled="disabled">Category</option>
            						<?php echo eo_wbc_prime_category(get_option('eo_wbc_first_slug'),' -- ') ?>
                                    <?php echo eo_wbc_attributes(); ?>
            					</select>
            					<p class="info">( Select sub-category from first category. )</p>
    					</th>
                        <th class="manage-column column-columnname num" scope="col"><-------------></th>
                        <th class="manage-column column-columnname num" scope="col">
                            	<select name="eo_wbc_second_category" id="eo_wbc_second_category">
                                    <option disabled="disabled">Category</option>
        							<?php echo eo_wbc_prime_category(get_option('eo_wbc_second_slug'),' -- ') ?>
                                    <?php echo eo_wbc_attributes(); ?>
        						</select>            						
        						<p class="info">( Select sub-category from second category. )</p>
                        </th>
                        <th class="manage-column column-columnname num" scope="col">
                                <input type="number" name="eo_wbc_add_discount" min="0" max="100" value="0" step="0" style="text-align: right;"/>
                                <p class="info">( Discount rate in % )</p>
                        </th>
                                                    
                        <th class="manage-column column-columnname num" scope="col"><button class="button button-primary button-hero action" style="float: right">Save New Map</button></th>            
                     </tr>
                 </tbody>
           </table>
    </form>    
</div>

<script type="text/javascript">
	function eo_wbc_remove_map(first,second)
	{
			document.getElementsByName("eo_wbc_source")[0].value=first;
			document.getElementsByName("eo_wbc_target")[0].value=second;
			document.forms.eo_wbc_remove_frm.submit();
	}
</script>