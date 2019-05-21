<?php
class EO_WBC_Home
{
    public function __construct()
    {
        $this->eo_wbc_the_content(); // Add two buttons on designated section.
        $this->eo_wbc_clean();   // Cleanup session data
    }
	
    public static function eo_wbc_do_shortcode()
    {
      $self=new self; //initalize self instence.
      $self->eo_wbc_clean(); //cleanup session data
      return $self->eo_wbc_buttons().$self->eo_wbc_code(); //return two buttons to shortcode.
    }

    private function eo_wbc_the_content()
    {
        /**
         * Adding Buttons to start with
         * 1. Start with First Product
         * 2. Start with Second Product       
         */ 
        add_action('wp_footer',function(){  //setup button position in specific position of page.

          if(!get_option('eo_wbc_btn_setting') && !(get_option('eo_wbc_btn_position')=='hide'))
         {
            $script="<script>jQuery(document).ready(function(){ ";
            if(get_option('eo_wbc_btn_position')=='begining'){
              $script.="jQuery('.entry-content').prepend('".$this->eo_wbc_buttons()."');";
            }
            elseif(get_option('eo_wbc_btn_position')=='end') {
              $script.="jQuery('.entry-content').append('".$this->eo_wbc_buttons()."');";
            }
            elseif(get_option('eo_wbc_btn_position')=='middle') {
              $script.="var eo_wbc_mid=(jQuery('.entry-content').children().length/2);".
                    "jQuery('.entry-content').children(':eq('+Math.floor(eo_wbc_mid)+')').before('".$this->eo_wbc_buttons()."');";
            }
            else{
              $script.="var eo_wbc_count=jQuery('.entry-content').children().length;".
                    "if(eo_wbc_count<=Number('".(get_option('eo_wbc_btn_position')-1)."')){".
                    " jQuery('.entry-content').append('".$this->eo_wbc_buttons()."');".
                    "}else{".
                    " jQuery('.entry-content').children(':eq('+Number('".(get_option('eo_wbc_btn_position')-1)."')+')')".
                    ".before('".$this->eo_wbc_buttons().
                    "'); }";

            }            
            $script.="});</script>";            
            echo $script.$this->eo_wbc_code();            
          }  
        });
    }

    private function eo_wbc_code() //script to get color code from buttons
    {
        return '<script>'.
                'jQuery(document).ready(function($){'.
                  '$(".eo_button_container .woocommerce>.button").each(function(i,e){'.
                    '$(e).attr("href",$(e).attr("href")+"&EO_WBC_CODE="+window.btoa($(".woocommerce>a.button").css("background-color")+"/"+$(".woocommerce>a.button").css("color")));'.
                  '});'.
                '});'.
               '</script>';
    }

    private function eo_wbc_buttons(){ //the two buttons UI

        return '<div id="wbc_"><p style="font-size: 1.6em;text-align:center;">Make your own pair from recommandation</p></div>'.
        '<style>'.
            '.eo_button_container{'.
                'text-align: justify;'.
                'text-transform: uppercase;'.
                'margin-bottom: 40px;'.
                'display: block;'.
                'text-align: center;'.
            '}'.
            '.eo_button_container::after{'.
                'content: "";'.
                'display: inline-block;'.
                'float: none!important;'.
            '}'.            
            '@media (max-width: 600px){'.
             '.eo_button_container{'.
                'display:grid;'.
              '}'.
            '}'.
        '</style>'.        
        '<div class="eo_button_container" style="">'.
             '<span class="woocommerce">'.
                '<a class="checkout-button button alt wc-forward" href="'.
                get_bloginfo('url').
                get_option('eo_wbc_first_url').
                '?EO_WBC=1&BEGIN='.
                get_option('eo_wbc_first_slug').
                '&STEP=1" style="padding:12px 27px;font-size: 15px;color: #232323;">'.
                    'start with '.get_option('eo_wbc_first_name').
                '</a>'.
             '</span>'.
             '<span style="font-size: 15px;padding-left:10%;padding-right:10%;">OR</span>'.
             '<span class="woocommerce">'.
                 '<a class="checkout-button button alt wc-forward" href="'.
                 get_bloginfo('url').
                 get_option('eo_wbc_second_url').
                 '?EO_WBC=1&BEGIN='.
                 get_option('eo_wbc_second_slug').
                 '&STEP=1" style="padding:12px 21px;font-size: 15px;color: #232323;">'.
                    'start with '.get_option('eo_wbc_second_name').
                '</a>'.
             '</span>'.
        '</div>'.
        '<div style="white-space:nowrap;font-size:16px;"></div>';        
    }

    private function eo_wbc_clean()
    {        
      wc()->session->set('EO_WBC_SETS',NULL);             
    }
}