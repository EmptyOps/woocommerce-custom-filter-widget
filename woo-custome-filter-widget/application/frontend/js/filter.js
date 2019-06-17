//render products DOM to view
function woo_custome_filter_render_html(data) {			

	//Replace Result Count Status...
	if(jQuery('.woocommerce-result-count',jQuery(data)).html()!==undefined){								
		jQuery(".woocommerce-result-count").html(jQuery('.woocommerce-result-count',jQuery(data)).html());
	}
	else {
		jQuery(".woocommerce-result-count").html('');	
	}

	//Replacing Product listings....
	if(jQuery('.products,.product-listing',jQuery(data)).html()!==undefined){								
		jQuery(".products,.product-listing").html(jQuery('.products,.product-listing',jQuery(data)).html());
	}
	else{
		jQuery(".products,.product-listing").html('<p class="woocommerce-info" style="width: 100%;">No products were found matching your selection.</p>');
	}
	//Replacing Pagination details.....
	if(jQuery('.woocommerce-pagination,.pagination',jQuery(data)).html()!==undefined) {
		
		jQuery(".woocommerce-pagination,.pagination").html(jQuery('.woocommerce-pagination,.pagination',jQuery(data)).html());
	}
	else {
		jQuery(".woocommerce-pagination,.pagination").html('');	
	}

	//jQuery("body").fadeTo('fast','1')									
	jQuery("#loading").removeClass('loading');
	jQuery('.products,.product-listing,.woocommerce-pagination,.pagination').css('visibility','visible');
}

function woo_custome_filter_change(init_call=false) {				
	
	var form=jQuery("#woo_custome_filter_form");			
	jQuery.ajax({
		url:window.location.href,//form.attr('action'),
		data:form.serialize(),
		type:'GET',
		beforeSend:function(xhr){			
			jQuery("#loading").addClass('loading');							
		},
		complete : function(){
			console.log(this.url);
		},
		success:function(data){					
			//console.log(data);	
			woo_custome_filter_render_html(data);
		}
	});
}

jQuery(document).ready(function($){

	jQuery(".woocommerce-pagination,.pagination").html('');		

	//Trigger filter except checkbox.
	$("#woo_custome_filter").on('change',"input:not(:checkbox)",function(){
		$('[name="paged"]').val('1');
		woo_custome_filter_change();										
	});

	woo_custome_filter_change(true);

	//pagination for non-table based view
	$(".woocommerce-pagination,.pagination").on('click','a',function(event){
		
		event.preventDefault();
		event.stopPropagation();								
		
		if($(this).hasClass("next") || $(this).hasClass("prev")){
		
			if($(this).hasClass("next")){
				$("[name='paged']").val(parseInt($(".page-numbers.current").text())+1);
			}
			if($(this).hasClass("prev")){
				$("[name='paged']").val(parseInt($(".page-numbers.current").text())-1);
			}	
		}		
		else {
			$("[name='paged']").val($(this).text());
		}		
		woo_custome_filter_change();
	});
		
	//Enable Advance filter collapsable
	$( ".woo_custome_advance_filter").accordion({
	  collapsible: true,
	  active:false
	});	

	$(".woo_custome_advance_filter").click(function(){
		$(".woo_custome_advance_filter_display").slideToggle();
	});

	///////////////////////////////////////////////////////////
	//===================Filter Scrips Begins Here=============
	///////////////////////////////////////////////////////////
	
	$("[data-input='text_slider'][data-role='slider']").each(function(index,element){
		var slider=this;
		//Add Jquery-UI Slider
		$(slider).slider({
		  	range: true,
		    min: $(slider).data('min'),
		    max: $(slider).data('max'),
		    step: 0.1,
		    values: [$(slider).data('min'),$(slider).data('max')],
		    slide: function(event, ui) {					           				    		
			      $("input[name='min_"+$(slider).data('slug')+"']").val(ui.values[0]);
			      $("input[name='max_"+$(slider).data('slug')+"']").val(ui.values[1]);
		    },
		    stop:function(event, ui) {	    	
		    	var slider_min=$(slider).slider("option", "min");
		    	var slider_max=$(slider).slider("option", "max");
		    	var current_min=ui.values[0];
				var current_max=ui.values[1];
		    	if(slider_min==current_min && slider_max==current_max) {

		    		if($("[name='_attribute']").val().includes($(slider).data('slug'))) {
		    			$("[name='_attribute']").val($("[name='_attribute']").val().replace(','+$(slider).data('slug'),''));
		    			$("[name='_attribute']").val($("[name='_attribute']").val().replace($(slider).data('slug'),''));
		    		}
		    	}
		    	else {
		    		if(! $("[name='_attribute']").val().includes($(slider).data('slug'))) {
		    			$("[name='_attribute']").val($("[name='_attribute']").val()+','+$(slider).data('slug'))
		    		}
		    	}
		    	$('[name="paged"]').val('1');
		    	woo_custome_filter_change();
		    }
		});

		$("input[data-slug='"+$(slider).data('slug')+"']").change(function() {				    
			$(slider).slider("values", $(this).data("index"), $(this).val());
		});
	});


	$("[data-input='step_slider'][data-role='slider']").each(function(index,element){
		var slider=this;
		var items_name=$(slider).data('names').split(',');
		var items_slug=$(slider).data('slugs').split(',');
		
		$(slider).slider({
			range: true,
			min:1,						
			max: (items_name.length),
			values: [1,items_name.length],						
			slide: function(event, ui) {	
				
				$('[name="min_'+$(this).data('slug')+'"]').val(items_slug[ui.values[0]-1]);
				$('[name="max_'+$(this).data('slug')+'"]').val(items_slug[ui.values[1]-1]);
		    },
		    stop:function(event, ui) {

		    	var slider_min=$(slider).slider("option", "min");
		    	var slider_max=$(slider).slider("option", "max");
		    	var current_min=ui.values[0];
				var current_max=ui.values[1];
		    	if(slider_min==current_min && slider_max==current_max) {

		    		if( $("[name='_attribute']").val().includes($(slider).data('slug')) ) {
		    			$("[name='_attribute']").val($("[name='_attribute']").val().replace(','+$(slider).data('slug'),''));
		    			$("[name='_attribute']").val($("[name='_attribute']").val().replace($(slider).data('slug'),''));
		    		}
		    	}
		    	else {
		    		if(! $("[name='_attribute']").val().includes($(slider).data('slug'))) {
		    			$("[name='_attribute']").val($("[name='_attribute']").val()+','+$(slider).data('slug'));
		    		}
		    	}
		    	$('[name="paged"]').val('1');
		    	woo_custome_filter_change();
			}

		});

		var scale = 100 / (items_name.length - 1);
		$.each(items_name,function(key,value){
			var w = scale;
			if(key === 0 || key === items_name.length-1) w = scale/2;
			$('#legend_'+$(slider).data('slug')).append("<label title='"+value+"' style='width:"+w+"%'>"+value+"</label>");
		});
	});

	$("[data-input='dropdown'][data-role='dropdown']").each(function(index,element){
		var dropdown=this;
		//$(dropdown).selectmenu();
		$(dropdown).on('change',function() {

		 	$('[name="paged"]').val('1');	
			woo_custome_filter_change();
		});			
	});

	$("[data-input='checkbox'][data-role='checkbox']").each(function(index,element){
		var checkbox=this;
		jQuery(checkbox).on('click',function(){

			jQuery('[name="checklist_'+$(checkbox).data('slug')+'"]').val('');

			var list=Array();
			jQuery('.checklist_'+$(checkbox).data('slug')+':checked').each(function(i,e){				
				list.push(jQuery(e).val());
			});
			jQuery('[name="checklist_'+$(checkbox).data('slug')+'"]').val(list.join(","));


			var check_val=jQuery('[name="checklist_'+$(checkbox).data('slug')+'"]').val();

	    	if( ( jQuery('.checklist_'+$(checkbox).data('slug')+':checkbox').length==jQuery('.checklist_'+$(checkbox).data('slug')+':checkbox:checked').length)
	    	  || (jQuery('.checklist_'+$(checkbox).data('slug')+':checkbox:checked').length==0 ) ) {

	    		if($("[name='_attribute']").val().includes($(checkbox).data('slug'))) {
	    			$("[name='_attribute']").val($("[name='_attribute']").val().replace(','+$(checkbox).data('slug'),''));
	    			$("[name='_attribute']").val($("[name='_attribute']").val().replace($(checkbox).data('slug'),''));
	    		}
	    	}
	    	else {
	    		if(! $("[name='_attribute']").val().includes($(checkbox).data('slug'))) {
	    			$("[name='_attribute']").val($("[name='_attribute']").val()+','+$(checkbox).data('slug'));
	    		}
	    	}
			
			$('[name="paged"]').val('1');	
			woo_custome_filter_change();
		});				
	});

	$("[data-input='price_slider'][data-role='price_slider']").each(function(index,element){
		var price_slider=this;
		$(price_slider).slider({
			range: true,
		    min: $(price_slider).data('min'),
		    max: $(price_slider).data('max'),
			step: 1,
			values: [$(price_slider).data('min'), $(price_slider).data('max')],
		    slide: function(event, ui) {
		      
		     	$("#price_min_text").val("$"+ui.values[0]);
		        $("#price_max_text").val("$"+ui.values[1]);
		      
		      	$("#price_min").val(ui.values[0]);
		      	$("#price_max").val(ui.values[1]);
		    },
		    stop:function(event, ui) {
		    	$("[name='paged']").val("1");
		    	woo_custome_filter_change();
		    }
		});

		$("#price_min_text,#price_max_text").change(function() {		    
			$(price_slider).slider("values", $(this).data("index"), $(this).val().replace('$',''));
			$("[name='paged']").val("1");
		    woo_custome_filter_change();
		});		
	});

	$("[data-input='select_icons'][data-role='select_icons']").each(function(index,element){
		var icon=this;
		$(icon).click(function(){
			var filter_list=jQuery('[name="cat_filter_'+$(icon).data('slug')+'"]');
			if($(filter_list).val().includes( jQuery(this).data('icon-slug') ) ) {

				list=$(filter_list).val();
				list=( list.trim()?$(filter_list).val().split(","):Array());
				list.splice(list.indexOf(jQuery(this).data('icon-slug')),1);
				$(filter_list).val(list.join(","));				
			}
			else {
				list=$(filter_list).val();
				list=( list.trim()?$(filter_list).val().split(","):Array());
				list.push(jQuery(this).data("icon-slug"));				
				$(filter_list).val(list.join(","));				
			}					
			
			$(icon).toggleClass('woo_custome_filter_icon_select');
			$('[name="paged"]').val('1');
			woo_custome_filter_change();
		});		
	});		
});