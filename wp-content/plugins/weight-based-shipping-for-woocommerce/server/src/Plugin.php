<?php
namespace Wbs;

use Wbs\Common\Once;
use Wbs\Services\ApiService;
use Wbs\Services\LegacyConfigService;
use Wbs\Services\ServiceInstaller;


/**
 * @property-read PluginMeta $meta
 * @property-read LegacyConfigService $legacyConfig
 */
class Plugin
{   
    const ID = 'wbs';

    /**
     * @param string $entrypoint
     * @return void
     */
    static public function setupOnce($entrypoint)
    {
        if (!isset(self::$instance)) {
            $plugin = new Plugin($entrypoint);
            $plugin->setup();
            self::$instance = $plugin;
        }
    }

    /**
     * @return self
     */
    static public function instance()
    {
        return self::$instance;
    }

    /**
     * @param string $entrypoint
     */
    public function __construct($entrypoint)
    {
        $entrypoint = wp_normalize_path($entrypoint);

        $this->entrypoint = $entrypoint;
        $this->root = $root = dirname($this->entrypoint).'/server';

        $this->legacyConfigFactory = new Once(function() { return new LegacyConfigService(); });
        $this->metaFactory = new Once(function() use($entrypoint, $root) { return new PluginMeta($entrypoint, $root); });
    }

    public function setup()
    {
        register_activation_hook($this->entrypoint, array($this, '__resetShippingCache'));
        register_deactivation_hook($this->entrypoint, array($this, '__resetShippingCache'));

        add_filter('woocommerce_shipping_methods', array($this, '__woocommerceShippingMethods'));
        add_filter('plugin_action_links_' . plugin_basename($this->entrypoint), array($this, '__pluginActionLinks'));

        if (is_admin() && defined('DOING_AJAX') && DOING_AJAX) {
            $services = new ServiceInstaller();
            $services->installIfReady(new ApiService($this->legacyConfigFactory));
        }

        add_action('woocommerce_init', function() {
            if (function_exists('wc_get_shipping_method_count') && wc_get_shipping_method_count(true) == 0) {
                set_transient(
                    'wc_shipping_method_count_1_' . \WC_Cache_Helper::get_transient_version('shipping'), 1,
                    DAY_IN_SECONDS * 30
                );
            }
        });
    }

    /**
     * @internal
     */
    function __woocommerceShippingMethods($shippingMethods)
    {
        $shippingMethods[self::ID] = ShippingMethod::className();
        return $shippingMethods;
    }

    /**
     * @internal
     */
    function __pluginActionLinks($links)
    {
        $newLinks = array();
        if (!defined('WC_VERSION') || version_compare(WC_VERSION, '2.6.0', '>=')) {
            $newLinks[self::shippingUrl()] = 'Shipping Zones';
            $newLinks[self::shippingUrl(self::ID)] = 'Global Shipping Rules';
        } else {
            $newLinks[self::shippingUrl(self::ID)] = 'Settings';
        }

        foreach ($newLinks as $url => &$text) {
            $text = '<a href="'.esc_html($url).'">'.esc_html($text).'</a>';
        }

        array_splice($links, 0, 0, $newLinks);

        return $links;
    }

    /**
     * @internal
     */
    function __resetShippingCache()
    {
        $reset = function() {
            \WC_Cache_Helper::get_transient_version('shipping', true);
        };

        if (did_action('woocommerce_init')) {
            $reset();
        } else {
            add_action('woocommerce_init', $reset);
        }
    }

    /**
     * @internal
     */
    function __get($property)
    {
        switch ((string)$property) {
            case 'legacyConfig':
                return call_user_func($this->legacyConfigFactory);
            case 'meta':
                return call_user_func($this->metaFactory);
            default:
                trigger_error("Undefined property '{$property}'", E_USER_NOTICE);
                return null;
        }
    }


    /** @var self */
    private static $instance;
    
    /** @var string */
    private $entrypoint;
    
    /** @var string */
    private $root;

    /** @var callable */
    private $legacyConfigFactory;
    
    /** @var callable */
    private $metaFactory;


    static private function shippingUrl($section = null)
    {
        $query = array(
            "page" => "wc-settings",
            "tab" => "shipping",
        );

        if (isset($section)) {
            $query['section'] = $section;
        }

        $query = http_build_query($query);

        return admin_url("admin.php?{$query}");
    }
}