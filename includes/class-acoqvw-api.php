<?php

if (!defined('ABSPATH')) {
    exit;
}

class ACOQVW_Api
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
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    public function __construct()
    {
        $this->token = ACOQVW_TOKEN;

        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    $this->token . '/v1',
                    '/adminSettings/',
                    array(
                        'methods' => 'GET',
                        'callback' => array($this, 'adminSettings'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );

                register_rest_route(
                    $this->token . '/v1',
                    '/updateAdminSettings/',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'updateAdminSettings'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );

                register_rest_route(
                    $this->token . '/v1',
                    '/updateSettings/(?P<page>[a-zA-Z0-9-]+)',
                    array(
                        'methods' => 'POST',
                        'callback' => array($this, 'updateSettings'),
                        'permission_callback' => array($this, 'getPermission'),
                    )
                );


            }
        );
    }

    public function adminSettings(){
        $options = array(
            'general',
            'trigger',
            'quickview',
        );
        $response = array();
        if($options){
            foreach($options as $option){
                if($data = get_option( ACOQVW_TOKEN."_".$option, false )){
                    $response[$option."Settings"] = $data;
                }
            }
            return new WP_REST_Response($response, 200);

        } else {
            return new WP_REST_Response(__('Error Fetching Data','quick-view-for-woocommerce'), 503);
        }
    }


    public function updateAdminSettings($request){
        $settings = $request->get_params();
        $options = array(
            'general',
            'trigger',
            'quickview',
        );
        $response = array();
        if($options){
            foreach($options as $option){
                $option_key = 'acoqvw_'.$option;
                if($settings[$option]){
                    if($acoqvw_settings = get_option( $option_key, false )){
                        if($acoqvw_settings !== $settings[$option]){
                            update_option( $option_key, $settings[$option], true);
                        } 
                    } else {
                        add_option( $option_key, $settings[$option], '', 'yes' );
                    }
                }
            }
            return new WP_REST_Response(true, 200);
        } 
    }

    public function updateSettings($request){
        $settings = $request->get_params();
        
        if(!empty($settings['page']) && isset($settings['page'])){
            $option = 'acoqvw_'.$settings['page'];
            $data = $settings['data'];
            $action = $settings['action'];

            if($acoqvw_settings = get_option( $option, false )){
                if($acoqvw_settings !== $data){
                    if(update_option( $option, $data, true)){
                        if($action == 'reset') {
                            return new WP_REST_Response(__('Settings are successfully reset to defaults','quick-view-for-woocommerce'), 200);
                        } else {
                            return new WP_REST_Response(__('All Changes are successfully saved','quick-view-for-woocommerce'), 200);
                        }
                    } else {
                        return new WP_REST_Response(__('Error Updating Settings. Please check back after some time.','quick-view-for-woocommerce'), 503);
                    }
                } else {
                    if($action == 'reset') {
                        return new WP_REST_Response(__('Settings are successfully reset to defaults','quick-view-for-woocommerce'), 200);
                    } else {
                        return new WP_REST_Response(__('All Changes are successfully saved','quick-view-for-woocommerce'), 200);
                    }
                }
            } else {
                if(add_option( $option, $data, '', 'yes' )){
                    if($action == 'reset') {
                        return new WP_REST_Response(__('Settings are successfully reset to defaults','quick-view-for-woocommerce'), 200);
                    } else {
                        return new WP_REST_Response(__('All Changes are successfully saved','quick-view-for-woocommerce'), 200);
                    }
                } else {
                    return new WP_REST_Response(__('Error Updating Settings. Please check back after some time.','quick-view-for-woocommerce'), 503);
                }
            }
        } else {
            return new WP_REST_Response(__('Error Updating Settings. Please check back after some time.','quick-view-for-woocommerce'), 503);
        }
    }


    /**
     *
     * Ensures only one instance of APIFW is loaded or can be loaded.
     *
     * @param string $file Plugin root path.
     * @return Main APIFW instance
     * @see WordPress_Plugin_Template()
     * @since 1.0.0
     * @static
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Permission Callback
     **/
    public function getPermission()
    {
        if (current_user_can('administrator') || current_user_can('manage_woocommerce')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }
}
