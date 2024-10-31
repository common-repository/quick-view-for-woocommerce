<?php

if (!defined('ABSPATH')) {
    exit;
}

class ACOQVW_Public
{

    /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $instance = null;

    /**
     * The version number.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $version;

    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    /**
     * The plugin assets URL.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Constructor function.
     *
     * @access  public
     * @param string $file Plugin root file path.
     * @since   1.0.0
     */
    public function __construct($file = '')
    {
        $this->version = ACOQVW_VERSION;
        $this->token = ACOQVW_TOKEN;
        /**
         * Check if WooCommerce is active
         * */
        if ($this->isWoocommerceActivated()) {
            $this->file = $file;
            $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

            if($this->isPluginEnabledInternally()){
                // Load frontend CSS.
                add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_styles'), 99);
                // Load frontend JS.
                add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'), 99);

                add_action('init', array($this, 'init'));
                
                //Register Triggers
                add_action( 'init', array($this, 'acoqvw_register_shortcodes') );
                add_action( 'init', array($this, 'acoqvw_register_buttons') );

                // Load action for product template.
			    $this->acoqvw_quickview_register_template_actions();

                // Ajax Calls
                add_action( 'wp_ajax_acoqvw_get_quickview', array($this, 'show_quickview_modal' ));  
                add_action( 'wp_ajax_nopriv_acoqvw_get_quickview', array($this, 'show_quickview_modal' ));
                add_filter( 'woocommerce_add_to_cart_form_action', array( $this, 'avoid_redirecting_to_single_page' ), 10, 1 );
            }
        }
    }

    /**
     * Register Actions for Quick View Template
     * @access public
     * @return void
     */

    public function acoqvw_quickview_register_template_actions() {
        //Sale Label
        add_action( 'acoqvw_quickview_sale_label', 'woocommerce_show_product_sale_flash', 10 );
        
        // Image.
        add_action( 'acoqvw_quickview_product_image',  array($this, 'quickview_show_product_slider_gallery'), 20 );

        // Summary.
        add_action( 'acoqvw_quickview_before_product_summary', 'woocommerce_template_single_title', 10 );
        add_action( 'acoqvw_quickview_before_product_summary', 'woocommerce_template_single_rating', 20 );
        add_action( 'acoqvw_quickview_before_product_summary', 'woocommerce_template_single_price', 30 );

        add_action( 'acoqvw_quickview_product_summary', 'woocommerce_template_single_excerpt', 10 );

        add_action( 'acoqvw_quickview_product_add_to_cart', 'woocommerce_template_single_add_to_cart', 10 );

        // Compatibility With Product Labels
        add_action( 'acoqvw_before_gallery_slider_outer', array( $this, 'compatibility_with_product_labels' ), 10 );
    }

    /**
     * Compatibility With Product Labels That displayed in product detail page
     * @since 1.1.3
     */
    public function compatibility_with_product_labels() {

        // Acowebs Product Labels
        if(class_exists('ACOPLW_Badge')) {
            // Detail Page Badge
            $badgeDetail = get_option('acoplw_detail_page_badge') ? get_option('acoplw_detail_page_badge') : 0; 
            
            if ($badgeDetail) { 
                $badge = new ACOPLW_Badge();
                $badge->acoplwBadgeDetail();
            }
        }
    }

    /**
     * Function for Quick View Gallery Template
     * @access public
     * @return string
     */

    function quickview_show_product_slider_gallery(){
        ob_start(); 
            include( plugin_dir_path(__FILE__) . 'templates/acoqvw-quick-view-gallery.php' );
        $response = ob_get_contents();
        ob_end_clean();
        echo $response;
    }

    /**
     * Function for Quick View Thumbnail Template
     * @access public
     * @return string
     */

    function quickview_show_thumbnail(){
        ob_start(); 
            include( plugin_dir_path(__FILE__) . 'templates/acoqvw-quick-view-thumbnail.php' );
        $response = ob_get_contents();
        ob_end_clean();
        echo $response;
    }
    

    

    /**
     * Ajax Call to get Quick View Popup
     *
     * @access  public
     * @return  void 
     */
   
    function show_quickview_modal() {
        $acoqvw_product_id = intval( sanitize_text_field( $_POST['id'] ) );
        
        if(isset($acoqvw_product_id) && !empty($acoqvw_product_id)){

            $quickview = get_option($this->token.'_quickview', false);
            // Set the main wp query for the product.
			wp( 'p=' . $acoqvw_product_id . '&post_type=product' );

            
            //Add Action for View Detail Button Only if Quick View Short code is Executed
            if(isset($quickview['acoqvw_enableDetailButton']) && $quickview['acoqvw_enableDetailButton'] === true){
                add_action( 'woocommerce_after_add_to_cart_button', array($this, 'show_view_detail_button'));
            }

            ob_start(); 
			    include( plugin_dir_path(__FILE__) . 'templates/acoqvw-quick-view-content.php' );
            $response = ob_get_contents();
            ob_end_clean();

            echo json_encode(array('result'=>$response));
        } else {
            wp_send_json_error( __('Something went wrong. Please try again later.', 'quick-view-for-woocommerce'), 400);
        }
        die();
    }

    /**
     * Register Short Codes
     *
     * @access  public
     * @return  void 
     */
    public function acoqvw_register_shortcodes(){
        add_shortcode('acoqvw_quickview', array($this, 'acoqvw_shortcode_quickview_function'));
    }

    
    /**
     * Short code function - [acoqvw_quickview]
     *
     * @access  public
     * @return  string 
     */
    function acoqvw_shortcode_quickview_function($attr, $content = "") {
        global $product;

        if(isset($product) && !empty($product)){
            $direct_code = false;
            $product_id = $product->get_id();

            $trigger = get_option($this->token.'_trigger', false);
            $quickview = get_option( $this->token.'_quickview', false );
            $general = get_option($this->token . '_general', false);

            $qvEnablePlugins = isset($general['acoqvw_enable_plugins']) ? $general['acoqvw_enable_plugins'] : false;
            
            //Enqueue scripts when called shortcode
            if (!$qvEnablePlugins) {
                wp_enqueue_script('flexslider');
                wp_enqueue_script('wc-add-to-cart-variation');
            }


            $button_style = $trigger['acoqvw_buttonStyle'];
            $media = new ACOQVW_Media();

            $button_label = '';

            if(isset($button_style) && !empty($button_style)){
                switch($button_style){
                    case 'icon': 
                        $button_label = "<span class='acoqvw_trigger_icon'>".$media->get_icon('eye')."</span>";
                        break;
                    case 'label':
                        $button_label = __($trigger['acoqvw_buttonLabel'], 'quick-view-for-woocommerce');
                        break;
                    case 'icon_label':
                        $button_label = "<span class='acoqvw_trigger_icon'>".$media->get_icon('eye')."</span>".__($trigger['acoqvw_buttonLabel'],'quick-view-for-woocommerce');
                        break;
                    case 'label_icon':
                        $button_label = __($trigger['acoqvw_buttonLabel'],'quick-view-for-woocommerce')."<span class='acoqvw_trigger_icon'>".$media->get_icon('eye')."</span>";
                        break;
                }
            }

            $custom_class="acoqvw_quickview_button ";
            if($trigger['acoqvw_enable_custom_style']){
                $custom_class.="acoqvw_quickview_button_style ";
            } else {
                $custom_class.="button  ";
            }

            if($direct_code){
                $custom_class.="acoqvw_quickview_modal ";
            } else {
                if(
                    isset($quickview['acoqvw_quickviewType']) && 
                    !empty($quickview['acoqvw_quickviewType']) &&
                    ($quickview['acoqvw_quickviewType'] == 'modal')
                ) {
                    $custom_class.="acoqvw_quickview_modal ";
                } else if(
                    isset($quickview['acoqvw_quickviewType']) && 
                    !empty($quickview['acoqvw_quickviewType']) &&
                    ($quickview['acoqvw_quickviewType'] == 'cascading')
                ) {
                    $custom_class.="acoqvw_quickview_cascading ";
                }
            }
            
            $content="";
            $content.=(!$direct_code) ? "<div class='acoqvw_trigger_outer'>" : "";
            $content.="<a href='".get_permalink( $product_id )."' class='".$custom_class."' data-product_id='".$product_id."'>".$button_label."</a>";
            $content.=(!$direct_code) ? "</div>" : "";
        }
        return apply_filters('acoqvw_trigger_html', $content, $custom_class, $product_id, $button_label);
    }
    

    /**
     * Register Triggers - Buttons
     *
     * @access  public
     * @return  void 
     */
    public function acoqvw_register_buttons(){
        $positions = array(
            'before_add_to_cart' => array('hook' => 'woocommerce_after_shop_loop_item', 'priority' => 7),
            'after_add_to_cart' => array('hook' => 'woocommerce_after_shop_loop_item', 'priority' => 15)
        );

        $trigger = get_option($this->token.'_trigger', false);
        $general = get_option($this->token.'_general', false);

        if(isset($trigger['acoqvw_buttonPosition']) && !empty($trigger['acoqvw_buttonPosition'])){
            $pos = $trigger['acoqvw_buttonPosition'];
            
            // Support for wishlist plugin
            if(isset($general['acoqvw_enable_in_wishlist']) && !empty($general['acoqvw_enable_in_wishlist'])){
                if($general['acoqvw_enable_in_wishlist']) {
                    if($pos === 'before_add_to_cart'){
                        add_action( 'awwlm_before_add_to_cart_button', array( $this, 'acoqvw_show_button' ), 7 );
                    } else {
                        add_action( 'awwlm_after_add_to_cart_button', array( $this, 'acoqvw_show_button' ), 15 );
                    }   
                }
            }
            add_action( $positions[ $pos ]['hook'], array( $this, 'acoqvw_show_button' ), $positions[ $pos ]['priority'] );
        }
    }

     /**
     * funvtion for View Detail page Button Display
     *
     * @access  public
     * @return  string 
     */
    public function show_view_detail_button(){
        global $product;
        $quickview = get_option( $this->token.'_quickview', false );
        $label = (isset($quickview['acoqvw_quickviewDetailButtonLabel'])) ? $quickview['acoqvw_quickviewDetailButtonLabel']: '';
        
        $button_output="<a href='".get_permalink( $product->get_id() )."' class='button acoqvw_view_details_button'>".__($label,'quick-view-for-woocommerce')."</a>";
        
        echo apply_filters('acoqvw_view_detail_button_html', $button_output, $label, $product);
    }


    /**
     * Execute Shortcode for Button Display
     *
     * @access  public
     * @return  string 
     */
    public function acoqvw_show_button(){
        echo do_shortcode( "[acoqvw_quickview]" );
    }


    /**
     * Print the global data in js
     *
     * @since  1.0.2
     * @access  public
     * @return  string 
     */
    function print_global_data(){
        $quickview = get_option($this->token.'_quickview', false);
        $wcpa_global_vars = array();

        // Slider Settings And Options to JS to Initialize Slider
        $slider_options = array();
        $slider_options['single_image'] = ((isset($quickview['acoqvw_quickviewGalleryType'])) && ($quickview['acoqvw_quickviewGalleryType']=='thumb')) ? true : false;
        $slider_options['enable_arrows'] = ((isset($quickview['acoqvw_enableImageSliderArrows'])) && !empty($quickview['acoqvw_enableImageSliderArrows'])) ? $quickview['acoqvw_enableImageSliderArrows'] : false;
        $wcpa_global_vars['slider'] = $slider_options;
        
        $wcpa_global_vars['ajax_url'] = admin_url('admin-ajax.php');
        $wcpa_global_vars['columns'] = wc_get_loop_prop( 'columns' );
        
        wp_localize_script($this->token . '-frontend', 'acoqvw_global_vars', $wcpa_global_vars);
    }


    /**
     * Check if is quick view
     *
     * @access public
     * @since  1.0.0
     * @return bool
     */
    public function acoqvw_is_quick_view() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && 'acoqvw_get_quickview' === $_REQUEST['action'] );
    }

    /**
     * Avoid redirecting to the single product page on add to cart action in quick view
     *
     * @since  1.0.0
     * @access public
     * @param string $value The redirect url value.
     * @return string
     */
    public function avoid_redirecting_to_single_page( $value ) {
        if ( $this->acoqvw_is_quick_view() ) {
            return '';
        }
        return $value;
    }

    /**
     * Check if plugin is enabled in plugin settings
     *
     * @access  private
     * @return  boolean plugin enable status
     */
    private function isPluginEnabledInternally(){
        $general = get_option($this->token.'_general', false);

        if(isset($general) && !empty($general)){
            if($general['acoqvw_enable']){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    
    /**
     * Check if woocommerce is activated
     *
     * @access  public
     * @return  boolean woocommerce install status
     */
    public function isWoocommerceActivated()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            return true;
        }
        if (is_multisite()) {
            $plugins = get_site_option('active_sitewide_plugins');
            if (isset($plugins['woocommerce/woocommerce.php'])) {
                return true;
            }
        }
        return false;
    }

    /** Handle Post Type registration all here
     */
    public function init(){

    }

    /**
     * Ensures only one instance of APIFW_Front_End is loaded or can be loaded.
     *
     * @param string $file Plugin root file path.
     * @return Main APIFW_Front_End instance
     * @since 1.0.0
     * @static
     */
    public static function instance($file = '')
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($file);
        }
        return self::$instance;
    }

    /**
     * Load Front End CSS.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function frontend_enqueue_styles()
    {
        wp_register_style($this->token . '-frontend', esc_url($this->assets_url) . 'css/frontend.css', array(), $this->version);
        wp_enqueue_style($this->token . '-frontend');
        $this->acoqvw_enqueue_custom_style();
    }

    /**
     * Load Front End JS.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function frontend_enqueue_scripts()
    {
        $general = get_option($this->token . '_general', false);
        $qvEnablePlugins = isset($general['acoqvw_enable_plugins']) ? $general['acoqvw_enable_plugins'] : false;
        if ($qvEnablePlugins) {
            $wc_dir = plugin_dir_url('woocommerce/woocommerce.php');
            wp_register_script('flexslider', $wc_dir . 'assets/js/flexslider/jquery.flexslider.min.js', array('jquery'), '$this->version', true);
            wp_register_script('wc-add-to-cart-variation', $wc_dir . 'assets/js/frontend/add-to-cart-variation.min.js', array('jquery'), $this->version, true);

            wp_enqueue_script('flexslider');
            wp_enqueue_script('wc-add-to-cart-variation');
        }
        
        /**
         * Compatibility
         */
        
        // WooCommerce Composite Products comaptibility
        wp_enqueue_script( 'wc-add-to-cart-composite' );
      
        wp_register_script( $this->token . '-frontend', esc_url($this->assets_url) . 'js/frontend.js', array(), $this->version, true );
        wp_enqueue_script( $this->token . '-frontend' );
        $this->print_global_data();
    }
    

    /**
     * Load Front End Custom Inline CSS.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    function acoqvw_enqueue_custom_style() {
        $style = new ACOQVW_Style();
        $custom_css = $style->acoqvw_inline_custom_css();
        if( $custom_css ) {
            $handle = $this->token . '-frontend';
            wp_add_inline_style( $handle, $custom_css );
        }
    }
}