<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Conditional_Product_Fees_For_Checkout
 * @subpackage Woocommerce_Conditional_Product_Fees_For_Checkout/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Conditional_Product_Fees_For_Checkout
 * @subpackage Woocommerce_Conditional_Product_Fees_For_Checkout/public
 * @author     Multidots <inquiry@multidots.in>
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Woocommerce_Conditional_Product_Fees_For_Checkout_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Conditional_Product_Fees_For_Checkout_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Conditional_Product_Fees_For_Checkout_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-conditional-product-fees-for-checkout-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Conditional_Product_Fees_For_Checkout_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Conditional_Product_Fees_For_Checkout_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-conditional-product-fees-for-checkout-public.js', array('jquery'), $this->version, false);

        wp_localize_script($this->plugin_name, 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    function woocommerce_locate_template_product_fees_conditions($template, $template_name, $template_path) {

        global $woocommerce;

        $_template = $template;

        if (!$template_path)
            $template_path = $woocommerce->template_url;

        $plugin_path = woocommerce_conditional_product_fees_for_woocommerce_plugin_path() . '/woocommerce/';

        $template = locate_template(
                array(
                    $template_path . $template_name,
                    $template_name
                )
        );

        // Modification: Get the template from this plugin, if it exists
        if (!$template && file_exists($plugin_path . $template_name))
            $template = $plugin_path . $template_name;

        // Use default template
        if (!$template)
            $template = $_template;
        // Return what we found
        return $template;
    }

    public function conditional_fee_add_to_cart($package) {
        global $woocommerce, $wpdb, $post;

        $fees_args = array(
            'post_type' => 'wc_conditional_fee',
            'post_status' => 'publish',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'posts_per_page' => -1
        );

        $get_all_fees = get_posts($fees_args);

        $cart_array = $woocommerce->cart->get_cart();
        $cart_sub_total = $woocommerce->cart->subtotal;
        $cart_final_products_array = array();
        $cart_products_subtotal = 0;

        if (!empty($get_all_fees)) {
            foreach ($get_all_fees as $fees) {
                $is_passed = array();
                $title = get_the_title($fees->ID) ? get_the_title($fees->ID) : 'Fee';
                $getFeesCostOriginal = get_post_meta($fees->ID, 'fee_settings_product_cost', true);
                $getFeesPerQty = trim(get_post_meta($fees->ID, 'fee_chk_qty_price', true));

                $getFeesCost = $getFeesCostOriginal;

                $getFeeType = get_post_meta($fees->ID, 'fee_settings_select_fee_type', true);
                $getFeetaxable = get_post_meta($fees->ID, 'fee_settings_select_taxable', true);
                $getFeeStartDate = get_post_meta($fees->ID, 'fee_settings_start_date', true);
                $getFeeEndDate = get_post_meta($fees->ID, 'fee_settings_end_date', true);
                $getFeeStatus = get_post_meta($fees->ID, 'fee_settings_status', true);

                $fee_optional = get_post_meta($fees->ID, 'fee_settings_optional_gift', true);
                $apply_fee_default = get_post_meta($fees->ID, 'by_default_checkbox_checked', true);

                if (isset($getFeeStatus) && $getFeeStatus == 'off') {
                    continue;
//                  return false;
                }

                $get_condition_array = get_post_meta($fees->ID, 'product_fees_metabox', true);

                /* Percentage Logic Start */
                if (isset($getFeesCost) && !empty($getFeesCost) && $getFeesCost != '') {
                    if (isset($getFeeType) && !empty($getFeeType) && $getFeeType == 'percentage') {

                        if (!empty($get_condition_array)) {

                            $product_array = array();

                            foreach ($get_condition_array as $key => $value) {
                                if (array_search('product', $value)) {
                                    $product_array[$key] = $value;
                                }
                            }

                            //Check if is product exist
                            if (is_array($product_array) && !empty($product_array) && !empty($cart_array)) {

                                foreach ($product_array as $product) {

                                    if ($product['product_fees_conditions_condition'] == 'product') {

                                        if ($product['product_fees_conditions_is'] == 'is_equal_to') {
                                            if (!empty($product['product_fees_conditions_values'])) {
                                                foreach ($product['product_fees_conditions_values'] as $product_id) {
                                                    foreach ($cart_array as $key => $value) {

                                                        if ($product_id == $value['product_id']) {
                                                            $cart_final_products_array[] = $value;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if ($product['product_fees_conditions_is'] == 'not_in') {
                                            if (!empty($product['product_fees_conditions_values'])) {
                                                foreach ($cart_array as $key => $value) {
                                                    $cart_final_products_array[] = $value;
                                                }
                                            }
                                        }
                                    }
                                }

                                foreach ($cart_final_products_array as $cart_item) {
                                    $cart_products_subtotal += $cart_item['line_total'];
                                }

                                $percentage_subtotal = $cart_products_subtotal;
                            } else {
                                $percentage_subtotal = $cart_sub_total;
                            }
                        }

                        $fees_cost = ($percentage_subtotal * $getFeesCost) / 100;
                    } else {
                        $fees_cost = $getFeesCost;
                    }
                } else {
                    $fees_cost = '';
                }

                if (!empty($get_condition_array)) {
                    $country_array = array();
                    $product_array = array();

                    foreach ($get_condition_array as $key => $value) {
                        if (array_search('country', $value)) {
                            $country_array[$key] = $value;
                        }
                        if (array_search('product', $value)) {
                            $product_array[$key] = $value;
                        }
                    }

                    //Check if is country exist
                    if (is_array($country_array) && isset($country_array) && !empty($country_array) && !empty($cart_array)) {
                        $selected_country = $woocommerce->customer->get_shipping_country();
                        $is_passed['has_fee_based_on_country'] = '';
                        $passed_country = array();
                        $notpassed_country = array();
                        foreach ($country_array as $country) {
                            if ($country['product_fees_conditions_is'] == 'is_equal_to') {
                                if (!empty($country['product_fees_conditions_values'])) {
                                    foreach ($country['product_fees_conditions_values'] as $country_id) {
                                        $passed_country[] = $country_id;
                                        if ($country_id == $selected_country) {
                                            $is_passed['has_fee_based_on_country'] = 'yes';
                                        }
                                    }
                                }
                            }
                            if ($country['product_fees_conditions_is'] == 'not_in') {
                                if (!empty($country['product_fees_conditions_values'])) {
                                    foreach ($country['product_fees_conditions_values'] as $country_id) {
                                        $notpassed_country[] = $country_id;
                                        if ($country_id == 'all' || $country_id == $selected_country) {
                                            $is_passed['has_fee_based_on_country'] = 'no';
                                        }
                                    }
                                }
                            }
                        }
                        if (empty($is_passed['has_fee_based_on_country']) && empty($passed_country)) {
                            $is_passed['has_fee_based_on_country'] = 'yes';
                        } elseif (empty($is_passed['has_fee_based_on_country']) && !empty($passed_country)) {
                            $is_passed['has_fee_based_on_country'] = 'no';
                        }
                    }

                    //Check if is product exist
                    if (is_array($product_array) && isset($product_array) && !empty($product_array) && !empty($cart_array)) {
                        $cart_product = $this->fee_array_column($cart_array, 'product_id');

                        $is_passed['has_fee_based_on_product'] = '';
                        $passed_product = array();
                        $notpassed_product = array();

                        foreach ($product_array as $product) {
                            if ($product['product_fees_conditions_is'] == 'is_equal_to') {
                                if (!empty($product['product_fees_conditions_values'])) {
                                    foreach ($product['product_fees_conditions_values'] as $product_id) {

                                        $_product = wc_get_product($product_id);

                                        $passed_product[] = $product_id;
                                        if (in_array($product_id, $cart_product)) {
                                            $is_passed['has_fee_based_on_product'] = 'yes';
                                        }
                                    }
                                }
                            }
                            if ($product['product_fees_conditions_is'] == 'not_in') {
                                if (!empty($product['product_fees_conditions_values'])) {
                                    foreach ($product['product_fees_conditions_values'] as $product_id) {
                                        $notpassed_product = $product_id;
                                        if (in_array($product_id, $cart_product)) {
                                            $is_passed['has_fee_based_on_product'] = 'no';
                                        }
                                    }
                                }
                            }
                        }
                        if (empty($is_passed['has_fee_based_on_product']) && empty($passed_product)) {
                            $is_passed['has_fee_based_on_product'] = 'yes';
                        } elseif (empty($is_passed['has_fee_based_on_product']) && !empty($passed_product)) {
                            $is_passed['has_fee_based_on_product'] = 'no';
                        }
                    }
                }
                if (isset($is_passed) && !empty($is_passed) && is_array($is_passed)) {
                    if (!in_array('no', $is_passed)) {

                        $texable = (isset($getFeetaxable) && !empty($getFeetaxable) && $getFeetaxable == 'yes') ? true : false;
                        $currentDate = strtotime(date('d-m-Y'));
                        $feeStartDate = isset($getFeeStartDate) && $getFeeStartDate != '' ? strtotime($getFeeStartDate) : '';
                        $feeEndDate = isset($getFeeEndDate) && $getFeeEndDate != '' ? strtotime($getFeeEndDate) : '';
                        if (($currentDate >= $feeStartDate || $feeStartDate == '') && ($currentDate <= $feeEndDate || $feeEndDate == '')) {
                            $woocommerce->cart->add_fee($title, $fees_cost, $texable, '');
                        }
                    }
                }
            }
        }
    }

    public function fee_array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if (!isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

    public function array_flatten($array) {
        if (!is_array($array)) {
            return FALSE;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->array_flatten($value));
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Find a matching zone for a given package.
     * @since  2.6.0
     * @uses   wc_make_numeric_postcode()
     * @param  object $package
     * @return WC_Shipping_Zone
     */
    public function wc_get_shipping_zone() {
        global $wpdb, $woocommerce;

        $country = strtoupper(wc_clean($woocommerce->customer->get_shipping_country()));
        $state = strtoupper(wc_clean($woocommerce->customer->get_shipping_state()));
        $continent = strtoupper(wc_clean(WC()->countries->get_continent_code_for_country($country)));
        $postcode = wc_normalize_postcode(wc_clean($woocommerce->customer->get_shipping_postcode()));
        $cache_key = WC_Cache_Helper::get_cache_prefix('shipping_zones') . 'wc_shipping_zone_' . md5(sprintf('%s+%s+%s', $country, $state, $postcode));
        $matching_zone_id = wp_cache_get($cache_key, 'shipping_zones');

        if (false === $matching_zone_id) {

            // Work out criteria for our zone search
            $criteria = array();
            $criteria[] = $wpdb->prepare("( ( location_type = 'country' AND location_code = %s )", $country);
            $criteria[] = $wpdb->prepare("OR ( location_type = 'state' AND location_code = %s )", $country . ':' . $state);
            $criteria[] = $wpdb->prepare("OR ( location_type = 'continent' AND location_code = %s )", $continent);
            $criteria[] = "OR ( location_type IS NULL ) )";

            // Postcode range and wildcard matching
            $postcode_locations = $wpdb->get_results("SELECT zone_id, location_code FROM {$wpdb->prefix}woocommerce_shipping_zone_locations WHERE location_type = 'postcode';");

            if ($postcode_locations) {
                $zone_ids_with_postcode_rules = array_map('absint', wp_list_pluck($postcode_locations, 'zone_id'));
                $matches = wc_postcode_location_matcher($postcode, $postcode_locations, 'zone_id', 'location_code', $country);
                $do_not_match = array_unique(array_diff($zone_ids_with_postcode_rules, array_keys($matches)));

                if (!empty($do_not_match)) {
                    $criteria[] = "AND zones.zone_id NOT IN (" . implode(',', $do_not_match) . ")";
                }
            }
            // Get matching zones
            $matching_zone_id = $wpdb->get_var("
				SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones
				LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'
				WHERE " . implode(' ', $criteria) . "
				ORDER BY zone_order ASC LIMIT 1
			");

            wp_cache_set($cache_key, $matching_zone_id, 'shipping_zones');
        }

        return $matching_zone_id ? $matching_zone_id : 0;
    }

    public function remove_currency($price) {
        $wc_currency_symbol = get_woocommerce_currency_symbol();
        $new_price = str_replace($wc_currency_symbol, '', $price);
        $new_price2 = (double) preg_replace('/[^.\d]/', '', $new_price);
        return $new_price2;
    }

}
