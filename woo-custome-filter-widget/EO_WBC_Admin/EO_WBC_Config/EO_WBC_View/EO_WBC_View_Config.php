<?php    
//Footer Rating bar :)
add_filter( 'admin_footer_text',function($footer_text){
    return __("<p id='footer-left' class='alignleft'>
        If you like <strong>WooCommerce Bundle Choice</strong> please leave us a <a href='https://wordpress.org/support/plugin/woo-bundle-choice/reviews?rate=5#new-post' target='_blank' class='wc-rating-link' data-rated='Thanks :)'>★★★★★</a> rating. A huge thanks in advance! </p>");
});

function eo_wbc_admin_config_category_options()
{
    return  get_categories(array(
        'hierarchical' => 1,
        'show_option_none' => '',
        'hide_empty' => 0,
        'parent' => 0,
        'taxonomy' => 'product_cat'
    ));    
}

//the below code is intended to be used in future.
/*function eo_wbc_admin_page_lists()
{
    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    ); 
    return get_pages($args); 
}*/
        //deviding super-category details ie: name and slug to array container
        // - So that it can be attached to <select> menu item 
        $cat_name=array();
        $cat_slug=array(); 
        $categories=eo_wbc_admin_config_category_options();
        foreach ($categories as $cat)
        {
            $cat_name[]=$cat->name;
            $cat_slug[]=$cat->slug;
        }
?>
<style>
    .info{
        color:grey;
        font-style: italic;
    }
    fieldset{
        border: 1px solid black;
    }
    @media only screen and (max-width: 600px) {
        #wpwrap input,select,tr{
           display: grid !important;        
        }
        table,input,select{
            width: 100%;
        }
    }
    legend{
        font-size: larger;
        font-weight: bold;
        margin: 3px;
        background-color:rgb(255,255,255);
    }
    fieldset{
        padding: 10px;
        border-radius: 3px;
    }
    #form_ui table,input,select{
        width: -webkit-fill-available;
    }
    tr{
        vertical-align: initial;
    }    
</style>
<div class="wrap woocommerce">
<h1></h1>
<?php	EO_WBC_Head_Banner::get_head_banner(); ?>  
    <br/>
        <p><a href="https://wordpress.org/support/plugin/woo-bundle-choice" target="_blank">If you are facing any issue, please write to us immediately.</a></p>
	<br/>
    <h2>Configurations </h2>    
    <hr/>    
    <form action="" method="post">
    <?php wp_nonce_field('eo_wbc_nonce_config'); ?>
    <input type="hidden" name="eo_wbc_action" value="eo_wbc_save_config">    
    <table id="form_ui">
    	<tbody>
            <tr>
                <th colspan="2" style="text-align: left;"><h2><u>Choice Buttons Configuration</u></h2></th>
            </tr>
            <tr>               
                <td>
                    <fieldset>
                        <legend>Buttons Configuration</legend>
                        <span class="info">( Set custom position of choice buttons. )</span>
                        <hr/>
                        <table>
                            <tr>
                                <td><strong>Setting</strong></td>
                                <td>
                                    <div>
                                        <select name="eo_wbc_btn_setting" style="width: 100%;">
                                            <option value="0" selected="selected">Default</option>
                                            <option value="1">Shortcode</option>
                                        </select>
                                    </div>
                                    <span class="info">( Choose type of setup.<br/>&nbsp;Default : non-technical users.<br/>&nbsp;Shortcode : technical users only. )</span>
                                </td>                        
                            </tr>
                            <tr class="eo_wbc_btn_setting_position_toggle">
                                <td>
                                    <strong>Position</strong>
                                    <div class="eo_wbc_position_toggle" hidden="hidden">
                                        <br/>                                    
                                        <p>&nbsp;</p>                                  
                                    </div>                                
                                </td>                           
                                <td>
                                    <select name='eo_wbc_btn_position' style="width: 100%;">
                                        <option value="begining" selected="selected">Begining</option>
                                        <option value="middle">Middle</option>
                                        <option value="end">End</option>
                                        <option value="2">After 1st section</option>
                                        <option value="3">After 2nd section</option>
                                        <option value="4">After 3rd section</option>
                                        <option value="5">After 4th section</option>
                                        <option value="6">After 5th section</option>
                                        <option value="7">After 6th section</option>
                                        <option value="8">After 7th section</option>
                                        <option value="9">After 8th section</option>                                    
                                        <option value="10">After 9th section</option>
                                        <option value="hide">Hide on home page</option>
                                    </select>                                
                                    <span class="info">( Position of buttons on your home page. )</span>
                                </td>
                            </tr>                        
                        </table>
                    </fieldset>                    
                </td>                
            </tr>
            <tr>
                <th colspan="2" style="text-align: left;"><h2><u>Navigation & Product Category</u></h2></th>
            </tr>
    		<tr>    			
    			<td>
                    <fieldset>
                        <legend>First Category</legend>                        
                        <span class="info">( The first of any two product that would you like to present. )</span>                        
                        <hr/>
                        <table>
                        <tr>                    
                            <td>
                                <strong>Name</strong>                                                           
                            </td>
                            <td>
                                <select name='eo_wbc_first_name' onChange="nameChanged('first',this)" style="width: 100%;">
                                <?php 
                                    foreach ($cat_name as $name)
                                    {
                                        echo "<option name='".$name."'>".$name."</option>";
                                    }
                                  ?>
                                </select>
                                <span class="info">( Name to your first category. )</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Slug</strong>                               
                            </td>                           
                            <td>
                                <select name='eo_wbc_first_slug' onChange="slug2url('first',this)" style="width: 100%;">
                                    <?php 
                                        foreach ($cat_slug as $slug)
                                        {
                                            echo "<option name='".$slug."'>".$slug."</option>";
                                        }
                                    ?>
                                </select>
                                <span class="info">( Optional! slug is url friendly name. )</span>                          
                            </td>
                        </tr>
                        <tr>
                            <td><strong>URL</strong></td>
                            <td>
                                <div><input type="text" width="100%" size=30 placeholder="relative url of first product" 
                                        name="eo_wbc_first_url" value="<?php echo get_option("eo_wbc_first_url") ?>"></div>                                     
                                <span class="info">( Optional! Set SEO friendly url of your like. )</span>                          
                            </td>
                        </tr>                                           
                    </table>
                    </fieldset>    				
    				<br/><hr/><br/>
    			</td>
    		</tr>
    		<tr>
    			<td>
                    <fieldset>
                        <legend>Second Category</legend>
                        <span class="info">( The second of any two product that would you like to present. )</span>
                        <hr/>
                        <table>
                            <tr>                    
                                <td><strong>Name</strong></td>
                                <td>
                                    <select name='eo_wbc_second_name' onChange="nameChanged('second',this)" style="width: 100%;">
                                        <?php 
                                            foreach ($cat_name as $name)
                                            {
                                                echo "<option name='".$name."'>".$name."</option>";
                                            }
                                        ?>
                                    </select>
                                    <span class="info">( Name to your second category. )</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Slug</strong></td>
                                <td>
                                    <select name='eo_wbc_second_slug' onChange="slug2url('second',this)" style="width: 100%;">
                                        <?php 
                                            foreach ($cat_slug as $slug)
                                            {
                                                echo "<option name='".$slug."'>".$slug."</option>";
                                            }
                                        ?>
                                    </select> 
                                    <span class="info">( Optional! slug is url friendly name. )</span>                          
                                </td>
                            </tr>
                            <tr>
                                <td><strong>URL</strong></td>
                                <td>
                                    <div><input type="text" width="100%" size=30 placeholder="relative url of second product" 
                                        name="eo_wbc_second_url" value="<?php echo get_option("eo_wbc_second_url") ?>"></div>                               
                                    <p class="info">( Optional! Set SEO friendly url of your like )</p>                         
                                </td>
                            </tr>                                                   
                        </table>
                    </fieldset>
                    <br/><hr/><br/>
    			</td>
    		</tr>                     
    		<tr>    			
    			<td>
                    <fieldset>
                        <legend>Preview</legend>
                        <span class="info">( The final result of first category and second category. )</span>
                        <hr/>
                        <table>
                            <tr>                    
                                <td><strong>Name</strong></td>
                                <td>
                                    <input type="text" name="eo_wbc_collection_title" value="<?php
                                                
                                                    echo get_option('eo_wbc_collection_title')?get_option('eo_wbc_collection_title'):"Preview";
                                                                                                
                                        ?>" placeholder="Title of third step" style="width: 100%;" required="required">
                                    <span class="info">( Name to combination of two categories. )</span>
                                </td>
                            </tr>                                                   
                        </table>
                    </fieldset>    				
        			<br/><hr/><br/>
    			</td>
    		</tr>
            <tr>
                <th colspan="2" style="text-align: left;"><h2><u>Horizontal Filter Configurations</u></h2></th>
            </tr>  
            <tr>               
                <td>
                    <fieldset>
                        <legend>Filter Configuration</legend>
                        <span class="info">( Configure horizontal filter bar. )</span>
                        <hr/>
                        <table>
                            <tr>
                                <td><strong>Filter Status</strong></td>
                                <td>
                                    <div>
                                        &nbsp;<input type="checkbox" name="eo_wbc_filter_enable" <?php echo (get_option('eo_wbc_filter_enable')=='1')?'checked="checked"':''; ?> value='1'/>
                                        <span class="info">( check here to enable horizontal filter bar at category page. )</span>
                                    </div>                                    
                                    <br/>
                                </td>                        
                            </tr>
                        </table>
                    </fieldset>
                    <br/><hr/><br/>
                </td>
            </tr>	
			<tr>				
    			<td>    			
    				<button class="button button-primary button-hero action" style="float: right">Save</button>    			
    			</td>
                <td></td>
                <td></td>
    		</tr>		
    	</tbody>    	
    </table>
    </form>    
</div>
<script type="text/javascript">	
	<?php 
	   if(!is_null($categories))
	   {
	       $cat_map=array();
	       foreach ($categories as $cat)
	       {
	           $cat_map[$cat->name]=$cat->slug;
	       }
	       echo "var category = ".json_encode($cat_map).";";
	   }
	   // initializing values of all configuration controllers
	   // by collecting data from database 
	   echo "document.getElementsByName('eo_wbc_first_name')[0].value='".get_option('eo_wbc_first_name')."';";
	   echo "document.getElementsByName('eo_wbc_first_slug')[0].value='".get_option('eo_wbc_first_slug')."';";
	   echo "document.getElementsByName('eo_wbc_first_url')[0].value='".get_option('eo_wbc_first_url')."';";
	   echo "document.getElementsByName('eo_wbc_second_name')[0].value='".get_option('eo_wbc_second_name')."';";
	   echo "document.getElementsByName('eo_wbc_second_slug')[0].value='".get_option('eo_wbc_second_slug')."';";
	   echo "document.getElementsByName('eo_wbc_second_url')[0].value='".get_option('eo_wbc_second_url')."';";       
	   
	?>
    jQuery(document).ready(function($){
        
        //Load values before any action.
        $("[name='eo_wbc_btn_position']").val("<?php echo get_option('eo_wbc_btn_position')?get_option('eo_wbc_btn_position'):'begining'; ?>");        

        //function to toggle textbox which indicates position of buttons in container.
        function toggle_btn_position(){
            if($("[name='eo_wbc_btn_position']").val()=='custom'){
                $(".eo_wbc_position_toggle").fadeIn();
            }
            else{
                $(".eo_wbc_position_toggle").hide();
            }
        }

        //function to handle change in setting type of two buttons.
        $("[name='eo_wbc_btn_setting']").change(function(){            
            if($(this).val()==0){ //Default setting routine is selected
                $(".eo_wbc_btn_setting_position_toggle").show();
                $(".eo_wbc_btn_setting_page_toggle").hide();
                $(".eo_wbc_position_toggle").show();                
                toggle_btn_position();
            }
            else{ //Shortcode setting routine is selected
                $(".eo_wbc_btn_setting_page_toggle").show();
                $(".eo_wbc_btn_setting_position_toggle").hide();
            }            
        }).val("<?php echo get_option('eo_wbc_btn_setting')?get_option('eo_wbc_btn_setting'):"0"; ?>").trigger('change');

        //function to handle on change of page positon if value is "custom".
        $("[name='eo_wbc_btn_position']").change(function(){
            toggle_btn_position();
        });        
    });  

    //function to change ulr controller's text on change of slug dropbox.
    function slug2url(option,element)
    {
        if(element.value.trim().length>0){
            var target=document.getElementsByName("eo_wbc_"+option.trim()+"_url")[0];
            target.value="";
            target.placeholder='/product-category/'+element.value+'/';
        }       
    }

    //function to change slug dropbox on change occured on name dropbox
    function nameChanged(option,element)
    {               
        document.getElementsByName("eo_wbc_"+option.trim()+"_slug")[0].value=category[element.value];
        document.getElementsByName("eo_wbc_"+option.trim()+"_slug")[0].onchange();
    }  
</script>	