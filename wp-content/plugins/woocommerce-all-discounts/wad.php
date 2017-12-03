<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.orionorigin.com/
 * @since             0.1
 * @package           Wad
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce All Discounts
 * Plugin URI:        http://www.orionorigin.com/plugins/woocommerce-all-discounts
 * Description:       Manage your shop discounts like a pro.
 * Version:           2.0.1
 * Author:            ORION
 * Author URI:        http://www.orionorigin.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wad
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WAD_VERSION', '2.0.1' );
define( 'WAD_URL', plugins_url('/', __FILE__) );
define( 'WAD_DIR', dirname(__FILE__) );
define( 'WAD_MAIN_FILE', 'woocommerce-all-discounts/wad.php' );
define( 'WAD_PLUGIN_NAME', 'Woocommerce All Discounts & Deals');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wad-activator.php
 */
function activate_wad() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wad-activator.php';
	Wad_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wad-deactivator.php
 */
function deactivate_wad() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wad-deactivator.php';
	Wad_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wad' );
register_deactivation_hook( __FILE__, 'deactivate_wad' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wad.php';
require plugin_dir_path(__FILE__) . 'includes/class-wad-discount.php';
require plugin_dir_path(__FILE__) . 'includes/class-wad-products-list.php';
if(!function_exists("o_admin_fields"))
require plugin_dir_path(__FILE__) . 'includes/utils.php';
require plugin_dir_path(__FILE__) . 'includes/functions.php';
if (!class_exists('\Drewm\MailChimp'))
    require plugin_dir_path(__FILE__) . 'includes/MailChimp.php';
if (!class_exists('Mailin'))
 require plugin_dir_path(__FILE__) . 'includes/mailin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1
 */
function run_wad() {

	$plugin = new Wad();
	$plugin->run();

}
run_wad();
