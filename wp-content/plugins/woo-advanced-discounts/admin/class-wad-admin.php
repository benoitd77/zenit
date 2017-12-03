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
    public function __construct($plugin_name, $version) {

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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wad-admin.css', array(), $this->version, 'all');
        wp_enqueue_style("o-flexgrid", plugin_dir_url(__FILE__) . 'css/flexiblegs.css', array(), $this->version, 'all');
//                wp_enqueue_style( "acd-tooltip", plugin_dir_url( __FILE__ ) . 'css/tooltip.css', array(), $this->version, 'all' );
        wp_enqueue_style("o-ui", plugin_dir_url(__FILE__) . 'css/UI.css', array(), $this->version, 'all');
        wp_enqueue_style("o-datepciker", plugin_dir_url(__FILE__) . 'js/datepicker/css/datepicker.css', array(), $this->version, 'all');
        wp_enqueue_style("wad-datetimepicker", plugin_dir_url(__FILE__) . 'js/datetimepicker/jquery.datetimepicker.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wad-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script("o-admin", plugin_dir_url(__FILE__) . 'js/o-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script("wad-tabs", plugin_dir_url(__FILE__) . 'js/SpryAssets/SpryTabbedPanels.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("acd-accordion", plugin_dir_url(__FILE__) . 'js/SpryAssets/SpryAccordion.js', array('jquery'), $this->version, false);
        wp_enqueue_script("wad-serializejson", plugin_dir_url(__FILE__) . 'js/jquery.serializejson.min.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker", plugin_dir_url(__FILE__) . 'js/datepicker/js/datepicker.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-eye", plugin_dir_url(__FILE__) . 'js/datepicker/js/eye.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-util", plugin_dir_url(__FILE__) . 'js/datepicker/js/util.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-layout", plugin_dir_url(__FILE__) . 'js/datepicker/js/layout.js', array('jquery'), $this->version, false);
        wp_enqueue_script("wad-datetimepicker", plugin_dir_url(__FILE__) . 'js/datetimepicker/jquery.datetimepicker.full.min.js', array('jquery'), $this->version, false);
    }

    /**
     * Initialize the plugin sessions
     */
    function init_sessions() {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION["active_discounts"]))
            $_SESSION["active_discounts"] = array();
    }

    /**
     * Builds all the plugin menu and submenu
     */
    public function add_wad_menu() {
        $parent_slug = "edit.php?post_type=o-discount";
        add_submenu_page($parent_slug, __('Products Lists', 'wad'), __('Products Lists', 'wad'), 'manage_product_terms', 'edit.php?post_type=o-list', false);
        add_submenu_page($parent_slug, __('Settings', 'wad'), __('Settings', 'wad'), 'manage_product_terms', 'wad-manage-settings', array($this, 'get_wad_settings_page'));
        add_submenu_page($parent_slug, __('Pro features', 'wad' ), __( 'Pro features', 'wad' ), 'manage_product_terms', 'wad-pro-features', array($this, "get_wad_pro_features_page"));
    }

    public function get_wad_settings_page() {
        if ((isset($_POST["wad-options"]) && !empty($_POST["wad-options"]))) {
            update_option("wad-options", $_POST["wad-options"]);
        }
        wad_remove_transients();
        ?>
        <div class="o-wrap cf">
            <h1><?php _e("Woocommerce All Discounts Settings", "wad"); ?></h1>
            <form method="POST" action="" class="mg-top">
                <div class="postbox" id="wad-options-container">
                    <?php
                    $begin = array(
                        'type' => 'sectionbegin',
                        'id' => 'wad-datasource-container',
                        'table' => 'options',
                    );
                    $enable_cache = array(
                        'title' => __('Cache discounts', 'wad'),
                        'name' => 'wad-options[enable-cache]',
                        'type' => 'select',
                        'options' => array(0 => "No", 1 => "Yes"),
                        'desc' => __('whether or not to store the discounts in the cache to increase the pages load speed. Cache is valid for 12hours', 'wad'),
                        'default' => '',
                    );

                    $end = array('type' => 'sectionend');
                    $settings = array(
                        $begin,
                        $enable_cache,
                        $end
                    );
                    echo o_admin_fields($settings);
                    ?>
                </div>
                <input type="submit" class="button button-primary button-large" value="<?php _e("Save", "wad"); ?>">
            </form>
        </div>
        <?php
        global $o_row_templates;
        ?>
        <script>
            var o_rows_tpl =<?php echo json_encode($o_row_templates); ?>;
        </script>
        <?php
    }
    
    function get_wad_pro_features_page()
    {
        $messages=  $this->get_pro_features_messages();
        
        ?>
        <div class="wrap">
            <h1>Need more features? Let's go pro!</h1>
            <p style="color: red; font-size: 14px;font-weight: bold;">
                This extension is a trial version which is NOT optimized for speed and therefore may slow down large websites. 
                If you notice that behaviour on your website, we STRONGLY advise you to upgrade to the pro version which is very optimized.
            </p>
            <div id="wad-pro-features">
                <div class="o-wrap">
                    <?php
                            foreach ($messages as $message_key=>$message)
                            {
                                ?>
                    <div class="col xl-1-3 wad-infox">
                        <p>
                        <h3><?php echo $message_key;?></h3>
                        </p>
                        <p>
                            <?php echo ucfirst($message);?>
                        </p>

                        <a href="http://www.orionorigin.com/plugins/woocommerce-all-discounts/?utm_source=Free%20Trial&utm_medium=cpc&utm_term=<?php echo urlencode($message_key);?>&utm_campaign=Woocommerce%20All%20Discounts" class="button"  target="_blank">Click here to unlock</a></p>
                    </div>
                                <?php
                            }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    function get_pro_features_messages()
    {
        $messages=array(
            "Improved Speed"=>"Do you feel the plugin is a bit slow? Upgrade to make it faster.",
            "Bulk discounts per category"=>"Create a quantity based pricing per product category by setting the quantities intervals (minimum and maximum quantities) and apply a percentage or fixed amount discount off each product price.",
            "Bulk discounts per user role"=>"Create a quantity based pricing per customer role by setting the quantities intervals (minimum and maximum quantities) and apply a percentage or fixed amount discount off each product price.",
            "First time order discounts"=>"Increase your chances to convert a first time visitor to a customer by automatically applying a discount to his first order.",
            "N-th order discount"=>"Reward and reinforce your customers loyalty by assigning a dynamic discount to those who purchased from your store a certain number of times.",
            "Discounts based on the customer email domain"=>"Offer any type of discount to any customer who registers using an email address based on a specific domain name.",
            "Free gifts"=>"create a \"Buy one, get one for free\" kind of discount",
            "Shipping Country"=>"apply a discount based on the shipping country.",
            "Billing Country"=>"apply a discount based on the billing country.",
            "Payment gateways"=>"apply a discount if the customers checks out with a specific payment gateway.",
            "Usage limit"=>"limits the number of customers who can use a discount.",
            "Periodic discounts"=>"automatically enable a discount periodically.",
            "Social networks"=>"apply a discounts if the customer is following you on social networks.",
            "Groups based discounts"=>"apply a discount is the customer belong to a specific group.",
            "Newsletters based discounts"=>"offer a discount if the customer subscribed to your newsletters.",
            "Taxes inclusion"=>"apply discounts on subtotal with or without the taxes.",
            "Specific users discounts"=>"apply discounts for specific(s) customer(s).",
            "Currency based discounts"=>"apply discounts depending on the customer selected currency (useful for currency switchers).",
            "Previous purchases discounts"=>"ability to define a discount based on previously purchased products.",
        );
        return $messages;
    }
    
    function get_ad_messages()
    {
        global $pagenow;
        $messages=  $this->get_pro_features_messages();
        $random_message_key=  array_rand($messages);
        if(($pagenow=="post-new.php"
            ||$pagenow=="post.php"
            ||(isset($_GET["post_type"])&&$_GET["post_type"]=="o-discount")
            ||(isset($_GET["post_type"])&&$_GET["post_type"]=="product")
            ||(isset($_GET["page"])&&$_GET["page"]=="o-list")
        )
            &&
            (isset($_GET["page"])&&$_GET["page"]!="wad-pro-features"))
        {
            echo '<div class="wad-info">
               <p><strong>'.$random_message_key.'</strong>: '.$messages[$random_message_key].' <a href="http://www.orionorigin.com/plugins/woocommerce-all-discounts/?utm_source=Free%20Trial&utm_medium=cpc&utm_term='.urlencode($random_message_key).'&utm_campaign=Woocommerce%20All%20Discounts" class="button"  target="_blank">Click here to unlock</a></p>
            </div>';
        }

    }
    
    function get_review_suggestion_notice()
    {
        $ignore_notices=  get_option( 'wad_admin_notice_ignore' );
        $dismiss_transient=get_transient( 'wad_notice_dismiss' );
        if($ignore_notices||$dismiss_transient!==false)
            return;
        
        $two_week_review_ignore = add_query_arg( array( 'wad_admin_notice_ignore' => '1' ) );
        $two_week_review_temp = add_query_arg( array( 'wad_admin_notice_temp_ignore' => '1', 'wad_int' => 14 ) );
        $one_week_support = add_query_arg( array( 'wad_admin_notice_ignore' => '1' ) );
        ?>
        <div class="update-nag wad-admin-notice">
            <div class="wad-notice-logo"></div> 
            <p class="wad-notice-title">Leave A Review? </p> 
            <p class="wad-notice-body">We hope you've enjoyed using Woocommerce Advanced Discounts! Would you consider leaving us a review on WordPress.org? </p>
            <ul class="wad-notice-body wad-red">
                <li> <span class="dashicons dashicons-smiley"></span><a href="<?php echo $two_week_review_ignore;?>"> I've already left a review</a></li>
                <li><span class="dashicons dashicons-calendar-alt"></span><a href="<?php echo $two_week_review_temp;?>">Maybe Later</a></li>
                <li><span class="dashicons dashicons-external"></span><a href="https://wordpress.org/support/view/plugin-reviews/woo-advanced-discounts?filter=5" target="_blank">Sure! I'd love to!</a></li>
            </ul>
            <a href="<?php echo $one_week_support;?>" class="dashicons dashicons-dismiss"></a>
        </div>
        <?php
    }
    
    // Ignore function that gets ran at admin init to ensure any messages that were dismissed get marked
    public function admin_notice_ignore() {

        // If user clicks to ignore the notice, update the option to not show it again
        if ( isset($_GET['wad_admin_notice_ignore']) && current_user_can( 'manage_product_terms' ) ) {
                update_option( 'wad_admin_notice_ignore', true );
                $query_str = remove_query_arg( 'wad_admin_notice_ignore' );
                wp_redirect( $query_str );
                exit;
        }
    }

    // Temp Ignore function that gets ran at admin init to ensure any messages that were temp dismissed get their start date changed
    public function admin_notice_temp_ignore() {

        // If user clicks to temp ignore the notice, update the option to change the start date - default interval of 14 days
        if ( isset($_GET['wad_admin_notice_temp_ignore']) && current_user_can( 'manage_product_terms' ) ) {            
            $interval = ( isset( $_GET[ 'wad_int' ] ) ? $_GET[ 'wad_int' ] : 14 );
            set_transient( 'wad_notice_dismiss', true, MINUTE_IN_SECONDS*$interval * DAY_IN_SECONDS );
            $query_str = remove_query_arg( array( 'wad_admin_notice_temp_ignore', 'wad_int' ) );
            wp_redirect( $query_str );
            exit;
        }
    }
    
    /**
     * Redirects the plugin to the about page after the activation
     */
    function wad_redirect() {
        if (get_option('wad_do_activation_redirect', false)) {
            delete_option('wad_do_activation_redirect');
            wp_redirect(admin_url('edit.php?post_type=o-discount&page=wad-pro-features'));
        }
    }

}
