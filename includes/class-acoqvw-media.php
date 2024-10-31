<?php

/**
 * Load All Media In front End
 *
 * @class   ACOQVW_Media
 */

if (!defined('ABSPATH')) {
    exit;
}


class ACOQVW_Media {

    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    /**
     * The Quick View Data.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $quickview;

    public function __construct($file = ''){
        $this->token = ACOQVW_TOKEN;
        $this->quickview = get_option($this->token.'_quickview', false);
    }

    /**
     * Get the Icons
     *
     * @param string $icon - Icon name.
     * @return string SVG Inline
     * @since 1.0.0
     * @static
     */
    public function get_icon($icon) {
        if(isset($icon) && !empty($icon)){
            switch($icon) {
                case 'eye' : 
                    return '<svg version="1.1" viewBox="0 0 168.1 168.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><circle cx="84.048" cy="84.044" r="20.699"/><path d="m167.06 81.422c-1.516-1.604-37.579-39.41-83.017-39.41-45.433 0-81.491 37.806-83.003 39.41-1.379 1.473-1.379 3.77 0 5.236 1.518 1.604 37.577 39.427 83.003 39.427 45.438 0 81.512-37.823 83.017-39.427 1.389-1.466 1.389-3.775 0-5.236zm-83.007 34.621c-17.639 0-31.989-14.353-31.989-32.006 0-17.639 14.35-32 31.989-32 17.638 0 32.008 14.356 32.008 32-4e-3 17.643-14.37 32.006-32.008 32.006zm-25.366-62.214c-8.645 7.275-14.269 18.044-14.269 30.208 0 12.159 5.624 22.929 14.269 30.217-23.92-7.693-42.704-23.88-49.418-30.217 6.715-6.33 25.503-22.519 49.418-30.208zm50.744 60.413c8.639-7.28 14.27-18.052 14.27-30.205s-5.626-22.917-14.277-30.197c23.919 7.693 42.689 23.86 49.407 30.197-6.719 6.331-25.489 22.501-49.4 30.205z"/></svg>';
                    break;
                default: 
                    return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get the Preloaders
     *
     * @param string $loader - Loader name.
     * @return string SVG Loader Inline
     * @since 1.0.0
     * @static
     */
    public function get_preloader($loader) {
        if(isset($loader) && !empty($loader)){
            switch($loader) {
                case 'loader1' : 
                    return '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="50" cy="50" r="32" fill="transparent" stroke="%23ffffff" stroke-dasharray="50.26548245743669 50.26548245743669" stroke-linecap="round" stroke-width="8"><animateTransform attributeName="transform" dur="1s" keyTimes="0;1" repeatCount="indefinite" type="rotate" values="0 50 50;360 50 50"/></circle></svg>';
                    break;
                default: 
                    return false;
            }
        } else {
            return false;
        }
    }

    
    /**
     * Get the Slider Arrows
     *
     * @param string $arrow - arrow name.
     * @return array SVG arrow Inline
     * @since 1.0.0
     * @static
     */
    public function get_arrows($arrow) {
        if(isset($arrow) && !empty($arrow)){
            $ArrowColor = ($this->quickview['acoqvw_imageSliderArrowColor']) ? str_replace('#','%23',$this->quickview['acoqvw_imageSliderArrowColor']) : '%23000000';
            switch($arrow) {
                case 'arrow1' :
                    $arrow = array();
                    $arrow['left'] = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 492.004 492.004" transform="rotate(180)" fill="'.$ArrowColor.'"><path d="M382.678 226.804L163.73 7.86C158.666 2.792 151.906 0 144.698 0s-13.968 2.792-19.032 7.86l-16.124 16.12c-10.492 10.504-10.492 27.576 0 38.064L293.398 245.9l-184.06 184.06c-5.064 5.068-7.86 11.824-7.86 19.028 0 7.212 2.796 13.968 7.86 19.04l16.124 16.116a26.72 26.72 0 0 0 19.032 7.86c7.208 0 13.968-2.792 19.032-7.86L382.678 265c5.076-5.084 7.864-11.872 7.848-19.088.016-7.244-2.772-14.028-7.848-19.108z"/></svg>';
                    $arrow['right'] = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 492.004 492.004" fill="'.$ArrowColor.'"><path d="M382.678 226.804L163.73 7.86C158.666 2.792 151.906 0 144.698 0s-13.968 2.792-19.032 7.86l-16.124 16.12c-10.492 10.504-10.492 27.576 0 38.064L293.398 245.9l-184.06 184.06c-5.064 5.068-7.86 11.824-7.86 19.028 0 7.212 2.796 13.968 7.86 19.04l16.124 16.116a26.72 26.72 0 0 0 19.032 7.86c7.208 0 13.968-2.792 19.032-7.86L382.678 265c5.076-5.084 7.864-11.872 7.848-19.088.016-7.244-2.772-14.028-7.848-19.108z"/></svg>';
                    return $arrow;
                    break;
                default: 
                    return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Get the Quick View Close Button
     *
     * @param string $close - close button name.
     * @return string SVG close Button Inline
     * @since 1.0.0
     * @static
     */
    public function get_close_button($close) {
        if(isset($close) && !empty($close)){
            switch($close) {
                case 'close1' :
                    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 365.696 365.696"><path d="M243.188 182.859L356.32 69.727c12.5-12.5 12.5-32.766 0-45.246L341.238 9.398c-12.504-12.504-32.77-12.504-45.25 0L182.859 122.527 69.727 9.375c-12.5-12.5-32.766-12.5-45.246 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.988c-12.504 12.504-12.504 32.77 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.246 0l113.133-113.133L295.988 356.32c12.504 12.5 32.77 12.5 45.25 0l15.082-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0" fill="%23151e67"/></svg>';
                    break;
                default: 
                    return false;
            }
        } else {
            return false;
        }
    }
    
    

}
