<h2>Shortcode Generator</h2>
<form name="filter_form" id="filter_form">
<table class="form-table">
	<tbody>		
		
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wcfw_shortcode_opt_filter">
					Filter
				</label>
			</th>
			<td class="forminp">
				<select name="wcfw_shortcode_opt_filter" id="wcfw_shortcode_opt_filter" data-type="<?php echo strlen(__categories__())>0?'0':'1'; ?>" data-parent="0"> 
					<optgroup label="Parents - for creating dependency." id="wcfw_shortcode_opt_parent">
						
					</optgroup>
					<optgroup label="Category">
						<?php echo __categories__(); ?>
					</optgroup>
					<optgroup label="Attribute">
						<?php echo __attributes__(); ?>
					</optgroup>
				</select>
				<br/>
				<span class="wcfw_help">( select cateogry or attribute to filter on. )</span>
			</td>
		</tr>	

		<tr>
			<th scope="row" class="titledesc">
				<label for="wcfw_shortcode_opt_label">Label</label>
			</th>
			<td class="forminp">
				<input name="wcfw_shortcode_opt_label" id="wcfw_shortcode_opt_label" type="text"> 					
				<br/>
				<span class="wcfw_help">( label to display over input controller. )</span>	
			</td>
		</tr>	

		<tr>
			<th scope="row" class="titledesc">
				<label for="wcfw_shortcode_opt_input_type">Input Type</label>
			</th>
			<td class="forminp">
				<select id="wcfw_shortcode_opt_input_type" name="wcfw_shortcode_opt_input_type">
					<option value="icon">Icon Only</option>
            		<option value="icon_text">Icon and Text</option>
            		<option value="text_slider">Text slider</option>
            		<option value="step_slider">Step slider</option>
            		<option value="checkbox">Checkbox</option>
                    <option value="dropdown">Dropdown</option>
				</select>
				<br/>
				<span class="wcfw_help">( type of input controller. )</span>	
			</td>
		</tr>	

		<tr>
			<th scope="row" class="titledesc">
				<label for="wcfw_shortcode_opt_name">Name</label>
			</th>
			<td class="forminp">
				<input name="wcfw_shortcode_opt_name" id="wcfw_shortcode_opt_name" type="text"> 					
				<br/>
				<span class="wcfw_help">( a unique name to form parent-child relationship. )</span>	
			</td>
		</tr>			
	</tbody>
</table>
</form>

<input type="submit" name="submit" id="add_filter" class="button button-primary" value="Add Filter">
<br/>
<br/>
<h2>Available Filters</h2>
<table id="filters" class="wp-list-table widefat fixed striped pages">
	<thead>
		<tr>
			<th class="manage-column column-cb">Filter ID</th>
			<th class="manage-column column-cb">Type</th>
			<th class="manage-column column-cb">Label</th>
			<th class="manage-column column-cb">Input</th>
			<th class="manage-column column-cb">Name</th>
			<th class="manage-column column-cb">Parent</th>
			<th class="manage-column column-cb">Remove</th>
		</tr>
	</thead>
	<tbody style="cursor: grabbing;">
		<tr id="no-filter-available">			
			<th style="text-align: center;background-color: #ff000073;color: white;" colspan="7">No Filter Available.</th>
		</tr>
	</tbody>	
</table>
<br/>
<h2>Generate Shortcode</h2>
<input type="submit" name="generate" id="generate_filter" class="button button-primary disabled" value="Generate Shortcode">
<?php
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui');
	wp_enqueue_script('jquery-ui-sortable');
?>
<script>
	    
	jQuery(document).ready(function($){

		var parents=Array();
		var filters=Array();

		//Action on reorder performed or add/deletion from list
		function reorder_filters(){
			filters=Array();
	        jQuery("#filters tbody>tr").each(function(i,e){
	        	var table_row=Array();
				jQuery(e).find('td').each(function(i,e){						
					table_row.push(jQuery(e).text());
				});					
				filters.push(table_row);
			});				
		}

		//reorderable table action.
		$("#filters tbody").sortable({
		    helper: function(e,tr){
		    	var $originals = tr.children();
			    var $helper = tr.clone();
			    $helper.children().each(function(index) {
			        $(this).width($originals.eq(index).width())
			    });
			    return $helper;
		    },
		    stop: function(e,ui){
		    	$('td.index', ui.item.parent()).each(function (i) {
		            $(this).html(i + 1);
		        });
		    	reorder_filters();
		    }
		}).disableSelection();

		//remove filter action
		$("#filters").on('click','.remove-filter',function(){
			var parent=$(this).parentsUntil('tbody').find("td:eq(4)").text();
			$(this).parentsUntil('tbody').remove();			
			if($("#filters tbody>tr").length<1){
				$("#filters tbody").html('<tr id="no-filter-available"><th style="text-align: center;background-color: #ff000073;color: white;" colspan="7">No Filter Arrayvailable.</th></tr>');	
				
				if(!$("#generate_filter").hasClass('disabled')){
					$("#generate_filter").addClass('disabled');
				}			
			}									
			if(parents.indexOf(parent)){
				parents.pop(parent);
				$("#wcfw_shortcode_opt_parent").find("[value='"+parent+"']").remove();
			}

			reorder_filters();
		});

		//add filter action
		$("#add_filter").click(function(){
			
			var filter=$("#wcfw_shortcode_opt_filter").val();
			console.log(filter);
			var type=$("#wcfw_shortcode_opt_filter").find('[value="'+filter+'"]').data('type');
			console.log(type);
			if(filter.trim().length==0){ alert("Filter field is required!"); return; }

			var label=$("#wcfw_shortcode_opt_label").val();
			if(label.trim().length==0){ alert("Label field is required!"); return; }

			var input=$("#wcfw_shortcode_opt_input_type").val();			
			var name=$("#wcfw_shortcode_opt_name").val();
			if(name.trim().length==0){ alert("Name field is required!"); return; }

			var parent=$("#wcfw_shortcode_opt_filter").find('[value="'+filter+'"]').data('parent');

			if($("#no-filter-available").length){
				$("#no-filter-available").remove();
			}			

			$("#filters tbody").append(
				'<tr>'+
					'<td class="title column-title has-row-actions column-primary">'+filter+'</td>'+
					'<td class="title column-title has-row-actions column-primary">'+(type=='0'?'Category':'Attribute')+'</td>'+
					'<td class="title column-title has-row-actions column-primary">'+label+'</td>'+
					'<td class="title column-title has-row-actions column-primary">'+input+'</td>'+
					'<td class="title column-title has-row-actions column-primary">'+name+'</td>'+
					'<td class="title column-title has-row-actions column-primary">'+(parent=='0'||parent==undefined?'Parent':'Child')+'</td>'+
					'<th><span style="font-size: 2em;cursor: pointer;" class="remove-filter">&times;</span></th>'+
				'</tr>'
			);

			if(type==0){

				if(parents.indexOf(parent)<=0){
					parents.push(parent);
					$("#wcfw_shortcode_opt_parent").append('<option value="'+name+'" data-type="'+type+'" data-parent="1">'+name+'</option>');
				}
			}

			if($("#generate_filter").hasClass('disabled')){
				$("#generate_filter").removeClass('disabled');
			}
			reorder_filters();
			document.filter_form.reset();
		});

		$("#generate_filter").click(function(){
			if(!$(this).hasClass('disabled')){

				var shortcode="[woo_custome_filter_begin]";								
				$.each(filters,function(i,e){
					shortcode+="[woo_custome_filter input='"+e[3]+"' ";
					if(!isNaN(e[0])){
						shortcode+="id='"+e[0]+"' ";
					}
					shortcode+=" label='"+e[2]+"' type='"+(e[1]=='Category'?'0':'1')+"' node_type='"+e[5]+"' "; 
					if(isNaN(e[0])){
						shortcode+="parent_node='"+e[0]+"' ";
					}
					else{
						shortcode+="parent_node='' ";					
					}	
					shortcode+=" node_name='"+e[4]+"']";	
				});				
				shortcode+="[woo_custome_filter_end filter_size='"+$("#filters").find('tbody>tr').length+"']";
				
				$(this).replaceWith("<textarea style='width: 100%;'>"+shortcode+"</textarea>");
			}
		});
	});	
</script>