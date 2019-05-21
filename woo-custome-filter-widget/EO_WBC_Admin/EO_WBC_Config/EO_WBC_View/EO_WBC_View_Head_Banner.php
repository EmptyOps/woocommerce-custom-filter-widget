<?php 
class EO_WBC_Head_Banner{
    public static function get_head_banner()
    {
        ?>
       	<div>
        	<span style="display: inline-block;">
        		<img style="max-height: 100px;max-width:100px;position:relative;top:1em;" alt="Your Choice" src="<?php echo plugin_dir_url(dirname(__FILE__)).'EO_WBC_View/EO_WBC_Img/EO_WBC_Cart.png'; ?>">
        	</span>&nbsp;&nbsp;
        	<div style="display: inline-block;">
        		<h1><?php _e(constant('EO_WBC_PLUGIN_NAME')."&nbsp;&nbsp;&nbsp;".constant('EO_WBC_PLUGIN_VERSION')) ?></h1>
        		<p class="info">
        			Thank you for installing Woocommerce Bundle Choice! Woocommerce Bundle Choice excites users to buy with joy.	
        		</p>
        	</div>	
        </div>
        <?php         
    }
}

