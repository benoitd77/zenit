<?php
namespace Wbs;

use WbsVendors\Dgm\Arrays\Arrays;
use WbsVendors\Dgm\Shengine\Interfaces\IProcessor;
use WbsVendors\Dgm\Shengine\Processing\Processor;
use WbsVendors\Dgm\Shengine\Units;
use WbsVendors\Dgm\Shengine\Woocommerce\Converters\PackageConverter;
use WbsVendors\Dgm\Shengine\Woocommerce\Converters\RateConverter;
use WbsVendors\Dgm\WcTools\WcTools;
use Wbs\Services\ApiService;
use Wbs\Services\ApiService\Apis\ConfigApi;
use Wbs\Services\ApiService\Apis\LegacyConfigApi;
use WC_Shipping_Method;
use WP_Term;


class ShippingMethod extends WC_Shipping_Method
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct($instanceId = null)
    {
        $this->plugin_id = Plugin::ID;
        $this->id = Plugin::ID;
        $this->title = $this->method_title = 'Weight Based Shipping';
        $this->instance_id = absint($instanceId);

        $this->supports = array(
            'settings',
            'shipping-zones',
            'instance-settings',
            'global-instance',
        );

        $this->init_settings();
    }

    public function config($config = null)
    {
        $optionKey = $this->get_option_key();

        if (func_num_args()) {
            $updated = update_option($optionKey, $config);
            if ($updated) {
                WcTools::purgeWoocommerceShippingCache();
            }
        } else {
            $config = get_option($optionKey, null);
            $config['enabled'] = WcTools::yesNo2Bool(isset($config['enabled']) ? $config['enabled'] : true);
        }

        return $config;
    }

    public function calculate_shipping($_package = array())
    {
        $package = PackageConverter::fromWoocommerceToCore($_package);

        $processor = new Processor();
        $rules = $this->loadRules($processor);
        $rates = $processor->process($rules, $package);

        $_rates = RateConverter::fromCoreToWoocommerce(
            $rates,
            $this->title,
            join('_', array_filter(array($this->id, @$this->instance_id)))
        );

        foreach ($_rates as $_rate) {
            $this->add_rate($_rate);
        }
    }

    public function admin_options()
    {
        if (did_action('admin_enqueue_scripts')) {
            $this->_enqueueAssets();
        } else {
            add_action('admin_enqueue_scripts', array($this, '_enqueueAssets'));
        }

        parent::admin_options();
    }

    public function get_admin_options_html()
    {
        ob_start();
            /** @noinspection PhpIncludeInspection */
            include(Plugin::instance()->meta->paths->tplFile);
        return ob_get_clean();
    }

    public function get_instance_id()
    {
        // A hack to prevent Woocommerce 2.6 from skipping global method instance
        // rates in WC_Shipping::calculate_shipping_for_package()
        return (method_exists('parent', 'get_instance_id') ? parent::get_instance_id() : $this->instance_id) ?: -1;
    }

    public function get_option_key()
    {
        return join('_', array_filter(array(
            $this->plugin_id,
            $this->instance_id,
            'config',
        )));
    }

    public function init_settings()
    {
        $this->settings = $this->config();
        $this->enabled = $this->settings['enabled'] = WcTools::bool2YesNo($this->settings['enabled']);
    }

    public function get_instance_option_key()
    {
        return self::get_option_key();
    }

    public function init_instance_settings()
    {
        $this->init_settings();
    }

    public function _enqueueAssets() {

        $plugin = Plugin::instance();
        $version = $plugin->meta->version;
        $paths = $plugin->meta->paths;

        if (defined('WBS_DEV')) {
            wp_register_script('wbs-polyfills', $paths->getAssetUrl('polyfills.js'));
            wp_register_script('wbs-vendor', $paths->getAssetUrl('vendor.js'), array('wbs-polyfills'));
            wp_enqueue_script('wbs-app', $paths->getAssetUrl('app.js'), array('jquery', 'wbs-polyfills', 'wbs-vendor'));
        } else {
            wp_enqueue_script('wbs-app', $paths->getAssetUrl('client.js'), array('jquery'), $version);
        }

        wp_enqueue_script('jquery-ui-sortable');

        $currencyPlacement = explode('_', get_option('woocommerce_currency_pos'));

        wp_localize_script('wbs-app', 'wbs_js_data', array(

            'locations' => self::getAllLocations(),

            'shippingClasses' => self::getAllShippingClasses(),

            'weightUnit' => get_option('woocommerce_weight_unit'),

            'currency' => array(
                'symbol' => html_entity_decode(get_woocommerce_currency_symbol()),
                'right' => $currencyPlacement[0] === 'right',
                'withSpace' => @$currencyPlacement[1] === 'space',
            ),

            'config' => $this->config(),

            'isGlobalInstance' => empty($this->instance_id),

            'endpoints' => array(

                'config' =>
                    ApiService::endpoint(ConfigApi::className())->url(array('instance_id' => $this->instance_id)),

                'legacyConfig' =>
                    $plugin->legacyConfig->exists()
                        ? ApiService::endpoint(LegacyConfigApi::className())->url()
                        : null
            ),
        ));
    }

    static public function className()
    {
        return get_called_class();
    }

    static private function getStateCode($cc, $sc = null)
    {
        if (self::isWildcardStateCode($sc)) {
            $sc = null;
        }

        return rtrim("{$cc}:{$sc}", ":");
    }

    static private function isWildcardStateCode($sc)
    {
        return !$sc || $sc === '*';
    }

    static private function getAllLocations()
    {
        $locations = array();

        foreach (WC()->countries->get_shipping_countries() as $cc => $country) {

            $country = html_entity_decode($country);

            $locations[] = array(
                'id' => self::getStateCode($cc, '*'),
                'name' => $country
            );

            if ($states = WC()->countries->get_states($cc)) {

                foreach ($states as $sc => $state) {

                    $state = html_entity_decode($state);

                    $locations[] = array(
                        'id' => self::getStateCode($cc, $sc),
                        'name' => "{$country} — {$state}"
                    );
                }
            }
        }

        return $locations;
    }

    static private function getAllShippingClasses()
    {
        return Arrays::map(WC()->shipping->get_shipping_classes(), function(WP_Term $term) {
            return array(
                'id' => (string)$term->term_id,
                'name' => (string)$term->name,
                'slug' => (string)$term->slug,
            );
        });
    }

    private function loadRules(IProcessor $processor)
    {
        $config = $this->config();

        $_rules =  array();
        if (isset($config['rules'])) {
            $_rules = $config['rules'];
        }

        $mapper = new RulesMapper(
            Units::fromPrecisions(
                pow(10, wc_get_price_decimals()),
                1000,
                1000
            ),
            $processor
        );

        $rules = $mapper->read($_rules);

        return $rules;
    }
}