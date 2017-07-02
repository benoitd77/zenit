<?php
    class WBS_Upgrader
    {
        public static function setup($pluginFile)
        {
            if (!isset(self::$instance)) {
                $upgrader = new self($pluginFile);
                $upgrader->onLoad();
                self::$instance = $upgrader;
            }
        }

        public static function instance()
        {
            return self::$instance;
        }

        public function __construct($pluginFile)
        {
            $this->pluginFile = $pluginFile;
            $this->upgradeNotices = new WBS_Upgrade_Notices('woowbs_upgrade_notices', 'woowbs_remove_upgrade_notice');
        }

        public function onLoad()
        {
            $this->setupHooks();
        }

        public function onAdminInit()
        {
            $this->checkForUpgrade();
        }

        public function onAdminNotices()
        {
            $this->upgradeNotices->show();
        }

        public function removeUpgradeNotices()
        {
            $id = @$_GET[$this->upgradeNotices->getRemoveNoticeUrlFlagName()];
            if (!isset($id)) {
                return false;
            }

            return $this->upgradeNotices->remove($id);
        }

        /** @var WBS_Upgrader */
        private static $instance;
        private $pluginFile;
        private $upgradeNotices;

        private function checkForUpgrade()
        {
            $updateCurrentVersion = false;

            $currentVersion = null; {
                $pluginData = get_plugin_data($this->pluginFile, false, false);
                $currentVersion = $pluginData['Version'];
            }

            $previousVersion = get_option('woowbs_version');
            if (empty($previousVersion)) {
                $hasSomeRules = !!self::listAvailableProfileIds();
                $hasSomeRules = $hasSomeRules || !!self::listAvailableProfileIds('woocommerce_', 'WC_Weight_Based_Shipping_');
                $previousVersion = $hasSomeRules ? '2.6.8' : $currentVersion;
                $updateCurrentVersion = true;
            }

            if ($previousVersion !== $currentVersion) {

                $updateCurrentVersion = true;

                if (version_compare($previousVersion, '2.2.1') < 0) {
                    $this->upgradeNotices->add(WBS_Upgrade_Notice::createBehaviourChangeNotice('2.2.1', '
                        Previously, weight-based shipping option has not been shown to user if total
                        weight of their cart is zero. Since version 2.2.1 this is changed so shipping
                        option is available to user with price set to Handling Fee. If it does not
                        suite your needs well you can return previous behavior by setting Min Weight
                        to something a bit greater zero, e.g. 0.001, so that zero-weight orders will
                        not match constraints and the shipping option will not be shown.
                    '));
                }

                if (version_compare($previousVersion, '2.4.0') < 0) {
                    $profiles = WBS_Profile_Manager::instance()->profiles();
                    foreach ($profiles as $profile) {
                        $option = $profile->get_wp_option_name();
                        $config = get_option($option);
                        $config['extra_weight_only'] = 'no';
                        update_option($option, $config);
                    }
                }

                if (version_compare($previousVersion, '2.6.3') < 0) {
                    $this->upgradeNotices->add(WBS_Upgrade_Notice::createBehaviourChangeNotice('2.6.2', '
                        Previously, base Handling Fee has not been added to the shipping price if user
                        cart contained only items with shipping classes specified in the Shipping
                        Classes Overrides section. Starting from version 2.6.2 base Handling Fee is
                        applied in any case. This behavior change is only affecting you if you use
                        Shipping Classes Overrides. If you don\'t use it just ignore this message.
                    '));
                }

                if (version_compare($previousVersion, '4.0.0') < 0) {

                    // Rename settings options
                    if ($ruleIdsToUpdate = self::listAvailableProfileIds('woocommerce_', 'WC_Weight_Based_Shipping_')) {

                        foreach ($ruleIdsToUpdate as $oldSettingsName => $id) {

                            $settings = get_option($oldSettingsName);

                            # Don't remove old settings entry. Keep it as a backup.
                            //delete_option($oldSettingsName);

                            $newSettingsName = WBS_Profile_Manager::getRuleSettingsOptionName($id);
                            if (!get_option($newSettingsName)) {
                                update_option($newSettingsName, $settings);
                            }
                        }

                        // Reset cache to load renamed rules
                        WBS_Profile_Manager::instance(true);
                    }

                    $rangeFieldsMap = array(
                        'weight'      => array( 'min_weight',   'max_weight',   false ),
                        'subtotal'    => array( 'min_subtotal', 'max_subtotal', false ),
                        'price_clamp' => array( 'min_price',    'max_price',    true  ),
                    );

                    foreach (WBS_Profile_Manager::instance()->profiles() as $profile) {

                        $option = $profile->get_wp_option_name();
                        $config = get_option($option);

                        // Convert conditions
                        foreach ($rangeFieldsMap as $newField => $oldFields) {

                            if (!isset($config[$newField])) {

                                $range = null;

                                $min = (float)@$config[$oldFields[0]];
                                $max = (float)@$config[$oldFields[1]];
                                if ($min || $max) {
                                    $range = new WbsRange(
                                        $min ? $min : null,
                                        $max ? $max : null,
                                        true,
                                        $oldFields[2] || !!$max
                                    );
                                }

                                if (!isset($range)) {
                                    $range = new WbsRange();
                                }

                                $config[$newField] = $range->toArray();
                            }

                            unset($config[$oldFields[0]]);
                            unset($config[$oldFields[1]]);
                        }

                        $weightRange = WbsRange::fromArray(@$config['weight']);
                        $extraWeightOnly = @$config['extra_weight_only'];
                        unset($config['extra_weight_only']);

                        // Convert progressive rate
                        if (!isset($config['weight_rate'])) {

                            $config['weight_rate'] = self::convertToProgressiveRate(
                                @$config['rate'],
                                @$config['weight_step'],
                                $extraWeightOnly,
                                $weightRange->min
                            )->toArray();

                            unset($config['weight_step']);
                            unset($config['rate']);
                        }

                        // Convert shipping class rates
                        if (!isset($config['shipping_class_rates'])) {

                            /** @var WBS_Shipping_Class_Override_Set $oldRates */
                            if (($oldRates = @$config['shipping_class_overrides']) &&
                                ($oldRates = $oldRates->getOverrides())) {

                                $rates = new WbsBucketRates();

                                /** @var WBS_Shipping_Rate_Override $oldRate */
                                foreach ($oldRates as $oldRate) {

                                    $rates->add(new WbsBucketRate(
                                        $oldRate->getClass(),
                                        $oldRate->getFee(),
                                        self::convertToProgressiveRate(
                                            $oldRate->getRate(),
                                            @$oldRate->getWeightStep(),
                                            $extraWeightOnly,
                                            $weightRange->min
                                        )
                                    ));
                                }

                                $config['shipping_class_rates'] = $rates;
                            }

                            unset($config['shipping_class_overrides']);
                        }

                        update_option($option, $config);
                    }
                }
            }

            if ($updateCurrentVersion) {
                update_option('woowbs_version', $currentVersion);
            }
        }

        private function setupHooks()
        {
            add_action('admin_init', array($this, 'onAdminInit'));
            add_action('admin_notices', array($this, 'onAdminNotices'));
        }

        private static function convertToProgressiveRate($rate, $weightStep, $extraWeightOnly, $minWeightRange)
        {
            $rate = $rate * ($weightStep ? $weightStep : 1);
            $weightStep = $rate ? $weightStep : 0;
            $skip = $rate && $extraWeightOnly !== 'no' ? $minWeightRange : 0;
            return new WbsProgressiveRate($rate, $weightStep, $skip);
        }

        private static function listAvailableProfileIds($pluginPrefix = WC_Weight_Based_Shipping::PLUGIN_PREFIX, $idPrefix = null)
        {
            $ids = array();

            $settingsOptionNamePattern = sprintf('/^%s%s(\\w+)_settings$/',
                preg_quote($pluginPrefix, '/'), preg_quote($idPrefix, '/')
            );

            foreach (array_keys(wp_load_alloptions()) as $option) {
                $matches = array();
                if (preg_match($settingsOptionNamePattern, $option, $matches)) {
                    $ids[$matches[0]] = $matches[1];
                }
            }

            return $ids;
        }
    }