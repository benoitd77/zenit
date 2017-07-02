<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Conditional_Product_Fees_For_Checkout
 * @subpackage Woocommerce_Conditional_Product_Fees_For_Checkout/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Conditional_Product_Fees_For_Checkout
 * @subpackage Woocommerce_Conditional_Product_Fees_For_Checkout/includes
 * @author     Multidots <inquiry@multidots.in>
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woocommerce_Conditional_Product_Fees_For_Checkout_i18n {
	/**
	 * The domain specified for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $domain    The domain identifier for this plugin.
	 */
	private $domain;

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile = $this->domain . '-' . $locale . '.mo';
		$path = WP_PLUGIN_DIR . '/' . trim( $this->domain.'/languages', '/' );
		load_textdomain( $this->domain, $path . '/'. $mofile );
		$plugin_rel_path = apply_filters( 'woocommerce_woocommerce_conditional_product_fees_for_checkout_translation_file_rel_path', $this->domain.'/languages' );
		load_plugin_textdomain( $this->domain, false, $plugin_rel_path ); 

	}
/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @since    1.0.0
	 * @param    string    $domain    The domain that represents the locale of this plugin.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}


}
