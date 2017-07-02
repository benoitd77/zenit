<?php

/**

 * Plugin Name:       Woo Conditional Product Fees for Checkout
 * Plugin URI:        https://store.multidots.com/woocommerce-conditional-product-fees-checkout/
 * Description:       With this plugin, you can create and manage complex fee rules in WooCommerce store without the help of a developer.
 * Version:           1.5
 * Author:            Multidots
 * Author URI:        https://store.multidots.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-conditional-product-fees-for-checkout
 * Domain Path:       /languages
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!defined('WCPFC_PLUGIN_VERSION')) {
    define('WCPFC_PLUGIN_VERSION', '1.5');
   
}
if (!defined('WCPFC_PLUGIN_URL'))
    define('WCPFC_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('WCPFC_PLUGIN_DIR'))
    define('WCPFC_PLUGIN_DIR', dirname(__FILE__));

if (!defined('WCPFC_PLUGIN_DIR_PATH')) {
    define('WCPFC_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}
if (!defined('WCPFC_SLUG')) {
    define('WCPFC_SLUG', 'woo-conditional-product-fees-for-checkout');
}
if (!defined('WCPFC_PLUGIN_BASENAME')) {
    define('WCPFC_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-conditional-product-fees-for-checkout-activator.php
 */
function activate_woocommerce_conditional_product_fees_for_checkout() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-conditional-product-fees-for-checkout-activator.php';
    Woocommerce_Conditional_Product_Fees_For_Checkout_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-conditional-product-fees-for-checkout-deactivator.php
 */
function deactivate_woocommerce_conditional_product_fees_for_checkout() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-conditional-product-fees-for-checkout-deactivator.php';
    Woocommerce_Conditional_Product_Fees_For_Checkout_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_woocommerce_conditional_product_fees_for_checkout');
register_deactivation_hook(__FILE__, 'deactivate_woocommerce_conditional_product_fees_for_checkout');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-woocommerce-conditional-product-fees-for-checkout.php';

/**
 * The core plugin include constant file for set constant.
 */
require plugin_dir_path(__FILE__) . 'includes/constant.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_conditional_product_fees_for_checkout() {

    $plugin = new Woocommerce_Conditional_Product_Fees_For_Checkout();
    $plugin->run();
}
run_woocommerce_conditional_product_fees_for_checkout();

function woocommerce_conditional_product_fees_for_woocommerce_plugin_path() {

    return untrailingslashit(plugin_dir_path(__FILE__));
}
