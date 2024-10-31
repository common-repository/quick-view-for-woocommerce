<?php
/**
 * Plugin Name: Quick View For Woocommerce
 * Version: 1.2.4
 * Description: Quick View For Woocommerce enables customer to have a quick look of product easily without visiting product page.
 * Author: Acowebs
 * Author URI: http://acowebs.com
 * Requires at least: 4.4.0
 * Tested up to: 6.4
 * Requires PHP: 7.0 or higher
 * Text Domain: quick-view-for-woocommerce
 * WC requires at least: 3.0.0
 * WC tested up to: 7.4
 */
 
if (!defined('ABSPATH')) {
    exit;
}

define('ACOQVW_TOKEN', 'acoqvw');
define('ACOQVW_VERSION', '1.2.4');
define('ACOQVW_FILE', __FILE__);
define('ACOQVW_PLUGIN_NAME', 'Quick View For Woocommerce');

// Helpers.
require_once realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes/helpers.php';

// Init.
add_action('plugins_loaded', 'acoqvw_init');
if (!function_exists('acoqvw_init')) {
    /**
     * Load plugin text domain
     *
     * @return  void
     */
    function acoqvw_init()
    {
        $plugin_rel_path = basename(dirname(__FILE__)) . '/languages'; /* Relative to WP_PLUGIN_DIR */
        load_plugin_textdomain('quick-view-for-woocommerce', false, $plugin_rel_path);
    }
}

// Loading Classes.
if (!function_exists('ACOQVW_autoloader')) {

    function ACOQVW_autoloader($class_name)
    {
        if (0 === strpos($class_name, 'ACOQVW')) {
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
            require_once $classes_dir . $class_file;
        }
    }
}
spl_autoload_register('ACOQVW_autoloader');

// Backend UI.
if (!function_exists('ACOQVW_Backend')) {
    function ACOQVW_Backend()
    {
        return ACOQVW_Backend::instance(__FILE__);
    }
}
if (!function_exists('ACOQVW_Public')) {
    function ACOQVW_Public()
    {
        return ACOQVW_Public::instance(__FILE__);
    }
}
// Front end.
ACOQVW_Public();
if (is_admin()) {
    ACOQVW_Backend();
}
new ACOQVW_Api();
