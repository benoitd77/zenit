<?php
/*
  Plugin Name: WooCommerce Currency Switcher
  Plugin URI: http://currency-switcher.com/
  Description: Currency Switcher for WooCommerce - the plugin that allows to customers switch currencies on woocommerce store site
  Author: realmag777
  Version: 2.1.8
  Requires at least: WP 4.1.0
  Tested up to: WP 4.7
  Text Domain: woocommerce-currency-switcher
  Domain Path: /languages
  Forum URI: #
  Author URI: http://www.pluginus.net/
 */

if (!defined('ABSPATH'))
{
    exit; // Exit if accessed directly
}

if (defined('DOING_AJAX'))
{
    if (isset($_REQUEST['action']))
    {
//do not recalculate refund amounts when we are in order backend
        if ($_REQUEST['action'] == 'woocommerce_refund_line_items')
        {
            return;
        }

        if (isset($_REQUEST['order_id']) AND $_REQUEST['action'] == 'woocommerce_load_order_items')
        {
            return;
        }
    }
}


//block for custom support code experiments
/*
  if ($_SERVER['REMOTE_ADDR'] != 'xxx.155.xx.190')
  {
  return;
  }
 */
//***
define('WOOCS_VERSION', '2.1.8');
define('WOOCS_MIN_WOOCOMMERCE', '2.4');
define('WOOCS_PATH', plugin_dir_path(__FILE__));
define('WOOCS_LINK', plugin_dir_url(__FILE__));
define('WOOCS_PLUGIN_NAME', plugin_basename(__FILE__));

//classes
include_once WOOCS_PATH . 'classes/storage.php';
include_once WOOCS_PATH . 'classes/cron.php';
include_once WOOCS_PATH . 'classes/fixed.php';

//09-12-2016
final class WOOCS {

//http://docs.woothemes.com/wc-apidocs/class-WC_Order.html
    public $storage = null;
    public $cron = NULL;
    public $cron_hook = 'woocs_update_rates_wpcron';
    public $wp_cron_period = DAY_IN_SECONDS;
    public $settings = array();
    public $fixed = NULL;
    public $default_currency = 'USD'; //EUR -> set any existed currency here if USD is not exists in your currencies list
    public $current_currency = 'USD'; //EUR -> set any existed currency here if USD is not exists in your currencies list
    public $currency_positions = array();
    public $currency_symbols = array();
    public $is_multiple_allowed = false; //from options
    public $is_fixed_enabled = false; //from options, works if is_multiple_allowed enabled
    public $force_pay_bygeoip_rules = false; //from options, works if is_fixed_enabled enabled
    public $is_geoip_manipulation = true; //from options, works if is_multiple_allowed is NOT enabled
    public $decimal_sep = '.';
    public $thousands_sep = ',';
    public $rate_auto_update = ''; //from options
    public $shop_is_cached = true;
    private $is_first_unique_visit = false;
    public $no_cents = array('JPY', 'TWD'); //recount price without cents always!!
    public $price_num_decimals = 2;
    public $bones = array(
        'reset_in_multiple' => false, //normal is false
        'disable_currency_switching' => false//normal is false. To force the customer to pay in Welcome currency for example, do it by your own logic
    ); //just for some setting for current wp theme adapting - for support only - it is logic hack - be care!!

    public function __construct()
    {
        $this->storage = new WOOCS_STORAGE(get_option('woocs_storage', 'transient'));
        $this->init_no_cents();
        if (!defined('DOING_AJAX'))
        {
//we need it if shop uses cache plugin, in such way prices will be redraw by AJAX
            $this->shop_is_cached = get_option('woocs_shop_is_cached', 0);
        }

//+++
        add_filter('pre_option_woocommerce_price_num_decimals', array($this, 'woocommerce_price_num_decimals'));
        add_filter('woocommerce_add_to_cart_hash', array($this, 'woocommerce_add_to_cart_hash'));
//+++
        $currencies = $this->get_currencies();
        if (!empty($currencies) AND is_array($currencies))
        {
            foreach ($currencies as $key => $currency)
            {
                if ($currency['is_etalon'])
                {
                    $this->default_currency = $key;
                    break;
                }
            }
        }

//+++
        /*
          if (!$this->storage->is_isset('woocs_first_unique_visit'))
          {
          $this->storage->set_val('woocs_first_unique_visit', 0);
          }
         */
        $this->is_geoip_manipulation = get_option('woocs_is_geoip_manipulation', 0);
        $this->is_multiple_allowed = get_option('woocs_is_multiple_allowed', 0);
        if ($this->is_geoip_manipulation)
        {
            $this->is_multiple_allowed = true;
        }
        $this->is_fixed_enabled = get_option('woocs_is_fixed_enabled', 0);
        $this->force_pay_bygeoip_rules = get_option('woocs_force_pay_bygeoip_rules', 0);
        $this->rate_auto_update = get_option('woocs_currencies_rate_auto_update', 'no');
//$this->decimal_sep = wp_specialchars_decode(stripslashes(get_option('woocommerce_price_decimal_sep')), ENT_QUOTES);
//$this->thousands_sep = wp_specialchars_decode(stripslashes(get_option('woocommerce_price_thousand_sep')), ENT_QUOTES);
//+++
        $this->currency_positions = array('left', 'right', 'left_space', 'right_space');
        $this->init_currency_symbols();

//+++
        $is_first_activation = (int) get_option('woocs_first_activation', 0);
        if (!$is_first_activation)
        {
            update_option('woocs_first_activation', 1);
            update_option('woocs_drop_down_view', 'ddslick');
            update_option('woocs_currencies_aggregator', 'yahoo');
            update_option('woocs_welcome_currency', $this->default_currency);
            update_option('woocs_is_multiple_allowed', 0);
            update_option('woocs_is_fixed_enabled', 0);
            update_option('woocs_force_pay_bygeoip_rules', 0);
            update_option('woocs_is_geoip_manipulation', 0);
            update_option('woocs_show_flags', 1);
            update_option('woocs_show_money_signs', 1);
            update_option('woocs_customer_signs', '');
            update_option('woocs_customer_price_format', '');
            update_option('woocs_currencies_rate_auto_update', 'no');
            update_option('woocs_rate_auto_update_email', 0);
            //update_option('woocs_use_curl', 0);
            update_option('woocs_storage', 'transient');
            update_option('woocs_geo_rules', '');
            //update_option('woocs_use_geo_rules', 0);
            update_option('woocs_hide_cents', '');
            update_option('woocs_decimals', array());
            update_option('woocs_price_info', 0);
            update_option('woocs_no_cents', '');
            update_option('woocs_restrike_on_checkout_page', 0);
            update_option('woocs_shop_is_cached', 0);
            update_option('woocs_show_approximate_amount', 0);
            $this->reset_currency();
        }
//+++
//simple checkout itercept
        if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'woocommerce_checkout')
        {
            $_REQUEST['woocommerce-currency-switcher'] = $this->escape($this->storage->get_val('woocs_current_currency'));
            $this->current_currency = $this->escape($this->storage->get_val('woocs_current_currency'));
            $_REQUEST['woocs_in_order_currency'] = $this->current_currency;
        }

//paypal query itercept
        if (isset($_REQUEST['mc_currency']) AND ! empty($_REQUEST['mc_currency']))
        {
            if (array_key_exists($_REQUEST['mc_currency'], $currencies))
            {
                $_REQUEST['woocommerce-currency-switcher'] = $this->escape($_REQUEST['mc_currency']);
            }
        }

//WELCOME USER CURRENCY ACTIVATION
        if ((int) $this->storage->get_val('woocs_first_unique_visit') == 0)
        {
            $this->is_first_unique_visit = true;
            $this->storage->set_val('woocs_current_currency', $this->get_welcome_currency());
        }

//+++
        if (isset($_REQUEST['woocommerce-currency-switcher']))
        {
            if (array_key_exists($_REQUEST['woocommerce-currency-switcher'], $currencies))
            {
                $this->storage->set_val('woocs_current_currency', $this->escape($_REQUEST['woocommerce-currency-switcher']));
            } else
            {
                $this->storage->set_val('woocs_current_currency', $this->default_currency);
            }
        }
//+++
//*** check currency in browser address
        if (isset($_GET['currency']) AND ! empty($_GET['currency']))
        {
            $allow_currency_switching = !$this->bones['disable_currency_switching'];

            //1 issue closing
            if (!get_option('woocs_is_multiple_allowed', 0))
            {
                if (isset($_GET['wc-ajax']) AND ( $_GET['wc-ajax'] == 'get_refreshed_fragments' OR $_GET['wc-ajax'] == 'update_order_review'))
                {
                    if (isset($_SERVER['REQUEST_URI']))
                    {
                        if (substr_count($_SERVER['REQUEST_URI'], '/checkout/'))
                        {
                            $allow_currency_switching = false;
                        }
                    }
                }
            }

            if (array_key_exists(strtoupper($_GET['currency']), $currencies) AND $allow_currency_switching)
            {
                $this->storage->set_val('woocs_current_currency', strtoupper($this->escape($_GET['currency'])));
            }
        }
//+++
        if ($this->storage->is_isset('woocs_current_currency'))
        {
            $this->current_currency = $this->storage->get_val('woocs_current_currency');
        } else
        {
            $this->current_currency = $this->default_currency;
        }
        $this->storage->set_val('woocs_default_currency', $this->default_currency);
//+++
//if we want to be paid in the basic currency - not multiple mode
//old code for woocomerce < 2.4, left for comatibility with old versions of woocommerce
        if (isset($_REQUEST['action']) AND ! $this->is_multiple_allowed)
        {
            if ($_REQUEST['action'] == 'woocommerce_update_order_review')
            {
                $this->reset_currency();
            }
        }

//+++ FILTERS
        add_filter('woocommerce_paypal_args', array($this, 'apply_conversion'));
        add_filter('woocommerce_paypal_supported_currencies', array($this, 'enable_custom_currency'), 9999);
        add_filter('woocommerce_currency_symbol', array($this, 'woocommerce_currency_symbol'), 9999);
        add_filter('woocommerce_currency', array($this, 'get_woocommerce_currency'), 9999);


//main recount hook
        if ($this->is_multiple_allowed)
        {
            add_filter('woocommerce_get_price', array($this, 'raw_woocommerce_price'), 9999, 2);
        } else
        {
            add_filter('raw_woocommerce_price', array($this, 'raw_woocommerce_price'), 9999);
        }


//+++
        if ($this->is_multiple_allowed)
        {
//wp-content\plugins\woocommerce\includes\abstracts\abstract-wc-product.php #795
            /* Alda: Had to removed the filter as it is redundant with the woocommerce_get_price hook */
//I back it 07-01-2016 because of it is really need.
//Comment next 2 hooks if double recount is for sale price http://c2n.me/3sCQFkX
            add_filter('woocommerce_get_regular_price', array($this, 'raw_woocommerce_price'), 9999, 2);
            add_filter('woocommerce_get_sale_price', array($this, 'raw_woocommerce_price'), 9999, 2);
//***
            add_filter('woocommerce_get_variation_regular_price', array($this, 'raw_woocommerce_price'), 9999, 4);
            add_filter('woocommerce_get_variation_sale_price', array($this, 'raw_woocommerce_price'), 9999, 4);
            add_filter('woocommerce_variation_prices', array($this, 'woocommerce_variation_prices'), 9999, 3);
//***
//add_filter('woocommerce_get_variation_price', array($this, 'raw_woocommerce_price'), 9999, 1);
            add_filter('woocommerce_variation_prices_price', array($this, 'woocommerce_variation_prices'), 9999, 3);
            add_filter('woocommerce_variation_prices_regular_price', array($this, 'woocommerce_variation_prices'), 9999, 3);
            add_filter('woocommerce_variation_prices_sale_price', array($this, 'woocommerce_variation_prices'), 9999, 3);
            add_filter('woocommerce_get_variation_prices_hash', array($this, 'woocommerce_get_variation_prices_hash'), 9999, 3);
        }
//***


        add_filter('woocommerce_price_format', array($this, 'woocommerce_price_format'), 9999);
        add_filter('woocommerce_thankyou_order_id', array($this, 'woocommerce_thankyou_order_id'), 9999);
        add_filter('woocommerce_before_resend_order_emails', array($this, 'woocommerce_before_resend_order_emails'), 1);
        add_filter('woocommerce_email_actions', array($this, 'woocommerce_email_actions'), 10);
        add_action('woocommerce_order_status_completed', array($this, 'woocommerce_order_status_completed'), 1);
        add_action('woocommerce_order_status_completed_notification', array($this, 'woocommerce_order_status_completed_notification'), 1);
//add_filter('formatted_woocommerce_price', array($this, 'formatted_woocommerce_price'), 9999);
//if ($this->is_multiple_allowed) - moved inside of function woocommerce_package_rates
        {
            add_filter('woocommerce_package_rates', array($this, 'woocommerce_package_rates'), 9999);
        }
//sometimes woocommerce_product_is_on_sale is works on single page for show OnSale icon for all currencies
//add_filter('woocommerce_product_is_on_sale', array($this, 'woocommerce_product_is_on_sale'), 9999, 2);
//for shop cart
        add_filter('woocommerce_cart_totals_order_total_html', array($this, 'woocommerce_cart_totals_order_total_html'), 9999, 1);
        add_filter('wc_price_args', array($this, 'wc_price_args'), 9999);


//for refreshing mini-cart widget
        add_filter('woocommerce_before_mini_cart', array($this, 'woocommerce_before_mini_cart'), 9999);
        add_filter('woocommerce_after_mini_cart', array($this, 'woocommerce_after_mini_cart'), 9999);


//shipping
        add_filter('woocommerce_shipping_free_shipping_is_available', array($this, 'woocommerce_shipping_free_shipping_is_available'), 99, 2);
//for woo >= 2.6.x
        add_filter('woocommerce_shipping_legacy_free_shipping_is_available', array($this, 'woocommerce_shipping_free_shipping_is_available'), 99, 2);


//add_filter('was_calculate_shipping_costs', array($this, 'was_calculate_shipping_costs'), 99, 4);
//add_filter('woocommerce_update_shipping_method', array($this, 'woocommerce_update_shipping_method'), 1);
//orders view on front
//add_filter('woocommerce_view_order', array($this, 'woocommerce_view_order'), 1);
        add_action('woocommerce_get_order_currency', array($this, 'woocommerce_get_order_currency'), 1, 2);
//add_filter('woocommerce_get_formatted_order_total', array($this, 'woocommerce_get_formatted_order_total'), 1, 2);
//+++
//+++ AJAX ACTIONS
        add_action('wp_ajax_woocs_save_etalon', array($this, 'save_etalon'));
        add_action('wp_ajax_woocs_get_rate', array($this, 'get_rate'));

        add_action('wp_ajax_woocs_convert_currency', array($this, 'woocs_convert_currency'));
        add_action('wp_ajax_nopriv_woocs_convert_currency', array($this, 'woocs_convert_currency'));

        add_action('wp_ajax_woocs_rates_current_currency', array($this, 'woocs_rates_current_currency'));
        add_action('wp_ajax_nopriv_woocs_rates_current_currency', array($this, 'woocs_rates_current_currency'));

        add_action('wp_ajax_woocs_get_products_price_html', array($this, 'woocs_get_products_price_html'));
        add_action('wp_ajax_nopriv_woocs_get_products_price_html', array($this, 'woocs_get_products_price_html'));

        add_action('wp_ajax_woocs_recalculate_order_data', array($this, 'woocs_recalculate_order_data'));
//+++

        add_action('woocommerce_settings_tabs_array', array($this, 'woocommerce_settings_tabs_array'), 9999);
        add_action('woocommerce_settings_tabs_woocs', array($this, 'print_plugin_options'), 9999);

//+++
        add_action('widgets_init', array($this, 'widgets_init'));
        add_action('wp_head', array($this, 'wp_head'), 999);
        add_action('wp_footer', array($this, 'wp_footer'), 9999);
        add_action('body_class', array($this, 'body_class'), 9999);
//***
        add_action('save_post', array($this, 'save_post'), 1);
        add_action('admin_head', array($this, 'admin_head'), 1);
        //add_action('admin_footer-edit.php', array($this, 'admin_footer'), 99);//UNDER DEV
        //add_filter('bulk_actions-edit-shop_order', array($this, 'shop_order_bulk_actions'));//UNDER DEV
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_init', array($this, 'admin_init'), 1);
//price formatting on front ***********
        add_action('woocommerce_price_html', array($this, 'woocommerce_price_html'), 1, 2);

//if ($this->is_multiple_allowed)
        {
            add_action('woocommerce_variable_price_html', array($this, 'woocommerce_price_html'), 1, 2);
            add_action('woocommerce_variable_sale_price_html', array($this, 'woocommerce_price_html'), 1, 2);
            add_action('woocommerce_sale_price_html', array($this, 'woocommerce_price_html'), 1, 2);
            add_action('woocommerce_grouped_price_html', array($this, 'woocommerce_price_html'), 1, 2);
        }



//*** additional
//wpo_wcpdf_order_number is -> compatibility for https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/stats/
//add_action('wpo_wcpdf_order_number', array($this, 'wpo_wcpdf_order_number'), 1);
        add_action('wpo_wcpdf_process_template_order', array($this, 'wpo_wcpdf_process_template_order'), 1, 2);
        add_action('woocs_exchange_value', array($this, 'woocs_exchange_value'), 1);
//add_action('wcml_raw_price_amount', array($this, 'wcml_raw_price_amount'), 1);
//for coupons
        add_filter('woocommerce_coupon_get_discount_amount', array($this, 'woocommerce_coupon_get_discount_amount'), 9999, 5);



//*** comment it!!! It is custom work experiments ....
//add_action('woocommerce_cart_get_taxes', array($this, 'woocommerce_cart_get_taxes'), 11, 2);
//add_filter('woocommerce_cart_total', array($this, 'woocommerce_cart_total'), 11, 1);
//add_filter('woocommerce_after_calculate_totals', array($this, 'woocommerce_after_calculate_totals'), 9999, 1);
//***
//*************************************
        add_shortcode('woocs', array($this, 'woocs_shortcode'));
        add_shortcode('woocs_get_sign_rate', array($this, 'get_sign_rate'));
        add_shortcode('woocs_converter', array($this, 'woocs_converter'));
        add_shortcode('woocs_rates', array($this, 'woocs_rates'));
        add_shortcode('woocs_show_current_currency', array($this, 'woocs_show_current_currency'));
        add_shortcode('woocs_show_custom_price', array($this, 'woocs_show_custom_price'));
        add_shortcode('woocs_geo_hello', array($this, 'woocs_geo_hello'));

        if ($this->is_multiple_allowed)
        {
            add_action('the_post', array($this, 'the_post'), 1);
            add_action('load-post.php', array($this, 'admin_action_post'), 1);
        }

//+++
        add_action('woocs_update_rates_wpcron', array($this, 'rate_auto_update'), 10);
        $this->cron = new PN_WP_CRON_WOOCS('woocs_rates_wpcron');
        $this->wp_cron_period = (int) $this->get_woocs_cron_schedules($this->rate_auto_update);
        $this->make_rates_auto_update();
//***
        if ($this->is_fixed_enabled OR $this->is_geoip_manipulation)
        {
            $this->fixed = new WOOCS_FIXED();
        }
    }

//for normal shippng update if to change currency
    public function woocommerce_add_to_cart_hash($hash)
    {
        return "";
    }

    public function init()
    {
        if (!class_exists('WooCommerce'))
        {
            return;
        }

//global $wp;
        wp_enqueue_script('jquery');

//+++
        load_plugin_textdomain('woocommerce-currency-switcher', false, dirname(plugin_basename(__FILE__)) . '/languages');


//filters
        add_filter('plugin_action_links_' . WOOCS_PLUGIN_NAME, array($this, 'plugin_action_links'));
        add_filter('woocommerce_currency_symbol', array($this, 'woocommerce_currency_symbol'), 9999);

//***
//if we use GeoLocation
//$this->init_geo_currency();
//set default cyrrency for wp-admin of the site
        if (is_admin() AND ! is_ajax())
        {
            $this->current_currency = $this->default_currency;
        } else
        {
//if we are in the a product backend and loading its variations
            if (is_ajax() AND ( isset($_REQUEST['action']) AND $_REQUEST['action'] == 'woocommerce_load_variations'))
            {
                $this->current_currency = $this->default_currency;
            }
        }

        if (is_ajax())
        {
            $actions = false;
//code for manual order adding
            if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'woocommerce_add_order_item')
            {
                $actions = true;
            }

            if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'woocommerce_save_order_items')
            {
                $actions = true;
            }

            if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'woocommerce_calc_line_taxes')
            {
                $actions = true;
            }
//***
            if ($actions AND current_user_can('edit_shop_orders'))
            {
                $this->current_currency = $this->default_currency;
//check if is order has installed currency
                $currency = get_post_meta($_REQUEST['order_id'], '_order_currency', TRUE);
                if (!empty($currency))
                {
                    $this->current_currency = $currency;
                }
            }
        }

        if (is_ajax() AND isset($_REQUEST['action'])
                AND $_REQUEST['action'] == 'woocommerce_mark_order_status'
                AND isset($_REQUEST['status']) AND $_REQUEST['status'] == 'completed'
                AND isset($_REQUEST['order_id'])
        )
        {
            $currency = get_post_meta($_REQUEST['order_id'], '_order_currency', true);
            if (!empty($currency))
            {
                $_REQUEST['woocs_in_order_currency'] = $currency;
                $this->current_currency = $currency;
            }
        }



//if we want to be paid in the basic currency - not multiple mode
        if (!$this->is_multiple_allowed)
        {
            if (isset($_GET['wc-ajax']) AND $_GET['wc-ajax'] == 'update_order_review')
            {
                $this->reset_currency();
            }


//compatibility for WC_Gateway_PayPal_Express_AngellEYE
            if (isset($_GET['wc-api']) AND isset($_GET['pp_action']) AND isset($_GET['use_paypal_credit']))
            {
                if ($_GET['pp_action'] == 'expresscheckout')
                {
                    $this->reset_currency();
                }
            }
        }

//***
//Show Approx. data info
        if ($this->is_use_geo_rules() AND get_option('woocs_show_approximate_amount', 0) AND ( isset(WC()->cart)/* AND WC()->cart->subtotal > 0 */))
        {

            add_filter('woocommerce_cart_total', array($this, 'woocommerce_cart_total'), 9999, 1);

            add_filter('woocommerce_cart_item_price', array($this, 'woocommerce_cart_item_price'), 9999, 3);
            add_filter('woocommerce_cart_item_subtotal', array($this, 'woocommerce_cart_item_subtotal'), 9999, 3);
            add_filter('woocommerce_cart_subtotal', array($this, 'woocommerce_cart_subtotal'), 9999, 3);

            add_filter('woocommerce_cart_totals_taxes_total_html', array($this, 'woocommerce_cart_totals_taxes_total_html'), 9999, 1);
            add_filter('woocommerce_cart_tax_totals', array($this, 'woocommerce_cart_tax_totals'), 9999, 2);
            add_filter('woocommerce_cart_shipping_method_full_label', array($this, 'woocommerce_cart_shipping_method_full_label'), 9999, 2);
        }

//***
    }

    public function make_rates_auto_update($reset = false)
    {
        if ($this->rate_auto_update != 'no' AND ! empty($this->rate_auto_update))
        {
            if ($this->wp_cron_period)
            {
                if ($reset)
                {
                    $this->cron->reset($this->cron_hook, $this->wp_cron_period);
                }

                $this->woocs_wpcron_init();
            }
        }
    }

    public function woocs_wpcron_init($remove = false)
    {
        if ($remove)
        {
            $this->cron->remove($this->cron_hook);
            return;
        }

        if ($this->wp_cron_period)
        {
            if (!$this->cron->is_attached($this->cron_hook, $this->wp_cron_period))
            {
                $this->cron->attach($this->cron_hook, time(), $this->wp_cron_period);
            }

            $this->cron->process();
        }
    }

    public function get_woocs_cron_schedules($key = '')
    {
        $schedules = array(
            'min15' => 15 * MINUTE_IN_SECONDS,
            'min30' => 30 * MINUTE_IN_SECONDS,
            'min45' => 45 * MINUTE_IN_SECONDS,
            'hourly' => HOUR_IN_SECONDS,
            'twicedaily' => HOUR_IN_SECONDS * 12,
            'daily' => DAY_IN_SECONDS,
            'week' => WEEK_IN_SECONDS,
            'month' => WEEK_IN_SECONDS * 4,
            'min1' => MINUTE_IN_SECONDS,
        );
//print_r($schedules);
        if (!empty($key) AND isset($schedules[$key]))
        {
            return (int) $schedules[$key];
        } else
        {
            return NULL;
        }

        return $schedules;
    }

    public function get_currency_price_num_decimals($currency, $default = 2)
    {
        $currencies = $this->get_currencies();
        if (isset($currencies[$currency]['decimals']))
        {
            $default = $currencies[$currency]['decimals'];
        }

        return $default;
    }

    public function woocommerce_price_num_decimals($default)
    {
        $this->price_num_decimals = $this->get_currency_price_num_decimals($this->current_currency);

        return $this->price_num_decimals;
    }

    public function body_class($classes)
    {
        $classes[] = 'currency-' . strtolower($this->current_currency);
        return $classes;
    }

    public function init_currency_symbols()
    {
//includes/wc-core-functions.php #217
        $this->currency_symbols = array(
            '&#36;', '&euro;', '&yen;', '&#1088;&#1091;&#1073;.', '&#1075;&#1088;&#1085;.', '&#8361;',
            '&#84;&#76;', 'د.إ', '&#2547;', '&#82;&#36;', '&#1083;&#1074;.',
            '&#107;&#114;', '&#82;', '&#75;&#269;', '&#82;&#77;', 'kr.', '&#70;&#116;',
            'Rp', 'Rs', 'Kr.', '&#8362;', '&#8369;', '&#122;&#322;', '&#107;&#114;',
            '&#67;&#72;&#70;', '&#78;&#84;&#36;', '&#3647;', '&pound;', 'lei', '&#8363;',
            '&#8358;', 'Kn', '-----'
        );

        $this->currency_symbols = apply_filters('woocs_currency_symbols', array_merge($this->currency_symbols, $this->get_customer_signs()));
    }

    private function init_no_cents()
    {
        $no_cents = get_option('woocs_no_cents', '');
        $currencies = $this->get_currencies();
//***
        if (!empty($currencies) AND is_array($currencies))
        {
            $currencies = array_keys($currencies);
            $currencies = array_map('strtolower', $currencies);
            if (!empty($no_cents))
            {
                $no_cents = explode(',', $no_cents);
                if (!empty($no_cents) AND is_array($no_cents))
                {
                    foreach ($no_cents as $value)
                    {
                        if (in_array(strtolower($value), $currencies))
                        {
                            $this->no_cents[] = $value;
                        }
                    }
                }
            }
        }

        return $this->no_cents;
    }

//for auto rate update sheduler
    public function rate_auto_update()
    {
        $currencies = $this->get_currencies();
//***
        $_REQUEST['no_ajax'] = TRUE;
        $request = array();
        foreach ($currencies as $key => $currency)
        {
            if ($currency['is_etalon'] == 1)
            {
                continue;
            }
            $_REQUEST['currency_name'] = $currency['name'];
            $request[$key] = (float) $this->get_rate();
        }
//*** checking and assigning data
        foreach ($currencies as $key => $currency)
        {
            if ($currency['is_etalon'] == 1)
            {
                continue;
            }
            if (isset($request[$key]) AND ! empty($request[$key]) AND $request[$key] > 0)
            {
                $currencies[$key]['rate'] = $request[$key];
            }
        }

        //***
        static $email_is_sent = false;
        if (isset($_REQUEST['woocs_cron_running']) AND ! $email_is_sent)
        {
            if (get_option('woocs_rate_auto_update_email', 0))
            {
                $message = sprintf(__('<strong>Base currency of the site is: %s</strong>', 'woocommerce-currency-switcher'), $this->default_currency);
                $message.='<br /><br /><ul>';
                foreach ($currencies as $code => $curr)
                {
                    if ($code == $this->default_currency)
                    {
                        continue;
                    }

                    $message.='<li><b>' . $code . '</b>: <i>' . $curr['rate'] . '</i><br /><br /></li>';
                }
                $message.='</ul>';
                //***
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";
                mail(get_bloginfo('admin_email'), 'Currency rates updated on ' . get_bloginfo('name'), $message, $headers);
            }

            $email_is_sent = true;
        }
        //***

        update_option('woocs', $currencies);
    }

    public function init_geo_currency()
    {
        $done = false;
        $pd = WC_Geolocation::geolocate_ip();
        $this->storage->set_val('woocs_user_country', $pd['country']);
        //***
        if ($this->is_use_geo_rules())
        {
            $rules = $this->get_geo_rules();

            $is_allowed = $this->is_first_unique_visit AND function_exists('wc_clean') AND function_exists('wp_validate_redirect');
            if ($is_allowed)
            {

                if (isset($pd['country']) AND ! empty($pd['country']))
                {

                    if (!empty($rules))
                    {
                        foreach ($rules as $curr => $countries)
                        {
                            if (!empty($countries) AND is_array($countries))
                            {
                                foreach ($countries as $country)
                                {
                                    if ($country == $pd['country'])
                                    {
                                        $this->set_currency($curr);
                                        $done = true;
                                        break(2);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->storage->set_val('woocs_first_unique_visit', 1);

        return $done;
    }

    public function get_currency_by_country($country_code)
    {
        $rules = $this->get_geo_rules();
        if (!empty($rules))
        {
            foreach ($rules as $currency => $countries)
            {
                if (!empty($countries) AND is_array($countries))
                {
                    foreach ($countries as $country)
                    {
                        if ($country == $country_code)
                        {
                            return $currency;
                        }
                    }
                }
            }
        }

        return '';
    }

    /**
     * Show action links on the plugin screen
     */
    public function plugin_action_links($links)
    {
        return array_merge(array(
            '<a href="' . admin_url('admin.php?page=wc-settings&tab=woocs') . '">' . __('Settings', 'woocommerce-currency-switcher') . '</a>',
            '<a target="_blank" href="' . esc_url('http://currency-switcher.com/documentation/') . '">' . __('Documentation', 'woocommerce-currency-switcher') . '</a>'
                ), $links);

        return $links;
    }

    public function widgets_init()
    {
        require_once WOOCS_PATH . 'classes/widgets/widget-woocs-selector.php';
        require_once WOOCS_PATH . 'classes/widgets/widget-currency-rates.php';
        require_once WOOCS_PATH . 'classes/widgets/widget-currency-converter.php';
        register_widget('WOOCS_SELECTOR');
        register_widget('WOOCS_RATES');
        register_widget('WOOCS_CONVERTER');
//overide woocs slider js
        wp_register_script('wc-price-slider', WOOCS_LINK . 'js/price-slider.js', array('jquery', 'jquery-ui-slider'));
    }

    public function admin_enqueue_scripts()
    {
        if (isset($_GET['tab']) AND $_GET['tab'] == 'woocs')
        {
            wp_enqueue_style('woocommerce-currency-switcher-options', WOOCS_LINK . 'css/options.css');
        }
    }

    public function admin_head()
    {
        if (isset($_GET['woocs_reset']))
        {
            delete_option('woocs');
        }
//wp_enqueue_scripts('jquery');
        if (isset($_GET['page']) AND isset($_GET['tab']))
        {
            if ($_GET['page'] == 'wc-settings'/* AND $_GET['tab'] == 'woocs' */)
            {
                wp_enqueue_script('woocs-admin', WOOCS_LINK . 'js/admin.js', array('jquery'));
            }
        }
    }

    public function admin_footer()
    {
        global $post_type;

        if ('shop_order' == $post_type)
        {
            ?>
            <script type="text/javascript">
                jQuery(function () {
                    jQuery('<option>').val('woocs_convert_to_default').text('<?php _e('WOOCS - recalculate orders', 'woocommerce-currency-switcher') ?>').appendTo('select[name="action"]');
                    jQuery('<option>').val('woocs_convert_to_default').text('<?php _e('WOOCS -  recalculate orders', 'woocommerce-currency-switcher') ?>').appendTo('select[name="action2"]');
                });
            </script>
            <?php
        }
    }

    /**
     * Process the new bulk actions for changing order currency to default.
     */
    public function shop_order_bulk_actions()
    {
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();

        //print_r($action);exit;
    }

    public function admin_init()
    {
        if ($this->is_multiple_allowed)
        {
            add_meta_box('woocs_order_metabox', __('WOOCS Order Info', 'woocommerce-currency-switcher'), array($this, 'woocs_order_metabox'), 'shop_order', 'side', 'default');
        }
    }

//for orders hook
    public function save_post($order_id)
    {
        if (current_user_can('edit_shop_orders'))
        {
            if (!empty($_POST))
            {
                global $post;
                if (is_object($post))
                {
                    if (($post->post_type == 'shop_order' || $post->post_type == 'shop_subscription') AND isset($_POST['woocs_order_currency']))
                    {
                        $currencies = $this->get_currencies();
                        $currencies_keys = array_keys($currencies);
                        $currency = $this->escape($_POST['woocs_order_currency']);
                        if (in_array($currency, $currencies_keys))
                        {

//changing order currency
                            update_post_meta($order_id, '_order_currency', $currency);

                            update_post_meta($order_id, '_woocs_order_rate', $currencies[$currency]['rate']);
                            wc_add_order_item_meta($order_id, '_woocs_order_rate', $currencies[$currency]['rate'], true);

                            update_post_meta($order_id, '_woocs_order_base_currency', $this->default_currency);
                            wc_add_order_item_meta($order_id, '_woocs_order_base_currency', $this->default_currency, true);

                            update_post_meta($order_id, '_woocs_order_currency_changed_mannualy', time());
                            wc_add_order_item_meta($order_id, '_woocs_order_currency_changed_mannualy', time(), true);
                        }
                    }
                }
            }
        }
    }

//for orders hook
    public function the_post($post)
    {
        if (is_object($post) AND $post->post_type == 'shop_order')
        {
            $currency = get_post_meta($post->ID, '_order_currency', true);
            if (!empty($currency))
            {
                $_REQUEST['woocs_in_order_currency'] = $currency;
                $this->current_currency = $currency;
            }
        }

        return $post;
    }

//for orders hook
    public function admin_action_post()
    {
        if (isset($_GET['post']))
        {
            $post_id = $_GET['post'];
            $post = get_post($post_id);
            if (is_object($post) AND $post->post_type == 'shop_order')
            {
                $currency = get_post_meta($post->ID, '_order_currency', true);
                if (!empty($currency))
                {
                    $_REQUEST['woocs_in_order_currency'] = $currency;
                    $this->current_currency = $currency;
                }
            }
        }
    }

    public function woocs_order_metabox($post)
    {
        $data = array();
        $data['post'] = $post;
        $data['order'] = new WC_Order($post->ID);
        echo $this->render_html(WOOCS_PATH . 'views/woocs_order_metabox.php', $data);
    }

    public function wp_head()
    {
//*** if the site is visited for the first time lets execute geo ip conditions
        $this->init_geo_currency();
//***
        wp_enqueue_script('jquery');
        wp_enqueue_script('wc-price-slider');
        $currencies = $this->get_currencies();
        ?>
        <script type="text/javascript">
            var woocs_is_mobile = <?php echo (int) wp_is_mobile() ?>;
            var woocs_drop_down_view = "<?php echo $this->get_drop_down_view(); ?>";
            var woocs_current_currency = <?php echo json_encode((isset($currencies[$this->current_currency]) ? $currencies[$this->current_currency] : $currencies[$this->default_currency])) ?>;
            var woocs_default_currency = <?php echo json_encode($currencies[$this->default_currency]) ?>;
            var woocs_array_of_get = '{}';
        <?php if (!empty($_GET)): ?>
            <?php
//sanitization of $_GET array
            $sanitized_get_array = array();
            foreach ($_GET as $key => $value)
            {
                $sanitized_get_array[$this->escape($key)] = $this->escape($value);
            }
            ?>
                woocs_array_of_get = '<?php echo str_replace("'", "", json_encode($sanitized_get_array)); ?>';
        <?php endif; ?>

            woocs_array_no_cents = '<?php echo json_encode($this->no_cents); ?>';

            var woocs_ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            var woocs_lang_loading = "<?php _e('loading', 'woocommerce-currency-switcher') ?>";
            var woocs_shop_is_cached =<?php echo (int) $this->shop_is_cached ?>;
        </script>
        <?php
        if ($this->get_drop_down_view() == 'ddslick')
        {
            wp_enqueue_script('jquery.ddslick.min', WOOCS_LINK . 'js/jquery.ddslick.min.js', array('jquery'));
        }

        if ($this->get_drop_down_view() == 'chosen' OR $this->get_drop_down_view() == 'chosen_dark')
        {
            wp_enqueue_script('chosen-drop-down', WOOCS_LINK . 'js/chosen/chosen.jquery.min.js', array('jquery'));
            wp_enqueue_style('chosen-drop-down', WOOCS_LINK . 'js/chosen/chosen.min.css');
//dark chosen
            if ($this->get_drop_down_view() == 'chosen_dark')
            {
                wp_enqueue_style('chosen-drop-down-dark', WOOCS_LINK . 'js/chosen/chosen-dark.css');
            }
        }

        if ($this->get_drop_down_view() == 'wselect')
        {
            wp_enqueue_script('woocs_wselect', WOOCS_LINK . 'js/wselect/wSelect.min.js', array('jquery'));
            wp_enqueue_style('woocs_wselect', WOOCS_LINK . 'js/wselect/wSelect.css');
        }

//+++
        wp_enqueue_style('woocommerce-currency-switcher', WOOCS_LINK . 'css/front.css');
        wp_enqueue_script('woocommerce-currency-switcher', WOOCS_LINK . 'js/front.js', array('jquery'));
//+++
//trick for refreshing header cart after currency changing - code left just for history
        if (isset($_GET['currency']))
        {
//wp-content\plugins\woocommerce\includes\class-wc-cart.php
//apply_filters('woocommerce_update_cart_action_cart_updated', true);
//$this->current_currency = $_GET['currency'];
//$_POST['update_cart'] = 1;
//WC_Form_Handler::update_cart_action();
//private function set_cart_cookies
//wc_setcookie('woocommerce_items_in_cart', 0, time() - HOUR_IN_SECONDS);
//wc_setcookie('woocommerce_cart_hash', '', time() - HOUR_IN_SECONDS);
//do_action('woocommerce_cart_reset', WC()->cart, true);
//do_action('woocommerce_calculate_totals', WC()->cart);
        }


//if customer paying pending order from the front
//checkout/order-pay/1044/?pay_for_order=true&key=order_55b764a4b7990
        if (isset($_GET['pay_for_order']) AND is_checkout_pay_page())
        {
            if ($_GET['pay_for_order'] == 'true' AND isset($_GET['key']))
            {
                $order_id = wc_get_order_id_by_order_key($_GET['key']);
                $currency = get_post_meta($order_id, '_order_currency', TRUE);
                $this->set_currency($currency);
            }
        }

//+++
//if we want to be paid in the basic currency - not multiple mode and in is_geoip_manipulation
        if (!$this->is_multiple_allowed OR ( $this->is_geoip_manipulation AND ! $this->is_multiple_allowed))
        {
            if (is_checkout() OR is_checkout_pay_page())
            {
                $this->reset_currency();
            }
        }


        //logic hack for some cases when shipping for example is wrong in
        //non multiple mode but customer doesn work allow pay in user selected currency
        if ($this->is_multiple_allowed)
        {
            if ((is_checkout() OR is_checkout_pay_page()) AND $this->bones['reset_in_multiple'])
            {
                $this->reset_currency();
            }
        }



        if ($this->force_pay_bygeoip_rules)
        {
            if (is_checkout() OR is_checkout_pay_page())
            {
                $this->force_pay_bygeoip_rules();
            }
        }
    }

    public function woocommerce_settings_tabs_array($tabs)
    {
        $tabs['woocs'] = __('Currency', 'woocommerce-currency-switcher');
        return $tabs;
    }

    public function print_plugin_options()
    {
        if (isset($_POST['woocs_name']) AND ! empty($_POST['woocs_name']))
        {
            $result = array();
            update_option('woocs_drop_down_view', $this->escape($_POST['woocs_drop_down_view']));
            update_option('woocs_currencies_aggregator', $this->escape($_POST['woocs_currencies_aggregator']));
            update_option('woocs_welcome_currency', $this->escape($_POST['woocs_welcome_currency']));
//***
            update_option('woocs_is_multiple_allowed', (int) $_POST['woocs_is_multiple_allowed']);
            update_option('woocs_is_geoip_manipulation', (int) $_POST['woocs_is_geoip_manipulation']);
            if ((int) $_POST['woocs_is_multiple_allowed'])
            {
                update_option('woocs_is_fixed_enabled', (int) $_POST['woocs_is_fixed_enabled']);
                if ((int) $_POST['woocs_is_fixed_enabled'])
                {
                    update_option('woocs_force_pay_bygeoip_rules', (int) $_POST['woocs_force_pay_bygeoip_rules']);
                } else
                {
                    update_option('woocs_force_pay_bygeoip_rules', 0);
                }
            } else
            {
                update_option('woocs_is_fixed_enabled', 0);
                update_option('woocs_force_pay_bygeoip_rules', 0);
            }
//***
            update_option('woocs_customer_signs', $this->escape($_POST['woocs_customer_signs']));
            update_option('woocs_customer_price_format', $this->escape($_POST['woocs_customer_price_format']));
            update_option('woocs_currencies_rate_auto_update', $this->escape($_POST['woocs_currencies_rate_auto_update']));
            update_option('woocs_rate_auto_update_email', (int) $_POST['woocs_rate_auto_update_email']);
            update_option('woocs_show_flags', (int) $_POST['woocs_show_flags']);
            update_option('woocs_show_money_signs', (int) $_POST['woocs_show_money_signs']);
            //update_option('woocs_use_curl', (int) $_POST['woocs_use_curl']);
            update_option('woocs_storage', $this->escape($_POST['woocs_storage']));
            if (isset($_POST['woocs_geo_rules']))
            {
                $woocs_geo_rules = array();
                if (!empty($_POST['woocs_geo_rules']))
                {
                    foreach ($_POST['woocs_geo_rules'] as $curr_key => $countries)
                    {
                        $woocs_geo_rules[$this->escape($curr_key)] = array();
                        if (!empty($countries))
                        {
                            foreach ($countries as $curr)
                            {
                                $woocs_geo_rules[$this->escape($curr_key)][] = $this->escape($curr);
                            }
                        }
                    }
                }
                update_option('woocs_geo_rules', $woocs_geo_rules);
            } else
            {
                update_option('woocs_geo_rules', '');
            }
            //update_option('woocs_use_geo_rules', (int) $_POST['woocs_use_geo_rules']);
            update_option('woocs_hide_cents', (int) $_POST['woocs_hide_cents']);
            update_option('woocs_price_info', (int) $_POST['woocs_price_info']);
            update_option('woocs_no_cents', $this->escape($_POST['woocs_no_cents']));
            update_option('woocs_restrike_on_checkout_page', (int) $_POST['woocs_restrike_on_checkout_page']);
            update_option('woocs_show_approximate_amount', (int) $_POST['woocs_show_approximate_amount']);
            update_option('woocs_shop_is_cached', (int) $_POST['woocs_shop_is_cached']);
//***
            $cc = '';
            foreach ($_POST['woocs_name'] as $key => $name)
            {
                if (!empty($name))
                {
                    $symbol = $this->escape($_POST['woocs_symbol'][$key]); //md5 encoded

                    foreach ($this->currency_symbols as $s)
                    {
                        if (md5($s) == $symbol)
                        {
                            $symbol = $s;
                            break;
                        }
                    }

                    $result[strtoupper($name)] = array(
                        'name' => $name,
                        'rate' => floatval($_POST['woocs_rate'][$key]),
                        'symbol' => $symbol,
                        'position' => (in_array($this->escape($_POST['woocs_position'][$key]), $this->currency_positions) ? $this->escape($_POST['woocs_position'][$key]) : $this->currency_positions[0]),
                        'is_etalon' => (int) $_POST['woocs_is_etalon'][$key],
                        'hide_cents' => (int) @$_POST['woocs_hide_cents'][$key],
                        'decimals' => (int) @$_POST['woocs_decimals'][$key],
                        'description' => $this->escape($_POST['woocs_description'][$key]),
                        'flag' => $this->escape($_POST['woocs_flag'][$key]),
                    );

                    if ($_POST['woocs_rate'][$key] == 1)
                    {
                        $cc = $name;
                    }
                }
            }

            update_option('woocs', $result);
            if (!empty($cc))
            {
//set default currency for all woocommerce system
                update_option('woocommerce_currency', $cc);
            }
            $this->init_currency_symbols();
//***
            $this->rate_auto_update = $this->escape($_POST['woocs_currencies_rate_auto_update']);
            $this->wp_cron_period = $this->get_woocs_cron_schedules($this->rate_auto_update);
            $this->woocs_wpcron_init(true);
            $this->make_rates_auto_update(true);
//$this->reset_currency();
        }
//+++
        //wp_enqueue_style('open_sans_font', 'https://fonts.googleapis.com/css?family=Open+Sans');
        wp_enqueue_script('media-upload');
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('woocommerce-currency-switcher-options', WOOCS_LINK . 'js/options.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));

        $args = array();
        $args['currencies'] = $this->get_currencies();
        if ($this->is_use_geo_rules())
        {
            $args['geo_rules'] = $this->get_geo_rules();
        }
        echo $this->render_html(WOOCS_PATH . 'views/plugin_options.php', $args);
    }

    public function get_drop_down_view()
    {
        return apply_filters('woocs_drop_down_view', get_option('woocs_drop_down_view', 'ddslick'));
//return get_option('woocs_drop_down_view', 'ddslick');
    }

    public function get_currencies()
    {
//static $currencies = array();
//$_POST['woocs_name'] - reinit after saving
        /*
          if (!empty($currencies) AND ! isset($_POST['woocs_name']))
          {
          return $currencies;
          }
         *
         */

        $default = array(
            'USD' => array(
                'name' => 'USD',
                'rate' => 1,
                'symbol' => '&#36;',
                'position' => 'right',
                'is_etalon' => 1,
                'description' => 'USA dollar',
                'hide_cents' => 0,
                'flag' => '',
            ),
            'EUR' => array(
                'name' => 'EUR',
                'rate' => 0.89,
                'symbol' => '&euro;',
                'position' => 'left_space',
                'is_etalon' => 0,
                'description' => 'European Euro',
                'hide_cents' => 0,
                'flag' => '',
            )
        );

        $currencies = get_option('woocs', $default);
        $currencies = apply_filters('woocs_currency_data_manipulation', $currencies);

        /*
          //http://currency-switcher.com/how-to-manipulate-with-currencies-rates/
          foreach ($currencies as $key => $value)
          {
          if($key == 'EUR'){
          $currencies[$key]['rate']=$currencies[$key]['rate']+0.025;
          break;
          }
          }
         */

        if (empty($currencies) OR ! is_array($currencies))
        {
            $currencies = $default;
        }


        return $currencies;
    }

    public function get_geo_rules()
    {
        return get_option('woocs_geo_rules', array());
    }

    public function is_use_geo_rules()
    {
        //$is = get_option('woocs_use_geo_rules', 0);
        $is = true; //from v.2.1.8 always enabled
        $isset = class_exists('WC_Geolocation');

        return ($is && $isset);
    }

//need for paypal currencies supporting
    public function enable_custom_currency($currency_array)
    {
//https://developer.paypal.com/docs/classic/api/currency_codes/
//includes\gateways\paypal\class-wc-gateway-paypal.php => woo func
//function is_valid_for_use() =>Check if this gateway is enabled and available in the user's country
        $currency_array[] = 'usd';
        $currency_array[] = 'aud';
        $currency_array[] = 'brl';
        $currency_array[] = 'cad';
        $currency_array[] = 'czk';
        $currency_array[] = 'dkk';
        $currency_array[] = 'eur';
        $currency_array[] = 'hkd';
        $currency_array[] = 'huf';
        $currency_array[] = 'ils';
        $currency_array[] = 'jpy';
        $currency_array[] = 'myr';
        $currency_array[] = 'mxn';
        $currency_array[] = 'nok';
        $currency_array[] = 'nzd';
        $currency_array[] = 'php';
        $currency_array[] = 'pln';
        $currency_array[] = 'gbp';
        $currency_array[] = 'rub';
        $currency_array[] = 'sgd';
        $currency_array[] = 'sek';
        $currency_array[] = 'chf';
        $currency_array[] = 'twd';
        $currency_array[] = 'thb';
        $currency_array[] = 'try';
        $currency_array = array_map('strtoupper', $currency_array);
        return $currency_array;
    }

    public function woocommerce_currency_symbol($currency_symbol)
    {

        $currencies = $this->get_currencies();

        if (!isset($currencies[$this->current_currency]))
        {
            $this->reset_currency();
        }

        return $currencies[$this->current_currency]['symbol'];
    }

    public function get_woocommerce_currency()
    {
        return $this->current_currency;
    }

    //work in for multiple mode only from woocommerce 2.4
    //wp-content\plugins\woocommerce\includes\class-wc-product-variable.php #303
    public function woocommerce_variation_prices($prices_array)
    {


        $current_currency = $this->current_currency;

        //***

        if (in_array($current_currency, $this->no_cents)/* OR $currencies[$this->current_currency]['hide_cents'] == 1 */)
        {
            $precision = 0;
        } else
        {
            if ($current_currency != $this->default_currency)
            {
                $precision = $this->get_currency_price_num_decimals($current_currency, $this->price_num_decimals);
            } else
            {
                $precision = $this->get_currency_price_num_decimals($this->default_currency, $this->price_num_decimals);
            }
        }

        //***

        if (!empty($prices_array) AND is_array($prices_array))
        {
            foreach ($prices_array as $key => $values)
            {
                foreach ($values as $product_id => $price)
                {

                    $type = 'regular';
                    if ($key === 'sale_price' OR $key === 'price')
                    {
                        $type = 'sale';
                    }

                    $is_price_custom = false;

                    if ($this->is_fixed_enabled AND $this->fixed->is_exists($product_id, $current_currency, $type))
                    {
                        $tmp = number_format(floatval($this->fixed->get_value($product_id, $current_currency, $type)), $precision, $this->decimal_sep, '');

                        if ((int) $tmp !== -1)
                        {
                            $prices_array[$key][$product_id] = $tmp;
                            $is_price_custom = true;
                        }
                    }


                    if ($this->is_geoip_manipulation AND ! $is_price_custom)
                    {
                        $product = (object) array('id' => $product_id);
                        $price = $this->_get_geo_defined_price($product, $price);
                    }


                    if (!$is_price_custom)
                    {
                        if ($current_currency != $this->default_currency)
                        {
                            //$prices_array[$key][$product_id] = apply_filters('woocs_woocommerce_variation_prices', $this->woocs_exchange_value(floatval($price)));
                            $prices_array[$key][$product_id] = apply_filters('woocs_woocommerce_variation_prices', number_format(floatval($this->woocs_exchange_value(floatval($price))), $precision, $this->decimal_sep, ''));
                        }
                    }
                }
            }
        }





        //***
        /*
          if (!$is_price_custom)
          {
          if ($this->current_currency != $this->default_currency)
          {
          if (!empty($prices_array) AND is_array($prices_array))
          {
          foreach ($prices_array as $key => $values)
          {
          foreach ($values as $k => $v)
          {
          //$prices_array[$key][$k] = $this->woocs_exchange_value(floatval($v));

          }
          }
          }
          }
          }
         */
        //*** lets sort arrays by values to avoid wrong price displaying on the front
        if (!empty($prices_array) AND is_array($prices_array))
        {
            foreach ($prices_array as $key => $arrvals)
            {
                asort($arrvals);
                $prices_array[$key] = $arrvals;
            }
        }
        //***
        //echo '<pre>' . print_r($prices_array, true) . '</pre>';
        return $prices_array;
    }

    public function woocommerce_get_variation_prices_hash($price_hash, $product, $display)
    {
        //***
    }

    public function raw_woocommerce_price($price, $product = NULL, $min_or_max = NULL)
    {

        if (isset($_REQUEST['woocs_block_price_hook']))
        {
            return $price;
        }

        //to avoid 'Free!' text if price is zero - buy button will be hidden
        if (empty($price))
        {
            //return "";//doesn work correctly for the fixed prices
        }

//***

        $currencies = $this->get_currencies();


        if (in_array($this->current_currency, $this->no_cents)/* OR $currencies[$this->current_currency]['hide_cents'] == 1 */)
        {
            $precision = 0;
        } else
        {
            if ($this->current_currency != $this->default_currency)
            {
                $precision = $this->get_currency_price_num_decimals($this->current_currency, $this->price_num_decimals);
            } else
            {
                $precision = $this->get_currency_price_num_decimals($this->default_currency, $this->price_num_decimals);
            }
        }

//***
        $is_price_custom = false;
        if ($this->is_fixed_enabled)
        {
            if ($this->is_multiple_allowed AND $product !== NULL AND is_object($product))
            {

                if (isset($product->variation_id))
                {
                    $tmp = $this->_get_product_fixed_price($product->variation_id, $price, $precision);
                } else
                {
                    $tmp = $this->_get_product_fixed_price($product->id, $price, $precision);
                }

                if ((int) $tmp !== -1)
                {
                    $price = $tmp;
                    $is_price_custom = true;
                }
            }
        }
        //***
        if ($this->is_geoip_manipulation AND ! $is_price_custom)
        {
            if ($product !== NULL)
            {
                if (isset($product->variation_id))
                {
                    $product_emulator = (object) array('id' => $product->variation_id);
                } else
                {
                    $product_emulator = (object) array('id' => $product->id);
                }

                $price = $this->_get_geo_defined_price($product_emulator, $price);
            }
        }

        //***

        if (!$is_price_custom)
        {
            if ($this->current_currency != $this->default_currency)
            {
                //Edited this line to set default convertion of currency
                if (isset($currencies[$this->current_currency]) AND $currencies[$this->current_currency] != NULL)
                {
                    $price = number_format(floatval($price * $currencies[$this->current_currency]['rate']), $precision, $this->decimal_sep, '');
                } else
                {
                    $price = number_format(floatval($price * $currencies[$this->default_currency]['rate']), $precision, $this->decimal_sep, '');
                }
            }
        }


        return apply_filters('woocs_raw_woocommerce_price', $price);

//some hints for price rounding
//http://stackoverflow.com/questions/11692770/rounding-to-nearest-50-cents
//$price = round($price * 2, 0) / 2;
//return round ( $price , 0 ,PHP_ROUND_HALF_EVEN );
//return number_format ($price, $this->price_num_decimals, $this->decimal_sep, $this->thousands_sep);
    }

    //for tooltip
    private function _get_min_max_variation_prices($product, $current_currency)
    {
        $currencies = $this->get_currencies();
        $prices_array = $product->get_variation_prices();
        $var_products_ids = array_keys($prices_array['regular_price']);
        $prices_array = array();
        if (!empty($var_products_ids))
        {
            foreach ($var_products_ids as $var_prod_id)
            {

                $is_price_custom = false;
                $regular_price = (float) get_post_meta($var_prod_id, '_regular_price', true);
                $sale_price = (float) get_post_meta($var_prod_id, '_sale_price', true);

                //+++

                if ($this->is_fixed_enabled)
                {
                    $type = 'regular';
                    $fixed_regular_price = -1;
                    $fixed_sale_price = -1;

                    if ($this->fixed->is_exists($var_prod_id, $current_currency, $type))
                    {
                        $tmp = $this->fixed->get_value($var_prod_id, $current_currency, $type);
                        if ((int) $tmp !== -1)
                        {
                            $fixed_regular_price = $tmp;
                        }
                    }

                    $type = 'sale';
                    if ($this->fixed->is_exists($var_prod_id, $current_currency, $type))
                    {
                        $tmp = $this->fixed->get_value($var_prod_id, $current_currency, $type);
                        if ((int) $tmp !== -1)
                        {
                            $fixed_sale_price = $tmp;
                        }
                    }

                    if ((int) $fixed_sale_price !== -1)
                    {
                        $prices_array[] = $fixed_sale_price;
                        $is_price_custom = true;
                    } else
                    {
                        if ((int) $fixed_regular_price !== -1)
                        {
                            $prices_array[] = $fixed_regular_price;
                            $is_price_custom = true;
                        }
                    }
                }


                if ($this->is_geoip_manipulation AND ! $is_price_custom)
                {
                    $product = (object) array('id' => $var_prod_id);
                    $regular_price = floatval($this->_get_geo_defined_price($product, $regular_price));
                    $sale_price = floatval($this->_get_geo_defined_price($product, $sale_price));
                    //echo $regular_price . '~~~' . $sale_price . '+++';
                }


                if (!$is_price_custom)
                {
                    $regular_price = floatval($currencies[$current_currency]['rate'] * $regular_price);
                    $sale_price = floatval($currencies[$current_currency]['rate'] * $sale_price);

                    if ($sale_price > 0)
                    {
                        $prices_array[] = $sale_price;
                    } else
                    {
                        $prices_array[] = $regular_price;
                    }
                }
            }
        }

        //***

        if (!empty($prices_array))
        {
            foreach ($prices_array as $key => $value)
            {
                if (floatval($value) <= 0)
                {
                    unset($prices_array[$key]);
                }
            }

            //echo '<pre>' . print_r($prices_array, true) . '</pre>';
            return array('min' => min($prices_array), 'max' => max($prices_array));
        }


        return array();
    }

    private function _get_product_fixed_price($product_id, $price, $precision = 2)
    {

        $type = $this->fixed->get_price_type($product_id, $price);

        if ($this->fixed->is_exists($product_id, $this->current_currency, $type))
        {
            return number_format(floatval($this->fixed->get_value($product_id, $this->current_currency, $type)), $precision, $this->decimal_sep, '');
        }

        return -1;
    }

    private function _get_geo_defined_price($product, $price)
    {
        $is_price_custom = false;
        if ($product !== NULL AND is_object($product))
        {
            $type = $this->fixed->get_price_type($product->id, $price);
            $product_geo_data = $this->fixed->get_product_geo_data($product->id);

            if (isset($product_geo_data[$type . '_price_geo']))
            {
                if (!empty($product_geo_data[$type . '_price_geo']))
                {
                    $user_country = $this->storage->get_val('woocs_user_country');
                    //$user_currency = $this->get_currency_by_country($country);
                    if (!empty($user_country))
                    {
                        if (!empty($product_geo_data['price_geo_countries']))
                        {
                            $price_key = '';
                            foreach ($product_geo_data['price_geo_countries'] as $block_key => $countries_codes)
                            {
                                if (!empty($countries_codes))
                                {
                                    foreach ($countries_codes as $country_code)
                                    {
                                        if ($country_code === $user_country)
                                        {
                                            $price_key = $block_key;
                                            break(2);
                                        }
                                    }
                                }
                            }

                            //***

                            if (isset($product_geo_data[$type . '_price_geo'][$price_key]))
                            {
                                $price = $product_geo_data[$type . '_price_geo'][$price_key];
                                $is_price_custom = true;
                            }
                        }
                    }
                }
            }
        }

        return $price;
    }

    public function get_welcome_currency()
    {
        return get_option('woocs_welcome_currency');
    }

    public function get_customer_signs()
    {
        $signs = array();
        $data = get_option('woocs_customer_signs', '');
        if (!empty($data))
        {
            $data = explode(',', $data);
            if (!empty($data) AND is_array($data))
            {
                $signs = $data;
            }
        }
        return $signs;
    }

    public function get_checkout_page_id()
    {
        return (int) get_option('woocommerce_checkout_page_id');
    }

    public function force_pay_bygeoip_rules()
    {
        //$use_geo_rules = get_option('woocs_use_geo_rules', 0);
        $use_geo_rules = true;
        if ($this->is_multiple_allowed AND $this->force_pay_bygeoip_rules AND $use_geo_rules)
        {
            $country = $this->storage->get_val('woocs_user_country');
            $user_currency = $this->get_currency_by_country($country);
            if (!empty($user_currency))
            {
                //$user_currency is empty its mean that current country is not in geo ip rules
                $this->set_currency($user_currency);
            }
            do_action('woocs_force_pay_bygeoip_rules', $country, $user_currency, $this->current_currency);
        }
    }

    public function woocommerce_price_format()
    {
        $currencies = $this->get_currencies();
        $currency_pos = 'left';
        if (isset($currencies[$this->current_currency]))
        {
            $currency_pos = $currencies[$this->current_currency]['position'];
        }
        $format = '%1$s%2$s';
        switch ($currency_pos)
        {
            case 'left' :
                $format = '%1$s%2$s';
                break;
            case 'right' :
                $format = '%2$s%1$s';
                break;
            case 'left_space' :
                $format = '%1$s&nbsp;%2$s';
                break;
            case 'right_space' :
                $format = '%2$s&nbsp;%1$s';
                break;
        }

        return apply_filters('woocs_price_format', $format, $currency_pos);
    }

//[woocs]
    public function woocs_shortcode($args)
    {
        if (empty($args))
        {
            $args = array();
        }
        return $this->render_html(WOOCS_PATH . 'views/shortcodes/woocs.php', $args);
    }

//[woocs_converter exclude="GBP,AUD" precision=2]
    public function woocs_converter($args)
    {
        if (empty($args))
        {
            $args = array();
        }
        return $this->render_html(WOOCS_PATH . 'views/shortcodes/woocs_converter.php', $args);
    }

//[woocs_rates exclude="GBP,AUD" precision=2]
    public function woocs_rates($args)
    {
        if (empty($args))
        {
            $args = array();
        }
        return $this->render_html(WOOCS_PATH . 'views/shortcodes/woocs_rates.php', $args);
    }

//[woocs_show_current_currency text="" currency="" flag=1 code=1]
    public function woocs_show_current_currency($atts)
    {
        $currencies = $this->get_currencies();
        extract(shortcode_atts(array(
            'text' => __('Current currency is:', 'woocommerce-currency-switcher'),
            'currency' => $this->current_currency,
            'flag' => 1,
            'code' => 1,
                        ), $atts));

        $args = array();
        $args['currencies'] = $currencies;
        $args['text'] = $text;
        $args['currency'] = $currency;
        $args['flag'] = $flag;
        $args['code'] = $code;
        return $this->render_html(WOOCS_PATH . 'views/shortcodes/woocs_show_current_currency.php', $args);
    }

//[woocs_show_custom_price value=20] -> value should be in default currency
    public function woocs_show_custom_price($atts)
    {
        extract(shortcode_atts(array('value' => 0), $atts));
        $currencies = $this->get_currencies();
        $convert = true;
        $_REQUEST['woocs_show_custom_price'] = TRUE;
        $wc_price = $this->wc_price($value, $convert, array('currency' => $currencies[$this->current_currency]['name']));
        unset($_REQUEST['woocs_show_custom_price']);
        return $wc_price;
    }

    //for geo ip demo
    public function woocs_geo_hello($atts = '')
    {
        $pd = array();
        $countries = array();
        $text = '';
        if (class_exists('WC_Geolocation'))
        {
            $c = new WC_Countries();
            $countries = $c->get_countries();
            $pd = WC_Geolocation::geolocate_ip();
        }
        if (!empty($pd) AND ! empty($countries))
        {
            $text = '<span style="color:green; font-size:18px; font-weight:normal; line-height:0;">' . sprintf(__('Your country is: <b>%s</b>. <br /><i style="color:black; font-size:11px; font-weight:normal;">(defined by woocommerce GeoIP functionality)</i>', 'woocommerce-currency-switcher'), $countries[$pd['country']]) . '</span>';
        } else
        {
            $text = '<i style="color:red; font-size:18px; font-weight:normal;">' . __('Your country is not defined! Troubles with GeoIp service.', 'woocommerce-currency-switcher') . '</i>';
        }

        return $text;
    }

//http://stackoverflow.com/questions/6918623/curlopt-followlocation-cannot-be-activated
    private function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

//ajax
    public function get_rate()
    {
        $is_ajax = true;
        if (isset($_REQUEST['no_ajax']))
        {
            $is_ajax = false;
        }

//***
//http://en.wikipedia.org/wiki/ISO_4217
        $mode = get_option('woocs_currencies_aggregator', 'yahoo');
        $request = "";
        //$woocs_use_curl = (int) get_option('woocs_use_curl', 0);
        $woocs_use_curl = 1;
        switch ($mode)
        {
            case 'yahoo':
//http://www.idiotinside.com/2015/01/28/create-a-currency-converter-in-php-python-javascript-and-jquery-using-yahoo-currency-api/
                $yql_base_url = "http://query.yahooapis.com/v1/public/yql";
                $yql_query = 'select * from yahoo.finance.xchange where pair in ("' . $this->default_currency . $this->escape($_REQUEST['currency_name']) . '")';
                $yql_query_url = $yql_base_url . "?q=" . urlencode($yql_query);
                $yql_query_url .= "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
//***
                if (function_exists('curl_init') AND $woocs_use_curl)
                {
                    $res = $this->file_get_contents_curl($yql_query_url);
                } else
                {
                    $res = file_get_contents($yql_query_url);
                }
//***
                $yql_json = json_decode($res, true);
                $request = (float) $yql_json['query']['results']['rate']['Rate'];


                break;

            case 'google':
                $amount = urlencode(1);
                $from_Currency = urlencode($this->default_currency);
                $to_Currency = urlencode($this->escape($_REQUEST['currency_name']));
                $url = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";

                if (function_exists('curl_init') AND $woocs_use_curl)
                {
                    $html = $this->file_get_contents_curl($url);
                } else
                {
                    $html = file_get_contents($url);
                }

                preg_match_all('/<span class=bld>(.*?)<\/span>/s', $html, $matches);
                if (isset($matches[1][0]))
                {
                    $request = floatval($matches[1][0]);
                } else
                {
                    $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                }

                break;

            case 'privatbank':
                //https://api.privatbank.ua/#p24/exchange
                $url = 'https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=4';

                if (function_exists('curl_init') AND $woocs_use_curl)
                {
                    $res = $this->file_get_contents_curl($url);
                } else
                {
                    $res = file_get_contents($url);
                }

                $currency_data = json_decode($res, true);
                $rates = array();


                if (!empty($currency_data))
                {
                    foreach ($currency_data as $c)
                    {
                        if ($c['base_ccy'] == 'UAH')
                        {
                            $rates[$c['ccy']] = floatval($c['sale']);
                        }
                    }
                }


                //***

                if (!empty($rates))
                {

                    if ($this->default_currency != 'UAH')
                    {
                        if ($_REQUEST['currency_name'] != 'UAH')
                        {
                            if (isset($_REQUEST['currency_name']))
                            {
                                $request = floatval($rates[$this->default_currency] / ($rates[$this->escape($_REQUEST['currency_name'])]));
                            } else
                            {
                                $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                            }
                        } else
                        {
                            $request = 1 / $rates[$this->default_currency];
                        }
                    } else
                    {
                        if ($_REQUEST['currency_name'] != 'UAH')
                        {
                            $request = $rates[$_REQUEST['currency_name']];
                        } else
                        {
                            $request = 1;
                        }
                    }
                } else
                {
                    $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                }

                //***

                if (!$request)
                {
                    $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                }


                break;



            case 'ecb':
                $url = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

                if (function_exists('curl_init') AND $woocs_use_curl)
                {
                    $res = $this->file_get_contents_curl($url);
                } else
                {
                    $res = file_get_contents($url);
                }

                $currency_data = simplexml_load_string($res);
                $rates = array();
                if (empty($currency_data->Cube->Cube))
                {
                    $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                    break;
                }



                foreach ($currency_data->Cube->Cube->Cube as $xml)
                {
                    $att = (array) $xml->attributes();
                    $rates[$att['@attributes']['currency']] = floatval($att['@attributes']['rate']);
                }


                //***

                if (!empty($rates))
                {

                    if ($this->default_currency != 'EUR')
                    {
                        if ($_REQUEST['currency_name'] != 'EUR')
                        {
                            if (isset($_REQUEST['currency_name']))
                            {
                                $request = floatval($rates[$this->default_currency] / ($rates[$this->escape($_REQUEST['currency_name'])]));
                            } else
                            {
                                $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                            }
                        } else
                        {
                            $request = 1 / $rates[$this->default_currency];
                        }
                    } else
                    {
                        if ($_REQUEST['currency_name'] != 'EUR')
                        {
                            $request = $rates[$_REQUEST['currency_name']];
                        } else
                        {
                            $request = 1;
                        }
                    }
                } else
                {
                    $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                }

                //***

                if (!$request)
                {
                    $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                }


                break;

            case 'rf':
                //http://www.cbr.ru/scripts/XML_daily_eng.asp?date_req=21/08/2015
                $xml_url = 'http://www.cbr.ru/scripts/XML_daily_eng.asp?date_req='; //21/08/2015
                $date = date('d/m/Y');
                $xml_url.=$date;
                if (function_exists('curl_init') AND $woocs_use_curl)
                {
                    $res = $this->file_get_contents_curl($xml_url);
                } else
                {
                    $res = file_get_contents($xml_url);
                }
//***
                $xml = simplexml_load_string($res) or die("Error: Cannot create object");
                $xml = $this->object2array($xml);
                $rates = array();
                $nominal = array();
//***
                if (isset($xml['Valute']))
                {
                    if (!empty($xml['Valute']))
                    {
                        foreach ($xml['Valute'] as $value)
                        {
                            $rates[$value['CharCode']] = floatval(str_replace(',', '.', $value['Value']));
                            $nominal[$value['CharCode']] = $value['Nominal'];
                        }
                    }
                }
//***
                if (!empty($rates))
                {
                    if ($this->default_currency != 'RUB')
                    {
                        if ($_REQUEST['currency_name'] != 'RUB')
                        {
                            if (isset($_REQUEST['currency_name']))
                            {
                                $request = floatval($rates[$this->default_currency] / ($rates[$this->escape($_REQUEST['currency_name'])] / $nominal[$this->escape($_REQUEST['currency_name'])]));
                            } else
                            {
                                $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                            }
                        } else
                        {
                            $request = 1 / (1 / $rates[$this->default_currency]);
                        }
                    } else
                    {
                        if ($_REQUEST['currency_name'] != 'RUB')
                        {
                            $request = 1 / $rates[$_REQUEST['currency_name']];
                        } else
                        {
                            $request = 1;
                        }
                    }
                } else
                {
                    $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                }

                //***

                if (!$request)
                {
                    $request = sprintf(__("no data for %s", 'woocommerce-currency-switcher'), $this->escape($_REQUEST['currency_name']));
                }

                break;

            default:
                break;
        }


//***
        if ($is_ajax)
        {
            echo $request;
            exit;
        } else
        {
            return $request;
        }
    }

    private function object2array($object)
    {
        return @json_decode(@json_encode($object), 1);
    }

//ajax
    public function save_etalon()
    {
        if (!is_ajax() OR ! current_user_can('manage_options'))
        {
//we need it just only for ajax update
            return "";
        }
//$this->make_rates_auto_update(true);
        $currencies = $this->get_currencies();
        $currency_name = $this->escape($_REQUEST['currency_name']);
        foreach ($currencies as $key => $currency)
        {
            if ($currency['name'] == $currency_name)
            {
                $currencies[$key]['is_etalon'] = 1;
            } else
            {
                $currencies[$key]['is_etalon'] = 0;
            }
        }
        update_option('woocs', $currencies);
//+++ get curr updated values back
        $request = array();
        $this->default_currency = strtoupper($this->escape($_REQUEST['currency_name']));
        $_REQUEST['no_ajax'] = TRUE;
        foreach ($currencies as $key => $currency)
        {
            if ($currency_name != $currency['name'])
            {
                $_REQUEST['currency_name'] = $currency['name'];
                $request[$key] = $this->get_rate();
            } else
            {
                $request[$key] = 1;
            }
        }

        echo json_encode($request);
        exit;
    }

//order data registration
    public function woocommerce_thankyou_order_id($order_id)
    {
        $currencies = $this->get_currencies();
//+++
        //$rate = get_post_meta($order_id, '_woocs_order_rate', true);
//if (intval($rate) === 0)
        {
//condition (!$rate) is lock of chaning order currency looking it using link like
//http://xxxxx.currency-switcher.com/checkout/order-received/1003/?key=wc_order_55a52c81ad4ef
//this needs if back in paypal process, but looks like it is more dangerous, so commented
//update_post_meta($order_id, '_order_currency', $this->current_currency);
        }
//+++
        update_post_meta($order_id, '_woocs_order_rate', $currencies[$this->current_currency]['rate']);
        wc_add_order_item_meta($order_id, '_woocs_order_rate', $currencies[$this->current_currency]['rate'], true);

        update_post_meta($order_id, '_woocs_order_base_currency', $this->default_currency);
        wc_add_order_item_meta($order_id, '_woocs_order_base_currency', $this->default_currency, true);

        update_post_meta($order_id, '_woocs_order_currency_changed_mannualy', 0);
        wc_add_order_item_meta($order_id, '_woocs_order_currency_changed_mannualy', 0, true);

        return $order_id;
    }

    public function woocommerce_cart_totals_order_total_html($output)
    {
//if ($this->current_currency == $this->default_currency)
        {
            return $output;
        }
//experimental feature. Do not use it.
//***
        $value = "&nbsp;(";
//***
        $currencies = $this->get_currencies();
        $amount = WC()->cart->total / $currencies[$this->current_currency]['rate'];
//***
        $cc = $this->current_currency;
        $this->current_currency = $this->default_currency;
        $value.=__('Total in basic currency: ', 'woocommerce-currency-switcher') . $this->wc_price($amount, false, array('currency' => $this->default_currency));
        $this->current_currency = $cc;
        $value.=")";
        return $output . $value;
    }

    public function wc_price_args($default_args)
    {
        if (in_array($this->current_currency, $this->no_cents))
        {
            $default_args['decimals'] = 0;
        }
        return $default_args;
    }

//***************************** email actions

    public function woocommerce_email_actions($email_actions)
    {
        $_REQUEST['woocs_order_emails_is_sending'] = 1;
        if (isset($_REQUEST['woocs_in_order_currency']))
        {
            $this->current_currency = $_REQUEST['woocs_in_order_currency'];
            //$this->default_currency = $_REQUEST['woocs_in_order_currency'];
        } else
        {
            global $post;
            if (is_object($post) AND $post->post_type == 'shop_order')
            {
                //processing button pressed in: wp-admin/edit.php?post_type=shop_order
                $currency = get_post_meta($post->ID, '_order_currency', true);
                if (!empty($currency))
                {
                    $_REQUEST['woocs_in_order_currency'] = $currency;
                    $this->current_currency = $currency;
                }
            } else
            {
                //processing button pressed in: wp-admin/post.php?post=1170&action=edit - inside of order by drop-down on the left
                //print_r($_POST);
                if (isset($_POST['order_status']) AND isset($_POST['post_ID']))
                {
                    $currency = get_post_meta((int) $_POST['post_ID'], '_order_currency', true);
                    //echo $currency;exit;
                    if (!empty($currency))
                    {
                        $_REQUEST['woocs_in_order_currency'] = $currency;
                        $this->current_currency = $currency;
                    }
                }
            }
        }



        return $email_actions;
    }

    public function woocommerce_before_resend_order_emails($order)
    {
        $currency = get_post_meta($order->id, '_order_currency', true);
        if (!empty($currency))
        {
            $_REQUEST['woocs_in_order_currency'] = $currency;
            $this->current_currency = $currency;
            $this->default_currency = $currency;
        }
    }

//when admin complete order
    public function woocommerce_order_status_completed($order_id)
    {
        if ($this->is_multiple_allowed)
        {
            $currency = get_post_meta($order_id, '_order_currency', true);
            if (!empty($currency))
            {
                $_REQUEST['woocs_in_order_currency'] = $currency;
                $this->default_currency = $currency;
            }
        }
    }

//wp-content\plugins\woocommerce\includes\class-wc-emails.php
//public static function init_transactional_emails()
//public static function send_transactional_email()
    public function woocommerce_order_status_completed_notification($args)
    {
        if ($this->is_multiple_allowed)
        {
            $order_id = $args;
            $currency = get_post_meta($order_id, '_order_currency', true);
            if (!empty($currency))
            {
                $_REQUEST['woocs_in_order_currency'] = $currency;
                $this->default_currency = $currency;
                $this->current_currency = $currency;
            }
        }
    }

//********************************************************************************

    public function wp_footer()
    {
//return; //return it for releases if you want
        if (isset($_GET['currency']))
        {
            ?>
            <script type="text/javascript">
                try {
                    jQuery(function () {
                        jQuery.cookie('woocommerce_cart_hash', '', {path: '/'});
                    });
                } catch (e) {

                }
            </script>
            <?php
        }
    }

//********************************************************************************

    public function render_html($pagepath, $data = array())
    {
        @extract($data);
        ob_start();
        include($pagepath);
        return ob_get_clean();
    }

    public function get_sign_rate($atts)
    {
        $sign = strtoupper($atts['sign']);
        $currencies = $this->get_currencies();
        $rate = 0;
        if (isset($currencies[$sign]))
        {
            $rate = $currencies[$sign]['rate'];
        }

        return $rate;
    }

//for hook woocommerce_paypal_args
    function apply_conversion($paypal_args)
    {
        if (in_array($this->current_currency, $this->no_cents))
        {
            $paypal_args['currency_code'] = $this->current_currency;
            foreach ($paypal_args as $key => $value)
            {
                if (strpos($key, 'amount_') !== false)
                {
                    $paypal_args[$key] = number_format($value, 0, $this->decimal_sep, '');
                } else
                {
                    if (strpos($key, 'tax_cart') !== false)
                    {
                        $paypal_args[$key] = number_format($value, 0, $this->decimal_sep, '');
                    }
                }
            }
        }

        return $paypal_args;
    }

    public function woocommerce_price_html($price_html, $product)
    {

        static $customer_price_format = -1;
        if ($customer_price_format === -1)
        {
            $customer_price_format = get_option('woocs_customer_price_format', '__PRICE__');
        }

        if (empty($customer_price_format))
        {
            $customer_price_format = '__PRICE__';
        }

//***
        $currencies = $this->get_currencies();

//+++
        if (!empty($customer_price_format))
        {
            $txt = '<span class="woocs_price_code" data-product-id="' . $product->id . '">' . $customer_price_format . '</span>';
            $txt = str_replace('__PRICE__', $price_html, $txt);
            $price_html = str_replace('__CODE__', $this->current_currency, $txt);
            $price_html = apply_filters('woocs_price_html_tail', $price_html);
        }


//hide cents on front as html element
        if (!in_array($this->current_currency, $this->no_cents))
        {
            if ($currencies[$this->current_currency]['hide_cents'] == 1)
            {
                $price_html = preg_replace('/\.[0-9][0-9]/', '', $price_html);
            }
        }



//add additional info in price html
        if (get_option('woocs_price_info', 0) AND ! (is_admin() AND ! isset($_REQUEST['get_product_price_by_ajax'])))
        {
            $info = "<ul class='woocs_price_info_list'>";
            $current_currency = $this->current_currency;
            foreach ($currencies as $сurr)
            {
                if ($сurr['name'] == $current_currency)
                {
                    continue;
                }
                $this->current_currency = $сurr['name'];
                $value = $product->price * $currencies[$сurr['name']]['rate'];
                $precision = $this->get_currency_price_num_decimals($сurr['name'], $this->price_num_decimals);
                $value = number_format($value, $precision, $this->decimal_sep, '');
                if ($product->product_type == 'variable')
                {

                    $min_value = $product->min_variation_price * $currencies[$сurr['name']]['rate'];
                    $max_value = $product->max_variation_price * $currencies[$сurr['name']]['rate'];
                    //***
                    $min_max_values = $this->_get_min_max_variation_prices($product, $сurr['name']);
                    if (!empty($min_max_values))
                    {
                        //echo '<pre>' . print_r($min_max_values, true) . '</pre>';
                        $min_value = $min_max_values['min'] /* $currencies[$сurr['name']]['rate'] */;
                        $max_value = $min_max_values['max'] /* $currencies[$сurr['name']]['rate'] */;
                    }
                    //+++
                    $_REQUEST['woocs_wc_price_convert'] = FALSE;

                    $var_price = $this->wc_price($min_value, array('currency' => $сurr['name']), false, $product);
                    $var_price.= ' - ';
                    $var_price.= $this->wc_price($max_value, array('currency' => $сurr['name']), false, $product);

                    unset($_REQUEST['woocs_wc_price_convert']);
                    $info.= "<li><b>" . $сurr['name'] . "</b>: " . $var_price . "</li>";
                } else
                {
                    //print_r($product);
                    $info.= "<li><span>" . $сurr['name'] . "</span>: " . $this->wc_price($value, false, array('currency' => $сurr['name']), $product) . "</li>";
                }
            }
            $this->current_currency = $current_currency;
            $info.= "</ul>";
            $info = '<div class="woocs_price_info"><span class="woocs_price_info_icon"></span>' . $info . '</div>';
            $price_html.=$info;
        }


        return $price_html;
    }

    public function woocommerce_coupon_get_discount_amount($discount, $discounting_amount, $cart_item, $single, $coupon)
    {

        if (version_compare(WOOCOMMERCE_VERSION, '2.4', '>=') AND $this->is_multiple_allowed)
        {
            if (is_object($coupon) AND method_exists($coupon, 'is_type'))
            {
                if (!$coupon->is_type(array('percent_product', 'percent')))
                {
                    $discount = $this->woocs_exchange_value(floatval($discount));
                }
            }
        }

        return $discount;
    }

//wp filter for values which is in basic currency and no possibility do it automatically
    public function woocs_exchange_value($value)
    {
        $currencies = $this->get_currencies();
        $value = $value * $currencies[$this->current_currency]['rate'];
        $precision = $this->get_currency_price_num_decimals($this->current_currency, $this->price_num_decimals);
        $value = number_format($value, $precision, $this->decimal_sep, '');
        return $value;
    }

//set it to default
    public function reset_currency()
    {
        $this->set_currency('');
    }

    public function set_currency($currency = '')
    {
        if (empty($currency))
        {
            $currency = $this->default_currency;
        }
        $this->storage->set_val('woocs_current_currency', $currency);
        $this->current_currency = $currency;
    }

//compatibility for https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/stats/
//hook commented, wpo_wcpdf_process_template_order uses for this
    public function wpo_wcpdf_order_number($order_id)
    {
//set order currency instead selected on the front
        $currency = get_post_meta($order_id, '_order_currency', TRUE);
        if (!empty($currency))
        {
            $this->current_currency = $currency;
        }

        return $order_id;
    }

//https://wordpress.org/support/topic/multi-currency-on-invoices?replies=8
    public function wpo_wcpdf_process_template_order($template_type, $order_id)
    {
        global $wpo_wcpdf;

        if (isset($wpo_wcpdf->export->order->id))
        {
            $order_id = (int) $wpo_wcpdf->export->order->id;
            $currency = get_post_meta($order_id, '_order_currency', TRUE);
            if (!empty($currency))
            {
                $this->current_currency = $currency;
            }
        }
    }

//***

    public function woocommerce_get_order_currency($order_currency, $order)
    {

        if (!is_ajax() AND ! is_admin() AND is_object($order))
        {

            $currency = get_post_meta($order->id, '_order_currency', TRUE);
            if (!empty($currency))
            {
                $this->current_currency = $currency;
            }
        }

        return $order_currency;
    }

    public function woocommerce_view_order($order_id)
    {

        if (!is_ajax() AND ! is_admin())
        {
            $currency = get_post_meta($order_id, '_order_currency', TRUE);
            if (!empty($currency))
            {
                $this->current_currency = $currency;
            }
        }

        return $order_id;
    }

    public function woocommerce_package_rates($rates)
    {
        $currencies = $this->get_currencies();

//***
        if ($this->is_multiple_allowed)
        {
            if ($this->current_currency != $this->default_currency)
            {
                $currencies = $this->get_currencies();
                foreach ($rates as $rate)
                {
                    $value = $rate->cost * $currencies[$this->current_currency]['rate'];
                    $precision = $this->get_currency_price_num_decimals($this->current_currency, $this->price_num_decimals);
                    $rate->cost = number_format(floatval($value), $precision, $this->decimal_sep, '');
//VAT values for another currency in the shipping
//https://wordpress.org/support/topic/vat-values-are-not-switched-to-another-currency-for-shipping
                    if (isset($rate->taxes))
                    {
                        $taxes = $rate->taxes;
                        if (!empty($taxes))
                        {
                            foreach ($taxes as $order => $tax)
                            {
                                $value_tax = $tax * $currencies[$this->current_currency]['rate'];
                                $rate->taxes[$order] = number_format(floatval($value_tax), $precision, $this->decimal_sep, '');
                            }
                        }
                    }
                }
            }
        }

        return $rates;
    }

    public function wcml_raw_price_amount($value)
    {
        return $this->woocs_exchange_value($value);
    }

//ajax
    public function woocs_convert_currency()
    {
        $currencies = $this->get_currencies();
        $v = $currencies[$_REQUEST['to']]['rate'] / $currencies[$_REQUEST['from']]['rate'];
        if (in_array($_REQUEST['to'], $this->no_cents))
        {
            $_REQUEST['precision'] = 0;
        }
        $value = number_format($v * $_REQUEST['amount'], intval($_REQUEST['precision']), $this->decimal_sep, '');


        wp_die($value);
    }

//for refreshing mini-cart widget
    public function woocommerce_before_mini_cart()
    {
        $_REQUEST['woocs_woocommerce_before_mini_cart'] = 'mini_cart_refreshing';
        WC()->cart->calculate_totals();
    }

//for refreshing mini-cart widget
    public function woocommerce_after_mini_cart()
    {
        unset($_REQUEST['woocs_woocommerce_before_mini_cart']);
    }

//ajax
    public function woocs_rates_current_currency()
    {
        wp_die(do_shortcode('[woocs_rates exclude="' . $this->escape($_REQUEST['exclude']) . '" precision="' . $this->escape($_REQUEST['precision']) . '" current_currency="' . $this->escape($_REQUEST['current_currency']) . '"]'));
    }

//log test data while makes debbuging
    public function log($string)
    {
        $handle = fopen(WOOCS_PATH . 'log.txt', 'a+');
        $string.= PHP_EOL;
        fwrite($handle, $string);
        fclose($handle);
    }

    public function wc_price($price, $convert = true, $args = array(), $product = NULL)
    {
        if (!isset($_REQUEST['woocs_wc_price_convert']))
        {
            $_REQUEST['woocs_wc_price_convert'] = true;
        }
        extract(apply_filters('wc_price_args', wp_parse_args($args, array(
            'ex_tax_label' => false,
            'currency' => '',
            'decimal_separator' => $this->decimal_sep,
            'thousand_separator' => $this->thousands_sep,
            'decimals' => 2,
            'price_format' => $this->woocommerce_price_format()
        ))));

        $decimals = $this->get_currency_price_num_decimals($currency, $this->price_num_decimals);

//***
        $currencies = $this->get_currencies();
        if (isset($currencies[$currency])/* AND !isset($_REQUEST['woocs_show_custom_price']) */)
        {
            if ($currencies[$currency]['hide_cents'])
            {
                $decimals = 0;
            }
        }

//***
        $negative = $price < 0;
        $special_convert = false;
        $is_price_custom = false;
        try {
            if ($product !== NULL AND is_object($product))
            {

                if ($this->is_multiple_allowed)
                {
                    if ($this->is_fixed_enabled)
                    {
                        $type = $this->fixed->get_price_type($product->id, $price);
                        if ($this->fixed->is_exists($product->id, $currency, $type))
                        {
                            $special_convert = true;
                            $is_price_custom = true;
                            if (floatval($this->fixed->get_value($product->id, $currency, $type)) > 0)
                            {
                                $price = floatval($this->fixed->get_value($product->id, $currency, $type));
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            
        }

        //***


        if ($this->is_geoip_manipulation AND ! $is_price_custom)
        {
            $price = $this->_get_geo_defined_price($product, $price);
            $special_convert = $convert = true;
        }

        //***

        if ($convert AND $_REQUEST['woocs_wc_price_convert'] AND ! $is_price_custom)
        {
            //$price = apply_filters('raw_woocommerce_price', floatval($negative ? $price * -1 : $price ));
            $price = $this->raw_woocommerce_price(floatval($negative ? $price * -1 : $price ), $product);
        }


        $price = apply_filters('formatted_woocommerce_price', number_format($price, $decimals, $decimal_separator, $thousand_separator), $price, $decimals, $decimal_separator, $thousand_separator);

        //***

        if (apply_filters('woocommerce_price_trim_zeros', false) && $decimals > 0)
        {
            $price = wc_trim_zeros($price);
        }

        $formatted_price = ( $negative ? '-' : '' ) . sprintf($price_format, get_woocommerce_currency_symbol($currency), $price);
        $return = '<span class="woocs_amount">' . $formatted_price . '</span>';

        if ($ex_tax_label && wc_tax_enabled())
        {
            $return .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
        }

//***
//***

        return apply_filters('wc_price', $return, $price, $args);
    }

//woo hook
    public function woocommerce_product_is_on_sale($value, $product)
    {
//$product->get_sale_price() !== $product->get_regular_price() && $product->get_sale_price() === $product->get_price()
        $is_sale = false;
        $sale_price = $product->sale_price;
        $regular_price = $product->regular_price;
        $price = $product->price;

//***
//https://www.skyverge.com/blog/get-a-list-of-woocommerce-sale-products/
        if ($product->product_type == 'variable')
        {
            /*
              $_REQUEST['woocs_block_price_hook'] = 1;
              remove_all_filters('woocommerce_product_is_on_sale');
              if ($product->is_on_sale())
              {
              $is_sale = true;
              }
              add_filter('woocommerce_product_is_on_sale', array($this, 'woocommerce_product_is_on_sale'), 9999, 2);
              unset($_REQUEST['woocs_block_price_hook']);
             */
        } else
        {
            if ($sale_price !== $regular_price AND ( $price === $sale_price))
            {
                $is_sale = true;
            }
        }


        return $is_sale;
    }

//woo hook
//wp-content\plugins\woocommerce\includes\shipping\free-shipping\class-wc-shipping-free-shipping.php #192
    public function woocommerce_shipping_free_shipping_is_available($is_available, $package)
    {
        global $wpdb;
//$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", "%_transient_%"));
//print_r(WC()->session);
//unset(WC()->session->shipping_for_package_0);
//unset(WC()->session->shipping_method_counts);
//***
        global $woocommerce;
        $currencies = $this->get_currencies();

        if (version_compare(WOOCOMMERCE_VERSION, '2.6.0', '<'))
        {
            $free_shipping_settings = get_option('woocommerce_free_shipping_settings');
            $min_amount = $free_shipping_settings['min_amount'];

            if ($min_amount > 0)
            {
                if ($this->current_currency != $this->default_currency)
                {
                    $min_amount = $this->woocs_exchange_value($min_amount);
                    if ($min_amount <= $package['contents_cost'])
                    {
                        $is_available = true;
                    } else
                    {
                        $is_available = false;
                    }
                }
            }
        } else
        {

//from woo 2.6.0 control of free shipping is doing in woocommerce_package_rates
//wp-content\plugins\woocommerce\includes\class-wc-shipping.php -> calculate_shipping_for_package
//https://wordpress.org/support/topic/shop-for-x-more-to-get-free-shipping-in-woocommerce-26x
            $wc_shipping = WC_Shipping::instance();
            if ($wc_shipping->enabled)
            {
                if (!empty($wc_shipping->shipping_methods))
                {
                    foreach ($wc_shipping->shipping_methods as $key => $o)
                    {
                        if (get_class($o) == 'WC_Shipping_Free_Shipping')
                        {
                            $free_shipping_id = (int) $o->instance_id;
//$_REQUEST['free_shipping_id'] = $free_shipping_id;
//***
                            $free_shipping_settings = get_option('woocommerce_free_shipping_' . $free_shipping_id . '_settings');
                            $allows_array = array('min_amount', 'either', 'both');
                            if (in_array($free_shipping_settings['requires'], $allows_array))
                            {
                                $min_amount = $free_shipping_settings['min_amount'];

//$amount = floatval(preg_replace('#[^\d.]#', '', $woocommerce->cart->get_cart_total()));

                                if (isset(WC()->session->subtotal_ex_tax))
                                {
                                    $amount = WC()->session->subtotal_ex_tax;
                                } else
                                {
                                    $amount = WC()->session->subtotal;
                                }


                                if ($this->is_multiple_allowed)
                                {
                                    if ($this->current_currency != $this->default_currency)
                                    {
                                        $amount = (float) $this->back_convert($amount, $currencies[$this->current_currency]['rate']);
                                        $amount = $amount + $amount * 0.001; //correction because of cents
                                    }
                                }

//echo $amount . '+++' . $this->current_currency . '+++' . $this->default_currency;

                                if ($min_amount > $amount)
                                {
                                    $is_available = false;
                                }

//***
                                $free_shipping_coupon = false;
                                if (!empty($woocommerce->cart->applied_coupons))
                                {
                                    $coupon = new WC_Coupon($woocommerce->cart->applied_coupons[0]);
                                    $free_shipping_coupon_val = get_post_meta($coupon->id, 'free_shipping', true);
                                    if ($free_shipping_coupon_val == 'yes')
                                    {
//in in coupom enabled 'Allow free shipping' checkbox
                                        $free_shipping_coupon = true;
                                    }
                                }

                                if ($free_shipping_settings['requires'] == 'both')
                                {
                                    if ($free_shipping_coupon AND $amount >= $min_amount)
                                    {
                                        $is_available = true;
                                    } else
                                    {
                                        $is_available = false;
                                    }
                                }

//***

                                if ($free_shipping_settings['requires'] == 'either')
                                {
                                    if ($free_shipping_coupon)
                                    {
                                        $is_available = true;
                                    }

                                    if ($amount >= $min_amount)
                                    {
                                        $is_available = true;
                                    }
                                }
                            }
//$is_available=true;
//***
                            break;
                        }
                    }
                }
            }
        }

//***

        return $is_available;
    }

//ajax
//for price redrawing on front if site using cache plugin functionality
    public function woocs_get_products_price_html()
    {
        $result = array();
        if (isset($_REQUEST['products_ids']))
        {
//***
            $this->init_geo_currency();
//***
            $_REQUEST['get_product_price_by_ajax'] = 1;

//add_action('woocommerce_price_html', array($this, 'woocommerce_price_html'), 1, 2);

            $products_ids = $_REQUEST['products_ids'];
//***
            if (!empty($products_ids) AND is_array($products_ids))
            {
                foreach ($products_ids as $p_id)
                {
                    $product = wc_get_product($p_id);
                    if (is_object($product))
                    {
                        $result[$p_id] = $product->get_price_html();
                    }
                }
            }
        }
//***
        $data = array();
        $data['ids'] = $result;
        $data['current_currency'] = $this->current_currency;
        wp_die(json_encode($data));
    }

//count amount in basic currency from any currency
    public function back_convert($amount, $rate, $precision = 4)
    {
        return number_format((1 / $rate) * $amount, $precision, '.', '');
    }

//recalculation order to basic currency data if order is in any another currency
    public function recalculate_order($order_id)
    {

        $order_currency = get_post_meta($order_id, '_order_currency', true);
//lets avoid recalculation for order which is already in
        if ($order_currency == $this->default_currency OR empty($order_currency))
        {
            return;
        }
        $decimals = $this->get_currency_price_num_decimals($order_currency, $this->price_num_decimals);

//***
        $currencies = $this->get_currencies();
        $_woocs_order_rate = get_post_meta($order_id, '_woocs_order_rate', true);
        if (empty($_woocs_order_rate))
        {
            $_woocs_order_rate = $currencies[$order_currency]['rate'];
        }
//***

        update_post_meta($order_id, '_woocs_order_currency', $this->default_currency);
        update_post_meta($order_id, '_order_currency', $this->default_currency);

        update_post_meta($order_id, '_woocs_order_base_currency', $this->default_currency);
        wc_update_order_item_meta($order_id, '_woocs_order_base_currency', $this->default_currency);

        update_post_meta($order_id, '_woocs_order_rate', 1);
        wc_update_order_item_meta($order_id, '_woocs_order_rate', 1);

        update_post_meta($order_id, '_woocs_order_currency_changed_mannualy', time());
        wc_add_order_item_meta($order_id, '_woocs_order_currency_changed_mannualy', time(), true);

//***

        $_order_shipping = get_post_meta($order_id, '_order_shipping', true);
        update_post_meta($order_id, '_order_shipping', $this->back_convert($_order_shipping, $_woocs_order_rate));

        $_order_total = get_post_meta($order_id, '_order_total', true);
        update_post_meta($order_id, '_order_total', $this->back_convert($_order_total, $_woocs_order_rate));

        $_refund_amount = get_post_meta($order_id, '_refund_amount', true);
        update_post_meta($order_id, '_refund_amount', $this->back_convert($_refund_amount, $_woocs_order_rate));

        $_cart_discount_tax = get_post_meta($order_id, '_cart_discount_tax', true);
        update_post_meta($order_id, '_cart_discount_tax', $this->back_convert($_cart_discount_tax, $_woocs_order_rate));

        $_order_tax = get_post_meta($order_id, '_order_tax', true);
        update_post_meta($order_id, '_order_tax', $this->back_convert($_order_tax, $_woocs_order_rate));

        $_order_shipping_tax = get_post_meta($order_id, '_order_shipping_tax', true);
        update_post_meta($order_id, '_order_shipping_tax', $this->back_convert($_order_shipping_tax, $_woocs_order_rate));

        $_cart_discount = get_post_meta($order_id, '_cart_discount', true);
        update_post_meta($order_id, '_cart_discount', $this->back_convert($_cart_discount, $_woocs_order_rate));

//***

        global $wpdb;
        $get_items_sql = $wpdb->prepare("SELECT order_item_id,order_item_type FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d ", $order_id);
        $line_items = $wpdb->get_results($get_items_sql, ARRAY_N);
        if (!empty($line_items) AND is_array($line_items))
        {
            foreach ($line_items as $v)
            {
                $order_item_id = $v[0];
                $order_item_type = $v[1];

                switch ($order_item_type)
                {
                    case 'line_item':

                        $amount = wc_get_order_item_meta($order_item_id, '_line_subtotal', true);
                        wc_update_order_item_meta($order_item_id, '_line_subtotal', $this->back_convert($amount, $_woocs_order_rate, $decimals));

                        $amount = wc_get_order_item_meta($order_item_id, '_line_total', true);
                        wc_update_order_item_meta($order_item_id, '_line_total', $this->back_convert($amount, $_woocs_order_rate, $decimals));

                        $amount = wc_get_order_item_meta($order_item_id, '_line_subtotal_tax', true);
                        wc_update_order_item_meta($order_item_id, '_line_subtotal_tax', $this->back_convert($amount, $_woocs_order_rate, $decimals));

                        $amount = wc_get_order_item_meta($order_item_id, '_line_tax', true);
                        wc_update_order_item_meta($order_item_id, '_line_tax', $this->back_convert($amount, $_woocs_order_rate, $decimals));

                        $_line_tax_data = wc_get_order_item_meta($order_item_id, '_line_tax_data', true);
                        if (!empty($_line_tax_data) AND is_array($_line_tax_data))
                        {
                            foreach ($_line_tax_data as $key => $values)
                            {
                                if (!empty($values))
                                {
                                    if (is_array($values))
                                    {
                                        foreach ($values as $k => $value)
                                        {
                                            if (is_numeric($value))
                                            {
                                                $_line_tax_data[$key][$k] = $this->back_convert($value, $_woocs_order_rate, $decimals);
                                            }
                                        }
                                    } else
                                    {
                                        if (is_numeric($values))
                                        {
                                            $_line_tax_data[$key] = $this->back_convert($values, $_woocs_order_rate, $decimals);
                                        }
                                    }
                                }
                            }
                        }

                        wc_update_order_item_meta($order_item_id, '_line_tax_data', $_line_tax_data);

                        break;

                    case 'shipping':
                        $amount = wc_get_order_item_meta($order_item_id, 'cost', true);
                        wc_update_order_item_meta($order_item_id, 'cost', $this->back_convert($amount, $_woocs_order_rate, $decimals));


                        $taxes = wc_get_order_item_meta($order_item_id, 'taxes', true);

                        if (!empty($taxes) AND is_array($taxes))
                        {
                            foreach ($taxes as $key => $values)
                            {
                                if (!empty($values))
                                {
                                    if (is_array($values))
                                    {
                                        foreach ($values as $k => $value)
                                        {
                                            if (is_numeric($value))
                                            {
                                                $taxes[$key][$k] = $this->back_convert($value, $_woocs_order_rate, $decimals);
                                            }
                                        }
                                    } else
                                    {
                                        if (is_numeric($values))
                                        {
                                            $taxes[$key] = $this->back_convert($values, $_woocs_order_rate, $decimals);
                                        }
                                    }
                                }
                            }
                        }

                        wc_update_order_item_meta($order_item_id, 'taxes', $taxes);

                        break;

                    case 'tax':
                        $amount = wc_get_order_item_meta($order_item_id, 'tax_amount', true);
                        wc_update_order_item_meta($order_item_id, 'tax_amount', $this->back_convert($amount, $_woocs_order_rate, 3));

                        $amount = wc_get_order_item_meta($order_item_id, 'shipping_tax_amount', true);
                        wc_update_order_item_meta($order_item_id, 'shipping_tax_amount', $this->back_convert($amount, $_woocs_order_rate, $decimals));

                        break;

                    default:
                        break;
                }
            }
        }

//***

        $order = new WC_Order($order_id);
        $refunds = $order->get_refunds();

        if (!empty($refunds))
        {
            foreach ($refunds as $refund)
            {
                $post_id = $refund->id;
                $amount = get_post_meta($post_id, '_refund_amount', true);
                update_post_meta($post_id, '_refund_amount', $this->back_convert($amount, $_woocs_order_rate, $decimals));
                $amount = get_post_meta($post_id, '_order_total', true);
                update_post_meta($post_id, '_order_total', $this->back_convert($amount, $_woocs_order_rate, $decimals));
                update_post_meta($post_id, '_order_currency', $this->default_currency);
            }
        }
    }

//ajax
    public function woocs_recalculate_order_data()
    {
        if (!current_user_can('manage_options'))
        {
            return;
        }

        $this->recalculate_order((int) $_REQUEST['order_id']);
        wp_die('done');
    }

//***************** BEGIN ADDITIONAL INFO HTML ON THE CHECKOUT+CART ***************
//only attach some info in html
//wp-content\plugins\woocommerce\templates\cart\cart.php
    public function woocommerce_cart_item_price($product_price, $cart_item, $cart_item_key)
    {
        $user_currency = $this->get_currency_by_country($this->storage->get_val('woocs_user_country'));
        $currencies = $this->get_currencies();

        if ($user_currency != $this->current_currency AND ! empty($user_currency))
        {
            $tmp_curr_currency = $this->current_currency;
            $this->set_currency($user_currency);


//***
            $back_convert = true;
            if ($user_currency == $this->default_currency)
            {
                $back_convert = false;
            }
            if ($this->is_multiple_allowed)
            {
                $back_convert = true;
            }
            if (!$this->is_multiple_allowed AND ( $user_currency !== $this->default_currency))
            {
                $back_convert = false;
            }
//***


            if ($back_convert)
            {
                $cart_price = $this->back_convert($cart_item['line_total'], $currencies[$tmp_curr_currency]['rate']) / $cart_item['quantity'];
            } else
            {
                $cart_price = $cart_item['line_total'] / $cart_item['quantity'];
            }


            $wc_price = $this->wc_price($cart_price, true);
            $product_price.=$this->get_cart_item_price_html($wc_price);


            $this->set_currency($tmp_curr_currency);
        }

        return $product_price;
    }

//wp-content\plugins\woocommerce\templates\cart\cart.php
    public function woocommerce_cart_item_subtotal($product_subtotal, $cart_item, $cart_item_key)
    {
        $user_currency = $this->get_currency_by_country($this->storage->get_val('woocs_user_country'));
        $currencies = $this->get_currencies();

        if ($user_currency != $this->current_currency AND ! empty($user_currency))
        {
            $tmp_curr_currency = $this->current_currency;
            $this->set_currency($user_currency);

            $cart_amount = $cart_item['line_subtotal'];

//***
            $back_convert = true;
            if ($user_currency == $this->default_currency)
            {
                $back_convert = false;
            }
            if ($this->is_multiple_allowed)
            {
                $back_convert = true;
            }
            if (!$this->is_multiple_allowed AND ( $user_currency !== $this->default_currency))
            {
                $back_convert = false;
            }
//***

            if ($back_convert)
            {
                $cart_amount = $this->back_convert($cart_amount, $currencies[$tmp_curr_currency]['rate']);
            }


            $wc_price = $this->wc_price($cart_amount, true);
            $product_subtotal.=$this->get_cart_item_price_html($wc_price);


            $this->set_currency($tmp_curr_currency);
        }

        return $product_subtotal;
    }

//wp-content\plugins\woocommerce\templates\cart\cart-totals.php
    public function woocommerce_cart_subtotal($cart_subtotal, $compound, $woo)
    {
        $user_currency = $this->get_currency_by_country($this->storage->get_val('woocs_user_country'));
        $currencies = $this->get_currencies();

        if ($user_currency != $this->current_currency AND ! empty($user_currency))
        {

            $amount = 0;
            if ($compound)
            {
                $amount = $woo->cart_contents_total + $woo->shipping_total + $woo->get_taxes_total(false, false);
// Otherwise we show cart items totals only (before discount)
            } else
            {

// Display varies depending on settings
                if ($woo->tax_display_cart == 'excl')
                {
                    $amount = $woo->subtotal_ex_tax;
                } else
                {
                    $amount = $woo->subtotal;
                }
            }
//***


            $tmp_curr_currency = $this->current_currency;
            $this->set_currency($user_currency);

//***
            $back_convert = true;
            if ($user_currency == $this->default_currency)
            {
                $back_convert = false;
            }
            if ($this->is_multiple_allowed)
            {
                $back_convert = true;
            }
            if (!$this->is_multiple_allowed AND ( $user_currency !== $this->default_currency))
            {
                $back_convert = false;
            }
//***

            if ($back_convert)
            {
                $amount = $this->back_convert($amount, $currencies[$tmp_curr_currency]['rate']);
            }

            $wc_price = $this->wc_price($amount, true);
            $cart_subtotal.=$this->get_cart_item_price_html($wc_price);


            $this->set_currency($tmp_curr_currency);
        }

        return $cart_subtotal;
    }

//wp-content\plugins\woocommerce\includes\class-wc-cart.php
    public function woocommerce_cart_total($html_value)
    {

        $user_currency = $this->get_currency_by_country($this->storage->get_val('woocs_user_country'));
        $currencies = $this->get_currencies();

//***
        if ($user_currency != $this->current_currency AND ! empty($user_currency))
        {

            $tmp_curr_currency = $this->current_currency;
            $this->set_currency($user_currency);
            $total = WC()->cart->total;

//***
            $back_convert = true;
            if ($user_currency == $this->default_currency)
            {
                $back_convert = false;
            }
            if ($this->is_multiple_allowed)
            {
                $back_convert = true;
            }
            if (!$this->is_multiple_allowed AND ( $user_currency !== $this->default_currency))
            {
                $back_convert = false;
            }
//***

            if ($back_convert)
            {
                $total = $this->back_convert($total, $currencies[$tmp_curr_currency]['rate']);
            }

            $wc_price = $this->wc_price($total, true);
            $html_value.=$this->get_cart_item_price_html($wc_price);


            $this->set_currency($tmp_curr_currency);
        }

//*** comment this block, for jeroen shipping plugin only
        /*
          if ($this->is_multiple_allowed AND $this->current_currency != $this->default_currency)
          {
          $total = WC()->cart->total;
          $currencies = $this->get_currencies();
          if (!empty(WC()->cart->shipping_taxes))
          {
          //as it recounted twice - down it!
          foreach (WC()->cart->shipping_taxes as $key => $value)
          {
          $total = $total - ($value / $currencies[$this->current_currency]['rate'] - $value);
          }
          }

          WC()->cart->total = $total;
          return wc_price($total);
          }
         */



        return $html_value;
    }

//wp-content\plugins\woocommerce\includes\class-wc-cart.php
    public function woocommerce_cart_totals_taxes_total_html($html_value)
    {

        $user_currency = $this->get_currency_by_country($this->storage->get_val('woocs_user_country'));
        $currencies = $this->get_currencies();

        if ($user_currency != $this->current_currency AND ! empty($user_currency))
        {
            $tmp_curr_currency = $this->current_currency;
            $this->set_currency($user_currency);

            $total = 0;
            $compound = true;
            foreach (WC()->cart->taxes as $key => $tax)
            {
                if (!$compound && WC_Tax::is_compound($key))
                    continue;
                $total += $tax;
            }
            foreach (WC()->cart->shipping_taxes as $key => $tax)
            {
                if (!$compound && WC_Tax::is_compound($key))
                    continue;
                $total += $tax;
            }


//***
            $back_convert = true;
            if ($user_currency == $this->default_currency)
            {
                $back_convert = false;
            }
            if ($this->is_multiple_allowed)
            {
                $back_convert = true;
            }
            if (!$this->is_multiple_allowed AND ( $user_currency !== $this->default_currency))
            {
                $back_convert = false;
            }
//***

            if ($back_convert)
            {
                $total = $this->back_convert($total, $currencies[$tmp_curr_currency]['rate']);
            }

            $wc_price = $this->wc_price($total, true);
            $html_value.=$this->get_cart_item_price_html($wc_price);


            $this->set_currency($tmp_curr_currency);
        }


        return $html_value;
    }

    public function woocommerce_cart_tax_totals($tax_totals, $woo)
    {
//$woo is WC_Cart
        $user_currency = $this->get_currency_by_country($this->storage->get_val('woocs_user_country'));
        $currencies = $this->get_currencies();

        if ($user_currency != $this->current_currency AND ! empty($user_currency))
        {
            $tmp_curr_currency = $this->current_currency;
            $this->set_currency($user_currency);

            if (!empty($tax_totals))
            {
                foreach ($tax_totals as $key => $o)
                {

                    $amount = $o->amount;


//***
                    $back_convert = true;
                    if ($user_currency == $this->default_currency)
                    {
                        $back_convert = false;
                    }
                    if ($this->is_multiple_allowed)
                    {
                        $back_convert = true;
                    }
                    if (!$this->is_multiple_allowed AND ( $user_currency !== $this->default_currency))
                    {
                        $back_convert = false;
                    }
//***

                    if ($back_convert)
                    {
                        $amount = $this->back_convert($amount, $currencies[$tmp_curr_currency]['rate']);
                    }

                    $wc_price = $this->wc_price($amount, true);
                    $o->formatted_amount.=$this->get_cart_item_price_html($wc_price);
                }
            }


            $this->set_currency($tmp_curr_currency);
        }



        return $tax_totals;
    }

//wp-content\plugins\woocommerce\includes\wc-cart-functions.php
    public function woocommerce_cart_shipping_method_full_label($label, $method)
    {
//$woo is WC_Cart
        if ($method->cost > 0)
        {
            $user_currency = $this->get_currency_by_country($this->storage->get_val('woocs_user_country'));
            $currencies = $this->get_currencies();

            if ($user_currency != $this->current_currency AND ! empty($user_currency))
            {
                $tmp_curr_currency = $this->current_currency;
                $this->set_currency($user_currency);

                if (WC()->cart->tax_display_cart == 'excl')
                {
                    $amount = $method->cost;
                } else
                {
                    $amount = $method->cost + $method->get_shipping_tax();
                }


//***
                $back_convert = true;
                if ($user_currency == $this->default_currency)
                {
                    $back_convert = false;
                }
                if ($this->is_multiple_allowed)
                {
                    $back_convert = true;
                }
                if (!$this->is_multiple_allowed AND ( $user_currency !== $this->default_currency))
                {
                    $back_convert = false;
                }
//***

                if ($back_convert)
                {
                    $amount = $this->back_convert($amount, $currencies[$tmp_curr_currency]['rate']);
                }

                $wc_price = $this->wc_price($amount, true);

                $label.=$this->get_cart_item_price_html($wc_price);

                $this->set_currency($tmp_curr_currency);
            }
        }

        return $label;
    }

    private function get_cart_item_price_html($wc_price)
    {
        $html = '<div class="woocs_cart_item_price">';
        $html.=apply_filters('woocs_get_approximate_amount_text', sprintf(__('(Approx. %s)'), $wc_price), $wc_price);
        $html.='</div>';
        return $html;
    }

//***************** END ADDITIONAL INFO HTML ON THE CHECKOUT+CART ***************
//custom code for Woocommerce Advanced Shipping by http://jeroensormani.com/ in multiple mode
    public function woocommerce_cart_get_taxes($taxes, $woo_cart)
    {
        if ($this->is_multiple_allowed AND $this->current_currency != $this->default_currency)
        {
            $currencies = $this->get_currencies();
            if (!empty($woo_cart->shipping_taxes))
            {
//as it recounted twice - down it!
                foreach ($woo_cart->shipping_taxes as $key => $value)
                {
                    $woo_cart->shipping_taxes[$key] = $value * $currencies[$this->current_currency]['rate'];
                }
            }
// Merge
            foreach (array_keys($woo_cart->taxes + $woo_cart->shipping_taxes) as $key)
            {
                $taxes[$key] = ( isset($woo_cart->shipping_taxes[$key]) ? $woo_cart->shipping_taxes[$key] : 0 ) + ( isset($woo_cart->taxes[$key]) ? $woo_cart->taxes[$key] : 0 );
            }
        }
        return $taxes;
    }

//class-wc-cart.php -> public function calculate_totals()
    public function woocommerce_after_calculate_totals($woo_cart)
    {
        if ($this->is_multiple_allowed AND $this->current_currency != $this->default_currency AND is_ajax())
        {
            if (isset($_POST['billing_address_1']))
            {
                $currencies = $this->get_currencies();
                if (!empty($woo_cart->shipping_taxes))
                {
//as it recounted twice - down it!
                    foreach ($woo_cart->shipping_taxes as $key => $value)
                    {
                        $woo_cart->shipping_taxes[$key] = $value * $currencies[$this->current_currency]['rate'];
                    }
                }
// Merge
                foreach (array_keys($woo_cart->taxes + $woo_cart->shipping_taxes) as $key)
                {
                    $woo_cart->taxes[$key] = ( isset($woo_cart->shipping_taxes[$key]) ? $woo_cart->shipping_taxes[$key] : 0 ) + ( isset($woo_cart->taxes[$key]) ? $woo_cart->taxes[$key] : 0 );
                }


//***

                $total = $woo_cart->total;
                $currencies = $this->get_currencies();
                if (!empty($woo_cart->shipping_taxes))
                {
//as it recounted twice - down it!
                    foreach ($woo_cart->shipping_taxes as $key => $value)
                    {
                        $total = $total - ($value / $currencies[$this->current_currency]['rate'] - $value);
                    }
                }

                $woo_cart->total = $total;
            }
        }
    }

    public function escape($value)
    {
        return sanitize_text_field(esc_html($value));
    }

}

//+++
if (isset($_GET['P3_NOCACHE']))
{
//stupid trick for that who believes in P3
    return;
}
//+++
$WOOCS = new WOOCS();
$GLOBALS['WOOCS'] = $WOOCS;
add_action('init', array($WOOCS, 'init'), 1);

//includes/wc-core-functions.php #156
//includes/wc-formatting-functions.php #297
//includes/admin/post-types/meta-boxes/class-wc-meta-box-order-totals.php
//wp-content\plugins\woocommerce\includes\wc-formatting-functions.php
//wp-content\plugins\woocommerce\includes\wc-cart-functions.php
//wp-content\plugins\woocommerce\includes\wc-conditional-functions.php
//wp-content\plugins\woocommerce\includes\class-wc-cart.php
//wp-content\plugins\woocommerce\includes\abstracts\abstract-wc-product.php


