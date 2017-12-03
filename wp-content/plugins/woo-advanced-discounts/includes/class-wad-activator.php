<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.orionorigin.com/
 * @since      0.1
 *
 * @package    Wad
 * @subpackage Wad/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1
 * @package    Wad
 * @subpackage Wad/includes
 * @author     ORION <support@orionorigin.com>
 */
class Wad_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1
	 */
	public static function activate() {
            GLOBAL $wp_rewrite;
            add_option('wad_do_activation_redirect', true);            
            $wp_rewrite->flush_rules(false);
	}

}
