<?php		
	if(isset($_GET['EO_WBC_CODE']) && $_GET['EO_WBC_CODE']){                   
		$color=explode('/',base64_decode(sanitize_text_field($_GET['EO_WBC_CODE'])));
		
		if(strpos(str_replace(' ','',$color[0]),'rgb(0,0,0')===0)
		{
			wc()->session->set('EO_WBC_BG_COLOR','rgba(128,128,128,0.5)');	
			wc()->session->set('EO_WBC_FG_COLOR','rgba(0,0,0,1.0)');
		}
		else
		{
			wc()->session->set('EO_WBC_BG_COLOR',$color[0]);
			wc()->session->set('EO_WBC_FG_COLOR',$color[1]);
		}                
    }
	add_action("wp_head",function(){			
		echo "<style>#crumbs ul li:after,#crumbs ul li:before{content:'';border-top:40px solid transparent;border-bottom:40px solid transparent;position:absolute;top:0}#crumbs{text-align:center;width:100%}#crumbs ul{padding: 0;margin: auto;position: relative;list-style:none;display:inline-table;width:inherit;margin:0}#crumbs ul li{width:calc((100% - 40px) / 3);display:inline;float:left;height:0;background:#F3F5FA;text-align:left;padding:30px 20px 50px 50px;position:relative;margin:0 10px 0 0;font-size:20px;text-decoration:none;color:#8093A7}#crumbs ul li:after{border-left:40px solid #F3F5FA;right:-40px;z-index:1}#crumbs ul li:before{border-left:40px solid #fff;left:0}#crumbs ul li:first-child{padding-left: 35px;border-top-left-radius:10px;border-bottom-left-radius:10px}#crumbs ul li:first-child:before,#crumbs ul li:last-child:after{display:none}#crumbs ul li:last-child{padding-right:40px;border-top-right-radius:10px;border-bottom-right-radius:10px}#crumbs ul li.active{background:".wc()->session->get('EO_WBC_BG_COLOR','#357DFD').";color:".wc()->session->get('EO_WBC_FG_COLOR','#fff')."}#crumbs ul li.active:after{border-left-color:".wc()->session->get('EO_WBC_BG_COLOR','#357DFD').";color:".wc()->session->get('EO_WBC_FG_COLOR','#fff')."}#crumbs ul li span.step{font-weight:bolder;font-size:1.3em;}#crumbs ul li span.step-name{white-space: nowrap;overflow: hidden;text-overflow:ellipsis;font-weight:400;font-size:1.2em;position:static}#crumbs ul li img{height:1.2em;width:1.2em;display:inline;border-radius:2px;position:static}#crumbs ul li div{vertical-align:middle !important;align-self:center;text-align:center;margin:auto;transform:translate(0,-20%)}@media only screen and (max-width: 600px) {.eo_wbc_change{ bottom:0px !important;} #crumbs ul li span.step-name{display:none;}#crumbs ul li span.step-name:before{content:'\n'}#crumbs ul li span.step{font-size:0.9em !important;vertical-align:-webkit-baseline-middle;} }</style><style>@media only screen and (max-width: 600px) {#crumbs ul li span.step{font-size:0.9em !important;}#crumbs ul li div{display: grid;}}#crumbs ul li span.step-name:before{content:' ';}#crumbs ul li a{color:inherit !important;}</style>";
	},100);		
?>
