<?php 
class WOO_CUSTOME_FILTER_Widget {

	function __construct()
	{	
        $this->enque_asset();				
		$this->get_widget();		
	}

	private function enque_asset()
	{		
		$site_url=site_url();

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-slider');

		wp_register_style( 'jquery-ui-css',plugin_dir_url( __FILE__ ).'css/jquery-ui.min.css');
		wp_enqueue_style( 'jquery-ui-css' );

		wp_register_style( 'woo_custome_filter_css',plugin_dir_url( __FILE__ ).'css/filter.css');
		wp_enqueue_style( 'woo_custome_filter_css' );

		wp_register_style( 'dataTables-css','https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css');
		wp_register_script('dataTables-js','https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.js');
		
		wp_enqueue_style( 'dataTables-css');
		wp_enqueue_script( 'dataTables-js');

		$fg_color='#357DFD';
		$bg_color='#357DFD';

		//wp-head here....
		echo "<style>						
				.loading{												
					background-image:url(".plugin_dir_url(__FILE__)."icon/spinner.gif);				
				}			
				#woo_custome_filter_table th{
					background-color: {$bg_color};
				}
				.ui-widget-header{
					border: 1px solid {$fg_color} !important;
				    background: {$fg_color} !important;
				    color: {$fg_color} !important;				    
				}

				.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active{
					    border: 1px solid {$fg_color} !important;
					    background: {$fg_color} !important;
				}
				.ui-widget.ui-widget-content{
					 border: 1px solid {$fg_color} !important;
					 background: {$fg_color} !important;
				}
				.woo_custome_filter_icon_select{
					border-bottom:2px solid {$fg_color} !important;
				}
				.woo_custome_filter_icon:hover:not(.none_editable){
					border-bottom:2px solid {$fg_color};						
				}
			</style>";		
            
        wp_register_script('woo_custome_filter_js',plugins_url('js/filter.js',__FILE__),array('jquery'));        
        wp_enqueue_script('woo_custome_filter_js');
        
	}		

	//Returns minimum value and maximum value of range;
	private function range_min_max($id,$title='',$filter_type=0) {
		
		$field_title='';	
		$field_slug='';
		$min_value=array("id"=>'',"slug"=>'',"name"=>"0","type"=>'');
		$max_value=array("id"=>'',"slug"=>'',"name"=>"0","type"=>'');

		if ($filter_type) {
			
			$term=wc_get_attribute($id);

			$field_title=empty($title)?$term->name:$title;
			$field_slug=$term->slug;

			$taxonomies=get_terms(array('taxonomy'=>wc_attribute_taxonomy_name_by_id($term->id),'hide_empty'=>false));

			$min_value=array("id"=>$taxonomies[0]->term_id,"slug"=>$taxonomies[0]->slug,"name"=>$taxonomies[0]->name,"type"=>'attr');
			$max_value=array("id"=>$taxonomies[0]->term_id,"slug"=>$taxonomies[0]->slug,"name"=>$taxonomies[0]->name,"type"=>'attr');

			foreach ($taxonomies as $taxonomy){
				if($taxonomy->name < $min_value['name']){
					$min_value=array("id"=>$taxonomy->term_id,"slug"=>$taxonomy->slug,"name"=>$taxonomy->name,"type"=>'attr');
				}

				if($taxonomy->name > $max_value['name']){
					$max_value=array("id"=>$taxonomy->term_id,"slug"=>$taxonomy->slug,"name"=>$taxonomy->name,"type"=>'attr');
				}				                	  	
        	}
		}		
		else {

			$category=get_term_by('id',$id,'product_cat');

			$field_title=empty($title)?$category->name:$title;
			$field_slug=$category->slug;

			$sub_categories = get_categories(array(
	            'hierarchical' => 1,
	            'show_option_none' => '',
	            'hide_empty' => false,
	            'parent' => $id,
	            'taxonomy' => 'product_cat'
	        ));

	        $min_value=array("id"=>$sub_categories[0]->term_id,"slug"=>$sub_categories[0]->slug,"name"=>$sub_categories[0]->name,"type"=>'cat');
			$max_value=array("id"=>$sub_categories[0]->term_id,"slug"=>$sub_categories[0]->slug,"name"=>$sub_categories[0]->name,"type"=>'cat');

	        foreach ($sub_categories as $sub_category) {

	        	if($sub_category->name < $min_value['name']){
					$min_value=array("id"=>$sub_category->term_id,"slug"=>$sub_category->slug,"name"=>$sub_category->name,"type"=>'cat');
				}

				if($sub_category->name > $max_value['name']){
					$max_value=array("id"=>$sub_category->term_id,"slug"=>$sub_category->slug,"name"=>$sub_category->name,"type"=>'cat');
				}
	        }			
		}		
		return array('min_value'=>$min_value,'max_value'=>$max_value,'title'=>$field_title,'slug'=>$field_slug);
	}
	
	//Generate text slider;
	private function input_text_slider($id,$title,$filter_type) {
		$filter=$this->range_min_max($id,$title,$filter_type);				
		?>
		<div>
			<fieldset>
				<legend><?php echo $filter['title']; ?></legend>
				<div class="woo_custome_input_text_slider">									
				    <input class="text_slider_<?php echo $filter['slug'] ?>" type="text"  name="min_<?php echo $filter['slug'] ?>" 
				    		data-index="0" value="<?php echo $filter['min_value']['name'] ?>" />				    

				    <input class="text_slider_<?php echo $filter['slug'] ?>" type="text"  name="max_<?php echo $filter['slug'] ?>" 
				    		data-index="1" value="<?php echo $filter['max_value']['name'] ?>" />    
				</div>			
				<div id="text_slider_<?php echo $filter['slug'] ?>" class="ui-slider" style="margin-top:0.5em;">
			</fieldset>
		</div><br/>
		<script>jQuery(document).ready(function($) {
			  	$("#text_slider_<?php echo $filter['slug'] ?>").slider({
				  	range: true,
				    min: <?php echo $filter['min_value']['name'] ?>,
				    max: <?php echo $filter['max_value']['name'] ?>,
				    step: 0.1,
				    values: [<?php echo $filter['min_value']['name'] ?>, <?php echo $filter['max_value']['name'] ?>],
				    slide: function(event, ui) {					           				    		
					      $("input[name='min_<?php echo $filter['slug'] ?>']").val(ui.values[0]);
					      $("input[name='max_<?php echo $filter['slug'] ?>']").val(ui.values[1]);
				    },
				    stop:function(event, ui) {
				    	var slider_min=$("#text_slider_<?php echo $filter['slug'] ?>").slider("option", "min");
				    	var slider_max=$("#text_slider_<?php echo $filter['slug'] ?>").slider("option", "max");
				    	var current_min=ui.values[0];
						var current_max=ui.values[1];
				    	if(slider_min==current_min && slider_max==current_max) {

				    		if($("[name='_attribute']").val().includes('<?php echo $filter['slug'] ?>')) {
				    			$("[name='_attribute']").val($("[name='_attribute']").val().replace(',<?php echo $filter['slug'] ?>',''));
				    			$("[name='_attribute']").val($("[name='_attribute']").val().replace('<?php echo $filter['slug'] ?>',''));
				    		}
				    	}
				    	else {
				    		if(! $("[name='_attribute']").val().includes('<?php echo $filter['slug'] ?>')) {
				    			$("[name='_attribute']").val($("[name='_attribute']").val()+',<?php echo $filter['slug'] ?>')
				    		}
				    	}
				    	$('[name="paged"]').val('1');
				    	woo_custome_filter_change();
				    }
				});

				$("input.text_slider_<?php echo $filter['slug'] ?>").change(function() {				    
					$("#text_slider_<?php echo $filter['slug'] ?>").slider("values", $(this).data("index"), $(this).val());
				});			

			});
		</script>
		<?php
	}

	//Returns all values in range;
	//@input : filter_type - wether it is category filter or term filter;
	private function range_steps($id,$title='',$filter_type=0) {

		$list=array();		
		$field_title='';	
		$field_slug='';

		if ($filter_type) {
			
			$term=wc_get_attribute($id);

			$field_title=empty($title)?$term->name:$title;
			$field_slug=$term->slug;			

			foreach (get_terms(array('taxonomy'=>wc_attribute_taxonomy_name_by_id($term->id),'hide_empty'=>false)) as $taxonomy){
				
				$list[]=array("id"=>$taxonomy->term_id,"slug"=>$taxonomy->slug,"name"=>$taxonomy->name,"type"=>'attr');                	  	
        	}
		}		
		else {

			$category=get_term_by('id',$id,'product_cat');

			$field_title=empty($title)?$category->name:$title;
			$field_slug=$category->slug;

			$sub_categories = get_categories(array(
	            'hierarchical' => 1,
	            'show_option_none' => '',
	            'hide_empty' => false,
	            'parent' => $id,
	            'taxonomy' => 'product_cat'
	        ));

	        foreach ($sub_categories as $sub_category) {
	        	$list[]=array("id"=>$sub_category->term_id,"slug"=>$sub_category->slug,"name"=>$sub_category->name,"type"=>'cat');
	        }	
		}		
		return array('list'=>$list,'title'=>$field_title,'slug'=>$field_slug);			
	}

	//Generate step slider;
	private function input_step_slider($id,$title,$filter_type) {

		$filter=$this->range_steps($id,$title,$filter_type);

		$items_name=array_column($filter['list'],'name');			
		$items_slug=array_column($filter['list'],'slug');	

		?>
		<div>
			<fieldset class='woo_custome_input_step_slider'>
				<legend><?php echo $filter['title']; ?></legend>
					<div id='filter_legend_<?php echo $filter['slug']; ?>'></div>
					<input type='hidden' name='<?php echo 'min_'.$filter['slug']; ?>' id='<?php echo 'min_'.$filter['slug']; ?>' 
					value='<?php echo $items_slug[0] ?>' class="step_slider_<?php echo $filter['slug']; ?>" />
					<input type='hidden' name='<?php echo 'max_'.$filter['slug']; ?>' id='<?php echo 'max_'.$filter['slug']; ?>' 
					value='<?php echo $items_slug[count($items_slug)-1]; ?>' class="step_slider_<?php echo $filter['slug']; ?>" />

				<div id='step_slider_<?php echo $filter['slug']; ?>' class='ui-slider'></div>
			</fieldset>
			<script>		
				jQuery(document).ready(function($){
					var items_name_<?php echo $filter['slug']; ?>=['<?php echo implode("','", $items_name); ?>'];
					var items_slug_<?php echo $filter['slug']; ?>=['<?php echo implode("','",$items_slug); ?>'];
					
					$('#step_slider_<?php echo $filter['slug']; ?>').slider({
						range: true,
						min:1,						
						max: (items_name_<?php echo $filter['slug']; ?>.length),
						values: [1,items_name_<?php echo $filter['slug']; ?>.length],						
						slide: function(event, ui) {	
							
							$('[name="<?php echo 'min_'.$filter['slug']; ?>"]')
									.val(items_slug_<?php echo $filter['slug']; ?>[ui.values[0]-1]);
							$('[name="<?php echo 'max_'.$filter['slug']; ?>"]')
									.val(items_slug_<?php echo $filter['slug']; ?>[ui.values[1]-1])
					    },
					    stop:function(event, ui) {

					    	var slider_min=$("#step_slider_<?php echo $filter['slug'] ?>").slider("option", "min");
					    	var slider_max=$("#step_slider_<?php echo $filter['slug'] ?>").slider("option", "max");
					    	var current_min=ui.values[0];
							var current_max=ui.values[1];
					    	if(slider_min==current_min && slider_max==current_max) {

					    		if($("[name='_attribute']").val().includes('<?php echo $filter['slug'] ?>')) {
					    			$("[name='_attribute']").val($("[name='_attribute']").val().replace(',<?php echo $filter['slug'] ?>',''));
					    			$("[name='_attribute']").val($("[name='_attribute']").val().replace('<?php echo $filter['slug'] ?>',''));
					    		}
					    	}
					    	else {
					    		if(! $("[name='_attribute']").val().includes('<?php echo $filter['slug'] ?>')) {
					    			$("[name='_attribute']").val($("[name='_attribute']").val()+',<?php echo $filter['slug'] ?>');
					    		}
					    	}
					    	$('[name="paged"]').val('1');
					    	woo_custome_filter_change();
						}

					});

					var scale = 100 / (items_name_<?php echo $filter['slug']; ?>.length - 1);
					$.each(items_name_<?php echo $filter['slug']; ?>, function(key,value){
						var w = scale;
						if(key === 0 || key === items_name_<?php echo $filter['slug']; ?>.length-1) w = scale/2;
						$('#filter_legend_<?php echo $filter['slug']; ?>')
							.append("<label title='"+value+"' style='width:"+w+"%'>"+value+"</label>");
					});				

				});				
			</script>
		</div>
		<?php
	}

	//Generate checkbox based filter option;
	private function input_checkbox($id,$title,$filter_type) {
		$filter=$this->range_steps($id,$title,$filter_type);		
		?>
		<div>
			<fieldset>
				<legend><?php echo $filter['title']; ?></legend>				
				<div style='display: flex;justify-content:space-evenly;'>
				<?php foreach ($filter['list'] as $term) : ?>
					<label for="check_<?php echo $term['slug']; ?>">
						<?php echo $term['name']; ?>
					    <input type="checkbox" checked="checked" 
					    		id='check_<?php echo $term['slug']; ?>'
					    		data-slug="<?php echo $term['slug']; ?>" 
					    		class="checklist_<?php echo $filter['slug'] ?>">
    				</label>
    			<?php endforeach; ?>
    			</div>
    			<input type="hidden" name="checklist_<?php echo $filter['slug']; ?>" value="<?php echo implode(',',array_column($filter['list'],'slug')); ?>">
			</fieldset><br/>
			<script>
				jQuery(document).ready(function($){
					jQuery('.checklist_<?php echo $filter['slug'] ?>').on('click',function(){
						jQuery('[name="checklist_<?php echo $filter['slug']; ?>"]').val('');
						jQuery('.checklist_<?php echo $filter['slug'] ?>:checked').each(function(i,e){
							
							jQuery('[name="checklist_<?php echo $filter['slug']; ?>"]').val(
								jQuery('[name="checklist_<?php echo $filter['slug']; ?>"]').val()
								+ 
								jQuery(e).attr('data-slug')
								+
								 ','
							);
						});
						var check_val=jQuery('[name="checklist_<?php echo $filter['slug']; ?>"]').val();
						//alert(check_val);
						jQuery('[name="checklist_<?php echo $term['slug']; ?>"]')
							.val(check_val.substr(0,check_val.length-1));

				    	if( ( jQuery('.checklist_<?php echo $filter['slug'] ?>:checkbox').length==jQuery('.checklist_<?php echo $filter['slug'] ?>:checkbox:checked').length)  || (jQuery('.checklist_<?php echo $filter['slug'] ?>:checkbox:checked').length==0 ) ) {

				    		if($("[name='_attribute']").val().includes('<?php echo $filter['slug'] ?>')) {
				    			$("[name='_attribute']").val($("[name='_attribute']").val().replace(',<?php echo $filter['slug'] ?>',''));
				    			$("[name='_attribute']").val($("[name='_attribute']").val().replace('<?php echo $filter['slug'] ?>',''));
				    		}
				    	}
				    	else {
				    		if(! $("[name='_attribute']").val().includes('<?php echo $filter['slug'] ?>')) {
				    			$("[name='_attribute']").val($("[name='_attribute']").val()+',<?php echo $filter['slug'] ?>');
				    		}
				    	}
						$('[name="paged"]').val('1');	
						woo_custome_filter_change();
					});				

				});
			</script>
		</div>
		<?php
	}

	private function slider_price() {

		$prices = $this->get_filtered_price();
		$min    = floor( $prices->min_price );
		$max    = ceil( $prices->max_price );
		
		echo '<div><fieldset><legend>Price</legend><div style="display: flex;justify-content: space-between;padding-bottom:5px;">
				<input type="hidden" name="min_price" value="'.$min.'" id="price_min"/>
				<input type="hidden" name="max_price" value="'.$max.'" id="price_max"/>
			    <input type="text" class="sliderValue" data-index="0" value="$'.$min.'" />				    
			    <input type="text" class="sliderValue" data-index="1" value="$'.$max.'" />    
				</div>				
				<div id="price_slider" class="ui-slider" style="margin-top:0.5em;"></div></fieldset></div><br/>
				<script>jQuery(document).ready(function($) {
				  $("#price_slider").slider({
				  	range: true,
				    min: '.$min.',
				    max: '.$max.',
				    step: 1,
				    values: ['.$min.', '.$max.'],
				    slide: function(event, ui) {
				      
				     	$("input.sliderValue[data-index=\'0\']").val("$"+ui.values[0]);
				        $("input.sliderValue[data-index=\'1\']").val("$"+ui.values[1]);
				      
				      	$("#price_min").val(ui.values[0]);
				      	$("#price_max").val(ui.values[1]);
				    },
				    stop:function(event, ui) {
				    	$("[name=\'paged\']").val("1");
				    	woo_custome_filter_change();
				    }
				  });

				  $("input.sliderValue").change(function() {
				    var $this = $(this);
				    $("#price_slider").slider("values", $this.data("index"), $this.val());
				  });
				});
			</script><div id="loading"></div>';	
	}
	
	private function get_widget() {

		$filters=unserialize(get_option('woo_custome_filter_widget'));

		echo '<form method="GET" name="woo_custome_filter_form" id="woo_custome_filter_form" style="clear: both;" >
				<div class="woo_custome_primary_filter">
					<input type="hidden" name="woo_custome_filter" value="1" />';

		$attr_list=array();
		////////////////////////////////////////////////////////
		//Main filter...........................................
		///////////////////////////////////////////////////////
		$advance_count=0;
		
		//Category Filters
		$_category=array();
		
		foreach ($filters as $key => $item) {			
			if($item['type']==0 && ($item['input']=='icon' OR $item['input']=='icon_text') && $item['advance']==0) {

				$this->woo_custome_filter_ui_icon($item['name'],$item['label'],$item['type'],$item['input']);								
				$_category[]=get_term_by('id',$item['name'],'product_cat')->slug;
			}
			elseif ($item['type']==0 && $item['advance']==0) {

				$this->input_step_slider($item['name'],$item['label'],$item['type']);		
			}				
		}			
		echo '<input type="hidden" name="_category" value="'.implode(',',$_category).'"/>';
		//Terms Filters
		foreach ($filters as $key => $item) {			
			if($item['type']==1 && $item['advance']==0)
			{
				switch ($item['input']) {
					case 'text_slider':
						$this->input_text_slider($item['name'],$item['label'],$item['type']);
						break;
					case 'step_slider':
						$this->input_step_slider($item['name'],$item['label'],$item['type']);
						break;
					case 'checkbox':
						$this->input_checkbox($item['name'],$item['label'],$item['type']);
						break;						
					default:
						$this->input_step_slider($item['name'],$item['label'],$item['type']);
				}
				$attr_list[]=wc_get_attribute($item['name'])->slug;
			}
			elseif( $item['advance']==1) {
				$advance_count++;
			}
		}			
		//Show price slider.
		$this->slider_price();		
		//Advance Filters
		if($advance_count){
		
			echo "<div class='woo_custome_advance_filter' style='margin-bottom:1.5em;'><h1 style='text-align:center'><a href='#'>Advance Filter</a></h1><div class='woo_custome_advance_filter_display'>";
			////////////////////////////////////////////////////////
			//Advance filter...........................................
			///////////////////////////////////////////////////////
			foreach ($filter as $key => $item) {
				if($item['type']==1 && $item['advance']==1) {
					
					switch ($item['input']) {
						case 'text_slider':
							$this->input_text_slider($item['name'],$item['label'],$item['type']);
							break;
						case 'step_slider':
							$this->input_step_slider($item['name'],$item['label'],$item['type']);
							break;
						case 'checkbox':
							$this->input_checkbox($item['name'],$item['label'],$item['type']);
							break;						
						default:
							$this->input_step_slider($item['name'],$item['label'],$item['type']);
					}
					$attr_list[]=wc_get_attribute($item['name'])->slug;
				}
			}
		}

		//echo '<input type="hidden" name="_attribute" id="eo_wbc_attr_query" value="'.implode(',',$attr_list).'" />';
		echo '<input type="hidden" name="_attribute" id="woo_custome_attr_query" value="" />';
		echo "</div></form>";
		/*if($this->eo_wbc_get_category()=='solitaire'){
			echo "<div class='eo_wbc_filter_big_menu'>
					<div class='eo_wbc_srch_btn'>compare</div>
					<div class='eo_wbc_srch_btn'>matches</div>
					<div class='eo_wbc_srch_btn'>reset</div>
				</div>";
		}*/
	}

	private function woo_custome_filter_ui_icon($id,$title='',$type=0,$input='icon') {

		$non_edit=false;
		$list=array();
		$cat_filter_list=array();
		$cat_query_list=!empty($_GET['CAT_LINK'])?explode(',',$_GET['CAT_LINK']):array();
		foreach (get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC', 'child_of'=>$id)) as $cat_item) {

			$list[]=array("icon" => wp_get_attachment_url( get_woocommerce_term_meta( $cat_item->term_id, 'thumbnail_id', true )),
							"name" => $cat_item->name,
							"slug"=> $cat_item->slug,
							"mark"=> in_array($cat_item->slug,$cat_query_list)
						);					
						
			if($non_edit==false && in_array($cat_item->slug,$cat_query_list)){
				$non_edit=true;						
			}

			if(in_array($cat_item->slug,$cat_query_list)) {
				$cat_filter_list[]=$cat_item->slug;
			}
		}
		
		?>
		<div>
			<fieldset>
				<legend><?php echo !empty($title) ? $title : get_term_by('id',$id,'product_cat')->name; ?></legend>
				<div class="woo_custome_icon_grid ">					
					<?php foreach ($list as $filter_icon): ?>
							<div style="text-align: center;margin: 0px 3% 4% !important; <?php echo !$non_edit?'cursor: pointer;':''?> " 
								title="<?php $filter_icon["name"]; ?>"
								class="woo_custome_filter_icon <?php echo $non_edit ? 'none_editable':'' ?> 
									<?php echo $filter_icon['mark'] ? 'woo_custome_filter_icon_select':''?>" 
								data-slug="<?php echo $filter_icon['slug']; ?>" 
								data-filter="<?php echo get_term_by('id',$id,'product_cat')->slug; ?>">
								<div>
									<img src='<?php echo $filter_icon['icon']; ?>' height='40' width='40' />
								</div>
								<?php if($input=='icon_text'): ?>
									<div><?php echo $filter_icon['name'] ?></div>
								<?php endif; ?>
							</div>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<input type='hidden' data-edit="<?php echo $non_edit?'0':'1'; ?>"
					name='cat_filter_<?php echo get_term_by('id',$id,'product_cat')->slug; ?>' 
					value='<?php echo implode(',', $cat_filter_list) ?>'/>
		</div>		
		<script>

			jQuery(document).ready(function($){
				jQuery('[data-filter="<?php echo get_term_by('id',$id,'product_cat')->slug; ?>"]').not(".none_editable").on('click',function(){

					var filter_list=jQuery('[name="cat_filter_<?php echo get_term_by('id',$id,'product_cat')->slug; ?>"]');
										
					if(filter_list.val().includes( jQuery(this).attr('data-slug'))){
						filter_list.val(filter_list.val().replace(','+jQuery(this).attr('data-slug'),''));
					}
					else {
						filter_list.val(filter_list.val()+','+jQuery(this).attr("data-slug"));
					}					

					var icon_val=jQuery('[name="cat_filter_<?php echo get_term_by('id',$id,'product_cat')->slug; ?>"]').val();					
					jQuery('[name="cat_filter_<?php echo get_term_by('id',$id,'product_cat')->slug; ?>"]')
						.val(icon_val.substr(0,icon_val.length));
					
					jQuery(this).toggleClass('woo_custome_filter_icon_select');
					$('[name="paged"]').val('1');
					woo_custome_filter_change();
				});				
			});
		</script>
		<?php
	}	

    //get min and max price.
	protected function get_filtered_price() {
		global $wpdb;

		$args       = wc()->query->get_main_query()->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

		if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = array(
				'taxonomy' => $args['taxonomy'],
				'terms'    => array( $args['term'] ),
				'field'    => 'slug',
			);
		}

		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query  = new WP_Tax_Query( $tax_query );

		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
		$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " 	WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
			AND {$wpdb->posts}.post_status = 'publish'
			AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
			AND price_meta.meta_value > '' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		$search = WC_Query::get_main_search_query_sql();
		if ( $search ) {
			$sql .= ' AND ' . $search;
		}

		$sql = apply_filters( 'woocommerce_price_filter_sql', $sql, $meta_query_sql, $tax_query_sql );

		return $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
	}
}	
?>