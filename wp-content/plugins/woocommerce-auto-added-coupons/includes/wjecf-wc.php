<?php

defined('ABSPATH') or die();

/**
 * 
 * Interface to WooCommerce. Handles version differences / backwards compatibility.
 * 
 * @since 2.3.7.2
 */
class WJECF_WC {

	protected $wrappers = array();

	/**
	 * Wrap a data object (WC 2.7 introduced WC_Data)
	 * @param type $object 
	 * @return type
	 */
	public function wrap( $object, $use_pool = true ) {
		if ( $use_pool ) {
			//Prevent a huge amount of wrappers to be initiated; one wrapper per object instance should do the trick
			foreach( $this->wrappers as $wrapper ) {
				if ($wrapper->holds( $object ) ) {
					//error_log('Reusing wrapper ' . get_class( $object ) );
					return $wrapper;
				}
			}
		}

		if ( is_numeric( $object ) ) {
			$post_type = get_post_type( $object );
			if ( $post_type == 'shop_coupon' ) {
				$object = WJECF_WC()->get_coupon( $object );
			} elseif ( $post_type == 'product' ) {
				$object = new WC_Product( $object );
			} 
		}


		if ( $object instanceof WC_Coupon ) {
			return $this->wrappers[] = new WJECF_Wrap_Coupon( $object );
		}

		if ( $object instanceof WC_Product ) {
			return $this->wrappers[] = new WJECF_Wrap_Product( $object );
		}

		throw new Exception( 'Cannot wrap ' . get_class( $object ) );
	}

    /**
     * Returns a specific item in the cart.
     *
     * @param string $cart_item_key Cart item key.
     * @return array Item data
	 */
	public function get_cart_item( $cart_item_key ) {		
    	if ( $this->check_woocommerce_version('2.2.9') ) {
			return WC()->cart->get_cart_item( $cart_item_key );
    	}

		return isset( WC()->cart->cart_contents[ $cart_item_key ] ) ? WC()->cart->cart_contents[ $cart_item_key ] : array();
   	}

	/**
	 * Get categories of a product (and anchestors)
	 * @param int $product_id 
	 * @return array product_cat_ids
	 */
	public function wc_get_product_cat_ids( $product_id ) {
		//Since WC 2.5.0
		if ( function_exists( 'wc_get_product_cat_ids' ) ) {
			return wc_get_product_cat_ids( $product_id );
		}

	    $product_cats = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );

	    foreach ( $product_cats as $product_cat ) {
	        $product_cats = array_merge( $product_cats, get_ancestors( $product_cat, 'product_cat' ) );
	    }
	    return $product_cats;
	}	

	/**
	 * @since 2.4.0 for WC 2.7 compatibility
	 * 
	 * Get a WC_Coupon object
	 * @param WC_Coupon|string $coupon The coupon code or a WC_Coupon object
	 * @return WC_Coupon The coupon object
	 */
	public function get_coupon( $coupon ) {
		if ( is_numeric( $coupon ) ) {
			//By id
			global $wpdb;
			$coupon = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE id = %s AND post_type = 'shop_coupon' AND post_status = 'publish'", $coupon ) );
		}
		if ( ! ( $coupon instanceof WC_Coupon ) ) {
			//By code
			$coupon = new WC_Coupon( $coupon );
		}
		return $coupon;
	}	

//ADMIN

	/**
	 * Display a WooCommerce help tip
	 * @param string $tip The tip to display
	 * @return string
	 */
	public function wc_help_tip( $tip ) {
		//Since WC 2.5.0
		if ( function_exists( 'wc_help_tip' ) ) {
			return wc_help_tip( $tip );
		}

		return '<img class="help_tip" style="margin-top: 21px;" data-tip="' . esc_attr( $tip ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
	}

   	/**
   	 * Renders a product selection <input>. Will use either select2 (WC2.3+) or chosen (< WC2.3)
   	 * @param string $dom_id 
   	 * @param string $field_name 
   	 * @param array $selected_ids Array of integers
   	 * @param string|null $placeholder 
   	 * @return void
   	 */
	public function render_admin_product_selector( $dom_id, $field_name, $selected_ids, $placeholder = null ) {
        $product_key_values = array();
        foreach ( $selected_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            if ( is_object( $product ) ) {
                $product_key_values[ esc_attr( $product_id ) ] = wp_kses_post( $product->get_formatted_name() );
            }
        }

		if ( $placeholder === null ) $placeholder = __( 'Search for a product…', 'woocommerce' );

		//In WooCommerce version 2.3.0 chosen was replaced by select2
    	if ( $this->check_woocommerce_version('2.3.0') ) {
			$this->render_admin_select2_product_selector( $dom_id, $field_name, $product_key_values, $placeholder );
		} else {
			$this->render_admin_chosen_product_selector( $dom_id, $field_name, $product_key_values, $placeholder );
		}
	}
	private function render_admin_chosen_product_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
		// $selected_keys_and_values must be an array of [ id => name ]

		echo '<select id="' . esc_attr( $dom_id ) . '" name="' . esc_attr( $field_name ) . '[]" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="' . esc_attr( $placeholder ) . '">';
		foreach ( $selected_keys_and_values as $product_id => $product_name ) {
			echo '<option value="' . $product_id . '" selected="selected">' . $product_name . '</option>';
		}
		echo '</select>';
	}
	private function render_admin_select2_product_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
		// $selected_keys_and_values must be an array of [ id => name ]

		$json_encoded = esc_attr( json_encode( $selected_keys_and_values ) );
	    echo '<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="' 
	    . esc_attr( $field_name ) . '" data-placeholder="' 
	    . esc_attr( $placeholder ) . '" data-action="woocommerce_json_search_products_and_variations" data-selected="' 
	    . $json_encoded . '" value="' . implode( ',', array_keys( $selected_keys_and_values ) ) . '" />';

	}   	


//VERSION

	/**
	 * Check whether WooCommerce version is greater or equal than $req_version
	 * @param string @req_version The version to compare to
	 * @return bool true if WooCommerce is at least the given version
	 */
	public function check_woocommerce_version( $req_version ) {
		return version_compare( $this->get_woocommerce_version(), $req_version, '>=' );
	}	

	private $wc_version = null;
	
	/**
	 * Get the WooCommerce version number
	 * @return string|bool WC Version number or false if WC not detected
	 */
	public function get_woocommerce_version() {
		if ($this->wc_version === null) {
		        // If get_plugins() isn't available, require it
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			
		    // Create the plugins folder and file variables
			$plugin_folder = get_plugins( '/woocommerce' );
			$plugin_file = 'woocommerce.php';
			
			// If the plugin version number is set, return it 
			if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
				$this->wc_version = $plugin_folder[$plugin_file]['Version'];
			} else {
				$this->wc_version = false; // Not found
			}

		}
		return $this->wc_version;
	}

//INSTANCE

	/**
	 * Singleton Instance
	 *
	 * @static
	 * @return Singleton Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	protected static $_instance = null;
}


class WJECF_Wrap {
	protected $object = null;
	public $use_wc27 = true;

	public function __construct( $object ) {
		$this->object = $object;
		//error_log('Wrapping ' . get_class( $object ) );
	}

	public function get_id() {
		//Since WC 2.7
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_id' ) ) ) {
			return $this->object->get_id();
		}
		return $this->object->id;
	}

	public function holds( $object ) {
		return $object === $this->object;
	}

	/**
	 * Get Meta Data by Key.
	 * @since  2.4.0
	 * @param  string $key
	 * @param  bool $single return first found meta, or all
	 * @return mixed
	 */
	public function get_meta( $meta_key, $single = true ) {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_meta' ) ) ) {			
			return $this->get_meta_wc27( $meta_key, $single );
		}

		return $this->get_meta_legacy( $meta_key, $single );

		//return get_post_meta( $this->object->id, $meta_key, $single );
		//If no value found: 
		//If $single is true, an empty string is returned.
		//If $single is false, an empty array is returned.
	}

	protected function get_meta_wc27( $meta_key, $single = true ) {
		$values = $this->object->get_meta( $meta_key, $single );
		if ($single) {
			return $values; //it's just one, dispite the plural in the name!
		}

		if ( $values === '' ) {
			return array(); //get_meta returns empty string if meta does not exist
		}

		return wp_list_pluck( array_values( $values ), 'value' ); //when not using array_values; the index might not start with 0
	}

	protected $meta_cache = array();

	protected function get_meta_legacy( $meta_key, $single = true ) {
		throw new Exception( sprintf( '%s::get_meta_legacy not implemented', get_class( $this ) ) );
	}

}

/**
 * Wrap a data object ( Coupons and products were converted to WC_Data since WC 2.7.0 )
 */
class WJECF_Wrap_Coupon extends WJECF_Wrap {

	public function exists() {
		return $this->get_id() > 0;
	}

	public function get_code() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_code' ) ) ) {
			return $this->object->get_code();
		}

		return $this->object->code;
	}

	public function get_amount() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_amount' ) ) ) {
			return $this->object->get_amount();
		}

		return $this->object->coupon_amount;
	}	

	public function get_individual_use() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_individual_use' ) ) ) {
			return $this->object->get_individual_use();
		}
		
		return $this->object->individual_use == 'yes';
	}

	public function get_limit_usage_to_x_items() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_limit_usage_to_x_items' ) ) ) {
			return $this->object->get_limit_usage_to_x_items();
		}
		
		return $this->object->limit_usage_to_x_items;
	}

	public function set_limit_usage_to_x_items( $limit_usage_to_x_items ) {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'set_limit_usage_to_x_items' ) ) ) {
			$this->object->set_limit_usage_to_x_items( $limit_usage_to_x_items );
		} else {
			$this->object->limit_usage_to_x_items = $limit_usage_to_x_items;
		}
	}


	public function get_email_restrictions() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_email_restrictions' ) ) ) {
			return $this->object->get_email_restrictions();
		}
		
		return $this->object->customer_email;
	}

	public function get_product_ids() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_product_ids' ) ) ) {
			return $this->object->get_product_ids();
		}
		
		return $this->object->product_ids;
	}

	public function get_free_shipping() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_free_shipping' ) ) ) {
			return $this->object->get_free_shipping();
		}
		
		return $this->object->enable_free_shipping();
	}	

	public function get_product_categories() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_product_categories' ) ) ) {
			return $this->object->get_product_categories();
		}
		
		return $this->object->product_categories;
	}

	public function get_minimum_amount() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_minimum_amount' ) ) ) {
			return $this->object->get_minimum_amount();
		}
		
		return $this->object->minimum_amount;
	}	

	/**
	 * Check the type of the coupon
	 * @param string|array $type The type(s) we want to check for
	 * @return bool True if the coupon is of the type
	 */
	public function is_type( $type ) {
		//Backwards compatibility 2.2.11
		if ( method_exists( $this->object, 'is_type' ) ) {
			return $this->object->is_type( $type );
		}
		
		return ( $this->object->discount_type == $type || ( is_array( $type ) && in_array( $this->object->discount_type, $type ) ) ) ? true : false;
	}	

	/**
	 * Update single meta data item by meta key.
	 * Call save() afterwards!
	 * @since  2.4.0
	 * @param  string $key
	 * @param  mixed $value The value; use null to clear
	 */
	public function set_meta( $meta_key, $value ) {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'update_meta_data' ) ) ) {			
			if ( $value === null ) {
				$this->object->delete_meta_data( $meta_key );
			} else {
				$this->object->update_meta_data( $meta_key, $value );
			}
			return;
		}

		$this->maybe_get_custom_fields();
		//WJECF()->log('...setting legacy meta ' . $meta_key );
		$this->legacy_custom_fields[ $meta_key ] = $value;
		$this->legacy_unsaved_keys[] = $meta_key;
	}

	/**
	 * Save the metadata
	 * @return id of this object
	 */
	public function save() {
		//WJECF()->log('Saving ' . $this->get_id() );
		if ( $this->use_wc27 && is_callable( array( $this->object, 'save' ) ) ) {			
			return $this->object->save();
		}

		//Save the unsaved...
		foreach( $this->legacy_unsaved_keys as $meta_key ) {
			//WJECF()->log('...saving legacy meta ' . $meta_key );
			$value = $this->legacy_custom_fields[ $meta_key ];
			if ( $value === null ) {
				delete_post_meta( $this->get_id(), $meta_key );
			} else {
				update_post_meta( $this->get_id(), $meta_key, $value );
			}
		}
		$this->legacy_unsaved_keys = array();

		return $this->get_id();
	}

	protected $legacy_custom_fields = null;
	protected $legacy_unsaved_keys = array();

	protected function maybe_get_custom_fields() {
		//Read custom fields if not yet done
		if ( is_null( $this->legacy_custom_fields ) ) {
			$this->legacy_custom_fields = $this->object->coupon_custom_fields;
		}
	}

	protected function get_meta_legacy( $meta_key, $single = true ) {
		//Read custom fields if not yet done
		$this->maybe_get_custom_fields();

		if ( isset( $this->legacy_custom_fields[ $meta_key ] ) ) {
			$values = $this->legacy_custom_fields[ $meta_key ];
			//WP_CLI::log( "LEGACY:" . print_r( $values, true ));
			if ($single) {
				return maybe_unserialize( reset( $values ) ); //reset yields the first
			}
			$values = array_map( 'maybe_unserialize', $values );
			return $values;
		}

		return $single ? '' : array();
	}

}

class WJECF_Wrap_Product extends WJECF_Wrap {

	public function is_variation() {
		return $this->object instanceof WC_Product_Variation;
	}

	/**
	 * Retrieve the id of the product or the variation id if it's a variant.
	 * 
	 * (2.4.0: Moved from WJECF_Controller to WJECF_WC)
	 * 
	 * @param WC_Product $product 
	 * @return int|bool The variation or product id. False if not a valid product
	 */
	public function get_product_or_variation_id() {
		if ( $this->is_variation() ) {
			return $this->get_variation_id();
		} elseif ( $this->object instanceof WC_Product ) {
			return $this->get_id();
		} else {
			return false;
		}
	}

	/**
	 * Retrieve the id of the parent product if it's a variation; otherwise retrieve this products id
	 * 
	 * (2.4.0: Moved from WJECF_Controller to WJECF_WC)
	 * 
	 * @param WC_Product $product 
	 * @return int|bool The product id. False if this product is not a variation
	 */
	public function get_variable_product_id() {
		if ( ! $this->is_variation() ) {
			return false;
		}

		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_parent_id' ) ) ) {
			return $this->object->get_parent_id();
		} else {
			return wp_get_post_parent_id( $this->object->variation_id );
		}
	}

	/**
	 * Get current variation id
	 * @return int|bool False if this is not a variation
	 */
	protected function get_variation_id() {
		if ( ! $this->is_variation() ) {
			return false;
		}

		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_id' ) ) ) {
			//WP_CLI::log( "get_variation_id:WC27 " . get_class( $this->object ) );
			return $this->object->get_id(); 
		} elseif ( is_callable( array( $this->object, 'get_variation_id' ) ) ) {
			//WP_CLI::log( "get_variation_id:LEGACY " . get_class( $this->object ) );
			return $this->object->get_variation_id(); 
		}
		//WP_CLI::log( "get_variation_id:VERY OLD " . get_class( $this->object ) );
		return $this->object->variation_id;
	}


	public function get_name() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_name' ) ) ) {
			return $this->object->get_name();
		} else {
			return $this->object->post->post_title;
		}
	}	

	public function get_description() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_description' ) ) ) {
			return $this->object->get_description();
		} else {
			return $this->object->post->post_content;
		}
	}

	public function get_short_description() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_short_description' ) ) ) {
			return $this->object->get_short_description();
		} else {
			return $this->object->post->post_excerpt;
		}
	}

	public function get_tag_ids() {
		if ( $this->use_wc27 && is_callable( array( $this->object, 'get_tag_ids' ) ) ) {
			return $this->object->get_tag_ids();
		} else {
			return $this->legacy_get_term_ids( 'product_tag' );
		}
	}

	protected function legacy_get_term_ids( $taxonomy ) {
		$terms = get_the_terms( $this->get_id(), $taxonomy );
		if ( false === $terms || is_wp_error( $terms ) ) {
			return array();
		}
		return wp_list_pluck( $terms, 'term_id' );
	}

}