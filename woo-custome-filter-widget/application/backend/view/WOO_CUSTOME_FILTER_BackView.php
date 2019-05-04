<?php

if(!function_exists('__categories__')){

	function __categories__($slug)
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
            $category_option_list.= "<option data-type='0' data-slug='{$base->slug}' value='".$base->term_id."'>".$base->name."</option>";
        }
        return $category_option_list;
    }
}
	
if(!function_exists('__attributes__')){

	function __attributes__()
    {
        $attributes="";        
        foreach (wc_get_attribute_taxonomies() as $item) {                     
        	$attributes .= "<option data-type='1' data-slug='{$item->attribute_name}' value='{$item->attribute_id}'>{$item->attribute_label}</option>";            
        }
        return $attributes;
    }
}
	
?>
<div>
	<div>
		<h1>Filter Menu configuration</h1>
	</div>
	<hr/>
	<form name="woo_custome_filter_action_remove" method="post">
		<?php wp_nonce_field('woo_custome_filter_action_remove'); ?>
		<input type="hidden" name="del_filter_name" value="" id="del_filter_name">				
	</form>
	<div class="wrap woocommerce">				
		<table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th class="manage-column column-columnname num" scope="col">Filter</th>                        
                    <th class="manage-column column-columnname num" scope="col">Label</th>
                    <th class="manage-column column-columnname num" scope="col">Advance</th>	                    
                    <th class="manage-column column-columnname num" scope="col">Input</th>	                    
                    <th class="manage-column column-columnname num" scope="col">Action</th>
                </tr>
            </thead>
        
            <tbody>                    
            	<?php 
            	
            	$filters_data=unserialize(get_option('woo_custome_filter_widget',"a:0:{}"));                	
            	if(count($filters_data)>0):
                	foreach ($filters_data as $item) :
                		$item=(array)$item;
                		$item_name=get_term_by('id',$item['name'],'product_cat')->name;
                		$item_name=empty($item_name)?wc_get_attribute($item['name'])->name:$item_name;
                	?>
                   	<tr class="alternate">	                    
                        <td class="column-columnname num"><?php echo $item_name; ?></td>
                        <td class="column-columnname num"><?php echo $item['label']; ?></td>
                        <td class="column-columnname num"><?php echo $item['advance']=='1'?'Yes':'No'; ?></td>
                        <td class="column-columnname num"><?php echo $item['input']; ?></td>	
                        <td class="column-columnname num">
                        	<a href="#" data-name="<?php echo $item['name']; ?>" class="del_filter_name">Remove</a>
                        </td>
                    </tr>                    
                	<?php 
                	endforeach;
                else: ?>
        			<tr>		        			
	            	    <td colspan=5 class="column-columnname num" style="color: red;font-weight: bold;">No record is available</td>
            	    </tr>                
        	<?php endif; ?>
            </tbody>
            
            <tfoot>                
            	<br/>
            	<form method="post" name="woo_custome_filter_form_add" >
            		<?php wp_nonce_field('woo_custome_filter_action_add'); ?>
            		<table style="background-color: white; border-radius: 2px; margin-top:1.5em; padding: 1.5em;">
            			<tr>
            				<td>Filter</td>
            				<td>
            					<input type="hidden" name="filter_type" value="0" id="filter_type">
                                <select name="filter_name" style="width: 100%;" onchange="document.getElementById('filter_type').value=this.options[this.selectedIndex].getAttribute('data-type')">                        		
                        			<?php echo __categories__(''); ?>
                        			<?php echo __attributes__(); ?>
                        		</select>                                
            				</td>
            			</tr>
            			<tr>
            				<td>
            					Label
            				</td>
            				<td>
            					<input type="text" name="filter_label" style="width: 100%;"/>	
            				</td>
            			</tr>
            			<tr>
            				<td>
            					Advance Option ?
            				</td>
            				<td>
            					<input type="checkbox" name="filter_advanced">	
            				</td>
            			</tr>
            			<tr>
            				<td>
            					Input Type
            				</td>
            				<td>
            					<select name="filter_input" style="width: 100%;">
	                        		<option value="icon">Icon Only</option>
	                        		<option value="icon_text">Icon and Text</option>
	                        		<option value="text_slider">Text slider</option>
	                        		<option value="step_slider">Step slider</option>
	                        		<option value="checkbox">Checkbox</option>
                        		</select>
            				</td>
            			</tr>
            			<tr>
            				<td>&nbsp;</td>                				
                        	<td><input type="submit" value="Save" class="submit button-primary" name="Submit"></td>
                        </tr>
            		</table>                		                		
                </form>
            </tfoot>
        </table>               
	</div>
	<script>
		jQuery(document).ready(function($){

			$(".del_filter_name").click(function(){				

				$("#del_filter_name").val($(this).attr('data-name'));				
				document.forms.woo_custome_filter_action_remove.submit();
				
			});						
		});            	    	
    </script>
</div>

