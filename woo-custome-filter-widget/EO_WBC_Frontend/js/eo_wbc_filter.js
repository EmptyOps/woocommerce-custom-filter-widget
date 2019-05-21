//render products DOM to view
function eo_wbc_filter_render_html(data) {			

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
		
		var links=jQuery(".products a,.product-listing a");
		jQuery.each(links,function(index,element) {

			var href=jQuery(element).attr('href');
			if(href.indexOf('?')==-1) {
				jQuery(element).attr('href',jQuery(element).attr('href')+eo_wbc_object.eo_product_url);
			}
			else {

				jQuery(element).attr('href',href.substring(0,href.indexOf('?'))+eo_wbc_object.eo_product_url);
			}									
		});
	}
	else {
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

//render table from JSON object
function eo_wbc_filter_render_table(data) {
	data=JSON.parse(data);				
	var count=data['count'];
	var page=data['page'];
	
	data=data['items'];				
	jQuery(".eo_wbc_srch_btn:eq(1)").html((count?count:'0') + " matches found");
	if( (data==undefined || data.length==0 || data.length==undefined) && page==1)
	{
		jQuery(".products,.product-listing").html('<p class="woocommerce-info" style="width: 100%;">No products were found matching your selection.</p>');			
		jQuery(".woocommerce-pagination,.pagination").html('');			
	}
	else {
	
		//jQuery('.products,.product-listing').html(
		html='';
		if(page==1){
			html = html + '<table id="eo_wbc_filter_table" class="display" style="width:100%">'+
							'<thead>'+
								'<tr>'+
									'<th>Compare</th>'+
									'<th>Shape</th>'+
									'<th>Caret</th>'+
									'<th>Colour</th>'+
									'<th>Clarity</th>'+
									'<th>Cut</th>'+
									'<th>Lab</th>'+
									'<th>Price</th>'+
								'</tr>'+
							'</thead><tbody>';
		}
		jQuery.each(data,function(i, item){
			html = html + "<tr style='cursor:pointer;' data-id='"+item.ID+" data-title='"+item.post_title+"' data-slug='"+item.post_name+"' >" +
							"<th><input type='checkbox' class='compare' /></th>"+
							"<td>"+item.image+" "+item.shape+"</td>"+
							"<td>"+item.caret+"</td>"+
							"<td>"+item.color+"</td>"+
							"<td>"+item.clarity+"</td>"+
							"<td>"+item.cut+"</td>"+
							"<td>"+item.lab+"</td>"+
							"<td>"+item.price+"</td>"+
						"</tr>";
		    //alert(item.price);
		});
		if(page==1){
			html = html + '</tbody></table>';
			jQuery('.products,.product-listing').html(html);
			jQuery("#eo_wbc_filter_table").DataTable({"paging":false,"info":false,"bFilter":false,"searching":false});
		}
		else{
			jQuery('.products,.product-listing').find("table#eo_wbc_filter_table>tbody").append(html);	
		}
		
		var site_url=eo_wbc_object.eo_part_site_url;
		var url_end=eo_wbc_object.eo_part_end_url;
		jQuery("#eo_wbc_filter_table").on('click','tr > td',function(){
			//alert(jQuery(this).parent().attr('data-slug'));
			window.location.href=site_url+jQuery(this).parent().attr('data-slug')+url_end;
		});
	}
	jQuery("#loading").removeClass('loading');
	jQuery('.products,.product-listing').css('visibility','visible');
}

function eo_wbc_filter_change(init_call=false) {				
	//flag indicates if to show products in tabular view or woocommerce's default style.
	var view_tabular=eo_wbc_object.eo_view_tabular;	
	var form=jQuery("#eo_wbc_filter");

	if(view_tabular=='0') {	

		var site_url=eo_wbc_object.eo_cat_site_url;
		var ajax_url=site_url+eo_wbc_object.eo_cat_query;					
		jQuery.ajax({
			url: ajax_url,//form.attr('action'),
			data:form.serialize(), // form data
			type:form.attr('method'), // POST
			beforeSend:function(xhr){
				//jQuery("body").fadeTo('slow','0.3')	
				jQuery("#loading").addClass('loading');							
			},
			complete : function(){
				//console.log(this.url);
			},
			success:function(data){		
				//console.log(JSON.stringify(data));
				eo_wbc_filter_render_html(data);
			}
		});

	}
	else
	{
		form_data=undefined;
		if(init_call){
			if( jQuery("[name='_category_query']").val().trim()=='' ) {
				form_data={_current_category:jQuery("[name='_current_category']").val().trim(),action:'eo_wbc_filter'};
			}
			else
			{
				//form_data={_category:jQuery("[name='_category']").val().trim(),action:'eo_wbc_filter'};	
				form_data=jQuery("[name='_category'],[name^='cat_filter_'],[name='action']").serialize();

			}
		}
		else{
			form_data=form.serialize();
		}

		jQuery.ajax({
			url: eo_wbc_object.eo_admin_ajax_url,//form.attr('action'),
			data:form_data, // form data
			type:'POST', // POST
			beforeSend:function(xhr){
				jQuery("#loading").addClass('loading');	
				//console.log(form_data);
				//console.log(JSON.stringify(form_data).replace("\\",''));
			},
			complete : function(){
				//console.log(this.url);
			},
			success:function(data){		
				//console.log(JSON.stringify(data).replace('\\',''));
				//jQuery("#loading").removeClass('loading');
				eo_wbc_filter_render_table(data);							
			}
		});						
	}
	return false;
}

jQuery(document).ready(function($){
	
	jQuery(".woocommerce-pagination,.pagination").html('');		

	$("#eo_wbc_filter").on('change',"input:not(:checkbox)",function(){
		$('[name="paged"]').val('1');
		eo_wbc_filter_change();										
	});

	eo_wbc_filter_change(true);

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
		eo_wbc_filter_change();
	});

	//pagination for table based view
    $(window).scroll(function() {
      	var last_row=jQuery("#eo_wbc_filter_table>tbody>tr:last");
      	if($(last_row).offset()!=undefined){
      		if ($(window).scrollTop() + $(window).height() >= $(last_row).offset().top) {

	            if(!$(last_row).attr('loaded')) {      

	                $(last_row).attr('loaded', true);

	                $('[name="paged"]').val(parseInt($('[name="paged"]').val())+1);
	                eo_wbc_filter_change();
	            }
	        }	
      	}        
    });
	
	/////////////////////////
	////////////////////////

	$( ".eo_wbc_advance_filter" ).accordion({
	  collapsible: true,
	  active:false
	});

	//compare
	jQuery(".eo_wbc_srch_btn:eq(0)").click(function(){	
		if(jQuery("#eo_wbc_filter_table>tbody>tr>th>:checkbox:checked").length>=2){
			jQuery("#eo_wbc_filter_table>tbody>tr>th>:checkbox:not(:checked)").parentsUntil("tbody").css('display','none');
		}
		else{
			alert("Please select at least 2 items to compare.");
		}    				
	});
	//all available
	jQuery(".eo_wbc_srch_btn:eq(1)").click(function(){					
		jQuery("#eo_wbc_filter_table>tbody>tr>th>:checkbox:not(:checked)").parentsUntil("tbody").css('display','');
	});
	//Reset form and display
	jQuery(".eo_wbc_srch_btn:eq(2)").click(function(){					
		///////////////////////////////////////////
		document.forms.eo_wbc_filter.reset();
		jQuery(".eo_wbc_srch_btn:eq(2)").trigger('reset');
		jQuery("#eo_wbc_attr_query").val("");
		jQuery('[name="paged"]').val('1');
		eo_wbc_filter_change(true);

	});

	jQuery(".products,.product-listing").on("click",".compare:checkbox",function(){
		
		if(jQuery(this).attr('checked')==undefined) {
			jQuery(this).attr("checked",true);
		}
		else {
			jQuery(this).attr("checked",false);
		}			

	});

	jQuery(".products,.product-listing").on('click','#eo_wbc_filter_table>tbody>tr>th',function(){
		var child_checkbox=$(this).children(":checkbox");

		if(child_checkbox.attr('checked')==undefined) {
			$(this).children(":checkbox").attr("checked",true);
		}
		else {
			$(this).children(":checkbox").attr("checked",false);
		}
		
	});

});