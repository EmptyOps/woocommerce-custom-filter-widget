jQuery(document).ready(function(){	
	jQuery(".summary,.summary-product,.entry-summary").on('click','#eo_wbc_add_to_cart:not(.disabled)',function(){			
		console.log("LOG : "+eo_wbc_object.url);
		window.location.href =eo_wbc_object.url+'&CART='+window.btoa(form_2_json(jQuery('form.cart')));		    			    
	});	
});

function form_2_json(form) {
	console.log(form.length);
	var form_data=form.serializeArray();
	var json={};
	jQuery.each(form_data,function(){
		json[this.name]=this.value || '';		
	});
	//console.log(json);
	return JSON.stringify(json);
}