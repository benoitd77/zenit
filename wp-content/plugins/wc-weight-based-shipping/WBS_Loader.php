<?php
    class WBS_Loader
    {
        const ADMIN_SECTION_NAME = 'wc_weight_based_shipping';

        public static function loadWbs($pluginFile)
        {
            if (!self::$loaded) {
                self::$loaded = true;
                new WBS_Loader($pluginFile);
            }
        }

        public function __construct($pluginFile)
        {
            $this->pluginFile = wp_normalize_path($pluginFile);
            add_action('plugins_loaded', array($this, 'load'), 0);
            add_filter('plugin_action_links_' . plugin_basename($this->pluginFile), array($this, '_outputSettingsLink'));
        }

        public function load()
        {
            if (!$this->woocommerceAvailable()) return;
            $this->loadLanguage();
            $this->loadFunctions();
            $this->loadClasses();
            WBS_Profile_Manager::setup();
            WBS_Upgrader::setup($this->pluginFile);
            add_filter('woocommerce_get_sections_shipping', array($this, '_fixShippingMethodsLinks'));

            if (is_admin()) {

                add_action('wp_ajax_woowbs_update_rules_order', array($this, '_updateRulesOrder'));

                if (@$_GET['section'] === self::ADMIN_SECTION_NAME) {
                    add_action('admin_enqueue_scripts', array($this, '_enqueueScripts'));
                }
            }
        }

        public function _outputSettingsLink($links)
        {
            array_unshift($links, '<a href="'.esc_html(WbsRuleUrls::generic()).'">'.__('Settings', 'woowbs').'</a>');
            return $links;
        }

        public function _fixShippingMethodsLinks($sections)
        {
            foreach (WBS_Profile_Manager::instance()->profiles() as $profile) {
                unset($sections[$profile->id]);
            }
            
            $sections[self::ADMIN_SECTION_NAME] = WC_Weight_Based_Shipping::getTitle();

            return $sections;
        }
        
        public function _loadClass($class)
        {
            foreach ($this->classDirs as $dir) {
                if (file_exists($file = "{$dir}/{$class}.php")) {
                    require_once($file);
                    return true;
                }
            }

            if (in_array($class, array('WBS_Shipping_Rate_Override', 'WBS_Shipping_Class_Override_Set'))) {
                /** @noinspection PhpIncludeInspection */
                require_once($this->legacyClassesFile);
            }

            return false;
        }

        public function _enqueueScripts()
        {
            wp_enqueue_script('woowbs-admin', plugins_url('assets/admin.js', $this->pluginFile), array('jquery-ui-sortable'));
            wp_localize_script('woowbs-admin', 'woowbs_ajax_vars', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        public function _updateRulesOrder() {
            $order = WbsFactory::getRulesOrderStorage();
            $order->set($_POST['profiles']);
            WbsWcTools::purgeWoocommerceShippingCache();
            wp_die();
        }

        private static $loaded;
        private $pluginFile;
        private $classDirs;
        private $legacyClassesFile;

        private function woocommerceAvailable()
        {
            return class_exists('WC_Shipping_Method');
        }

        private function loadLanguage()
        {
            load_plugin_textdomain('woowbs', false, dirname(plugin_basename($this->pluginFile)).'/lang/');
        }

        private function loadFunctions()
        {
            require_once(dirname(__FILE__) . "/functions.php");
        }

        private function loadClasses()
        {
            $wbsdir = dirname(__FILE__);
            $this->classDirs = array($wbsdir, "{$wbsdir}/Model", "{$wbsdir}/Upgrade");
            $this->legacyClassesFile = "{$wbsdir}/legacy.php";
            spl_autoload_register(array($this, '_loadClass'));
        }
    }