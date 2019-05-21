<?php
	function eo_wbc_prime_category_($slug)
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

	function eo_wbc_attributes_()
    {
        $attributes="";        
        foreach (wc_get_attribute_taxonomies() as $item) {                     
        	$attributes .= "<option data-type='1' data-slug='{$item->attribute_name}' value='{$item->attribute_id}'>{$item->attribute_label}</option>";            
        }
        return $attributes;
    }
?>
<div>
	<div>
		<h1>Filter Menu configuration</h1>
	</div>
	<hr/>
	<form name="eo_wbc_remove_filter" method="post">
		<?php wp_nonce_field('eo_wbc_nonce_remove_filter'); ?>
		<input type="hidden" name="eo_wbc_filter_name" value="" id="eo_wbc_remove_filter_id">
		<input type="hidden" name="eo_wbc_filter_action" value="" id="eo_wbc_remove_filter_action">
		<input type="hidden" name="eo_wbc_action" value="eo_wbc_remove_filter">
	</form>
	<div class="wrap woocommerce">		
		<div>			
		</div>
			<h3><?php echo get_option('eo_wbc_first_name','First category') ?>'s filter configuration</h3>
			<table class="widefat fixed" cellspacing="0">
                <thead>
	                <tr>
	                    <th class="manage-column column-columnname num" scope="col">Filter</th>                        
	                    <th class="manage-column column-columnname num" scope="col">Label</th>
	                    <th class="manage-column column-columnname num" scope="col">Type</th>
	                    <th class="manage-column column-columnname num" scope="col">Dependent</th>
	                    <th class="manage-column column-columnname num" scope="col">Input</th>	                    
	                    <th class="manage-column column-columnname num" scope="col">Action</th>
	                </tr>
                </thead>
            
                <tbody>                    
                	<?php 
                	
                	$filters_data=unserialize(get_option('eo_wbc_add_filter_first',"a:0:{}"));                	
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
	                        <td class="column-columnname num"><?php echo $item['dependent']=='1'?'Yes':'No'; ?></td>
	                        <td class="column-columnname num"><?php echo $item['input']; ?></td>	
	                        <td class="column-columnname num">
	                        	<a href="#" data-action="eo_wbc_add_filter_first" data-name="<?php echo $item['name']; ?>">Remove</a>
	                        </td>
	                    </tr>                    
	                	<?php 
	                	endforeach;
	                else:
                	?>
	        			<tr>		        			
		            	    <td colspan=6 class="column-columnname num" style="color: red;font-weight: bold;">No record is available</td>
	            	    </tr>                
            	    <?php
            	    endif;
            	     ?>
                </tbody>
                
                <tfoot>                
                	<form method="post" name="form_first">
                		<?php wp_nonce_field('eo_wbc_nonce_add_filter'); ?>
                		<input type="hidden" name="eo_wbc_action" value="eo_wbc_add_filter_first">                		
                		<tr>	                    	
	                        <td class="column-columnname num">
	                        	<label>Filter </label>
	                        	<select name="filter_name" data-group='first' onchange="document.getElementById('filter_type_first').value=this.options[this.selectedIndex].getAttribute('data-type')">                        		
	                        		<?php echo eo_wbc_prime_category_(get_option('eo_wbc_first_slug'),''); ?>
	                        		<?php echo eo_wbc_attributes_(); ?>
	                        	</select>
	                        	<input type="hidden" name="filter_type" value="0" data-group='first' id="filter_type_first">
	                        </td>
	                        <td class="column-columnname num">
	                        	<label>Label </label>
	                        	<input type="text" name="filter_label"/>
	                        </td>
	                        <td class="column-columnname num">
	                        	<label>Is advanced filter option ?</label>
	                        	<input type="checkbox" name="filter_advanced" class="form-input">
	                        </td>	                        
	                        <td class="column-columnname num">
	                        	<!-- <label>Show in both step ?</label>
	                        	<input type="checkbox" name="filter_dependent"> -->
	                        </td>
	                        <td class="column-columnname num">
	                        	<label>Input Type </label>
	                        	<select name="filter_input">
	                        		<option value="icon">Icon Only</option>
	                        		<option value="icon_text">Icon and Text</option>
	                        		<option value="text_slider">Text slider</option>
	                        		<option value="step_slider">Step slider</option>
	                        		<option value="checkbox">Checkbox</option>
	                        	</select>
	                        </td>	                        
	                        <td class="column-columnname num">
	                        	<input type="submit" value="Save" class="submit button-primary" name="Submit">
	                        </td>
	                    </tr>
                    </form>
                </tfoot>
            </table>    
            <br/><br/>
            <hr/>
            <h3><?php echo get_option('eo_wbc_second_name','First category') ?>'s filter configuration</h3>
			<table class="widefat fixed" cellspacing="0">
                <thead>
	                <tr>
	                    <tr>
	                    <th class="manage-column column-columnname num" scope="col">Filter</th>                        
	                    <th class="manage-column column-columnname num" scope="col">Label</th>
	                    <th class="manage-column column-columnname num" scope="col">Type</th>
	                    <th class="manage-column column-columnname num" scope="col">Dependent</th>
	                    <th class="manage-column column-columnname num" scope="col">Input</th>	                    
	                    <th class="manage-column column-columnname num" scope="col">Action</th>	                
	                </tr>
                </thead>
            
                <tbody>                    
                	<?php                 	
                	$filters_data=unserialize(get_option('eo_wbc_add_filter_second',"a:0:{}"));
                	if(count($filters_data)>0):
	                	foreach ($filters_data as $item) :

	                		$item=(array)$item;
	                		$item_name='';
	                		if(get_term_by('id',$item['name'],'product_cat')){
	                			$item_name=get_term_by('id',$item['name'],'product_cat')->name;
	                		}
	                		else{
	                			$item_name=empty($item_name)?wc_get_attribute($item['name'])->name:$item_name;	
	                		}          		
	                		
	                	?>
	                   	<tr class="alternate">	                    
	                        <td class="column-columnname num"><?php echo $item_name; ?></td>
	                        <td class="column-columnname num"><?php echo $item['label']; ?></td>
	                        <td class="column-columnname num"><?php echo $item['advance']=='1'?'Yes':'No'; ?></td>
	                        <td class="column-columnname num"><?php echo $item['dependent']=='1'?'Yes':'No'; ?></td>
	                        <td class="column-columnname num"><?php echo $item['input']; ?></td>	
	                        <td class="column-columnname num">
	                        	<a href="#" data-action="eo_wbc_add_filter_second" data-name="<?php echo $item['name']; ?>">Remove</a>
	                        </td>
	                    </tr>                    
	                	<?php 
	                	endforeach;
	                else:
	                ?>
	        			<tr>		        			
		            	    <td colspan=6 class="column-columnname num" style="color: red;font-weight: bold;">No record is available</td>
	            	    </tr>                
            	    <?php
            	    endif;
            	     ?>
                </tbody>
                
                <tfoot>                
                	<form method="post" name="form_second">
                		<?php wp_nonce_field('eo_wbc_nonce_add_filter'); ?>
                		<input type="hidden" name="eo_wbc_action" value="eo_wbc_add_filter_second">                		
                		<tr>	     
                			<td class="column-columnname num">
	                        	<label>Filter </label>
	                        	<select name="filter_name" data-group='second' onchange="document.getElementById('filter_type_second').value=this.options[this.selectedIndex].getAttribute('data-type')">                        		
	                        		<?php echo eo_wbc_prime_category_(get_option('eo_wbc_second_slug'),''); ?>
	                        		<?php echo eo_wbc_attributes_(); ?>
	                        	</select>
	                        	<input type="hidden" name="filter_type" value="0" data-group='second' id="filter_type_second">
	                        </td>                     	
	                        <td class="column-columnname num">
	                        	<label>Label </label>
	                        	<input type="text" name="filter_label"/>
	                        </td>
	                        <td class="column-columnname num">
	                        	<label>Is advanced filter option ?</label>
	                        	<input type="checkbox" name="filter_advanced" class="form-input">
	                        </td>	                        
	                        <td class="column-columnname num">
	                        	<!-- <label>Show in both step ?</label>
	                        	<input type="checkbox" name="filter_dependent"> -->
	                        </td>
	                        <td class="column-columnname num">
	                        	<label>Input Type </label>
	                        	<select name="filter_input">
	                        		<option value="icon">Icon Only</option>
	                        		<option value="icon_text">Icon and Text</option>
	                        		<option value="text_slider">Text slider</option>
	                        		<option value="step_slider">Step slider</option>
	                        		<option value="checkbox">Checkbox</option>
	                        	</select>
	                        </td>	                        
	                        <td class="column-columnname num">
	                        	<input type="submit" value="Save" class="submit button-primary" name="Submit">
	                        </td>
	                    </tr>
                    </form>
                </tfoot>
            </table>                
	</div>
	<script>

		jQuery(document).ready(function($){

			$("a:contains('Remove')").click(function(){				
				$("#eo_wbc_remove_filter_action").val($(this).attr('data-action'));
				$("#eo_wbc_remove_filter_id").val($(this).attr('data-name'));				
				document.forms.eo_wbc_remove_filter.submit();
			});			

			// $("[name='filter_name']").on('change',function(){

			// 	$("[data-group='"+$(this).attr('data-group')+"'][name='filter_type']").val(
			// 		jQuery("[name='filter_name'][data-group='"+$(this).attr('data-group')+"']").children('option:selected').attr('data-type')
			// 		);
			// });
		});            	
    	
    </script>
</div>

