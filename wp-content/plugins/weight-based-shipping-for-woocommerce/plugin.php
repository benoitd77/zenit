<?php
/**
 * Plugin Family Id: dangoodman/wc-weight-based-shipping
 * Plugin Name: WooCommerce Weight Based Shipping
 * Plugin URI: https://wordpress.org/plugins/weight-based-shipping-for-woocommerce/
 * Description: Simple yet flexible shipping method for WooCommerce.
 * Version: 5.0.8
 * Author: weightbasedshipping.com
 * Author URI: https://weightbasedshipping.com
 * Requires at least: 4.0
 * Tested up to: 4.7
 * WC requires at least: 2.3
 * WC tested up to: 3.0
 */

if (!class_exists('WbsVendors_DgmWpPrerequisitesChecker', false)) {
    require_once(dirname(__FILE__).'/server/vendor/dangoodman/wp-prerequisites-check/DgmWpPrerequisitesChecker.php');
}

if (WbsVendors_DgmWpPrerequisitesChecker::createAndCheck(
    'WooCommerce Weight Based Shipping',
    '5.3',
    '4.0',
    '2.3'
)) {
    include(dirname(__FILE__).'/bootstrap.php');
}