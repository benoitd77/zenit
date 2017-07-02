<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Conditional_Product_Fees_For_Checkout
 * @subpackage Woocommerce_Conditional_Product_Fees_For_Checkout/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Conditional_Product_Fees_For_Checkout
 * @subpackage Woocommerce_Conditional_Product_Fees_For_Checkout/admin
 * @author     Multidots <inquiry@multidots.in>
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Woocommerce_Conditional_Product_Fees_For_Checkout_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        global $post;
        /* @var $_GET type */
        if (isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'wcpfc-list' || $_GET['page'] == 'wcpfc-add-new' || $_GET['page'] == 'wcpfc-premium' || $_GET['page'] == 'wcpfc-get-started' || $_GET['page'] == 'wcpfc-information' || $_GET['page'] == 'wcpfc-edit-fee')) {
            wp_enqueue_style($this->plugin_name . '-choose-css', plugin_dir_url(__FILE__) . 'css/chosen.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name . '-jquery-ui-css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name . 'font-awesome', plugin_dir_url(__FILE__) . 'css/font-awesome.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name . '-webkit-css', plugin_dir_url(__FILE__) . 'css/webkit.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name . 'main-style', plugin_dir_url(__FILE__) . 'css/style.css', array(), 'all');
            wp_enqueue_style($this->plugin_name . 'media-css', plugin_dir_url(__FILE__) . 'css/media.css', array(), 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        global $post;
        if (isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'wcpfc-list' || $_GET['page'] == 'wcpfc-add-new' || $_GET['page'] == 'wcpfc-premium' || $_GET['page'] == 'wcpfc-get-started' || $_GET['page'] == 'wcpfc-information' || $_GET['page'] == 'wcpfc-edit-fee')) {
            wp_enqueue_style('wp-jquery-ui-dialog');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script($this->plugin_name . '-choose-js', plugin_dir_url(__FILE__) . 'js/chosen.jquery.min.js', array('jquery', 'jquery-ui-datepicker'), $this->version, false);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-conditional-product-fees-for-checkout-admin.js', array('jquery', 'jquery-ui-dialog', 'jquery-ui-accordion', 'jquery-ui-sortable'), $this->version, false);
            wp_localize_script($this->plugin_name, 'coditional_vars', array('plugin_url' => plugin_dir_url(__FILE__)));
            wp_enqueue_script($this->plugin_name . '-tablesorter-js', plugin_dir_url(__FILE__) . 'js/jquery.tablesorter.js', array('jquery'), $this->version, false);
        }
    }

    public function dot_store_menu_conditional_fee() {
        global $GLOBALS;
        if (empty($GLOBALS['admin_page_hooks']['dots_store'])) {
            add_menu_page(
                    'DotStore Plugins', __('DotStore Plugins'), 'manage_option', 'dots_store', array($this, 'dot_store_menu_page'), WCPFC_PLUGIN_URL . 'admin/images/menu-icon.png', 25
            );
        }
        add_submenu_page('dots_store', 'Get Started', 'Get Started', 'manage_options', 'wcpfc-get-started', array($this, 'wcpfc_get_started_page'));
        add_submenu_page('dots_store', 'Premium Version', 'Premium Version', 'manage_options', 'wcpfc-premium', array($this, 'premium_version_wcpfc_page'));
        add_submenu_page('dots_store', 'Introduction', 'Introduction', 'manage_options', 'wcpfc-information', array($this, 'wcpfc_information_page'));
        add_submenu_page('dots_store', 'WooCommerce Conditional Product Fees for Checkout', __('WooCommerce Conditional Product Fees for Checkout'), 'manage_options', 'wcpfc-list', array($this, 'wcpfc_fee_list_page'));
        add_submenu_page('dots_store', 'Add New', 'Add New', 'manage_options', 'wcpfc-add-new', array($this, 'wcpfc_add_new_fee_page'));
        add_submenu_page('dots_store', 'Edit Fee', 'Edit Fee', 'manage_options', 'wcpfc-edit-fee', array($this, 'wcpfc_edit_fee_page'));
    }

    public function dot_store_menu_page() {
        
    }

    public function wcpfc_information_page() {
        require_once('partials/wcpfc-information-page.php');
    }

    public function premium_version_wcpfc_page() {
        require_once('partials/wcpfc-premium-version-page.php');
    }

    public function wcpfc_fee_list_page() {
        require_once('partials/wcpfc-list-page.php');
    }

    public function wcpfc_add_new_fee_page() {
        require_once('partials/wcpfc-add-new-page.php');
    }

    public function wcpfc_edit_fee_page() {
        require_once('partials/wcpfc-add-new-page.php');
    }

    public function wcpfc_get_started_page() {
        require_once('partials/wcpfc-get-started-page.php');
    }

    function fee_settings_get_meta($value) {
        global $post;

        $field = get_post_meta($post->ID, $value, true);
        if (!empty($field)) {
            return is_array($field) ? stripslashes_deep($field) : stripslashes(wp_kses_decode_entities($field));
        } else {
            return false;
        }
    }

    function wcpfc_fees_conditions_save($post) {
        if (empty($post)) {
            return false;
        }
        if (isset($_POST['post_type']) && $_POST['post_type'] == 'wc_conditional_fee') {
            if ($post['fee_post_id'] == '') {
                $fee_post = array(
                    'post_title' => $post['fee_settings_product_fee_title'],
                    'post_status' => 'publish',
                    'post_type' => 'wc_conditional_fee',
                );
                $post_id = wp_insert_post($fee_post);
            } else {
                $fee_post = array(
                    'ID' => $post['fee_post_id'],
                    'post_title' => $post['fee_settings_product_fee_title'],
                    'post_status' => 'publish'
                );
                $post_id = wp_update_post($fee_post);
            }
            if (isset($post['fee_settings_product_cost']))
                update_post_meta($post_id, 'fee_settings_product_cost', esc_attr($post['fee_settings_product_cost']));
            if (isset($post['fee_chk_qty_price'])) {
                update_post_meta($post_id, 'fee_chk_qty_price', 'on');
            } else {
                update_post_meta($post_id, 'fee_chk_qty_price', 'off');
            }
            if (isset($post['fee_settings_select_fee_type']))
                update_post_meta($post_id, 'fee_settings_select_fee_type', esc_attr($post['fee_settings_select_fee_type']));
            if (isset($post['fee_settings_start_date']))
                update_post_meta($post_id, 'fee_settings_start_date', esc_attr($post['fee_settings_start_date']));
            if (isset($post['fee_settings_end_date']))
                update_post_meta($post_id, 'fee_settings_end_date', esc_attr($post['fee_settings_end_date']));
            if (isset($post['fee_settings_status'])) {
                update_post_meta($post_id, 'fee_settings_status', 'on');
            } else {
                update_post_meta($post_id, 'fee_settings_status', 'off');
            }
            if (isset($post['fee_settings_select_taxable']))
                update_post_meta($post_id, 'fee_settings_select_taxable', esc_attr($post['fee_settings_select_taxable']));
            if (isset($post['fee_settings_optional_gift']))
                update_post_meta($post_id, 'fee_settings_optional_gift', esc_attr($post['fee_settings_optional_gift']));
            if (isset($post['by_default_checkbox_checked'])) {
                update_post_meta($post_id, 'by_default_checkbox_checked', 'on');
            } else {
                update_post_meta($post_id, 'by_default_checkbox_checked', 'off');
            }
            $feesArray = array();
            $fees = isset($post['fees']) ? $post['fees'] : array();
            $condition_key = isset($post['condition_key']) ? $post['condition_key'] : array();
            $fees_conditions = $fees['product_fees_conditions_condition'];
            $conditions_is = $fees['product_fees_conditions_is'];
            $conditions_values = isset($fees['product_fees_conditions_values']) && !empty($fees['product_fees_conditions_values']) ? $fees['product_fees_conditions_values'] : array();
            $size = count($fees_conditions);
            foreach ($condition_key as $key => $value) {
                if (!array_key_exists($key, $conditions_values)) {
                    $conditions_values[$key] = array();
                }
            }
            uksort($conditions_values, 'strnatcmp');
            foreach ($conditions_values as $k => $v) {
                $conditionsValuesArray[] = $v;
            }
            for ($i = 0; $i < $size; $i++) {
                $feesArray[] = array(
                    'product_fees_conditions_condition' => $fees_conditions[$i],
                    'product_fees_conditions_is' => $conditions_is[$i],
                    'product_fees_conditions_values' => $conditionsValuesArray[$i]
                );
            }
            update_post_meta($post_id, 'product_fees_metabox', $feesArray);
            $complete_url = wp_nonce_url(home_url('/wp-admin/admin.php?page=wcpfc-list&'), '_wpnonce=' . $retrieved_nonce, '_wpnonce');
            wp_redirect($complete_url);
            exit();
        }
    }

    /**
     * Product spesifict starts
     */
    function product_fees_conditions_get_meta($value) {
        global $post;
        $field = get_post_meta($post->ID, $value, true);
        if (isset($field) && !empty($field)) {
            return is_array($field) ? stripslashes_deep($field) : stripslashes(wp_kses_decode_entities($field));
        } else {
            return false;
        }
    }

    public function product_fees_conditions_values_ajax() {
        global $wpdb, $post; // this is how you get access to the database
        /* prevent XSS. */
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $condition = ( isset($_POST['condition']) && !empty($_POST['condition']) ) ? $_POST['condition'] : '';
        $count = ( isset($_POST['count']) && !empty($_POST['count']) ) ? $_POST['count'] : '';
        $html = '';
        if ($condition == 'country') {
            $html .= $this->get_country_list($count);
        } elseif ($condition == 'product') {
            $html .= $this->get_product_list($count);
        }
        echo $html;
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    /**
     * Function for select country list
     *
     */
    public function get_country_list($count = '', $selected = array()) {
        global $woocommerce;
        $countries_obj = new WC_Countries();
        $getCountries = $countries_obj->__get('countries');
        $html = '<select name="fees[product_fees_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values multiselect2 product_fees_conditions_values_country" multiple="multiple">';
        if (!empty($getCountries)) {
            foreach ($getCountries as $code => $country) {
                $selectedVal = is_array($selected) && !empty($selected) && in_array($code, $selected) ? 'selected=selected' : '';
                $html .= '<option value="' . $code . '" ' . $selectedVal . '>' . $country . '</option>';
            }
        }

        $html .= '</select>';
        return $html;
    }

    /**
     * Function for select product list
     *
     */
    public function get_product_list($count = '', $selected = array()) {
        $product_array = array();

        $get_all_products = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));
        $html = '<select id="product-filter" rel-id="' . $count . '" name="fees[product_fees_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values multiselect2" multiple="multiple">';
        if (isset($get_all_products->posts) && !empty($get_all_products->posts)) {

            foreach ($get_all_products->posts as $get_all_product) {
                $selectedVal = is_array($selected) && !empty($selected) && in_array($get_all_product->ID, $selected) ? 'selected=selected' : '';
                if ($selectedVal != '') {
                    $html .= '<option value="' . $get_all_product->ID . '" ' . $selectedVal . '>' . '#' . $get_all_product->ID . ' - ' . $get_all_product->post_title . '</option>';
                }
            }
        }
        $html .= '</select>';
        return $html;
    }

    public function wc_multiple_delete_conditional_fee() {
        global $wpdb;
        $result = 0;
        /* prevent XSS. */
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $allVals = ( isset($_POST['allVals']) && !empty($_POST['allVals']) ) ? $_POST['allVals'] : '';


        if (!empty($allVals)) {
            foreach ($allVals as $val) {
                wp_delete_post($val);
                $result = 1;
            }
        }
        echo $result;
        wp_die();
    }

    public function welcome_conditional_fee_screen_do_activation_redirect() {
        // if no activation redirect
        if (!get_transient('_welcome_screen_activation_redirect_data')) {
            return;
        }

        // Delete the redirect transient
        delete_transient('_welcome_screen_activation_redirect_data');

        // if activating from network, or bulk
        if (is_network_admin() || isset($_GET['activate-multi'])) {
            return;
        }
        // Redirect to extra cost welcome  page

        wp_safe_redirect(add_query_arg(array('page' => 'wcpfc-get-started'), admin_url('admin.php')));
    }

    public function wcpfc_remove_admin_submenus() {
        remove_submenu_page('dots_store', 'wcpfc-information');
        remove_submenu_page('dots_store', 'wcpfc-premium');
        remove_submenu_page('dots_store', 'wcpfc-add-new');
        remove_submenu_page('dots_store', 'wcpfc-edit-fee');
        remove_submenu_page('dots_store', 'wcpfc-get-started');
    }

    public function product_fees_conditions_values_product_ajax() {
        global $wpdb, $post;
        $value = isset($_REQUEST['value']) ? $_REQUEST['value'] : '';
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        $product_array = array();
        $search_title = ' \'%' . $value . '%\'';
        $query = "SELECT  * FROM   $wpdb->posts
WHERE   	$wpdb->posts.post_title LIKE " . $search_title . "
AND       $wpdb->posts.post_type = 'product' AND $wpdb->posts.post_status = 'publish'
ORDER BY  $wpdb->posts.post_title";
        $get_all_products = $wpdb->get_results($query);
        $html = '';
        if (isset($get_all_products) && !empty($get_all_products)) {
            foreach ($get_all_products as $get_all_product) {
                $html .= '<option value="' . $get_all_product->ID . '">' . '#' . $get_all_product->ID . ' - ' . $get_all_product->post_title . '</option>';
            }
        }
        echo $html;
        wp_die();
    }

    public function wp_add_plugin_userfn_wcpfc_free() {
        $email_id = (isset($_POST["email_id"]) && !empty($_POST["email_id"])) ? $_POST["email_id"] : '';
        $log_url = $_SERVER['HTTP_HOST'];
        $cur_date = date('Y-m-d');
        $request_url = 'http://www.multidots.com/store/wp-content/themes/business-hub-child/API/wp-add-plugin-users.php';
        if (!empty($email_id)) {
            $request_response = wp_remote_post($request_url, array('method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array('user' => array('plugin_id' => '43', 'user_email' => $email_id, 'plugin_site' => $log_url, 'status' => 1, 'activation_date' => $cur_date)),
                'cookies' => array()));
            update_option('wcpfc_free_plugin_notice_shown', 'true');
        }
    }

}
