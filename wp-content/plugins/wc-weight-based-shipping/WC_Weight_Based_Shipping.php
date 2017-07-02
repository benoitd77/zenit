<?php
    class WC_Weight_Based_Shipping extends WC_Shipping_Method
    {
        const PLUGIN_PREFIX = 'woowbs_';

        public $plugin_id = self::PLUGIN_PREFIX;

        public $name;
        public $profile_id;

        /** @var WbsRange */
        public $weight;
        /** @var WbsRange */
        public $subtotal;
        public $subtotalWithTax;

        /** @var WbsProgressiveRate */
        public $weightRate;

        /** @var WbsBucketRates */
        public $shippingClassRates;

        /** @var WbsRange */
        public $priceClamp;

        public $valid = false;

        public $_stub = false;

        
        static public function getTitle()
        {
            return __('Weight Based', 'woowbs');
        }

        /** @noinspection PhpMissingParentConstructorInspection */
        public function __construct($profileId = null)
        {
            $manager = WBS_Profile_Manager::instance();

            // Force loading profiles when called from WooCommerce 2.3.9- save handler
            // to activate process_admin_option() with appropriate hook
            if (!isset($profileId)) {
                $manager->profiles();
            }

            $this->id = $manager->find_suitable_id($profileId);
            $this->profile_id = $profileId;

            $this->method_title = self::getTitle();

            $this->settingsHelper = new WbsSettingsHtmlTools($this);

            $this->safeInit();
        }

        public function __clone()
        {
            $manager = WBS_Profile_Manager::instance();

            $this->profile_id = $manager->new_profile_id();
            $this->id = $manager->find_suitable_id($this->profile_id);

            $this->name .= ' ('._x('copy', 'noun', 'woowbs').')';
            $this->settings['name'] = $this->name;

            $this->shippingClassRates = clone($this->shippingClassRates);

            $this->settingsHelper = new WbsSettingsHtmlTools($this);
        }


        public function init_form_fields()
        {
            static $regions;

            if (!isset($regions)) {
                $regions = array();
                foreach (WC()->countries->get_shipping_countries() as $cc => $country) {
                    $regions[self::getStateCode($cc, '*')] = $country;
                    if ($states = WC()->countries->get_states($cc)) {
                        foreach ($states as $sc => $state) {
                            $regions[self::getStateCode($cc, $sc)] = "{$country} — {$state}";
                        }
                    }
                }
            }

            $this->form_fields = array(
                ### Meta ###
                array(
                    'type' => 'title',
                    'title' => __('Rule Settings', 'woowbs'),
                ),
                    'enabled'    => array(
                        'title'   => __('Enable/Disable', 'woowbs'),
                        'type'    => 'checkbox',
                        'label'   => __('Enable this rule', 'woowbs'),
                        'default' => 'yes',
                    ),
                    'name'    => array(
                        'title'         => __('Label', 'woowbs'),
                        'description'   => __('This is an internal rule label, just for you. Customers don\'t see this.', 'woowbs'),
                        'type'          => 'text',
                        'default'       => sprintf($this->profile_id != 'main' ? __('rule #%s') : '%s', $this->profile_id),
                    ),
                    'title'      => array(
                        'title'       => __('Title', 'woowbs'),
                        'type'        => 'text',
                        'description' => __('This controls the title which the customer sees during checkout.', 'woowbs'),
                        'default'     => __('Weight Based Shipping', 'woowbs'),
                    ),
                    'tax_status' => array(
                        'title'       => __('Tax Status', 'woowbs'),
                        'type'        => 'select',
                        'default'     => 'taxable',
                        'class'		  => 'availability wc-enhanced-select',
                        'options'     => array(
                            'taxable'   => __('Taxable', 'woowbs'),
                            'none'      => __('None', 'woowbs'),
                        ),
                    ),
                ### Conditions ###
                array(
                    'type' => 'title',
                    'title' => __('Conditions', 'woowbs'),
                    'description' => __('Define when the delivery option should be shown to the customer. All the following conditions must be met to activate the rule.', 'woowbs'),
                ),
                    'availability' => array(
                        'title' 		=> __('Destination', 'woowbs'),
                        'type' 			=> 'select',
                        'default' 		=> 'all',
                        'class'			=> 'availability wc-enhanced-select',
                        'options'		=> array(
                            'all' 		    => __('All allowed', 'woowbs'),
                            'specific' 	    => __('Specific', 'woowbs'),
                            'excluding'     => __('All except specific', 'woowbs'),
                        ),
                    ),
                    'countries' => array(
                        'title' 		=> __('Regions', 'woowbs'),
                        'type' 			=> 'wbs_custom',
                        'wbs_real_type' => 'multiselect',
                        'wbs_row_class' => 'wbs-destination',
                        'class'			=> 'chosen_select',
                        'default' 		=> '',
                        'options'		=> $regions,
                        'custom_attributes' => array(
                            'data-placeholder' => __('Select regions', 'woowbs'),
                        ),
                        'html' =>
                            '<a class="select_all  button" href="#">'.__('Select all', 'woowbs').'</a> '.
                            '<a class="select_none button" href="#">'.__('Select none', 'woowbs').'</a>',
                    ),
                    'weight' => array(
                        'title'       => __('Order Weight', 'woowbs'),
                        'type'        => 'wbs_range',
                    ),
                    'subtotal' => array(
                        'title'       => __('Order Subtotal', 'woowbs'),
                        'type'              => 'wbs_custom',
                        'wbs_real_type'     => 'wbs_range',
                        'wbs_row_class'     => 'wbs-subtotal',
                    ),
                    'subtotal_with_tax' => array(
                        'title'             => __('Subtotal With Tax', 'woowbs'),
                        'type'              => 'checkbox',
                        'label'             => __('After tax included', 'woowbs'),
                    ),

                ### Calculations ###
                array(
                    'type' => 'title',
                    'title' => __('Costs', 'woowbs'),
                    'description' => __('This controls shipping price when this rule is active.', 'woowbs'),
                ),
                    'fee'        => array(
                        'title'       => __('Base Cost', 'woowbs'),
                        'type'        => 'decimal',
                        'description' => __('Leave empty or zero if your shipping price has no flat part.', 'woowbs'),
                    ),
                    'weight_rate' => array(
                        'title'       => __('Weight Rate', 'woowbs'),
                        'type'        => 'wbs_weight_rate',
                        'description' =>
                            __('Leave <code class="wbs-code">charge</code> field empty if your shipping price is flat.', 'woowbs').'<br>'.
                            __('Use <code class="wbs-code">over</code> field to skip weight part covered with Base Cost or leave it empty to charge for entire order weight.', 'woowbs'),
                    ),
                    'shipping_class_rates' => array(
                        'title'       => __('Shipping Classes', 'woowbs'),
                        'type'        => 'shipping_class_rates',
                        'description' => __('You can override some options for specific shipping classes', 'woowbs'),
                    ),

                ### Modificators ###
                array(
                    'type' => 'title',
                    'title' => __('Modificators', 'woowbs'),
                    'description' => __('With the following you can modify resulting shipping price', 'woowbs'),
                ),
                    'price_clamp' => array(
                        'title'           => __('Limit Total Cost', 'woowbs'),
                        'type'            => 'wbs_range',
                        'wbs_range_type'  => 'simple',
                        'description'     => __('If total shipping price (Base Cost + Weight Rate) exceeds specified range it will be changed to the either lower or upper bound appropriately.', 'woowbs'),
                    ),
            );

            $placeholders = array
            (
                'weight_unit' => __(get_option('woocommerce_weight_unit'), 'woowbs'),
                'currency' => get_woocommerce_currency_symbol(),
            );

            foreach ($this->form_fields as &$field)
            {
                $field['description'] = wbst(@$field['description'], $placeholders);
            }

            $this->form_fields = apply_filters('wbs_profile_settings_form', $this->form_fields, $this);
        }

        public function is_available($package)
        {
            $available = parent::is_available($package);
            $packageCountry = @$package['destination']['country'];
            $packageState = @$package['destination']['state'];

            if (!$available || !$packageCountry || $this->availability === 'all') {
                return $available;
            }

            $specifiedStateCodes = $this->countries;
            if (!$specifiedStateCodes) {
                $specifiedStateCodes = array();
            }

            $matches = count(array_intersect(
                array(
                    self::getStateCode($packageCountry, '*'),
                    self::getStateCode($packageCountry, $packageState),
                ),
                array_diff($specifiedStateCodes, self::countriesWithSpecificStates($specifiedStateCodes))
            ));

            $available = ($this->availability === 'excluding') === ($matches === 0);

            return $available;
        }

        public function calculate_shipping($package = array())
        {
            if (!$this->valid) {
                wc_add_notice("Price of shipping method '{$this->name}' couldn't be calculated");
                return;
            }

            $package = WbsPackage::fromWcPackage($package);

            if (!$this->weight->includes($package->getWeight()) ||
                !$this->subtotal->includes($package->getPrice($this->subtotalWithTax))) {
                return;
            }

            $defaultRate = new WbsBucketRate(
                '___wbs_base_pseudo_class',
                $this->fee,
                $this->weightRate
            );

            /** @var WbsItemBucket[] $buckets */
            $buckets = array(); {

                foreach ($package->getLines() as $line) {

                    $product = $line->getProduct();
                    $class = $product->get_shipping_class();

                    $rate = $this->shippingClassRates->findById($class);
                    if ($rate == null) {
                        $rate = $defaultRate;
                    }

                    $class = $rate->getId();
                    if (!isset($buckets[$class])) {
                        $buckets[$class] = new WbsItemBucket(0, $rate);
                    }

                    $buckets[$class]->add($line->getWeight());
                }

                $defaultClass = $defaultRate->getId();
                if (!isset($buckets[$defaultClass])) {
                    $buckets[$defaultClass] = new WbsItemBucket(0, $defaultRate);
                }
            }

            $price = 0;
            foreach ($buckets as $bucket) {
                $price += $bucket->calculate();
            }

            $price = $this->priceClamp->clamp($price);

            $this->add_rate(array(
                'id'       => $this->id,
                'label'    => $this->title,
                'cost'     => $price,
                'taxes'    => '',
                'calc_tax' => 'per_order'
            ));
        }

        public function admin_options()
        {
            static $already = false;

            /** @noinspection PhpUndefinedConstantInspection */
            if (version_compare(WC_VERSION, '2.6', '>=')) {
                if (!$already) {
                    $already = true;
                } else {
                    return;
                }
            }

            if (WBS_Upgrader::instance()->removeUpgradeNotices()) {
                $this->refresh();
            }

            $manager = WBS_Profile_Manager::instance(true);
            $profiles = $manager->profiles();
            $profile = $manager->profile();

            if (!empty($_GET['delete'])) {

                if (isset($profile)) {
                    delete_option($profile->get_wp_option_name());
                    WbsFactory::getRulesOrderStorage()->remove($profile->profile_id);
                }

                $this->refresh();
            }

            if (!isset($profile)) {
                $profile = new self();
                $profile->_stub = true;
                $profiles[] = $profile;
            }

            if ($profile->_stub &&
                ($sourceProfileId = @$_GET['duplicate']) != null &&
                ($sourceProfile = $manager->profile($sourceProfileId)) != null) {

                $duplicate = clone($sourceProfile);
                $duplicate->id = $profile->id;
                $duplicate->profile_id = $profile->profile_id;

                $profiles[array_search($profile, $profiles, true)] = $duplicate;
                $profile = $duplicate;
            }

            $create_profile_link_html =
                '<a class="add-new-h2" href="'.esc_html(WbsRuleUrls::create()).'">'.
                    esc_html__('Add New', 'woowbs').
                '</a>';

            ?>
                <h3><?php esc_html_e('Weight-based shipping', 'woowbs'); ?></h3>
                <p><?php esc_html_e('Lets you calculate shipping based on total weight of the cart. You can have multiple rules active.', 'woowbs'); ?></p>


                <table class="form-table">

                    <tr class="wbs-title">
                        <th colspan="2">
                            <h4><?php esc_html_e('Rules', 'woowbs'); echo $create_profile_link_html; ?></h4>
                        </th>
                    </tr>

                    <tr class="wbs-profiles">
                        <td colspan="2">
                            <?php self::listProfiles($profiles); ?>
                        </td>
                    </tr>

                    <?php $profile->generate_settings_html(); ?>
                </table>
            <?php
        }

        public function process_admin_options()
        {
            $result = parent::process_admin_options();

            $this->safeInit(true);

            $clone = WBS_Profile_Manager::instance()->profile($this->profile_id);
            if (isset($clone) && $clone !== $this) {
                $clone->init();
            }

            if ($result) {
                WbsWcTools::purgeWoocommerceShippingCache();
            }

            return $result;
        }

        public function display_errors()
        {
            foreach ($this->errors as $error) {
                WC_Admin_Settings::add_error($error);
            }
        }

        public function validate_positive_decimal_field($key)
        {
            return max(0, (float)$this->validate('decimal', $key));
        }

        public function validate_wbs_range_field($key)
        {
            return $this->settingsHelper->validateRangeHtml($key);
        }

        public function validate_wbs_weight_rate_field($key)
        {
            return $this->settingsHelper->validateWeightRateHtml($key);
        }

        public function validate_wbs_custom_field($key)
        {
            return $this->validate(@$this->form_fields[$key]['wbs_real_type'], $key);
        }

        public function validate_shipping_class_rates_field($key)
        {
            return $this->settingsHelper->validateShippingClasses($key);
        }

        public function validate_countries_field($key)
        {
            $value = $this->get_option($key);

            $newValue = @$_POST[$this->plugin_id . $this->id . '_' . $key];
            if (isset($newValue)) {
                $stateCodes = array_filter((array)$newValue);
                $countriesWithSpecificStates = $this->countriesWithSpecificStates($stateCodes);
                if ($this->validate('select', 'availability') == 'excluding') {
                    $stateCodes = array_diff($stateCodes, $countriesWithSpecificStates);
                } else {
                    $stateCodes = array_unique(array_merge($stateCodes, $countriesWithSpecificStates));
                }

                $value = $stateCodes;
            }

            return $value;
        }

        public function generate_positive_decimal_html($key, $data)
        {
            return $this->generate_decimal_html($key, $data);
        }

        public function generate_wbs_range_html($key, $data)
        {
            return $this->settingsHelper->generateRangeHtml($key, $data);
        }

        public function generate_wbs_weight_rate_html($key, $data)
        {
            return $this->settingsHelper->generateWeightRateHtml($key, $data);
        }

        public function generate_wbs_custom_html($key, $data)
        {
            $realType = @$data['wbs_real_type'];
            $data['type'] = $realType;
            unset($data['wbs_real_type']);

            $rowClass = @$data['wbs_row_class'];
            unset($data['wbs_row_class']);

            $generator = 'generate_text_html';
            if ($realType) {
                $realTypeGenerator = "generate_{$realType}_html";
                if (method_exists($this, $realTypeGenerator)) {
                    $generator = $realTypeGenerator;
                }
            }

            $html = $this->{$generator}($key, $data);

            if ($rowClass) {
                $html = preg_replace('/\<tr(.*?)(?:class="(.*?)")?(.*?)>/i', '<tr $1 class="$2 '.esc_html($rowClass).'" $3>', $html, 1);
            }

            return $html;
        }

        public function generate_shipping_class_rates_html($key, $data)
        {
            return $this->settingsHelper->generateShippingClassesHtml($key, $data);
        }

        public function get_description_html($data)
        {
            return parent::get_description_html($data) . @$data['html'];
        }

        public function get_wp_option_name()
        {
            return WBS_Profile_Manager::getRuleSettingsOptionName($this->id, $this->plugin_id);
        }

        public function getPostKey($key)
        {
            return method_exists($this, 'get_field_key')
                ? $this->get_field_key($key)
                : "{$this->plugin_id}{$this->id}_{$key}";
        }        


        private $settingsHelper;

        private function safeInit($showMessage = false)
        {
            try {

                $this->init();
                $this->valid = true;

            } catch (Exception $e) {

                $this->valid = false;
                $this->enabled = 'no';

                if ($showMessage) {
                    WC_Admin_Settings::add_error(
                        "Shipping rule '{$this->name}' has been deactivated due to an error. Double-check its settings and fix errors to make it active.
                        Unerlying error: \"{$e->getMessage()}\" at {$e->getFile()}:{$e->getLine()}."
                    );
                }
            }
        }

        private function init()
        {
            $this->init_form_fields();
            $this->init_settings();

            $this->enabled      = $this->get_option('enabled');
            $this->name         = $this->get_option('name');
            $this->title        = $this->get_option('title');
            $this->{'type'}     = 'order';
            $this->tax_status   = $this->get_option('tax_status');

            $this->availability     = $this->get_option('availability');
            $this->countries 	    = $this->get_option('countries');
            $this->weight           = $this->getRangeOption('weight');
            $this->subtotal         = $this->getRangeOption('subtotal');
            $this->subtotalWithTax  = $this->get_option('subtotal_with_tax') === 'yes';

            $this->fee = (float)$this->get_option('fee');
            $this->settings['fee'] = $this->formatFloat($this->fee);

            $this->weightRate = WbsProgressiveRate::fromArray($this->get_option('weight_rate', array()));

            if (empty($this->countries)) {
                $this->availability = $this->settings['availability'] = 'all';
            }

            $this->shippingClassRates = $this->get_option('shipping_class_rates', new WbsBucketRates());

            $this->priceClamp = $this->getRangeOption('price_clamp');
        }

        private function formatFloat($value, $zeroReplacement = '')
        {
            if ($value == 0) {
                return $zeroReplacement;
            }

            return wc_float_to_string($value);
        }

        private function makeCostString()
        {
            $baseCost = $this->fee ? wc_format_localized_price($this->fee) : null;

            $weightRate = null;
            if ($cost = $this->weightRate->getCost()) {

                $weightRate .= wc_format_localized_price($cost);

                $weightUnit = get_option('woocommerce_weight_unit');

                $step = $this->weightRate->getStep();
                $weightRate = sprintf(__('%s per %s %s', 'woowbs'), $weightRate, $step ? wc_format_localized_decimal($step) : null, $weightUnit);

                if ($skip = $this->weightRate->getSkip()) {
                    $weightRate = sprintf(__('%s (from %s %s)', 'woowbs'), $weightRate, $skip, $weightUnit);
                }
            }

            $cost = null;
            if ($baseCost && $weightRate) {
                $cost = sprintf(__('%s + %s'), $baseCost, $weightRate);
            } else if ($baseCost || $weightRate) {
                $cost = $baseCost . $weightRate;
            } else {
                $cost = __('Free', 'woowbs');
            }

            return $cost;
        }

        private function refresh()
        {
            echo '<script>location.href = ', json_encode(WbsRuleUrls::generic()), ';</script>';
            die();
        }

        private function getRangeOption($name)
        {
            return WbsRange::fromArray($this->get_option($name, array()));
        }

        private function validate($type, $key)
        {
            $result = null;

            $method = "validate_{$type}_field";
            if (!$type || !method_exists($this, $method)) {
                $method = 'validate_text_field';
            }
            
            /** @noinspection PhpUndefinedConstantInspection */
            if (version_compare(WC_VERSION, '2.6', '<')) {
                $result = $this->$method($key);
            } else {
                $result = $this->$method($key, @$_POST[$this->getPostKey($key)]);
            }

            return $result;
        }
        
        private static function listProfiles(array $profiles)
        {
            $current_profile_id = WBS_Profile_Manager::instance()->current_profile_id();

            ?>
            <table id="woowbs_shipping_methods" class="wc_shipping widefat">
                <thead>
                <tr>
                    <th class="sort"><i class="spinner"></i></th>
                    <th class="name">   <?php esc_html_e('Name', 'woowbs'); ?>                  </th>
                    <th>                <?php esc_html_e('Destination', 'woowbs'); ?>           </th>
                    <th>                <?php esc_html_e('Weight', 'woowbs'); ?>                </th>
                    <th>                <?php esc_html_e('Subtotal', 'woowbs'); ?>              </th>
                    <th>                <?php esc_html_e('Cost', 'woowbs'); ?>                  </th>
                    <th class="status"> <?php esc_html_e('Active', 'woowbs'); ?>                </th>
                    <th>                <?php esc_html_e('Actions'); ?>                         </th>
                </tr>
                </thead>
                <tbody>
                <?php /** @var WC_Weight_Based_Shipping[] $profiles */ ?>
                <?php foreach ($profiles as $profile): ?>
                    <tr
                        class="<?php echo ($profile->profile_id === $current_profile_id ? 'wbs-current' : null) ?>"
                        data-settings-url="<?php echo esc_html(WbsRuleUrls::edit($profile)) ?>"
                        data-profile-id="<?php echo esc_html($profile->id) ?>"
                    >
                        <td class="sort"></td>

                        <td class="name"><?php echo esc_html($profile->name)?></td>

                        <?php if ($profile->valid): ?>
                            <td class="countries">
                                <?php if ($profile->availability === 'all'): ?>
                                    <?php esc_html_e('Any', 'woowbs') ?>
                                <?php else: ?>
                                    <?php if ($profile->availability === 'excluding'): ?>
                                        <?php esc_html_e('All except', 'woowbs') ?>
                                    <?php endif; ?>
                                    <?php echo esc_html(join(', ', $profile->countries))?>
                                <?php endif; ?>
                            </td>

                            <?php foreach (array($profile->weight, $profile->subtotal) as $range): ?>
                                <td>
                                    <?php
                                    echo
                                    isset($range->min) || isset($range->max)
                                        ?
                                        ($range->minInclusive || !isset($range->min) ? '[' : '(') .
                                        esc_html(wc_format_localized_decimal((float)$range->min)) .
                                        '&nbsp;–&nbsp;' .
                                        (isset($range->max) ? esc_html(wc_format_localized_decimal($range->max)) : '<span style="font-family:monospace">&infin;</span>') .
                                        ($range->maxInclusive ? ']' : ')')
                                        :
                                        'Any';
                                    ?>
                                </td>
                            <?php endforeach; ?>

                            <td>
                                <?php echo esc_html($profile->makeCostString()); ?>
                            </td>
                        <?php else: ?>
                            <td colspan="4" align="center">
                                <?php esc_html_e('Invalid state', 'woowbs') ?>
                            </td>
                        <?php endif; ?>

                        <td class="status">
                            <?php if ($profile->enabled == 'yes'): ?>
                                <span class="status-enabled tips" data-tip="<?php esc_html_e('Enabled', 'woowbs')?>"><?php esc_html_e('Enabled', 'woowbs')?></span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td class="actions">
                            <a class="button" href="<?php echo esc_html(WbsRuleUrls::duplicate($profile)) ?>">
                                <?php esc_html_e('Duplicate', 'woowbs') ?>
                            </a>

                            <a class="button" href="<?php echo esc_html(WbsRuleUrls::delete($profile)) ?>"
                               onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this rule?', 'woowbs') ?>');">
                                <?php esc_html_e('Delete') ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!--suppress CssUnusedSymbol -->
            <style>
                #woowbs_shipping_methods td { cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                #woowbs_shipping_methods .sort { width: 1em; }
                #woowbs_shipping_methods td.sort { cursor: move; }
                #woowbs_shipping_methods th.sort { padding: 0; }
                #woowbs_shipping_methods .actions { white-space: nowrap; }
                #woowbs_shipping_methods .countries { max-width: 15em; }
                #woowbs_shipping_methods .wbs-current td { background-color: #eee; }
                #woowbs_shipping_methods tr:hover td { background-color: #ddd; }
                #woowbs_shipping_methods .spinner { display: none; visibility: visible; margin: 0; width: 17px; height: 17px; background-size: 17px 17px; }
                #woowbs_shipping_methods.in-progress .spinner { display: block; }
                tr.wbs-title th { padding: 2em 0 0 0; }
                tr.wbs-title h4 { font-size: 1.2em; }
                .wc-settings-sub-title { padding-top: 2em; }
                .form-table { border-top: 1px solid #aaa; }
                tr.wbs-profiles > td { padding: 0; }

                .wbs-destination label { display: none; }
                .wbs-destination .forminp { padding-top: 0; }
                .wbs-destination fieldset { margin-top: -15px !important; }

                .wbs-subtotal .forminp { padding-bottom: 0}
                .wbs-subtotal + tr > * { padding-top: 0 }
                .wbs-subtotal + tr label { display: none; }
                .wbs-minifield { width: 15.7em; }
                .wbs-minifield.wc_input_decimal { text-align: right; }
                .wbs-minifield-container { display: block ! important; }
                .wbs-minifield-label { display: inline-block; min-width: 3em; }
                .wbs-range-simple .wbs-minifield-label { min-width: 5em; }
                .wbs-range-simple .wbs-minifield { min-width: 19.7em; }
                .wbs-weight-rate .wbs-minifield-label { min-width: 5em; }
                .wbs-weight-rate .wbs-minifield { width: 17.3em; }

                .wbs-input-group {
                    display: inline-block;
                    height: 29px;
                    box-sizing: border-box;
                }

                .wbs-input-group-addon {
                    display: inline-block;
                    box-sizing: border-box;
                    height: 100%;
                    width: 2.5em;
                    text-align: center;
                    border: 1px solid #ddd;
                    padding: 3px 0;
                }

                .wbs-input-group-addon:first-child {
                    border-right: 0;
                    float: left;
                }

                .wbs-input-group-addon:last-child {
                    border-left: 0;
                    float: right;
                }

                .wbs-input-group input[type="text"] {
                    display: inline-block;
                    height: 100%;
                    box-sizing: border-box;
                    margin: 0;
                }

                .wbs-code {
                    font-size: inherit;
                    padding: 0 0.5em;
                    font-family: monospace;
                }

                .shippingrows {
                    overflow: hidden;
                }

                .shippingrows th, .shippingrows td {
                    width: auto;
                    white-space: nowrap;
                    padding: 1em;
                }

                .shippingrows .wc_input_decimal {
                    width: 7em;
                }

                .flat_rate .wbs-minifield-container {
                    display: inline !important;
                    margin-left: 1em;
                }

                .flat_rate .wbs-minifield-container:first-child {
                    margin-left: 0;
                }

                .flat_rate .wbs-minifield-label {
                    display: inline;
                }

                .flat_rate .wbs-minifield {
                    width: 6em;
                }

                .flat_rate_tpl {
                    display: none;
                }
            </style>
            <?php
        }

        private static function getStateCode($cc, $sc = null)
        {
            if (self::isWildcardStateCode($sc)) {
                $sc = null;
            }

            return rtrim("{$cc}:{$sc}", ":");
        }

        private static function isWildcardStateCode($sc) {
            return !$sc || $sc === '*';
        }

        private static function parseStateCode($string)
        {
            $ccsc = explode(':', $string, 2);
            $ccsc[1] = @$ccsc[1];
            return $ccsc;
        }

        private static function countriesWithSpecificStates($stateCodes)
        {
            $countriesWithSpecificStates = array();

            foreach ($stateCodes as $stateCode) {
                list($cc, $sc) = self::parseStateCode($stateCode);
                if (!self::isWildcardStateCode($sc)) {
                    $countriesWithSpecificStates[] = $cc;
                }
            }

            return $countriesWithSpecificStates;
        }
    }
?>