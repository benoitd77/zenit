<?php

/*
  Plugin Name: Woocommerce Shipping Calculator On Product Page
  Plugin URI: http://www.magerips.com
  Description: Allow your customer to calculate shipping before adding product to the cart with avaialable shipping methods.
  Author: Magerips
  Version: 1.4
  Author URI: http://www.magerips.com
 */

global $rpship_plugin_url, $rpship_plugin_dir;

$rpship_plugin_dir = dirname(__FILE__) . "/";
$rpship_plugin_url = plugins_url()."/" . basename($rpship_plugin_dir) . "/";
include_once $rpship_plugin_dir.'lib/main.php';

