<?php
/**
 * @package   IQ Test WordPress Plugin
 * @author    Zbyněk Hovorka
 * @link      https://testyouriqnow.com/
 * @copyright 2020 Zbynek Hovorka
 *
 * @wordpress-plugin
 * Plugin Name:       Test Your IQ WordPress Plugin
 * Plugin URI:        https://testyouriqnow.com/
 * Description:       With Test Your IQ WordPress Plugin you can test your intelligence
 * Version:           1.2
 * Author:            Zbyněk Hovorka
 * Author URI:        https://galleryplugins.com/
 * Text Domain:       test-your-iq
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin Folder Path
if (!defined('TYIQ_DIR')) {
    define('TYIQ_DIR', plugin_dir_path(__FILE__));
}

// Plugin Folder URL
if (!defined('TYIQ_URL')) {
    define('TYIQ_URL', plugin_dir_url(__FILE__));
}
//Base IQ
if (!defined('TYIQ_BASE_IQ')) {
    define('TYIQ_BASE_IQ', 68);
}
//Developer mode
if (!defined('TYIQ_DEV_MOD')) {
    define('TYIQ_DEV_MOD', 0); //1 allow development mod 0 is false
}

//Developer mode
if (!defined('TYIQ_SLUG')) {
    define('TYIQ_SLUG', 'test-your-iq'); //1 allow development mod 0 is false
}


/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once(plugin_dir_path(__FILE__) . 'public/class-iq-test.php');


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook(__FILE__, ['TYIQ_TestYourIq', 'activate']);
register_deactivation_hook(__FILE__, ['TYIQ_TestYourIq', 'deactivate']);

add_action('plugins_loaded', ['TYIQ_TestYourIq', 'get_instance']);

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {

    require_once(plugin_dir_path(__FILE__) . 'admin/class-iq-test-admin.php');
    add_action('plugins_loaded', ['TYIQ_TestYourIqAdmin', 'get_instance']);

}

// Include SEO
if (isset($_GET['iqresults']) && !empty($_GET['iqresults'])) {
    require_once(plugin_dir_path(__FILE__) . 'public/includes/class-seo.php');
    add_action('plugins_loaded', ['TYIQ_TestYourIqSeo', 'get_instance']);
}


