<?php
    class WBS_Profile_Manager
    {
        public static function setup()
        {
            self::instance();
        }

        public static function instance($resetCache = false)
        {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                add_filter('woocommerce_shipping_methods', array(self::$instance, '_registerProfiles'));
            }

            if ($resetCache) {
                unset(self::$instance->orderedProfiles);
                unset(self::$instance->profileInstances);
            }

            return self::$instance;
        }

        /** @return WC_Weight_Based_Shipping[] */
        public function profiles()
        {
            if (!isset($this->orderedProfiles)) {

                $this->orderedProfiles = array();

                /** @var WC_Shipping $shipping */
                $shipping = WC()->shipping;
                foreach ($shipping->load_shipping_methods() as $method)
                {
                    if ($method instanceof WC_Weight_Based_Shipping)
                    {
                        $this->orderedProfiles[] = $method;
                    }
                }
            }

            return $this->orderedProfiles;
        }

        public function profile($name = null)
        {
            $this->find_suitable_id($name);
            $profiles = $this->instantiateProfiles();
            return @$profiles[$name];
        }

        public function profile_exists($name)
        {
            $profiles = $this->instantiateProfiles();
            return isset($profiles[$name]);
        }

        public function find_suitable_id(&$profileId)
        {
            if (!$profileId && !($profileId = $this->current_profile_id())) {
                return $profileId = null;
            }

            return $profileId;
        }

        public function current_profile_id()
        {
            $profile_id = null;

            if (is_admin()) {
                
                if (empty($profile_id)) {
                    $profile_id = @$_GET['wbs_profile'];
                }

                if (empty($profile_id) && ($profiles = $this->profiles())) {
                    $profile_id = $profiles[0]->profile_id;
                }

                if (empty($profile_id)) {
                    $profile_id = 'main';
                }
            }

            return $profile_id;
        }

        public function new_profile_id()
        {
            if (!$this->profile_exists('main')) {
                return 'main';
            }

            $timestamp = time();

            $i = null;
            do {
                $new_profile_id = trim($timestamp.'-'.$i++, '-');
            } while ($this->profile_exists($new_profile_id));

            return $new_profile_id;
        }



        public function _registerProfiles($methods)
        {
            return array_merge($methods, $this->instantiateProfiles());
        }

        public static function listAvailableProfileIds($pluginPrefix = WC_Weight_Based_Shipping::PLUGIN_PREFIX, $idPrefix = null)
        {
            $ids = array();

            $settingsOptionNamePattern = sprintf('/^%s%s(\\w+)_settings$/',
                preg_quote($pluginPrefix, '/'), preg_quote($idPrefix, '/')
            );

            foreach (array_keys(wp_load_alloptions()) as $option) {
                $matches = array();
                if (preg_match($settingsOptionNamePattern, $option, $matches)) {
                    $ids[] = $matches[1];
                }
            }

            $ids = WbsFactory::getRulesOrderStorage()->sort($ids);

            return $ids;
        }

        public static function getRuleSettingsOptionName($ruleId, $pluginPrefix = WC_Weight_Based_Shipping::PLUGIN_PREFIX)
        {
            return sprintf('%s%s_settings', $pluginPrefix, $ruleId);
        }


        private static $instance;

        private $orderedProfiles;

        /** @var WC_Weight_Based_Shipping[] */
        private $profileInstances;

        private function instantiateProfiles()
        {
            if (!isset($this->profileInstances)) {

                $this->profileInstances = array();

                $profileIds = self::listAvailableProfileIds();
                if (empty($profileIds)) {
                    $profileIds[] = $this->new_profile_id();
                }

                foreach ($profileIds as $profileId) {
                    $this->profileInstances[$profileId] = new WC_Weight_Based_Shipping($profileId);
                }

                if (is_admin() &&
                    ($editingProfileId = @$_GET['wbs_profile']) &&
                    !isset($this->profileInstances[$editingProfileId])) {

                    $editingProfile = new WC_Weight_Based_Shipping($editingProfileId);
                    $editingProfile->_stub = true;
                    $this->profileInstances[$editingProfileId] = $editingProfile;
                }

                if ($currentProfile = $this->profile()) {
                    add_action(
                        'woocommerce_update_options_shipping_' . $currentProfile->id,
                        array($currentProfile, 'process_admin_options')
                    );
                }
            }

            return $this->profileInstances;
        }
    }
?>