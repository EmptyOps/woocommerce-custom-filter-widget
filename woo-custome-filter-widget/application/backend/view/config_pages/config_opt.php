<?php 
	wp_enqueue_script('wp-theme-plugin-editor');
	wp_enqueue_style('wp-codemirror');
?>
<script type="text/javascript">
	var cm_settings=<?php echo json_encode(array('codeEditor' =>wp_enqueue_code_editor(array('type' => 'text/css')))); ?>;
</script>
<form name="woo_custome_filter_configuration" method="post">
		<?php wp_nonce_field('woo_custom_filter-config_opt'); ?>		
		<h2>Input Controls Customization</h2>

		<h2 class="nav-tab-wrapper">
		    <a href="#" 
		        class="nav-tab config-opt-tabs nav-tab-active" data-target="#config-opt-tabs-basic" onclick="switch_tab(this,'#config-opt-tabs-button')">
		        Button
		    </a>
		    <a href="#" 
		        class="nav-tab config-opt-tabs" data-target="#config-opt-tabs-addcss" onclick="switch_tab(this,'#config-opt-tabs-dopdown')">
		        Dropdown
		    </a>    
		</h2>
		<script>
			
			function switch_tab(elem,tab){
				jQuery(".config-opt-tabs").removeClass('nav-tab-active');
				jQuery(elem).addClass('nav-tab-active');
				jQuery(".config-opt-tabs-content:not("+tab+")").hide();
				jQuery(tab).show();

				jQuery(tab).find('textarea:not([data-init="1"])').each(function(index,element){
					wp.codeEditor.initialize(jQuery(element), cm_settings);		
					jQuery(element).attr('data-init','1');
				});				
			}

			jQuery(document).ready(function($){
				jQuery(".config-opt-tabs-content:not(#config-opt-tabs-button)").hide();
			});
		</script>


		<?php 
			$config_data=unserialize(get_option('woo_custome_filter_widget_config',"a:0:{}")); 
			$config_data_available=count($config_data)>0?true:false;
		?>
		<div id="config-opt-tabs-button" class="config-opt-tabs-content">
			<h2>Submit Button Customization</h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_text">
								Button Text
							</label>
						</th>
						<td class="forminp">
							<input type="Text" name="wcfw_config_opt_submit_text" id="wcfw_config_opt_submit_text" required="required" value="<?php echo $config_data_available?$config_data['submit_text']:'' ?>"/>				
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_bg_color">
								Background Color
							</label>
						</th>
						<td class="forminp">
							<input type="Color" name="wcfw_config_opt_submit_bg_color" id="wcfw_config_opt_submit_bg_color" value="<?php echo $config_data_available?$config_data['submit_back_color']:'' ?>"/>				
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_border_color">
								Border Color
							</label>
						</th>
						<td class="forminp">
							<input type="Color" name="wcfw_config_opt_submit_border_color" id="wcfw_config_opt_submit_border_color" value="<?php echo $config_data_available?$config_data['submit_border_color']:'' ?>"/>				
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_font_color">
								Font Color
							</label>
						</th>
						<td class="forminp">
							<input type="Color" name="wcfw_config_opt_submit_font_color" id="wcfw_config_opt_submit_font_color" value="<?php echo $config_data_available?$config_data['submit_font_color']:'' ?>"/>				
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_font_size">
								Font Size
							</label>
						</th>
						<td class="forminp">
							<input type="number" step="1" name="wcfw_config_opt_submit_font_size" id="wcfw_config_opt_submit_font_size" required="required" value="<?php echo $config_data_available?$config_data['submit_font_size']:'14' ?>"/>px
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_padding">
								Padding
							</label>
						</th>
						<td class="forminp">
							<input type="number" step="1" name="wcfw_config_opt_submit_padding" id="wcfw_config_opt_submit_padding" required="required" value="<?php echo $config_data_available?$config_data['submit_padding']:'5' ?>"/>px
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_inline_css">
								Inline CSS
							</label>
						</th>
						<td class="forminp">														
							<textarea name="wcfw_config_opt_submit_inline_css" id="wcfw_config_opt_submit_inline_css" data-init="1" style="border: 1px solid #ddd;"><?php echo $config_data_available?$config_data['submit_inline_css']:'' ?></textarea>  
							<script>								 
								jQuery(document).ready(function($) {									
		  							wp.codeEditor.initialize($('#wcfw_config_opt_submit_inline_css'), cm_settings);	
								});
							</script>
						</td>
					</tr>			
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_additional_css">
								Additional CSS
							</label>
						</th>
						<td class="forminp">														
							<textarea name="wcfw_config_opt_submit_additional_css" id="wcfw_config_opt_submit_additional_css" data-init="1" style="border: 1px solid #ddd;"><?php echo $config_data_available?$config_data['submit_inline_css']:'' ?></textarea>  
							<script>								 
								jQuery(document).ready(function($) {									
		  							wp.codeEditor.initialize($('#wcfw_config_opt_submit_additional_css'), cm_settings);	
								});
							</script>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_submit_url">
								Redirect URL
							</label>
						</th>
						<td class="forminp">
							<input type="URL" name="wcfw_config_opt_submit_url" id="wcfw_config_opt_submit_url" required="required" value="<?php echo $config_data_available?$config_data['submit_url']:get_option('siteurl').'/shop/' ?>"/>
						</td>
					</tr>
				</tbody>
			</table>			
		</div>
		<div id="config-opt-tabs-dopdown" class="config-opt-tabs-content">
			<h2>Dropdown element Customization</h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_dropdown_bg_color">
								Background Color
							</label>
						</th>
						<td class="forminp">
							<input type="Color" name="wcfw_config_opt_dropdown_bg_color" id="wcfw_config_opt_dropdown_bg_color" value="<?php echo $config_data_available?$config_data['dopdown_back_color']:'' ?>" />				
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_dropdown_border_color">
								Border Color
							</label>
						</th>
						<td class="forminp">
							<input type="Color" name="wcfw_config_opt_dropdown_border_color" id="wcfw_config_opt_dropdown_border_color" value="<?php echo $config_data_available?$config_data['dropdown_border_color']:'' ?>" />				
						</td>
					</tr>

					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_dropdown_font_color">
								Font Color
							</label>
						</th>
						<td class="forminp">
							<input type="Color" name="wcfw_config_opt_dropdown_font_color" id="wcfw_config_opt_dropdown_font_color" value="<?php echo $config_data_available?$config_data['dropdown_font_color']:'' ?>"/>				
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_dropdown_font_size">
								Font Size
							</label>
						</th>
						<td class="forminp">
							<input type="number" step="1" name="wcfw_config_opt_dropdown_font_size" id="wcfw_config_opt_dropdown_font_size" required="required" value="<?php echo $config_data_available?$config_data['dropdown_font_size']:'14' ?>"/>px		
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_dropdown_padding">
								Padding
							</label>
						</th>
						<td class="forminp">
							<input type="number" step="1" name="wcfw_config_opt_dropdown_padding" id="wcfw_config_opt_dropdown_padding" required="required" value="<?php echo $config_data_available?$config_data['dropdown_padding']:'5' ?>"/>px				
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_dropdown_inline_css">
								Inline CSS
							</label>
						</th>
						<td class="forminp">														
							<textarea name="wcfw_config_opt_dropdown_inline_css" id="wcfw_config_opt_dropdown_inline_css" data-init="0" style="border: 1px solid #ddd;"><?php echo $config_data_available?$config_data['dropdown_inline_css']:'' ?></textarea>  
						</td>
					</tr>			
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wcfw_config_opt_dropdown_additional_css">
								Additional CSS
							</label>
						</th>
						<td class="forminp">														
							<textarea name="wcfw_config_opt_dropdown_additional_css" id="wcfw_config_opt_dropdown_additional_css" data-init="0" style="border: 1px solid #ddd;"><?php echo $config_data_available?$config_data['dropdown_add_css']:'' ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>		
		
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<td><input type="submit" value="Save" class="button button-primary" value="Add Filter"></td>
			</tr>
		</tbody>
	</table>
</form>