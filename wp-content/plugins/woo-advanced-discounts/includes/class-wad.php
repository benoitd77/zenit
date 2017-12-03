<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.orionorigin.com/
 * @since      0.1
 *
 * @package    Wad
 * @subpackage Wad/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1
 * @package    Wad
 * @subpackage Wad/includes
 * @author     ORION <support@orionorigin.com>
 */
class Wad {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1
	 * @access   protected
	 * @var      Wad_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1
	 */
	public function __construct() {

		$this->plugin_name = 'wad';
		$this->version = '0.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wad_Loader. Orchestrates the hooks of the plugin.
	 * - Wad_i18n. Defines internationalization functionality.
	 * - Wad_Admin. Defines all hooks for the admin area.
	 * - Wad_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.1
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wad-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wad-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wad-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wad-public.php';

		$this->loader = new Wad_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wad_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wad_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wad_Admin( $this->get_plugin_name(), $this->get_version() );
                
                $this->loader->add_action( 'init', $plugin_admin, 'init_sessions', 1);
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
                $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_wad_menu');
                
                //Free version ads and others
                $this->loader->add_action( 'admin_notices', $plugin_admin, 'get_ad_messages' );
                $this->loader->add_action( 'admin_notices', $plugin_admin, 'get_review_suggestion_notice' );
                // Runs the admin notice ignore function incase a dismiss button has been clicked
                $this->loader->add_action( 'admin_init', $plugin_admin, 'admin_notice_ignore' );
                // Runs the admin notice temp ignore function incase a temp dismiss link has been clicked
                $this->loader->add_action( 'admin_init', $plugin_admin, 'admin_notice_temp_ignore' );
                $this->loader->add_action( 'admin_init', $plugin_admin, 'wad_redirect' );
                
                $discount=new WAD_Discount(FALSE);
                $this->loader->add_action( 'init', $discount, 'register_cpt_discount' );
                $this->loader->add_action( 'add_meta_boxes', $discount, 'get_discount_metabox');
                $this->loader->add_action( 'save_post_o-discount', $discount, 'save_discount');
                $this->loader->add_action( 'save_post_product', $discount, 'save_discount');
                $this->loader->add_filter( 'manage_edit-o-discount_columns', $discount, 'get_columns');
                $this->loader->add_action( 'manage_o-discount_posts_custom_column', $discount, 'get_columns_values', 5, 2);
                $this->loader->add_action( 'woocommerce_product_write_panel_tabs',$discount, 'get_product_tab_label');
                $this->loader->add_action( 'woocommerce_product_data_panels', $discount, 'get_product_tab_data');
                $this->loader->add_action( 'woocommerce_product_meta_end', $discount, 'get_quantity_pricing_tables');
                
                $this->loader->add_filter( 'woocommerce_product_data_tabs', $discount, 'get_product_tab_label');
                
                $list=new WAD_Products_List(FALSE);
                $this->loader->add_action( 'init', $list, 'register_cpt_list' );
                $this->loader->add_action( 'add_meta_boxes', $list, 'get_list_metabox');
                $this->loader->add_action( 'save_post_o-list', $list, 'save_list');
                $this->loader->add_action( 'wp_ajax_evaluate-wad-query', $list, 'evaluate_wad_query');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1
	 * @access   private
	 */
	private function define_public_hooks() {
                $wc_version=  $this->get_wc_version();
		$plugin_public = new Wad_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
                $this->loader->add_action( 'init', $plugin_public, 'init_globals' );
//                $this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
                
                $discount=new WAD_Discount(false);
//                $this->loader->add_filter( 'woocommerce_cart_item_price', $discount, 'get_cart_item_html', 99, 3 );
                
                if( version_compare( $wc_version, "3.0.1", ">=" ) )
                {
                    $this->loader->add_filter( 'woocommerce_product_get_sale_price', $discount, 'get_sale_price', 99, 2 );
                    $this->loader->add_filter( 'woocommerce_product_get_price', $discount, 'get_regular_price', 99, 2 );
                    
                    //Variations prices
                    $this->loader->add_filter( 'woocommerce_product_variation_get_price', $discount, 'get_regular_price', 99, 2 );
                    $this->loader->add_filter( 'woocommerce_product_variation_get_sale_price', $discount, 'get_sale_price', 99, 2 );
                    
                }
                else
                {
                    $this->loader->add_filter( 'woocommerce_get_sale_price', $discount, 'get_sale_price', 99, 2 );
                    $this->loader->add_filter( 'woocommerce_get_price', $discount, 'get_regular_price', 99, 2 );
                }
                $this->loader->add_filter( 'woocommerce_cart_item_name', $discount, 'add_discount_desc_to_products_names', 99, 3 );

                //Saves the used discounts in the order
                $this->loader->add_action( 'woocommerce_checkout_update_order_meta', $discount, 'save_used_discounts' );
                
                //Makes sure the discounts id to save are initialized on the checkout page
                $this->loader->add_action( 'posts_selection', $discount, 'initialize_used_discounts_array' );
                
                //Variations prices(sale icon for variable products)
                $this->loader->add_filter( 'woocommerce_variation_prices_sale_price', $discount, 'get_sale_price', 99, 2 );
                $this->loader->add_filter( 'woocommerce_variation_prices', $discount, 'get_variations_prices', 99, 2 );
                
                $this->loader->add_action( 'woocommerce_cart_calculate_fees', $discount, 'woocommerce_custom_surcharge' );
                
                //We disable coupons if there is a discount somewhere
                $this->loader->add_action( 'woocommerce_coupons_enabled', $discount, 'get_coupons_status' );

	}
        
        function get_wc_version() {
            // If get_plugins() isn't available, require it
            if ( ! function_exists( 'get_plugins' ) )
                    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            // Create the plugins folder and file variables
            $plugin_folder = get_plugins( '/' . 'woocommerce' );
            $plugin_file = 'woocommerce.php';

            // If the plugin version number is set, return it 
            if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
                    return $plugin_folder[$plugin_file]['Version'];

            } else {
            // Otherwise return null
                    return NULL;
            }
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1
	 * @return    Wad_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
