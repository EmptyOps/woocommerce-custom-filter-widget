
<style>
    .info{
        color:grey;
        font-style: italic;        
        word-break: break-word;
    }    
    .eo_wbc_action{
        box-shadow: 0 2px 5px rgba(0,0,0,0.25);
        padding: 30px;
        
    }    
    :root {
      --breadcrumb-theme-1: #00bb9c;
      --breadcrumb-theme-2: #fff;
      --breadcrumb-theme-3: #00bb9c;
      --breadcrumb-theme-4: #00af92;
      --breadcrumb-theme-5: #60c7b6f5;
    }
    .breadcrumb {
      text-align: center;
      display: inline-block;
      box-shadow: 0 2px 5px rgba(0,0,0,0.25);
      overflow: hidden;
      border-radius: 5px;
      counter-reset: flag;
      width: 100% !important;  
    }
    .breadcrumb__step {
      text-decoration: none;
      outline: none;
      display: block;
      float: left;
      font-size: 12px;
      line-height: 35px;
      font-size:x-large;
      min-width:calc(33.3333% - 48px);
      padding: 20px 0px 20px 30px;
      position: relative;
      background: var(--breadcrumb-theme-2);
      color: var(--breadcrumb-theme-1);
      transition: background 0.5s;
    }
    .breadcrumb__step:first-child {
      padding-left: 50px;
      border-radius: 5px 0 0 5px;
    }
    .breadcrumb__step:first-child::before {
      left: 14px;
    }
    .breadcrumb__step:last-child {
      border-radius: 0 5px 5px 0;
      padding-right: 32px;
    }
    .breadcrumb__step:last-child::after {
      content: none;
    }
    .breadcrumb__step::before {  
      content: counter(flag);
      counter-increment: flag;
      border-radius: 100%;
      width: 30px;
      height: 30px;
      line-height: 30px;
      margin: 20px 0;
      position: absolute;
      top: 0;
      left: 45px;
      font-weight: bold;
      background: var(--breadcrumb-theme-2);
      box-shadow: 0 0 0 1px var(--breadcrumb-theme-1);
      color: var(--breadcrumb-theme-1) !important;
    }
    .breadcrumb__step::after {
      content: '';
      position: absolute;
      top: 0;
      right: -36px;
      width: 75px;
      height: 75px;
      transform: scale(0.707) rotate(45deg);
      z-index: 1;
      border-radius: 0 5px 0 500px;
      background: var(--breadcrumb-theme-2);
      transition: background 0.5s;
      box-shadow: 2px -2px 0 2px var(--breadcrumb-theme-4);
    }
    .breadcrumb__step--active {
      color: var(--breadcrumb-theme-2) !important;
      background: var(--breadcrumb-theme-1);
      cursor: pointer !important;
    }
    .breadcrumb__step--active--2 {
      color: var(--breadcrumb-theme-2) !important;
      background: var(--breadcrumb-theme-5);
      cursor: pointer !important;
    }
    .breadcrumb__step--active::before {
      color: var(--breadcrumb-theme-1);
    }
    .breadcrumb__step--active--2::before {
      color: var(--breadcrumb-theme-5);
    }
    .breadcrumb__step--active::after {
      background: var(--breadcrumb-theme-1);
    }
    .breadcrumb__step--active--2::after {
      background: var(--breadcrumb-theme-5);
    }
    .breadcrumb__step:hover {
      /* color: var(--breadcrumb-theme-2);
      background: var(--breadcrumb-theme-3); */
    }
    .breadcrumb__step:hover::before {
      /* color: var(--breadcrumb-theme-1); */
    }
    .breadcrumb__step:hover::after {
    /*   color: var(--breadcrumb-theme-1);
      background: var(--breadcrumb-theme-3); */
    }
        
    .breadcrumb a
    {
        text-decoration: none;
        color: var(--breadcrumb-theme-1);  
        /* cursor: default; */
    }

    blockquote:before {
        font-family: Georgia, serif;        
        font-size: 2em;        
        content: "\201C";
    }
    blockquote:after {
        font-family: Georgia, serif;
        position: relative;
        bottom: -1em;
        font-size:2em;
        content: "\201D";
    }

    @media screen and (max-width: 550px) {
          .breadcrumb a{
            color: var(--breadcrumb-theme-2);
          }
          .breadcrumb__step:first-child {
            color: var(--breadcrumb-theme-1);        
          }
    } 
    @media only screen and (max-width: 782px) {
        .breadcrumb{
            display: grid;
            border-radius: 0px;
        }
        .breadcrumb__step::before{
          top: -10px;
          left: 10px !important;
        }
        .breadcrumb__step::after{
          content: '';
          display:none;
        }
        .breadcrumb__step:first-child::before{
            left: 46px;
        }
        .breadcrumb a{
          height: 16px;
          border-bottom: 1px solid black;
          overflow: hidden;
          padding-top: 5px;
          padding-bottom: 25px;
          margin-bottom: 2px;
          color: var(--breadcrumb-theme-1);
        }
    }      
</style>
<?php 
  //Footer Rating bar :)
  add_filter( 'admin_footer_text',function($footer_text){
    return __("<p id='footer-left' class='alignleft'>
        If you like <strong>WooCommerce Bundle Choice</strong> please leave us a <a href='https://wordpress.org/support/plugin/woo-bundle-choice/reviews?rate=5#new-post' target='_blank' class='wc-rating-link' data-rated='Thanks :)'>★★★★★</a> rating. A huge thanks in advance! </p>");
  });
?>
<div class="wrap woocommerce">
<h1></h1>
	<?php EO_WBC_Head_Banner::get_head_banner(); ?> 
  <br/>
        <p><a href="https://wordpress.org/support/plugin/woo-bundle-choice" target="_blank">If you are facing any issue, please write to us immediately.</a></p>
	<br/>
	<hr/>
	<div style="clear:both;"></div>
	    <span><h3>Getting started! Just do two steps below and you are all done!</h3></span>	    
	    <?php 
	       $config_status=get_option('eo_wbc_config_category');
	       $map_status=get_option('eo_wbc_config_map');
	       
	       $bar_request=($config_status==0)?0:$config_status+$map_status;
	       
	       if(isset($_GET['bar']) && $_GET['bar']!==NULL)
	       {
	           $bar_request=$_GET['bar'];
	       }	       
	    ?>
	    <div class="breadcrumb">
	    	<a class="breadcrumb__step <?php echo $bar_request==0 ? 'breadcrumb__step--active--2':'breadcrumb__step--active'; ?>" href="<?php echo admin_url( 'admin.php?page=eo-wbc-home&bar=0' ); ?>">
	    			<strong>Select Two Categories</strong>
			</a>	    	
	    	<a class="breadcrumb__step <?php echo ($bar_request==1) ? 'breadcrumb__step--active--2':($bar_request<1?'':($bar_request>=1?'breadcrumb__step--active':'')) ?>" href="<?php echo $map_status?admin_url('admin.php?page=eo-wbc-home&bar=1'):'#'; ?>">
	    			<strong>Map Categories</strong>
	    	</a>
	    	<a class="breadcrumb__step <?php echo ($bar_request==2) ?'breadcrumb__step--active--2':($bar_request<2?'':($bar_request>=2?'breadcrumb__step--active':'')); ?>" 
	    					href="<?php echo ($config_status && $map_status)?admin_url('admin.php?page=eo-wbc-home&bar=2'):'#'; ?>">
	    			<strong>Done</strong>
	    	</a>
	    </div>
	    <br/>
	    <br/>	    
	    <div class="eo_wbc_action" >	    	
	    	<?php if($bar_request==0): ?>
	    	<span class="info">
	    		<span style="font-size: large;">Configure Woocommerce Bundled Choice Plugin by selecting two main product category. </span>
	    		<br/><br/>
	    		<div style="text-align: center;">
	    			<a href="<?php echo admin_url( 'admin.php?page=eo-wbc-setting&callback=1'); ?>" class="button button-primary button-hero action">Select Two Categories</a>
	    		</div>
	    	</span>
	    	<?php elseif ($bar_request==1): ?>
	    	<span class="info">
	    		<span style="font-size: large;">Creating map is process of binding links between product sub-categories, which will eventually let user combine products based on these category mappings. </span>
	    		<br/><br/>
	    		<div style="text-align: center;">
	    			<a href="<?php echo admin_url( 'admin.php?page=eo-wbc-map&callback=1' ); ?>" class="button button-primary button-hero action">Map Categories.</a>
	    		</div>
	    	</span>
	    	<?php elseif ($bar_request>=2) :?>
	    	<span class="info">
	    		<span style="font-size: large;">You are all done, plugin is working.</span>
	    		<br/><br/>
	    		<div style="text-align: center;">
	    			<a href="<?php echo bloginfo('url').'?#wbc_' ?>" class="button button-primary button-hero action">Let's check it out</a>
            <br/><br/>
            <blockquote>It will redirect to buttons widget on home, if you have used shortcode than find it on particular page yourself.</blockquote>
	    		</div>
	    	</span>
	    	<?php endif;?>
	    </div>
</div>
