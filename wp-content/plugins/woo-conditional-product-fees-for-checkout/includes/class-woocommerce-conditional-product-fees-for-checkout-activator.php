<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Conditional_Product_Fees_For_Checkout
 * @subpackage Woocommerce_Conditional_Product_Fees_For_Checkout/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Conditional_Product_Fees_For_Checkout
 * @subpackage Woocommerce_Conditional_Product_Fees_For_Checkout/includes
 * @author     Multidots <inquiry@multidots.in>
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Woocommerce_Conditional_Product_Fees_For_Checkout_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    //subscribe news
    public static function activate() {
        global $wpdb, $woocommerce;
        set_transient( '_welcome_screen_activation_redirect_data', true, 30 );
        add_option('wcpfc_version', Woocommerce_Conditional_Product_Fees_For_Checkout::WCPFC_VERSION);

        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !is_plugin_active_for_network('woocommerce/woocommerce.php')) {
            wp_die("<strong> WooCommerce Conditional Product Fees for Checkout</strong> Plugin requires <strong>WooCommerce</strong> <a href='" . get_admin_url(null, 'plugins.php') . "'>Plugins page</a>.");
        }
    }

}
