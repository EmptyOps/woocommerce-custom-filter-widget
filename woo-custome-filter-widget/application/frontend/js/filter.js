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
			console.log(data);	
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
});