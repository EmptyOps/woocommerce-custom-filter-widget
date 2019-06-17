<?php 
class WOO_CUSTOME_FILTER_Widget {
	
	public function enque_asset()
	{		
		$site_url=site_url();

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-slider');
		
		wp_enqueue_script( 'jquery-ui-selectmenu');

		wp_register_style( 'jquery-ui-css',"https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css");
		wp_enqueue_style( 'jquery-ui-css' );

		wp_register_style( 'woo_custome_filter_css',plugin_dir_url( __FILE__ ).'css/filter.css');
		wp_enqueue_style( 'woo_custome_filter_css' );

		wp_register_style( 'dataTables-css','https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css');
		wp_register_script('dataTables-js','https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.js');
		
		wp_enqueue_style( 'dataTables-css');
		wp_enqueue_script( 'dataTables-js');

		
		$config_data=unserialize(get_option('woo_custome_filter_widget_config',"a:0:{}")); 
		$config_data_available=count($config_data)>0?true:false;
		
		$filter_target_data=unserialize(get_option('woo_custome_filter_target',"a:0:{}"));

		$fg_color=($config_data_available?$config_data['dropdown_back_color']:'#357DFD');
		$bg_color=($config_data_available?$config_data['dropdown_font_color']:'#357DFD');

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
				".($filter_target_data?$filter_target_data['add_css']:'')."
			</style>";		        
	}		

	//Generate widget...
	public function get_widget() {

		$filters=unserialize(get_option('woo_custome_filter_widget'));

		if(!empty($filters)){

			$this->enque_asset();

			?>
			<form method="GET" name="woo_custome_filter_form" id="woo_custome_filter_form" style="clear: both;" >
					<input type="hidden" name="woo_custome_filter" value="1" />
					<input type="hidden" name="paged" value="1"/>
					<div class="woo_custome_primary_filter">
						
			<?php


			$attr_list=array();
			////////////////////////////////////////////////////////
			//Main filter...........................................
			///////////////////////////////////////////////////////
			$advance_count=0;
			
			//Category Filters
			$_category=array();
			
			
			if($filters){
				foreach ($filters as $key => $item) {			
					if($item['type']==0 && ($item['input']=='icon' OR $item['input']=='icon_text') && $item['advance']==0) {

						$filter=$this->filter_icon($item['name'],$item['label']);						
						$this->input_wrapper(
									$filter['title'],
									'input_icon',
									array($filter['slug'],
										$filter['list'],
										$item['input'],
										$item['name'],
										$item['type']
									)
								);

						$_category[]=$filter['slug'];
					}			
					elseif ($item['type']==0 && $item['advance']==0) {				

						switch ($item['input']) {
							
							case 'checkbox':							
								$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
								$this->input_wrapper(
									$filter['title'],
									'input_checkbox',
									array(
										$filter['slug'],
										array_column($filter['list'],'name'),
										array_column($filter['list'],'slug'),
										$item['name'],
										$item['type']
									) );
									$_category[]=$filter['slug'];
								break;

							default:														
								$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
								$this->input_wrapper(
									$filter['title'],
									'input_dropdown',
									array(
										$filter['slug'],
										array_column($filter['list'],'name'),
										array_column($filter['list'],'slug'),
										$item['name'],
										$item['type']
									) );
									$_category[]=$filter['slug'];
						}
						
					}				
				}			

				//Terms Filters
				foreach ($filters as $key => $item) {			
					if($item['type']==1 && $item['advance']==0)
					{
						switch ($item['input']) {
							case 'text_slider':
								$filter=$this->range_min_max($item['name'],$item['label'],$item['type']);
								$this->input_wrapper(
									$filter['title'],
									'input_text_slider',
									array(
										$filter['slug'],
										$filter['min_value']['name'],
										$filter['max_value']['name'],
										$item['name'],
										$item['type']
									) );
								break;
							case 'step_slider':
								$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
								$this->input_wrapper(
									$filter['title'],
									'input_step_slider',
									array(
										$filter['slug'],
										array_column($filter['list'],'name'),
										array_column($filter['list'],'slug'),
										$item['name'],
										$item['type']
									) );
								break;
							case 'checkbox':
								$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
								$this->input_wrapper(
									$filter['title'],
									'input_checkbox',
									array(
										$filter['slug'],
										array_column($filter['list'],'name'),
										array_column($filter['list'],'slug'),
										$item['name'],
										$item['type']
									) );							
								break;						
							case 'dropdown':
								$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
								$this->input_wrapper(
									$filter['title'],
									'input_dropdown',
									array(
										$filter['slug'],
										array_column($filter['list'],'name'),
										array_column($filter['list'],'slug'),
										$item['name'],
										$item['type']
									) );							
								break;
							default:
								$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
								$this->input_wrapper(
									$filter['title'],
									'input_step_slider',
									array(
										$filter['slug'],
										array_column($filter['list'],'name'),
										array_column($filter['list'],'slug'),
										$item['name'],
										$item['type']
									) );
						}
						$attr_list[]=wc_get_attribute($item['name'])->slug;
					}
					elseif( $item['advance']==1) {
						$advance_count++;
					}
				}			
				//Show price slider.
				$this->input_wrapper('Price','slider_price',array());
				//Advance Filters
				?></div><?php 
				if($advance_count){
				
					?>
					<div class='woo_custome_advance_filter'>
						<h1 style='text-align:center'>
							<a href='#'>Advance Filter</a>
						</h1>
					</div>
					<div class='woo_custome_advance_filter_display' style="display: none;">
					<?php
					////////////////////////////////////////////////////////
					//Advance filter...........................................
					///////////////////////////////////////////////////////

					foreach ($filters as $key => $item) {
						if($item['type']==0 && $item['advance']==1){						
							if($item['type']==0 && ($item['input']=='icon' OR $item['input']=='icon_text')) {

								$filter=$this->filter_icon($item['name'],$item['label']);
								$this->input_wrapper(
											$filter['title'],
											'input_icon',
											array($filter['slug'],
												$filter['list'],
												$item['input'],
												$item['name'],
												$item['type']
											)
										);

								$_category[]=$filter['slug'];
							}			
							elseif ($item['type']==0) {				

								switch ($item['input']) {
									
									case 'checkbox':							
										$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
										$this->input_wrapper(
											$filter['title'],
											'input_checkbox',
											array(
												$filter['slug'],
												array_column($filter['list'],'name'),
												array_column($filter['list'],'slug'),
												$item['name'],
												$item['type']
											) );
											$_category[]=$filter['slug'];
										break;

									default:														
										$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
										$this->input_wrapper(
											$filter['title'],
											'input_dropdown',
											array(
												$filter['slug'],
												array_column($filter['list'],'name'),
												array_column($filter['list'],'slug'),
												$item['name'],
												$item['type']
											) );
											$_category[]=$filter['slug'];
								}								
							}										
						}
					}

					foreach ($filters as $key => $item) {
						if($item['type']==1 && $item['advance']==1) {
							
							switch ($item['input']) {
								case 'text_slider':
									$filter=$this->range_min_max($item['name'],$item['label'],$item['type']);
									$this->input_wrapper(
										$filter['title'],
										'input_text_slider',
										array(
											$filter['slug'],
											$filter['min_value']['name'],
											$filter['max_value']['name'],
											$item['name'],
											$item['type']
										) );
									break;
								case 'step_slider':
									$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
									$this->input_wrapper(
										$filter['title'],
										'input_step_slider',
										array(
											$filter['slug'],
											array_column($filter['list'],'name'),
											array_column($filter['list'],'slug'),
											$item['name'],
											$item['type']
										) );
									break;
								case 'checkbox':
									$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
									$this->input_wrapper(
										$filter['title'],
										'input_checkbox',
										array(
											$filter['slug'],
											array_column($filter['list'],'name'),
											array_column($filter['list'],'slug'),
											$item['name'],
											$item['type']
										) );							
									break;						
								case 'dropdown':
									$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
									$this->input_wrapper(
										$filter['title'],
										'input_dropdown',
										array(
											$filter['slug'],
											array_column($filter['list'],'name'),
											array_column($filter['list'],'slug'),
											$item['name'],
											$item['type']
										) );							
									break;
								default:
									$filter=$this->range_steps($item['name'],$item['label'],$item['type']);							
									$this->input_wrapper(
										$filter['title'],
										'input_step_slider',
										array(
											$filter['slug'],
											array_column($filter['list'],'name'),
											array_column($filter['list'],'slug'),
											$item['name'],
											$item['type']
										) );
							}
							$attr_list[]=wc_get_attribute($item['name'])->slug;
						}
					}
				}
			}		
			?>
				</div>			
				<input type="hidden" name="_category" value="<?php echo implode(',',$_category); ?>" />
				<input type="hidden" name="_attribute" id="woo_custome_attr_query" value="<?php echo implode(',',$attr_list)?>" />			
				<div id="loading"></div>
			</form>
			<?php
			wp_register_script('woo_custome_filter_js',plugins_url('js/filter.js',__FILE__),array('jquery'));        
        	wp_enqueue_script('woo_custome_filter_js');
		}
	}

	private function input_wrapper($title,$function,$param){
		?>
			<div>
				<fieldset>
					<legend><?php echo $title; ?></legend>
					<?php call_user_func_array(array($this,$function),$param); ?>
				</fieldset>
			</div>
		<?php
	}

	//Returns minimum value and maximum value of range;
	public function range_min_max($id,$title='',$filter_type=0) {
		
		$field_title='';	
		$field_slug='';
		$min_value=array("id"=>'',"slug"=>'',"name"=>"0","type"=>'');
		$max_value=array("id"=>'',"slug"=>'',"name"=>"0","type"=>'');

		if ($filter_type) {
			
			$term=wc_get_attribute($id);

			$field_title=empty($title)?@$term->name:$title;
			$field_slug=@$term->slug;

			$taxonomies=get_terms(array('taxonomy'=>wc_attribute_taxonomy_name_by_id(@$term->id),'hide_empty'=>false));

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

	//Returns all values in range;
	//@input : filter_type - wether it is category filter or term filter;
	public function range_steps($id,$title='',$filter_type=0) {

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
			$field_slug=@$category->slug;

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
	
	public function filter_icon($id,$title='') {
		
		$category=get_term_by('id',$id,'product_cat');
		$title=empty($title)?@$category->name:$title;
		$slug=@$category->slug;

		$list=array();    		
		foreach (get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'ASC', 'parent'=>$id)) as $cat_item) {

			$list[]=array(  "icon" => wp_get_attachment_url( get_term_meta( $cat_item->term_id, 'thumbnail_id', true )),
							"name" => $cat_item->name,
							"slug"=> $cat_item->slug,							
						);					
		}
		return array('list'=>$list,'title'=>$title,'slug'=>$slug);
	}

	//Generate text slider
	public function input_text_slider($slug,$min,$max,$id,$type) {
		
		?>	
		<div>
			<div class="sliderValueContainer">									
			    <input type="text" name="min_<?php echo $slug; ?>" data-input="text_slider"
			     data-slug="<?php echo $slug; ?>" data-index="0" value="<?php echo $min; ?>" data-role="left-text" class="sliderValue"/>				    

			    <input type="text"  name="max_<?php echo $slug; ?>" data-input="text_slider"  
			     data-slug="<?php echo $slug; ?>" data-index="1" value="<?php echo $max; ?>" data-role="rigth-text" class="sliderValue"/>    
			</div>					
			<div id="text_slider_<?php echo $slug; ?>" class="ui-slider" data-input="text_slider" data-slug="<?php echo $slug; ?>"
			 data-role="slider" data-min="<?php echo $min; ?>" data-max="<?php echo $max; ?>" data-filter-id="<?php echo $id; ?>" data-type="<?php echo $type; ?>" style="margin-top:0.8em;"> </div>
		</div>			
		<?php
	}

	//Generate step slider;
	public function input_step_slider($slug,$items_name,$items_slug,$id,$type) {

		?>
		<div>
			<div>
				<div id='legend_<?php echo $slug; ?>' data-role="legend" data-input="step_slider" data-slug="<?php echo $slug; ?>"></div>
				<div id='step_slider_<?php echo $slug; ?>' data-input="step_slider" data-role="slider" class='ui-slider' data-slug="<?php echo $slug; ?>" data-names="<?php echo implode(',',$items_name); ?>" data-slugs="<?php echo implode(',',$items_slug); ?>" data-filter-id="<?php echo $id; ?>" data-type="<?php echo $type; ?>" ></div>
			</div>
			<input type='hidden' name='min_<?php echo $slug; ?>'  
					value='<?php echo $items_slug[0] ?>' data-slug="<?php echo $slug; ?>" />
			<input type='hidden' name='max_<?php echo $slug; ?>'
					value='<?php echo $items_slug[count($items_slug)-1]; ?>' data-slug="<?php echo $slug; ?>" />
		</div>
		<?php
	}

	//Generate dropdown based filter option;
	public function input_dropdown($slug,$items_name,$items_slug,$id,$type,$opt_title='All') {

		$list_items = array_combine($items_name,$items_slug);

		?>
		<div>
			<select style="width: 100%;" name="<?php echo $type==0?'cat_filter_'.$slug:'dropdown_'.$slug; ?>" id="dropdown_<?php echo $slug; ?>" data-slug="<?php echo $slug; ?>" data-role="dropdown" data-input="dropdown" data-filter-id="<?php echo $id; ?>" data-type="<?php echo $type; ?>" >

				<option selected="selected" value=""><?php echo $opt_title; ?></option>
				<?php foreach ($list_items as $name => $slug) : ?>
					<option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
				<?php endforeach;?>
			</select>						
		</div>
		<?php
	}

	//Generate checkbox based filter option;
	public function input_checkbox($slug,$items_name,$items_slug,$id,$type) {
		$list_items = array_combine($items_name,$items_slug);
		?>
			<div style='display: grid;justify-content:space-between;grid-template-columns: auto auto auto auto;'>
				<?php foreach ($list_items as $item_name=>$item_slug) : ?>
					<label for="check_<?php echo $item_slug; ?>" style="text-align: left !important;"><input type="checkbox" checked="checked" 
					    		id='check_<?php echo $item_slug; ?>'
					    		data-slug="<?php echo $slug; ?>"
					    		value="<?php echo $item_slug; ?>"
					    		class="checklist_<?php echo $slug; ?>" 
					    		data-input="checkbox" 
					    		data-role="checkbox"
					    		data-filter-id="<?php echo $id; ?>" 
					    		data-type="<?php echo $type; ?>"/><?php echo $item_name; ?></label>
    			<?php endforeach; ?>
    			<input type="hidden" name="checklist_<?php echo $slug; ?>" value="<?php echo implode(',',$items_slug); ?>">
    		</div>    			
			<br/>					
		<?php
	}

	//Generate Price Slider.
	public function slider_price() {

		$prices = $this->get_filtered_price();
		$min    = floor( $prices->min_price );
		$max    = ceil( $prices->max_price );		
		?>
		<div>
			<div>
				<input type="hidden" name="min_price" value="<?php echo $min; ?>" id="price_min"/>
				<input type="hidden" name="max_price" value="<?php echo $max; ?>" id="price_max"/>
			    <input type="text" class="sliderValue" data-index="0" value="$<?php echo $min; ?>" id="price_min_text" />				    
			    <input type="text" class="sliderValue" data-index="1" value="$<?php echo $max; ?>" id="price_max_text" style="float: right;"/>    
			</div>				
			<div id="price_slider" class="ui-slider" data-min="<?php echo $min; ?>" data-max="<?php echo $max; ?>" data-input='price_slider' data-role='price_slider' style="margin-top:0.5em;"></div>
			<br/>				
		</div>			
		<?php
	}
	
	//Generate Icon based filter menu.
	public function input_icon($slug,$list,$input='icon',$id,$type) {		
		?>
		<div>
			<div class="woo_custome_icon_grid ">					
				<?php foreach ($list as $icons): ?>
					<div style="text-align: center;margin: 0px 3% 4% !important;" 
						title="<?php $icons["name"]; ?>"
						class="woo_custome_filter_icon"
						data-input="select_icons"
						data-role="select_icons" 
						data-slug="<?php echo $slug; ?>"
						data-icon-slug="<?php echo $icons['slug']; ?>" 
						data-filter-id="<?php echo $id; ?>" 
						data-type="<?php echo $type; ?>"
						>
						<div>
							<img src='<?php echo $icons['icon']; ?>' height='40' width='40' style="margin: auto;padding-bottom: 3px;"/>
						</div>
						<?php if($input=='icon_text'): ?>
							<div><?php echo $icons['name'] ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>								
			</div>		
			<input type='hidden' name='cat_filter_<?php echo $slug; ?>' value=''/>
		</div>		
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