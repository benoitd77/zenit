<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.orionorigin.com/
 * @since      0.1
 *
 * @package    Wad
 * @subpackage Wad/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wad
 * @subpackage Wad/admin
 * @author     ORION <support@orionorigin.com>
 */
class Wad_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    0.1
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.1
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.1
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wad_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wad_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wad-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( "acd-flexgrid", plugin_dir_url( __FILE__ ) . 'css/flexiblegs.css', array(), $this->version, 'all' );
//                wp_enqueue_style( "acd-tooltip", plugin_dir_url( __FILE__ ) . 'css/tooltip.css', array(), $this->version, 'all' );
        wp_enqueue_style( "o-ui", plugin_dir_url( __FILE__ ) . 'css/UI.css', array(), $this->version, 'all' );
        wp_enqueue_style( "o-datepciker", plugin_dir_url( __FILE__ ) . 'js/datepicker/css/datepicker.css', array(), $this->version, 'all' );
        wp_enqueue_style( "wad-datetimepicker", plugin_dir_url( __FILE__ ) . 'js/datetimepicker/jquery.datetimepicker.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wad-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "o-admin", plugin_dir_url( __FILE__ ) . 'js/o-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "wad-tabs", plugin_dir_url( __FILE__ ) . 'js/SpryAssets/SpryTabbedPanels.js', array( 'jquery' ), $this->version, false );
//        wp_enqueue_script("acd-accordion", plugin_dir_url(__FILE__) . 'js/SpryAssets/SpryAccordion.js', array('jquery'), $this->version, false);
        wp_enqueue_script( "wad-serializejson", plugin_dir_url( __FILE__ ) . 'js/jquery.serializejson.min.js', array( 'jquery' ), $this->version, false );
//        wp_enqueue_script("o-datepicker", plugin_dir_url(__FILE__) . 'js/datepicker/js/datepicker.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-eye", plugin_dir_url(__FILE__) . 'js/datepicker/js/eye.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-util", plugin_dir_url(__FILE__) . 'js/datepicker/js/util.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-layout", plugin_dir_url(__FILE__) . 'js/datepicker/js/layout.js', array('jquery'), $this->version, false);
        wp_enqueue_script( "wad-datetimepicker", plugin_dir_url( __FILE__ ) . 'js/datetimepicker/build/jquery.datetimepicker.full.min.js', array( 'jquery' ), $this->version, false );
    }

    /**
     * Initialize the plugin sessions
     */
    function init_sessions() {
        if (!session_id()) {
            session_start();
        }

        if (!isset( $_SESSION[ "active_discounts" ] ))
            $_SESSION[ "active_discounts" ] = array();
        if (!isset( $_SESSION[ "social_data" ] ))
            $_SESSION[ "social_data" ] = array();
    }

    /**
     * Builds all the plugin menu and submenu
     */
    public function add_wad_menu() {
        $parent_slug = "edit.php?post_type=o-discount";
        add_submenu_page( $parent_slug, __( 'Products Lists', 'wad' ), __( 'Products Lists', 'wad' ), 'manage_product_terms', 'edit.php?post_type=o-list', false );
        add_submenu_page( $parent_slug, __( 'Settings', 'wad' ), __( 'Settings', 'wad' ), 'manage_product_terms', 'wad-manage-settings', array( $this, 'get_wad_settings_page' ) );
        add_submenu_page($parent_slug, __('Get Started', 'wad'), __('Get Started', 'wad'), 'manage_product_terms', 'wad-about', array($this, "get_about_page"));
    }

    public function get_wad_settings_page() {
        if ((isset( $_POST[ "wad-options" ] ) && !empty( $_POST[ "wad-options" ] ))) {
            $_POST[ "wad-options" ][ "social-desc" ] = stripslashes( wp_filter_post_kses( addslashes( $_POST[ "wad-options" ][ "social-desc" ] ) ) );
            update_option( "wad-options", $_POST[ "wad-options" ] );
            //echo stripslashes(wp_filter_post_kses(addslashes($_POST["wad-options"]["social-desc"])));
        }
        ?>
        <div class="o-wrap cf">
            <h1><?php _e( "Woocommerce All Discounts Settings", "wad" ); ?></h1>
            <form method="POST" action="" class="mg-top">
                <div class="postbox" id="wad-options-container">
                    <?php
                    $begin = array(
                        'type' => 'sectionbegin',
                        'id' => 'wad-datasource-container',
                        'table' => 'options',
                    );
                    $facebook_app_id = array(
                        'title' => __( 'App ID', 'wad' ),
                        'name' => 'wad-options[facebook-app-id]',
                        'type' => 'text',
                        'desc' => __( 'Facebook App ID', 'wad' ),
                        'default' => '',
                    );

                    $facebook_app_secret = array(
                        'title' => __( 'App Secret', 'wad' ),
                        'name' => 'wad-options[facebook-app-secret]',
                        'type' => 'text',
                        'desc' => __( 'Facebook App Secret', 'wad' ),
                        'default' => '',
                    );

                    $facebook = array(
                        'title' => __( 'Facebook', 'wad' ),
                        'desc' => __( 'Facebook APP settings', 'wad' ),
                        'type' => 'groupedfields',
                        'fields' => array( $facebook_app_id, $facebook_app_secret ),
                    );

                    $instagram_app_id = array(
                        'title' => __( 'Client ID', 'wad' ),
                        'name' => 'wad-options[instagram-app-id]',
                        'type' => 'text',
                        'desc' => __( 'Client ID', 'wad' ),
                        'default' => '',
                    );

                    $instagram_app_secret = array(
                        'title' => __( 'Client Secret', 'wad' ),
                        'name' => 'wad-options[instagram-app-secret]',
                        'type' => 'text',
                        'desc' => __( 'Client Secret', 'wad' ),
                        'default' => '',
                    );

                    $instagram = array(
                        'title' => __( 'Instagram', 'wad' ),
                        'desc' => __( 'Instagram Client settings', 'wad' ),
                        'type' => 'groupedfields',
                        'fields' => array( $instagram_app_id, $instagram_app_secret ),
                    );

                    $twitter_app_id = array(
                        'title' => __( 'Twitter Consumer Key', 'wad' ),
                        'name' => 'wad-options[twitter-app-id]',
                        'type' => 'text',
                        'desc' => __( 'Twitter Client ID', 'wad' ),
                        'default' => '',
                    );

                    $twitter_app_secret = array(
                        'title' => __( 'Twitter Consumer Secret', 'wad' ),
                        'name' => 'wad-options[twitter-app-secret]',
                        'type' => 'text',
                        'desc' => __( 'Twitter Client Secret', 'wad' ),
                        'default' => '',
                    );

//                    $twitter = array(
//                        'title' => __('Twitter', 'wad'),
//                        'desc' => __('Twitter Client settings', 'wad'),
//                        'type' => 'groupedfields',
//                        'fields' => array($twitter_app_id, $twitter_app_secret),
//                    );

                    $mailchimp_api_key_admin = array(
                        'title' => __( 'Mailchimp API KEY', 'wad' ),
                        'name' => 'wad-options[mailchimp-api-key]',
                        'type' => 'text',
                        'desc' => __( 'Used when a MailChimp based discount need to be set. <a href="http://kb.mailchimp.com/accounts/management/about-api-keys" target="blank">How to find my API Key?</a>', 'wad' ),
                        'default' => '',
                    );
                    $sendinblue_api_key_admin = array(
                        'title' => __( 'SendinBlue API KEY', 'wad' ),
                        'name' => 'wad-options[sendinblue-api-key]',
                        'type' => 'text',
                        'desc' => __( 'Used when a SendinBlue based discount need to be set.<a href="https://my.sendinblue.com/advanced/apikey" target="blank">How to find my API Key?</a>', 'wad' ),
                        'default' => '',
                    );

                    $social_description = array(
                        'title' => __( 'Social buttons description', 'wad' ),
                        'name' => 'wad-options[social-desc]',
                        'id' => 'social-desc-editor',
                        'type' => 'texteditor',
                        'desc' => __( 'Description of the social buttons on the cart page to help the customer understand what to do', 'wad' ),
                        'default' => '',
                    );

                    $envato_username = array(
                        'title' => __( 'Envato Username', 'wad' ),
                        'name' => 'wad-options[envato-username]',
                        'type' => 'text',
                        'desc' => __( 'Your envato username', 'wad' ),
                        'default' => '',
                    );

                    $envato_api_key = array(
                        'title' => __( 'Secret API Key', 'wad' ),
                        'name' => 'wad-options[envato-api-key]',
                        'type' => 'text',
                        'desc' => __( 'You can find your secret api key by following the instructions <a href="https://www.youtube.com/watch?v=KnwumvnWAIM" target="blank">here</a>.', 'wad' ),
                        'default' => '',
                    );

                    $envato_purchase_code = array(
                        'title' => __( 'Purchase Code', 'wad' ),
                        'name' => 'wad-options[purchase-code]',
                        'type' => 'text',
                        'desc' => __( 'You can find your purchase code by following the instructions <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-can-I-find-my-Purchase-Code-" target="blank">here</a>.', 'wad' ),
                        'default' => '',
                    );

                    $include_taxes = array(
                        'title' => __( 'Include shipping in taxes', 'wad' ),
                        'name' => 'wad-options[inc-shipping-in-taxes]',
                        'type' => 'select',
                        'options' => array( 'No' => "No", 'Yes' => "Yes" ),
                        'desc' => __( 'Wether or not to consider shipping as part of taxes', 'wad' ),
                        'default' => 'Yes',
                    );

                    $disable_coupons = array(
                        'title' => __( 'Disable coupons', 'wad' ),
                        'name' => 'wad-options[disable-coupons]',
                        'type' => 'select',
                        'options' => array( 0 => "No", 1 => "Yes" ),
                        'desc' => __( 'whether or not to disable the coupons usage when a cart discount is active.', 'wad' ),
                        'default' => '',
                    );
                    $display_cart_discounts_individually = array(
                        'title' => __( 'Display cart discounts individually', 'wad' ),
                        'name' => 'wad-options[individual-cart-discounts]',
                        'type' => 'select',
                        'options' => array( 0 => "No", 1 => "Yes" ),
                        'desc' => __( 'whether or not to display each cart discount individually on cart pages.', 'wad' ),
                        'default' => 1,
                    );
                    $completed_order_statuses = array(
                        'title' => __( 'Completed Orders Statuses', 'wad' ),
                        'name' => 'wad-options[completed-order-statuses]',
                        'type' => 'multiselect',
                        'options' => wc_get_order_statuses(),
                        'desc' => __( 'List of order statuses considered as completed (used when manipulating previous orders in the discounts conditions).', 'wad' ),
                        'default' => '',
                    );

                    $fast_extraction_algorithm = array(
                        'title' => __( 'Use new extraction algorithm', 'wad' ),
                        'name' => 'wad-options[new-extraction-algorithm]',
                        'type' => 'select',
                        'options' => array( 0 => "No", 1 => "Yes" ),
                        'desc' => __( 'Use new extraction algorithm recommanded for slow websites.<br><strong style="color:red">-Beta-</strong>.', 'wad' ),
                        'default' => 1,
                    );

                    $end = array( 'type' => 'sectionend' );
                    $settings = array(
                        $begin,
                        $facebook,
//                $twitter,
                        $instagram,
                        $mailchimp_api_key_admin,
                        $sendinblue_api_key_admin,
                        $social_description,
                        $disable_coupons,
                        $display_cart_discounts_individually,
                        $include_taxes,
                        $envato_username,
                        $envato_api_key,
                        $envato_purchase_code,
                        $completed_order_statuses,
                        $fast_extraction_algorithm,
                        $end
                    );
                    echo o_admin_fields( $settings );
                    ?>
                </div>
                <input type="submit" class="button button-primary button-large" value="<?php _e( "Save", "wad" ); ?>">
            </form>
        </div>
        <?php
        global $o_row_templates;
        ?>
        <script>
            var o_rows_tpl =<?php echo json_encode( $o_row_templates ); ?>;
        </script>
        <?php
    }

    /**
     * Builds the about page
     */
    function get_about_page() {
        ?>
        <div id='wad-about-page'>
            <div class="wrap">
                <div id="features-wrap">
                    <h2 class="feature-h2"><?php _e('Getting Started', 'wpd'); ?></h2>
                    <div class="list-posts-content">
                      <div class="o-wrap o-features xl-gutter-8">
                          <div class="o-col col xl-1-2">
                              <img class="vc_single_image-img " src="<?php echo WAD_URL."admin/images/features/"?>WAD-Icons-01.svg">
                              <h3 class="wad-title"><?php _e('HOW TO CREATE A WOOCOMMERCE DISCOUNT BASED ON THE CUSTOMER EMAIL DOMAIN?', 'wad'); ?></h3>
                              <p><?php _e('Email addresses from a company are grouped under the same email domain and can be easily used to target a group of customers for multiple purposes.', 'wpd'); ?></p>
                              <a href="https://woocommercealldiscounts.com/tutorials/create-woocommerce-discount-based-customer-email-domain/?utm_source=WAD%20Pro&utm_medium=cpc&utm_campaign=Woocommerce%20All%20Discounts&utm_term=how%20to%20create%20a%20discount%20on%20customer%20email%20domain" class="button" target="_blank"><?php _e('Learn More', 'wad'); ?></a>
                          </div>
                          <div class="o-col col xl-1-2">
                              <img class="vc_single_image-img " src="<?php echo WAD_URL."admin/images/features/"?>WAD-Icons-02.svg">
                              <h3 class="wad-title"><?php _e('HOW TO CREATE A WOOCOMMERCE DISCOUNT ON A PRODUCT VARIATION?', 'wad'); ?></h3>
                              <p><?php _e('Variable products are a product type in WooCommerce that lets you offer a set of variations on a product, with control over prices, stock, image and more for ...', 'wpd'); ?></p>
                              <a href="https://woocommercealldiscounts.com/tutorials/create-woocommerce-discount-product-variation/?utm_source=WAD%20Pro&utm_medium=cpc&utm_campaign=Woocommerce%20All%20Discounts&utm_term=how%20to%20create%20a%20discount%20on%20a%20product%20variation" class="button" target="_blank"><?php _e('Learn More', 'wad'); ?></a>
                          </div>
                          <div class="o-col col xl-1-2">
                              <img class="vc_single_image-img " src="<?php echo WAD_URL."admin/images/features/"?>WAD-Icons-07.svg">
                              <h3 class="wad-title"><?php _e('HOW TO CREATE A WOOCOMMERCE DISCOUNT PER PAYMENT METHOD?', 'wad'); ?></h3>
                              <p><?php _e('A payment gateway is a service that allows a customer to pay for his order using credit cards or direct payments methods. While there are hundreds of payment methods...', 'wpd'); ?></p>
                              <a href="https://woocommercealldiscounts.com/tutorials/create-woocommerce-discount-per-payment-method/?utm_source=WAD%20Pro&utm_medium=cpc&utm_campaign=Woocommerce%20All%20Discounts&utm_term=how%20to%20create%20a%20discount%20per%20payment%20method" class="button" target="_blank"><?php _e('Learn More', 'wad'); ?></a>
                          </div>
                          <div class="o-col col xl-1-2">
                              <img class="vc_single_image-img " src="<?php echo WAD_URL."admin/images/features/"?>WAD-Icons-08.svg">
                              <h3 class="wad-title"><?php _e('HOW TO CREATE A WOOCOMMERCE DISCOUNT ON THE FIRST ORDER OR THE NTH ORDER?', 'wad'); ?></h3>
                              <p><?php _e('A discount on the first order is the most effective way to convert a first time visitor into a paying customer. That conversion is the first and hardest step into...', 'wpd'); ?></p>
                              <a href="https://woocommercealldiscounts.com/tutorials/create-woocommerce-discount-first-order-nth-order/?utm_source=WAD%20Pro&utm_medium=cpc&utm_campaign=Woocommerce%20All%20Discounts&utm_term=how%20to%20create%20a%20discount%20on%20the%20nth%20order" class="button" target="_blank"><?php _e('Learn More', 'wad'); ?></a>
                          </div>
                          <div class="o-col col xl-1-2">
                              <img class="vc_single_image-img " src="<?php echo WAD_URL."admin/images/features/"?>WAD-Icons-24.svg">
                              <h3 class="wad-title"><?php _e('HOW TO CREATE A WOOCOMMERCE BULK DISCOUNT PER CUSTOMER ROLE OR GROUP?', 'wad'); ?></h3>
                              <p><?php _e('Bulk discounts per customer are particularly useful if you have different types of customers or different pricing strategies to apply.', 'wpd'); ?></p>
                              <a href="https://woocommercealldiscounts.com/tutorials/create-woocommerce-bulk-discount-per-customer-role-group/?utm_source=WAD%20Pro&utm_medium=cpc&utm_campaign=Woocommerce%20All%20Discounts&utm_term=how%20to%20create%20a%20bulk%20discount%20per%20customer%20role%20or%20group" class="button" target="_blank"><?php _e('Learn More', 'wad'); ?></a>
                          </div>
                          <div class="o-col col xl-1-2">
                              <img class="vc_single_image-img " src="<?php echo WAD_URL."admin/images/features/"?>WAD-Icons-25.svg">
                              <h3 class="wad-title"><?php _e('HOW TO CREATE A WOOCOMMERCE BULK DISCOUNT PER PRODUCT CATEGORY?', 'wad'); ?></h3>
                              <p><?php _e('Bulk discounts are one of the most popular deals mainly used to increase the average order size on online stores. Despite WooCommerce being one of the most...', 'wpd'); ?></p>
                              <a href="https://woocommercealldiscounts.com/tutorials/create-woocommerce-bulk-discount-per-product-category/?utm_source=WAD%20Pro&utm_medium=cpc&utm_campaign=Woocommerce%20All%20Discounts&utm_term=how%20to%20create%20a%20bulk%20discount%20per%20product%20category" class="button" target="_blank"><?php _e('Learn More', 'wad'); ?></a>
                          </div>
                          <div class="o-col col xl-1-2">
                              <img class="vc_single_image-img " src="<?php echo WAD_URL."admin/images/features/"?>WAD-Icons-26.svg">
                              <h3 class="wad-title"><?php _e('HOW TO CREATE A PRODUCTS LIST USING WOOCOMMERCE ALL DISCOUNTS?', 'wad'); ?></h3>
                              <p><?php _e('A product list is a subset of your shop’s products that you can target in the discount conditions or the products on which they apply while setting up a dynamic...', 'wpd'); ?></p>
                              <a href="https://woocommercealldiscounts.com/tutorials/create-products-list-using-woocommerce-discounts/?utm_source=WAD%20Pro&utm_medium=cpc&utm_campaign=Woocommerce%20All%20Discounts&utm_term=how%20to%20create%20a%20product%20list%20using%20woocommerce%20all%20discounts" class="button" target="_blank"><?php _e('Learn More', 'wad'); ?></a>
                          </div>
                      </div>
                    </div>
                </div>

            </div> 
        </div>
        <?php
    }

    /**
     * Runs the new version check and upgrade process
     * @return \WAD_Updater
     */
    function get_updater() {
//        do_action('wad_before_init_updater');
        require_once( WAD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-wad-updater.php' );
        $updater = new WAD_Updater();
        $updater->init();
        require_once( WAD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-wad-updating-manager.php' );
        $updater->setUpdateManager( new WAD_Updating_Manager( WAD_VERSION, $updater->versionUrl(), WAD_MAIN_FILE ) );
//        do_action('wad_after_init_updater');
        return $updater;
    }

    function get_license_activation_notice() {
        //if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] != '::1') {
        if (!get_option( 'wad-license-key' )) {
            ?>
            <div class="notice notice-error">
                <p><b>Woocommerce All Discounts: </b><?php _e( 'You have not activated your license yet. Please activate it in order to get the plugin working.', 'sample-text-domain' ); ?></p>
                <a class="button" id="wad-activate"><?php _e( "Activate", "wad" ); ?></a><img style="display:none; width: 30px; height: 30px;" id="spinner" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/spinner.gif'; ?> ">
                <p></p>
                <div id="license-message"></div>
            </div>
            <?php
        }
        //}
    }
    
    function activate_license() {
        global $wad_settings;
        if (isset( $wad_settings[ 'purchase-code' ] )  && $wad_settings['purchase-code'] != "" ) {
            $purchase_code = $wad_settings[ 'purchase-code' ];
            $site_url = get_site_url();
            $code = $_POST[ 'code' ];
            $plugin_name = WAD_PLUGIN_NAME;
            $url = "https://www.orionorigin.com/service/olicenses/v1/license/?purchase-code=" . $purchase_code . "&siteurl=" . urlencode( $site_url ) . "&name=" . $plugin_name . "&code=" . $code;
            $args=array('timeout' => 60,);
            $response = wp_remote_get( $url, $args);
            if (is_wp_error( $response )) {
                $error_message = $response->get_error_message();
                echo "Something went wrong: $error_message";
                die();
            }
            if (isset( $response[ "body" ] )) {
                $answer = $response[ "body" ];
            }

            if (is_array( json_decode( $answer, true ) )) {
                $data = json_decode( $answer, true );
                update_option('wad-license-key', $data[ 'key' ]);
                echo "200";
            } else {
                echo $answer;
            }
        } else {
            echo ("Purchase code not found. Please, set your purchase code in the plugin's settings. ");
        }


        die();
    }

    function o_verify_validity() {
        $wad_settings = get_option('wad-options');
        if ( isset($wad_settings[ 'purchase-code' ]) && $wad_settings['purchase-code'] != "" ) {
            $purchase_code = $wad_settings[ 'purchase-code' ];
            $site_url = get_site_url();
            $url = "https://www.orionorigin.com/service/olicenses/v1/checking/?purchase-code=" . $purchase_code . "&siteurl=" . urlencode( $site_url );
            $args=array('timeout' => 60);
            $response = wp_remote_get( $url, $args);

            if (!is_wp_error( $response )) {
                if (isset( $response[ "body" ] ) && intval($response[ "body" ] ) != 200) {
                    delete_option( "wad-license-key" );
                }
            }
        } else {
            if (get_option( "wad-license-key" )) {
                delete_option( "wad-license-key" );
            }
        }
    }
    
    /**
     * Redirects the plugin to the getting page after the activation
     */
    function wad_redirect() {
        if (get_option('wad_do_activation_redirect', false)) {
            delete_option('wad_do_activation_redirect');
            wp_redirect(admin_url('edit.php?post_type=o-discount&page=wad-about'));
        }
    }
}
