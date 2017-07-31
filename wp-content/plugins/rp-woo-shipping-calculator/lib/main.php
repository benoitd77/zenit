<?php
if (!class_exists('rp_shipping_calculator')) {

    class rp_shipping_calculator {

        private static $plugin_url;
        private static $plugin_dir;
        private static $plugin_title = "Shipping Calculator";
        private static $plugin_slug = "rpship-calculator-setting";
        private static $rpship_option_key = "rpship-calculator-setting";
        private $rpship_settings;
        public static $calculator_metakey = "__calculator_hide";

        public function __construct()
        {
            global $rpship_plugin_dir, $rpship_plugin_url;

            /* plugin url and directory variable */
            self::$plugin_dir = $rpship_plugin_dir;
            self::$plugin_url = $rpship_plugin_url;

            /* load shipping calculator setting */
            $this->rpship_settings = get_option(self::$rpship_option_key);

            /* create admin menu for shipping calculator setting */
            add_action("admin_menu", array($this, "admin_menu"));

            /* hook for calculate shipping with ajax */
            add_action('wp_ajax_nopriv_ajax_calc_shipping', array($this, 'ajax_calc_shipping'));
            add_action('wp_ajax_ajax_calc_shipping', array($this, 'ajax_calc_shipping'));
            /* hook update shipping method */
            add_action('wp_ajax_nopriv_update_shipping_method', array($this, 'update_shipping_method'));
            add_action('wp_ajax_update_shipping_method', array($this, 'update_shipping_method'));

            /* wp_footer hook */
            add_action("wp_footer", array($this, "wp_footer"));

            /* wp_header hook used for include css */
            add_action("wp_head", array($this, "wp_head"));

            /* register admin css and js for shipping calculator */
            add_action('admin_enqueue_scripts', array($this, 'admin_script'));

            /* shipping calculato shortcode */
            add_shortcode("shipping-calculator", array($this, "srt_shipping_calculator"));

            add_action('woocommerce_product_options_general_product_data', array($this, 'add_custom_price_box'));
            add_action('woocommerce_process_product_meta', array($this, 'custom_woocommerce_process_product_meta'), 2);
            /* check shipping button display on product page */
            if ($this->get_setting("enable_on_productpage") == 1) {
                /* hook for display shipping button on product page */

                if ($this->get_setting("button_pos") == 1)
                    add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 100);
                elseif ($this->get_setting("button_pos") == 2)
                    add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 20);
                else
                    add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 8);
            }
        }

        public function custom_woocommerce_process_product_meta($post_id)
        {
            $metavalue = isset($_POST[self::$calculator_metakey]) ? "yes" : "no";
            update_post_meta($post_id, self::$calculator_metakey, $metavalue);
        }

        public function add_custom_price_box()
        {
            $hide_calculator = "yes";
            if (isset($_GET["post"]))
                $hide_calculator = get_post_meta($_GET["post"], self::$calculator_metakey, true);
            woocommerce_wp_checkbox(array('id' => self::$calculator_metakey, 'value' => $hide_calculator, 'label' => __('Hide Shipping Calculator?', 'rphpa_hide_calculator')));
        }

        public function update_shipping_method()
        {
            WC_Shortcode_Cart::calculate_shipping();

            if (isset($_POST["product_id"]) && $this->check_product_incart($_POST["product_id"]) === false) {
                if (isset($_POST['variation_id']) && $_POST['variation_id'] != "" && $_POST['variation_id'] > 0) {
                    $cart_item_key = WC()->cart->add_to_cart($_POST["product_id"], 1, $_POST['variation_id']);
                } else {
                    $cart_item_key = WC()->cart->add_to_cart($_POST["product_id"]);
                }

                $packages = WC()->cart->get_shipping_packages();
                $packages = WC()->shipping->calculate_shipping($packages);
                $available_methods = WC()->shipping->get_packages();
                WC()->cart->remove_cart_item($cart_item_key);
            } else {
                $packages = WC()->cart->get_shipping_packages();
                $packages = WC()->shipping->calculate_shipping($packages);
                $available_methods = WC()->shipping->get_packages();
            }

            wc_clear_notices();
            if ($this->get_setting('shipping_type') == 1) {
                if (isset($available_methods[0]["rates"]) && count($available_methods[0]["rates"]) > 0) {
                    $count = 0;
                    foreach ($available_methods[0]["rates"] as $key => $method) {
                        $checked = ($count == 0) ? "checked=checked" : "";
                        echo '<input name="calc_shipping_method" type="radio" ' . $checked . ' ' . checked($key, WC()->session->chosen_shipping_method, false) . ' value="' . esc_attr($key) . '">&nbsp;' . wp_kses_post($method->label) . "<br>";
                        $count++;
                    }
                }
            } else {
                ?>
                <select name="calc_shipping_method" id="calc_shipping_method" class="shipping_method">
                    <option value=""><?php _e('Select a Shipping Method&hellip;', 'woocommerce'); ?></option>
                    <?php
                    if (isset($available_methods[0]["rates"]) && count($available_methods[0]["rates"]) > 0) {

                        foreach ($available_methods[0]["rates"] as $key => $method) {
                            //if ($method->enabled != 'no') {
                            echo '<option value="' . esc_attr($key) . '" ' . selected($key, WC()->session->chosen_shipping_method, false) . '>' . wp_kses_post($method->label) . '</option>';
                            //}
                        }
                    }
                    ?>
                </select>
                <?php
            }
            die();
        }

        /* function for display shipping calculator on product page */

        public function display_shipping_calculator()
        {
            global $product;
            if (get_post_meta($product->id, self::$calculator_metakey, true) != "yes")
                include_once self::$plugin_dir . 'view/shipping-calculator.php';
        }

        function srt_shipping_calculator()
        {

            ob_start();
            include_once self::$plugin_dir . 'view/shipping-calculator.php';
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        /* calculate shiiping */

        public function ajax_calc_shipping()
        {
            $returnResponse = array();
            if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "ajax_calc_shipping"):


                /* check shipping method and country selected */
                if ($_POST["calc_shipping_method"] == "") {
                    $returnResponse = array("code" => "error", "message" => __("Please select shipping method", "rpship"));
                } elseif ($_POST["calc_shipping_country"] == "") {
                    $returnResponse = array("code" => "error", "message" => __("Please select shipping country", "rpship"));
                } else {
                    $country = $_POST["calc_shipping_country"];

                    /* calculate shipping */
                    $shippingCharge = $this->get_shipping_text($_POST["calc_shipping_method"], $country);

                    /* get country full name */
                    $country = WC()->countries->countries[$country];

                    if (isset($shippingCharge['label'])) {
                        if (trim($this->get_setting("shipping_message")) != "") {
                            $message = str_replace(array("[shipping-method]", "[shipping-cost]", "[shipping-country]"), array($shippingCharge["label"], $shippingCharge["cost"], $country), $this->get_setting("shipping_message"));
                        } else {
                            $message = $shippingCharge["label"] . " : " . $shippingCharge["cost"];
                        }

                        $returnResponse = array("code" => "success", "message" => __($message, "rpship"));
                    } else if (isset($shippingCharge['code'])) {
                        $returnResponse = array("code" => "error", "message" => __($shippingCharge['message'], "rpship"));
                    } else {
                        $returnResponse = array("code" => "error", "message" => __("Selected Shipping method not available.", "rpship"));
                    }
                }
            endif;
            echo json_encode($returnResponse);
            die();
        }

        public function check_product_incart($product_id)
        {
            foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                $_product = $values['data'];

                if ($product_id == $_product->id) {
                    return true;
                }
            }
            return false;
        }

        /* function for calculate shiiping */

        public function get_shipping_text($shipping_method, $country)
        {
            global $woocommerce, $post;
            $returnResponse = array();
            WC_Shortcode_Cart::calculate_shipping();


            if (isset($_POST["product_id"]) && $this->check_product_incart($_POST["product_id"]) === false) {
                if (isset($_POST['variation_id']) && $_POST['variation_id'] != "" && $_POST['variation_id'] > 0) {
                    $cart_item_key = WC()->cart->add_to_cart($_POST["product_id"], 1, $_POST['variation_id']);
                } else {
                    $cart_item_key = WC()->cart->add_to_cart($_POST["product_id"]);
                }
                $packages = WC()->cart->get_shipping_packages();
                $packages = WC()->shipping->calculate_shipping($packages);
                $packages = WC()->shipping->get_packages();
                WC()->cart->remove_cart_item($cart_item_key);
            } else {
                $packages = WC()->cart->get_shipping_packages();
                $packages = WC()->shipping->calculate_shipping($packages);
                $packages = WC()->shipping->get_packages();
            }


            wc_clear_notices();

            if (isset($packages[0]["rates"][$shipping_method])) {
                $selectedShiiping = $packages[0]["rates"][$shipping_method];
                $returnResponse = array("label" => $selectedShiiping->label, "cost" => wc_price($selectedShiiping->cost));
            } else {
                $AllMethod = WC()->shipping->load_shipping_methods();
                $selectedMethod = $AllMethod[$shipping_method];
                $flag = 0;
                if ($selectedMethod->availability == "including"):
                    foreach ($selectedMethod->countries as $methodcountry) {
                        if ($country == $methodcountry) {
                            $flag = 1;
                        }
                    }
                    if ($flag == 0):
                        $message = $selectedMethod->method_title . " is not available in selected country.";
                        $returnResponse = array("code" => "error", "message" => $message);
                    endif;
                endif;
            }
            return $returnResponse;
        }

        public function admin_script()
        {
            if (is_admin()) {

                // Add the color picker css file       
                wp_enqueue_style('wp-color-picker');

                wp_enqueue_script('rpship-admin', self::$plugin_url . "assets/js/admin.js", array('wp-color-picker'), false, true);
            }
        }

        public function wp_head()
        {
            ?>

            <script type="text/javascript">
                var rp_ajax_url = "<?php echo admin_url("admin-ajax.php") ?>";
            </script>

            <?php
        }

        public function wp_footer()
        {
            wp_enqueue_script('wc-country-select');
            wp_enqueue_script(self::$plugin_slug, self::$plugin_url . "assets/js/shipping-calculater.js");
        }

        /* register admin menu for shipping calculator setting */

        public function admin_menu()
        {
            $wc_page = 'woocommerce';
            add_submenu_page($wc_page, self::$plugin_title, self::$plugin_title, "install_plugins", self::$plugin_slug, array($this, "calculator_setting_page"));
        }

        /* admin setting page for shipping calculator  */

        public function calculator_setting_page()
        {

            /* save shipping calculator setting */
            if (isset($_POST[self::$plugin_slug])) {
                $this->saveSetting();
            }
            /* include admin  shipping calculator setting file */
            include_once self::$plugin_dir . "view/shipping-setting.php";
        }

        /* function for save setting */

        public function saveSetting()
        {
            $arrayRemove = array(self::$plugin_slug, "btn-rpship-submit");
            $saveData = array();
            foreach ($_POST as $key => $value):
                if (in_array($key, $arrayRemove))
                    continue;
                $saveData[$key] = $value;
            endforeach;
            $this->rpship_settings = $saveData;
            update_option(self::$rpship_option_key, $saveData);
        }

        /* function for get setting */

        public function get_setting($key)
        {

            if (!$key || $key == "")
                return;

            if (!isset($this->rpship_settings[$key]))
                return;

            return $this->rpship_settings[$key];
        }

    }

}
/* load plugin if woocommerce plugin is activated */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    /* load shipping calculator plugin code */
    new rp_shipping_calculator();
}

