function bind_dependency(source,target,action){

	source=jQuery("[data-node-name='"+source+"']");
	target=jQuery("[data-node-name='"+target+"']");
	
	console.log(source.html());
	console.log(target.html());

	jQuery(target).find('select').prop('disabled',true);

	jQuery(source).on(action,"select",function(evt){

		if(jQuery(source).find('select').children(':eq(0)').val()==jQuery(source).find('select').val()){
			jQuery(target).find('select').prop('disabled',true);				
			jQuery(target).find('select').val(jQuery(target).find('select').children(':eq(0)').val());
			jQuery(target).find('select').trigger('click').trigger('slide').trigger('change');
			jQuery("#woo-custome-filter-redirect").attr('href','#');
		}
		else{			
			if(jQuery(source).find('select').attr('disabled') == undefined){				
				get_control(jQuery(source).find('select').val(),source,target);				
			}				
		}
	});			
}

function get_control(id,source,target){		        	
    jQuery.post(
                filter_ob.ajaxurl,
                {
                    'action': 'eo_custom_filter',
                    'slug': id,
                    'type': 0,
                    'title': jQuery(target).find('select').children(':eq(0)').html()
                },
        function (data, textStatus, XMLHttpRequest) {
        	if(data){        		
        		jQuery(target).html(data);
        		jQuery(target).find('select').removeAttr("disabled");
        		jQuery(target).find('select').trigger('click').trigger('slide').trigger('change');
        		jQuery("#woo-custome-filter-redirect").attr('href','#');        		
        	}        	
        }
    );
    return true;    
}

jQuery(document).ready(function($){
	$("#filter_container").on("change","select",function(){
		var list=Array();
		var change_stat=true;
		jQuery("#filter_container select").each(function(){
			if(jQuery(this).val().trim()){
			 	list[list.length]=(jQuery(this).val()); 			 	
			}
			else{
				change_stat=false;				
			}
		});

		var site_url='';
		/*if($("[name='_category']").val().trim().length){
			var _cat=Array();
			$.each($("[name='_category']").val().split(','),function(key,value){
				_cat.push($('[data-slug="'+value+'"]').val());
			});
			site_url+=filter_ob.cat_url+'/'+_cat.join('+')+'/?';
		}*/
		if($("[data-type='0']").length>0){
			var _cat=Array();
			jQuery.each(jQuery("[data-type='0']"),function(i,e){
				_cat.push(jQuery(e).val());
			});			
			site_url+=filter_ob.cat_url+_cat.join('/')+'/?';
		}
		else
		{
			site_url=filter_ob.shop_url+'/?'
		}
		if($("[name='_attribute']").val().trim().length){
			$.each($("[name='_attribute']").val().split(','),function(key,value){
				site_url+=value+"="+$('[data-slug="'+value+'"]').val()+"&";
			});		
		}		

		if(change_stat){
			jQuery("#woo-custome-filter-redirect").attr('href',site_url);
		}
	});
});