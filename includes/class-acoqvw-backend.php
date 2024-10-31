<?php

/**
 * Load Backend related actions
 *
 * @class   ACOQVW_Backend
 */

if (!defined('ABSPATH')) {
    exit;
}


class ACOQVW_Backend
{


    /**
     * Class intance for singleton  class
     *
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

    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * Suffix for Javascripts.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * The plugin assets URL.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;
    /**
     * The plugin hook suffix.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $hook_suffix = array();


    /**
     * Constructor function.
     *
     * @access  public
     * @param string $file plugin start file path.
     * @since   1.0.0
     */
    public function __construct($file = '')
    {
        $this->version = ACOQVW_VERSION;
        $this->token = ACOQVW_TOKEN;
        if($this->isWoocommerceActivated()){
            $this->file = $file;
            $this->dir = dirname($this->file);
            $this->assets_dir = trailingslashit($this->dir) . 'assets';
            $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));
            $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            $plugin = plugin_basename($this->file);

            // add action links to link to link list display on the plugins page.
            add_filter("plugin_action_links_$plugin", array($this, 'pluginActionLinks'));

            // reg activation hook.
            register_activation_hook($this->file, array($this, 'install'));
            // reg deactivation hook.
            register_deactivation_hook($this->file, array($this, 'deactivation'));

            // reg admin menu.
            add_action('admin_menu', array($this, 'registerRootPage'), 30);

            // Init functions, you can use it for posttype registration and all.


            // enqueue scripts & styles.
            add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'), 10, 1);
            add_action('admin_enqueue_scripts', array($this, 'adminEnqueueStyles'), 10, 1);

            // Plugin Deactivation Survey
            add_action('admin_footer', array($this, 'acoqvw_deactivation_form'));
        } else {
            $this->noticeNeedWoocommerce();
        }
    }

    /**
     * Ensures only one instance of Class is loaded or can be loaded.
     *
     * @param string $file plugin start file path.
     * @return Main Class instance
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
     * Show action links on the plugin screen.
     *
     * @param mixed $links Plugin Action links.
     *
     * @return array
     */
    public function pluginActionLinks($links)
    {
        $action_links = array(
            'settings' => '<a href="' . admin_url('admin.php?page=' . $this->token . '-admin-ui/') . '">' . esc_html__('Settings', 'quick-view-for-woocommerce') . '</a>',
        );

        return array_merge($action_links, $links);
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

    /**
     * Installation. Runs on activation.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function install()
    {
    }

    /**
     * WooCommerce not active notice.
     *
     * @access  public
     * @return void Fallack notice.
     */
    public function noticeNeedWoocommerce()
    {

        $error = sprintf(
        /* translators: %s: Plugin Name. */
            __(
                '%s requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to be installed & activated!',
                'quick-view-for-woocommerce'
            ),
            ACOQVW_PLUGIN_NAME
        );

        echo ('<div class="error"><p>' . $error . '</p></div>');
    }

    /**
     * Creating admin pages
     */
    public function registerRootPage()
    {
        $this->hook_suffix[] = add_menu_page( 
            __('Quick View', 'quick-view-for-woocommerce'),
            __('Quick View', 'quick-view-for-woocommerce'),
            'manage_woocommerce', 
            $this->token . '-admin-ui', 
            array($this, 'adminUi'), 
            esc_url($this->assets_url) . '/images/quickview-logo.svg', 25
        );
    }

    /**
     * Calling view function for admin page components
     */
    public function adminUi()
    {

        echo (
            '<div id="' . $this->token . '_ui_root">
  <div class="' . $this->token . '_loader"><p>' . __('Acowebs Woocommerce Quick View Plugin is loading Please wait for a while..', 'quick-view-for-woocommerce') . '</p></div>
</div>'
        );
    }


    /**
     * Load admin CSS.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function adminEnqueueStyles()
    {
        wp_enqueue_style( 'wpb-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700', false );
        wp_register_style($this->token . '-admin', esc_url($this->assets_url) . 'css/backend.css', array(), $this->version);
        wp_enqueue_style($this->token . '-admin');
    }

    /**
     * Load admin Javascript.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function adminEnqueueScripts()
    {
        if (!isset($this->hook_suffix) || empty($this->hook_suffix)) {
            return;
        }

        $screen = get_current_screen();

        if (in_array($screen->id, $this->hook_suffix, true)) {
            // Enqueue WordPress media scripts.
            if (!did_action('wp_enqueue_media')) {
                wp_enqueue_media();
            }

            if (!wp_script_is('wp-i18n', 'registered')) {
                wp_register_script('wp-i18n', esc_url($this->assets_url) . 'js/i18n.min.js', array(), $this->version, true);
            }
            // Enqueue custom backend script.
            wp_enqueue_script($this->token . '-backend', esc_url($this->assets_url) . 'js/backend.js', array('wp-i18n'), $this->version, true);
            // Localize a script.
            wp_localize_script(
                $this->token . '-backend',
                $this->token . '_object',
                array(
                    'api_nonce' => wp_create_nonce('wp_rest'),
                    'root' => rest_url($this->token . '/v1/'),
                    'assets_url' => $this->assets_url,
                )
            );
        }

        if($screen->id == 'plugins') {
            wp_enqueue_script($this->token . '-deactivation-message', esc_url($this->assets_url).'js/deactivate.js', array('jquery'), $this->version, true);
        }
    }

    /**
     * Deactivation hook
     */
    public function deactivation()
    {
    }


    
    /**
     * Deactivation form
     * @since 1.0.2
     * 
     */
    public function acoqvw_deactivation_form() {
        $currentScreen = get_current_screen();
        $screenID = $currentScreen->id;
        if ( $screenID == 'plugins' ) {
            $view = '<div id="acoqvw-aco-survey-form-wrap"><div id="acoqvw-aco-survey-form">
            <p>If you have a moment, please let us know why you are deactivating this plugin. All submissions are anonymous and we only use this feedback for improving our plugin.</p>
            <form method="POST">
                <input name="Plugin" type="hidden" placeholder="Plugin" value="'.ACOQVW_TOKEN.'" required>
                <input name="Date" type="hidden" placeholder="Date" value="'.date("m/d/Y").'" required>
                <input name="Website" type="hidden" placeholder="Website" value="'.get_site_url().'" required>
                <input name="Title" type="hidden" placeholder="Title" value="'.get_bloginfo( 'name' ).'" required>
                <input name="Version" type="hidden" placeholder="Version" value="'.ACOQVW_VERSION.'" required>
                <input type="radio" id="acoqvw-temporarily" name="Reason" value="I\'m only deactivating temporarily">
                <label for="acoqvw-temporarily">I\'m only deactivating temporarily</label><br>
                <input type="radio" id="acoqvw-notneeded" name="Reason" value="I no longer need the plugin">
                <label for="acoqvw-notneeded">I no longer need the plugin</label><br>
                <input type="radio" id="acoqvw-short" name="Reason" value="I only needed the plugin for a short period">
                <label for="acoqvw-short">I only needed the plugin for a short period</label><br>
                <input type="radio" id="acoqvw-better" name="Reason" value="I found a better plugin">
                <label for="acoqvw-better">I found a better plugin</label><br>
                <input type="radio" id="acoqvw-upgrade" name="Reason" value="Upgrading to PRO version">
                <label for="acoqvw-upgrade">Upgrading to PRO version</label><br>
                <input type="radio" id="acoqvw-requirement" name="Reason" value="Plugin doesn\'t meets my requirement">
                <label for="acoqvw-requirement">Plugin doesn\'t meets my requirement</label><br>
                <input type="radio" id="acoqvw-broke" name="Reason" value="Plugin broke my site">
                <label for="acoqvw-broke">Plugin broke my site</label><br>
                <input type="radio" id="acoqvw-stopped" name="Reason" value="Plugin suddenly stopped working">
                <label for="acoqvw-stopped">Plugin suddenly stopped working</label><br>
                <input type="radio" id="acoqvw-bug" name="Reason" value="I found a bug">
                <label for="acoqvw-bug">I found a bug</label><br>
                <input type="radio" id="acoqvw-other" name="Reason" value="Other">
                <label for="acoqvw-other">Other</label><br>
                <p id="acoqvw-aco-error"></p>
                <div class="acoqvw-aco-comments" style="display:none;">
                    <textarea type="text" name="Comments" placeholder="Please specify" rows="2"></textarea>
                    <p>For support queries <a href="https://support.acowebs.com/portal/en/newticket?departmentId=361181000000006907&layoutId=361181000000074011" target="_blank">Submit Ticket</a></p>
                </div>
                <button type="submit" class="aco_button" id="acoqvw-aco_deactivate">Submit & Deactivate</button>
                <a href="#" class="aco_button" id="acoqvw-aco_cancel">Cancel</a>
                <a href="#" class="aco_button" id="acoqvw-aco_skip">Skip & Deactivate</a>
            </form></div></div>';
            echo $view;
        } ?>
        <style>
            #acoqvw-aco-survey-form-wrap{ display: none;position: absolute;top: 0px;bottom: 0px;left: 0px;right: 0px;z-index: 10000;background: rgb(0 0 0 / 63%); } #acoqvw-aco-survey-form{ display:none;margin-top: 15px;position: fixed;text-align: left;width: 40%;max-width: 600px;min-width:350px;z-index: 100;top: 50%;left: 50%;transform: translate(-50%, -50%);background: rgba(255,255,255,1);padding: 35px;border-radius: 6px;border: 2px solid #fff;font-size: 14px;line-height: 24px;outline: none;}#acoqvw-aco-survey-form p{font-size: 14px;line-height: 24px;padding-bottom:20px;margin: 0;} #acoqvw-aco-survey-form .aco_button { margin: 25px 5px 10px 0px; height: 42px;border-radius: 6px;background-color: #1eb5ff;border: none;padding: 0 36px;color: #fff;outline: none;cursor: pointer;font-size: 15px;font-weight: 600;letter-spacing: 0.1px;color: #ffffff;margin-left: 0 !important;position: relative;display: inline-block;text-decoration: none;line-height: 42px;} #acoqvw-aco-survey-form .aco_button#acoqvw-aco_deactivate{background: #fff;border: solid 1px rgba(88,115,149,0.5);color: #a3b2c5;} #acoqvw-aco-survey-form .aco_button#acoqvw-aco_skip{background: #fff;border: none;color: #a3b2c5;padding: 0px 15px;float:right;}#acoqvw-aco-survey-form .acoqvw-aco-comments{position: relative;}#acoqvw-aco-survey-form .acoqvw-aco-comments p{ position: absolute; top: -24px; right: 0px; font-size: 14px; padding: 0px; margin: 0px;} #acoqvw-aco-survey-form .acoqvw-aco-comments p a{text-decoration:none;}#acoqvw-aco-survey-form .acoqvw-aco-comments textarea{background: #fff;border: solid 1px rgba(88,115,149,0.5);width: 100%;line-height: 30px;resize:none;margin: 10px 0 0 0;} #acoqvw-aco-survey-form p#acoqvw-aco-error{margin-top: 10px;padding: 0px;font-size: 13px;color: #ea6464;}
       </style>
    <?php }

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
