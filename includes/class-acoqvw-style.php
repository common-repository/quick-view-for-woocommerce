<?php

/**
 * Load All Inline Style In front End
 *
 * @class   ACOQVW_Style
 */

if (!defined('ABSPATH')) {
    exit;
}


class ACOQVW_Style {

    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    public function __construct($file = ''){
        $this->token = ACOQVW_TOKEN;
    }

    /**
     * Get Custom Styles
     *
     * @param void
     * @return string custom inline styles
     * @since 1.0.0
     * @static
     */
    public function acoqvw_inline_custom_css(){
        $custom_css = '';
        $button_css = array();
       
        $general = get_option($this->token.'_general', false);
        $btnMBE = (isset($general['acoqvw_mobile_enable']) && ($general['acoqvw_mobile_enable']==true)) ?  'none' : '';
        
        $cont_style = array();
        if($btnMBE) {
            $cont_style[] = 'display: '.$btnMBE.' !important';
        }
        $custom_css .= ' @media only screen and (max-width: 767px) { .acoqvw_trigger_outer {'.implode(";",$cont_style).'} } ';


        $trigger = get_option($this->token.'_trigger', false);
        $apply_style_to_trigger = false;
        if(isset($trigger['acoqvw_enable_custom_style'])){
            if($trigger['acoqvw_enable_custom_style']){
                $apply_style_to_trigger = true;
            }
        }

        $btnBR = ( isset($trigger['acoqvw_border_radius']) ) ? $trigger['acoqvw_border_radius'] : '';
        $btnPd = ( isset($trigger['acoqvw_buttonPadding']) ) ? $trigger['acoqvw_buttonPadding'] : '';
        $btnFs = ( isset($trigger['acoqvw_fontSize']) ) ? $trigger['acoqvw_fontSize'] : '';
        $btnFw = ( isset($trigger['acoqvw_buttonFontWeight']) ) ? $trigger['acoqvw_buttonFontWeight'] : '';
        $btnC = ( isset($trigger['acoqvw_buttonColor']) ) ? $trigger['acoqvw_buttonColor'] : '';
        $btnHC = ( isset($trigger['acoqvw_textHoverColor']) ) ? $trigger['acoqvw_textHoverColor'] : '';
        $btnBC = ( isset($trigger['acoqvw_borderColor']) ) ? $trigger['acoqvw_borderColor'] : '';
        $btnBHC = ( isset($trigger['acoqvw_borderHoverColor']) ) ? $trigger['acoqvw_borderHoverColor'] : '';
        $btnBG = ( isset($trigger['acoqvw_bgColor']) ) ? $trigger['acoqvw_bgColor'] : '';
        $btnBGH = ( isset($trigger['acoqvw_bgHoverColor']) ) ? $trigger['acoqvw_bgHoverColor'] : '';
        $btnStyle = ( isset($trigger['acoqvw_buttonStyle']) ) ? $trigger['acoqvw_buttonStyle'] : '';

        if( $btnBR ){
            $button_css[] = 'border-radius:'.$btnBR."px";
        }
        if( $btnPd ){
            $button_css[] = 'padding:'.$btnPd;
        }
        if( $btnFs ){
            $button_css[] = 'font-size:'.$btnFs."px";
            $button_css[] = 'line-height: 125%';
        }
        if( $btnFw ){
            $button_css[] = 'font-weight:'.$btnFw;
        }
        if( $btnC ){
            $button_css[] = 'color:'.$btnC;
        }
        if( $btnBC ){
            $button_css[] = 'border-color:'.$btnBC;
        }
        if( $btnBG ){
            $button_css[] = 'background-color:'.$btnBG;
        }
        $button_css[] = 'border-width:1px';
        $button_css[] = 'border-style: solid';

        $custom_css .= '.acoqvw_quickview_button_style {'.implode(";",$button_css).'}';

        $hover_button_css = array();

        if( $btnHC ){
            $hover_button_css[] = 'color:'.$btnHC;
        }
        if( $btnBHC ){
            $hover_button_css[] = 'border-color:'.$btnBHC;
        }
        if( $btnBGH ){
            $hover_button_css[] = 'background-color:'.$btnBGH;
        }

        $custom_css .= '.acoqvw_quickview_button_style:hover {'.implode(";",$hover_button_css).'}';

        //width of svg section
        $icon_width_css = array();

        if( $btnFs && $apply_style_to_trigger ){
            $width = (float)$btnFs / 16 * 1.7;
            $icon_width_css[] = 'width:'.$width."rem";
        } else {
            $icon_width_css[] = 'width: 1.5rem';
        }
        if($btnStyle == 'icon_label'){
            $icon_width_css[] = 'margin-right: 5px';
        } else if($btnStyle == 'label_icon') {
            $icon_width_css[] = 'margin-left: 5px';
        }

        $custom_css .= '.acoqvw_quickview_button span.acoqvw_trigger_icon {'.implode(";",$icon_width_css).'}';
        
        //SVG Color of button 

        $color = ($apply_style_to_trigger) ? $btnC : '#000000';
        $hover_color = ($apply_style_to_trigger) ? $btnHC : '#000000';

        $custom_css .= '.acoqvw_quickview_button span.acoqvw_trigger_icon svg { fill :'.$color.'; }';
        $custom_css .= '.acoqvw_quickview_button:hover span.acoqvw_trigger_icon svg { fill :'.$hover_color.'; }';

        /**
         * Quick View Settings
         */

        $quickview = get_option($this->token.'_quickview', false);
        
        $qvGT = ( isset($quickview['acoqvw_quickviewGalleryType']) ) ? $quickview['acoqvw_quickviewGalleryType'] : '';
        $qvT = ( isset($quickview['acoqvw_quickviewType']) ) ? $quickview['acoqvw_quickviewType'] : '';
        $qvMW = ( isset($quickview['acoqvw_modalWidth']) ) ? $quickview['acoqvw_modalWidth'] : '';
        $qvIW = ( isset($quickview['acoqvw_quickviewImageWidth']) ) ? $quickview['acoqvw_quickviewImageWidth'] : '';
        $qvIS = ( isset($quickview['acoqvw_quickviewImageSize']) ) ? $quickview['acoqvw_quickviewImageSize'] : '';
        $qvIP = ( isset($quickview['acoqvw_quickviewImagePosition']) ) ? $quickview['acoqvw_quickviewImagePosition'] : '';
        $qvAS = ( isset($quickview['acoqvw_imageSliderArrowSize']) ) ? $quickview['acoqvw_imageSliderArrowSize'] : '';
        $qvAC = ( isset($quickview['acoqvw_imageSliderArrowColor']) ) ? $quickview['acoqvw_imageSliderArrowColor'] : '';
        $qvEBCS = ( isset($quickview['acoqvw_enableButtonCustomStyle']) ) ? $quickview['acoqvw_enableButtonCustomStyle'] : '';
        $qvDBC = ( isset($quickview['acoqvw_ViewDetailbuttonColor']) ) ? $quickview['acoqvw_ViewDetailbuttonColor'] : '';
        $qvDBHC = ( isset($quickview['acoqvw_ViewDetailTextHoverColor']) ) ? $quickview['acoqvw_ViewDetailTextHoverColor'] : '';
        $qvDBBC = ( isset($quickview['acoqvw_ViewDetailBorderColor']) ) ? $quickview['acoqvw_ViewDetailBorderColor'] : '';
        $qvDBBHC = ( isset($quickview['acoqvw_ViewDetailBorderHoverColor']) ) ? $quickview['acoqvw_ViewDetailBorderHoverColor'] : '';
        $qvDBBGC = ( isset($quickview['acoqvw_ViewDetailBgColor']) ) ? $quickview['acoqvw_ViewDetailBgColor'] : '';
        $qvDBBGHC = ( isset($quickview['acoqvw_ViewDetailBgHoverColor']) ) ? $quickview['acoqvw_ViewDetailBgHoverColor'] : '';
        $qvACBC = ( isset($quickview['acoqvw_AddtoCartbuttonColor']) ) ? $quickview['acoqvw_AddtoCartbuttonColor'] : '';
        $qvACBHC = ( isset($quickview['acoqvw_AddtoCartTextHoverColor']) ) ? $quickview['acoqvw_AddtoCartTextHoverColor'] : '';
        $qvACBBC = ( isset($quickview['acoqvw_AddtoCartBorderColor']) ) ? $quickview['acoqvw_AddtoCartBorderColor'] : '';
        $qvACBBHC = ( isset($quickview['acoqvw_AddtoCartBorderHoverColor']) ) ? $quickview['acoqvw_AddtoCartBorderHoverColor'] : '';
        $qvACBBGC = ( isset($quickview['acoqvw_AddtoCartBgColor']) ) ? $quickview['acoqvw_AddtoCartBgColor'] : '';
        $qvACBBGHC = ( isset($quickview['acoqvw_AddtoCartBgHoverColor']) ) ? $quickview['acoqvw_AddtoCartBgHoverColor'] : '';
        $qvDBBR = ( isset($quickview['acoqvw_ViewDetailButtonBorderRadius']) ) ? $quickview['acoqvw_ViewDetailButtonBorderRadius'] : '';
        $qvDBP = ( isset($quickview['acoqvw_ViewDetailButtonPadding']) ) ? $quickview['acoqvw_ViewDetailButtonPadding'] : '';
        $qvDBFS = ( isset($quickview['acoqvw_ViewDetailFontSize']) ) ? $quickview['acoqvw_ViewDetailFontSize'] : '';
        $qvDBFW = ( isset($quickview['acoqvw_ViewDetailButtonFontWeight']) ) ? $quickview['acoqvw_ViewDetailButtonFontWeight'] : '';
        $qvCBGC = ( isset($quickview['acoqvw_Container_Background_Color']) ) ? $quickview['acoqvw_Container_Background_Color'] : '';
        $qvCFGC = ( isset($quickview['acoqvw_ContainerForegroundColor']) ) ? $quickview['acoqvw_ContainerForegroundColor'] : '';
        $qvCP = ( isset($quickview['acoqvw_ContainerPadding']) ) ? $quickview['acoqvw_ContainerPadding'] : '';
        $qvCBR = ( isset($quickview['acoqvw_ContainerBorderRadius']) ) ? $quickview['acoqvw_ContainerBorderRadius'] : '';
        $qvCAIn = 'zoomIn';
        $qvCAOut = 'zoomOut';
        
        $qvOCIn = 'fadeIn';
        $qvOCOut = 'fadeOut';

        /**
         * Quick View Styles
         */
        //Preloader Style And preloader select
        $media = new ACOQVW_Media();
        $preloader_css= array();
        $PreL = $media->get_preloader('loader1');

        if( $PreL ){
            $preloader_css[] = "background-image: url('data:image/svg+xml;utf8,".$PreL."')";
        }

        $custom_css .= '.acoqvw_quickview_container .acoqvw_preloader{'.implode(";",$preloader_css).'}';

        //Container Styling
        $cont_style = array();
        if($qvCFGC) {
            $cont_style[] = 'background-color: '.$qvCFGC;
        }
        if($qvOCIn){
            $cont_style[] = '-webkit-animation: '.$qvOCIn.' 1s ease 0s 1 forwards';
            $cont_style[] = 'animation: '.$qvOCIn.' 1s ease 0s 1 forwards';
        }
        $custom_css .= '.acoqvw_quickview_container {'.implode(";",$cont_style).'}';

        $cont_style = array();
        if($qvOCOut){
            $cont_style[] = '-webkit-animation: '.$qvOCOut.' 0.8s ease 0s 1 forwards';
            $cont_style[] = 'animation: '.$qvOCOut.' 0.8s ease 0s 1 forwards';
        }
        $custom_css .= '.acoqvw_quickview_container.hide {'.implode(";",$cont_style).'}';

        //Inner Container Styling
        $cont_style = array();
        if($qvCBGC) {
            $cont_style[] = 'background-color: '.$qvCBGC;
        }
        if($qvCBR){
            $cont_style[] = 'border-radius: '.$qvCBR.'px';
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner, .acoqvw_quickview_container .acoqvw_quickview {'.implode(";",$cont_style).'}';
        
        $cont_style = array();
        if($qvCBGC) {
            $cont_style[] = 'background-color: '.$qvCBGC;
        }
        if($qvCBR){
            $cont_style[] = 'border-radius: '.$qvCBR.'px 0px 0px '.$qvCBR.'px';
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_gallery_inner {'.implode(";",$cont_style).'}';


        //If Modal
        $cont_style = array();
        if($qvMW){
            $cont_style[] = 'max-width: '.$qvMW.'px';
        }
        if($qvCAIn){
            $cont_style[] = '-webkit-animation: '.$qvCAIn.' 0.8s ease 0s 1 forwards';
            $cont_style[] = 'animation: '.$qvCAIn.' 0.8s ease 0s 1 forwards';
        }
        $custom_css .= '#acoqvw_quickview_modal_window .acoqvw_quickview {'.implode(";",$cont_style).'}';

        $cont_style = array();
        if($qvCAOut){
            $cont_style[] = '-webkit-animation: '.$qvCAOut.' 0.8s ease 0s 1 forwards';
            $cont_style[] = 'animation: '.$qvCAOut.' 0.8s ease 0s 1 forwards';
        }
        $custom_css .= '#acoqvw_quickview_modal_window .acoqvw_quickview.hide {'.implode(";",$cont_style).'}';

        //If Cascade
        $cont_style = array();
        $cont_style[] = 'display : none';
        $custom_css .= '#acoqvw_quickview_cascade_window .acoqvw_quickview {'.implode(";",$cont_style).'}';

        $cont_style = array();
        $cont_style[] = "margin-top:  calc(32px + 20px);";
        $custom_css .= '#acoqvw_quickview_cascade_window {'.implode(";",$cont_style).'}';

        //Close Button
        $close = $media->get_close_button('close1');
        $cont_style = array();
        if($qvCAOut){
            $cont_style[] = "background-image: url('data:image/svg+xml;utf8,".$close."')";
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_close {'.implode(";",$cont_style).'}';
    
        //Quick View Image Section Styling
        $cont_style = array();
        if($qvGT=='do-not-show'){
            $cont_style[] = 'display: none';
        }
        if($qvIW){
            $cont_style[] = 'width: '.$qvIW.'%';
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec {'.implode(";",$cont_style).'}';
        
        //Image styles
        $cont_style = array();
        if($qvIS){
            $cont_style[] = 'object-fit: '.$qvIS;
        }
        if($qvIP){
            $cont_style[] = 'object-position: '.$qvIP;
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_gallery_inner .acoqvw_gallery_slider .flex-viewport .acoqvw_sliders li img, .acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_thumbnail_container .acoqvw_thumbnail_image img {'.implode(";",$cont_style).'}';
        
        //Arrow styles
        $cont_style = array();
        if($qvAS){
            $cont_style[] = 'width: '.$qvAS.'px';
            $cont_style[] = 'height: '.$qvAS.'px';
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_gallery_inner .acoqvw_gallery_slider .flex-direction-nav li a,
        .acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_gallery_inner .acoqvw_gallery_slider .flex-direction-nav li a::before {'.implode(";",$cont_style).'}';
        
        
        $arrows = $media->get_arrows('arrow1');
        $cont_style = array();
        if($arrows['right']){
            $cont_style[] = "background-image: url('data:image/svg+xml;utf8,".$arrows['right']."')";
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_gallery_inner .acoqvw_gallery_slider .flex-direction-nav li.flex-nav-next a::before {'.implode(";",$cont_style).'}';
        $cont_style = array();
        if($arrows['left']){
            $cont_style[] = "background-image: url('data:image/svg+xml;utf8,".$arrows['left']."')";
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_gallery_inner .acoqvw_gallery_slider .flex-direction-nav li.flex-nav-prev a::before {'.implode(";",$cont_style).'}';

        //shift arrow outside the div
        $cont_style = array();
        if($qvAS){
            $cont_style[] = "margin-right: -".$qvAS."px";
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_gallery_inner .acoqvw_gallery_slider .flex-direction-nav li.flex-nav-next a {'.implode(";",$cont_style).'}';
        $cont_style = array();
        if($qvAS){
            $cont_style[] = "margin-left: -".$qvAS."px";
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec .acoqvw_gallery_inner .acoqvw_gallery_slider .flex-direction-nav li.flex-nav-prev a {'.implode(";",$cont_style).'}';

        //Quick View Content Section Styling
        $cont_style = array();
        if($qvGT=='do-not-show'){
            $cont_style[] = 'width: 100%';
        } else if($qvIW){
            $width = 100 - floatval($qvIW);
            $cont_style[] = 'width: '.$width.'%';
        }
        if($qvCP){
            $cont_style[] = 'padding: '.$qvCP;
        }
        $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_contentSec {'.implode(";",$cont_style).'}';

        //Quick View Content Section Button Styling
        if($qvEBCS){
            $cont_style = array();
            if($qvDBC){
                $cont_style[] = 'color: '.$qvDBC;
            }
            if($qvDBBC){
                $cont_style[] = 'border: 1px solid '.$qvDBBC;
            }
            if($qvDBBGC){
                $cont_style[] = 'background-color: '.$qvDBBGC;
            }
            if($qvDBBR){
                $cont_style[] = 'border-radius: '.$qvDBBR.'px';
            }
            if($qvDBP){
                $cont_style[] = 'padding: '.$qvDBP;
            }
            if($qvDBFS){
                $cont_style[] = 'font-size: '.$qvDBFS.'px';
            }
            if($qvDBFW){
                $cont_style[] = 'font-weight: '.$qvDBFW;
            }

            $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_contentSec .cart .acoqvw_view_details_button{'.implode(";",$cont_style).'}';

            $cont_style = array();
            if($qvDBHC){
                $cont_style[] = 'color: '.$qvDBHC;
            }
            if($qvDBBHC){
                $cont_style[] = 'border: 1px solid '.$qvDBBHC;
            }
            if($qvDBBGHC){
                $cont_style[] = 'background-color: '.$qvDBBGHC;
            }

            $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_contentSec .cart .acoqvw_view_details_button:hover{'.implode(";",$cont_style).'}';

            $cont_style = array();
            if($qvACBC){
                $cont_style[] = 'color: '.$qvACBC;
            }
            if($qvACBBC){
                $cont_style[] = 'border: 1px solid '.$qvACBBC;
            }
            if($qvACBBGC){
                $cont_style[] = 'background-color: '.$qvACBBGC;
            }
            if($qvDBBR){
                $cont_style[] = 'border-radius: '.$qvDBBR.'px';
            }
            if($qvDBP){
                $cont_style[] = 'padding: '.$qvDBP;
            }
            if($qvDBFS){
                $cont_style[] = 'font-size: '.$qvDBFS.'px';
            }
            if($qvDBFW){
                $cont_style[] = 'font-weight: '.$qvDBFW;
            }

            $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_contentSec .cart .single_add_to_cart_button{'.implode(";",$cont_style).'}';

            $cont_style = array();
            if($qvACBHC){
                $cont_style[] = 'color: '.$qvACBHC;
            }
            if($qvACBBHC){
                $cont_style[] = 'border: 1px solid '.$qvACBBHC;
            }
            if($qvACBBGHC){
                $cont_style[] = 'background-color: '.$qvACBBGHC;
            }

            $custom_css .= '.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_contentSec .cart .single_add_to_cart_button:hover{'.implode(";",$cont_style).'}';
            
        }

        return $custom_css;
    }
}