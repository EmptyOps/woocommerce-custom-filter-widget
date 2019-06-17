
	<?php
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
    ?>

    <form name="woo_custome_filter_action_remove" method="post">
		<?php wp_nonce_field('woo_custome_filter_action_remove'); ?>
		<input type="hidden" name="del_filter_name" value="" id="del_filter_name">				
	</form>
    <div class="wrap woocommerce">              
        <h3>Setting</h3>
        <form method="post" name="woo_custome_filter_target" >
            <?php 
                $filter_target_data=unserialize(get_option('woo_custome_filter_target',"a:0:{}"));            
            ?>
            <?php wp_nonce_field('woo_custome_filter_target'); ?>
            <table style="background-color: white; border-radius: 2px; margin-top:1.5em; padding: 1.5em;">
                <tr>
                    <td><strong>Filter location</strong></td>
                    <td>                    
                        <input type="radio" name="filter_target" id="filter_target_shop" value="shop" onclick="document.getElementById('filter_target_select_cat').style.display='none'" <?php echo count($filter_target_data)&&$filter_target_data['page']!='category'?'checked="checked"':'' ?> ><label for="filter_target_shop">Shop Page</label>                    &nbsp;
                        <input type="radio" name="filter_target" id="filter_target_category" value="category" onclick="document.getElementById('filter_target_select_cat').style.display='table-row'" <?php echo count($filter_target_data)&&$filter_target_data['page']=='category'?'checked="checked"':'' ?> ><label for="filter_target_category">Category Page</label>
                    </td>
                </tr>
                <tr style="display: none;" id="filter_target_select_cat">
                    <td><strong>Category</strong></td>
                    <td>                    
                        <select name="filter_target_cat" style="width: 100%;">                             
                            <?php echo __categories__(''); ?>                        
                        </select> 
                    </td>
                    <?php echo count($filter_target_data)&&$filter_target_data['page']=='category'?"<script>document.getElementById('filter_target_select_cat').style.display='table-row';document.getElementsByName('filter_target_cat')[0].value='".$filter_target_data['cat_id']."'</script>":'' ?>
                </tr>
                <tr>
                    <td><strong>Additional CSS</strong></td>
                    <td class="forminp">                                                        
                        <textarea name="wcfw_shop_opt_submit_additional_css" id="wcfw_shop_opt_submit_additional_css" data-init="1" style="border: 1px solid #ddd;"><?php echo $filter_target_data?@$filter_target_data['add_css']:'' ?></textarea>  
                        <script>                                 
                            jQuery(document).ready(function($) {     
                                var cm_settings=<?php echo json_encode(array('codeEditor' =>wp_enqueue_code_editor(array('type' => 'text/css')))); ?>                              
                                wp.codeEditor.initialize($('#wcfw_shop_opt_submit_additional_css'), cm_settings); 
                            });
                        </script>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>                             
                    <td><input type="submit" value="Save" class="submit button-primary" name="Submit"></td>
                </tr>
            </table>                                                
        </form>

        <h3 style="margin-bottom: 0px;margin-top: 2em;">Filter contents</h3>	
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
                		$item_name=@get_term_by('id',$item['name'],'product_cat')->name;
                		$item_name=empty($item_name)?@wc_get_attribute($item['name'])->name:$item_name;
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
                                    <option value="dropdown">Dropdown</option>
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
