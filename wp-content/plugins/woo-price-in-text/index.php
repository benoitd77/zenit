<?php
/**
* Plugin Name: Woo Price in Text
* Description: Woo Price in Text
* Version: 1.0
* Author: ThemeHeap
* Author URI: themeheap.com
*/

//Add Shortcode Handler in Editor

if(!function_exists('wpintxt_add_shortcodehandler')){


	add_action('admin_head', 'wpintxt_add_shortcodehandler');

	function wpintxt_add_shortcodehandler() {
	  
		  // check if WYSIWYG is enabled
	    if (get_user_option('rich_editing') == 'true') {
	        add_filter("mce_external_plugins", "wpintxt_add_tinymce_plugin");
	        add_filter('mce_buttons', 'wpintxt_register_shortcode_handler');
	    }
	}
	function wpintxt_register_shortcode_handler($buttons) {
	    array_push($buttons, "wpintxt");
	    return $buttons;
	}
}

if(!function_exists('wpintxt_add_tinymce_plugin')){
	function wpintxt_add_tinymce_plugin($plugin_array) {
	    $plugin_array['wpintxt'] = plugin_dir_url(__FILE__). '/core/assets/js/shortcode_handler.js'; // CHANGE THE BUTTON SCRIPT HERE
	    return $plugin_array;
	}
}


if(!function_exists('woo_wpintxt_callback')){
add_shortcode('woo_wpintxt' , 'woo_wpintxt_callback');

	function woo_wpintxt_callback($atts, $content = "") {
		if ( class_exists( 'WooCommerce' ) ) {
			$atts = shortcode_atts(
				array(
					'text' => '',
					'sku' => '',
				), $atts, 'woo_wpintxt' );

			global $product;

			if( !empty( $atts['sku'])){
				$product_id = wc_get_product_id_by_sku( $atts['sku'] );
				$product =wc_get_product( $product_id );
			}
			if(!empty( $product)){
				$price = wc_price($product->price);
				$text = sprintf($atts['text'], $price);				
			}else{
				$text = 'Incorrect SKU';
			}
			echo "<p class='price-in-text'>$text</p>";
		} else {
	 		'Install WooCommerce';
		}
	}
}