<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.thedotstore.com/
 * @since      1.0.0
 *
 * @package    Advanced_Usps_Shipping_Method
 * @subpackage Advanced_Usps_Shipping_Method/includes
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
 * @since      1.0.0
 * @package    Advanced_Usps_Shipping_Method
 * @subpackage Advanced_Usps_Shipping_Method/includes
 * @author     theDotstore <support@thedotstore.com>
 */
if ( !class_exists( 'Advanced_Usps_Shipping_Method' ) ) {
	class Advanced_Usps_Shipping_Method {
		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      Advanced_Usps_Shipping_Method_Loader    $loader    Maintains and registers all hooks for the plugin.
		 */
		protected $loader;

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;

		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
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
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->version = defined( 'ADVANCED_USPS_SHIPPING_METHOD_VERSION' ) ? ADVANCED_USPS_SHIPPING_METHOD_VERSION : '1.0.0';
			$this->plugin_name = defined( 'ADVANCED_USPS_SHIPPING_METHOD_SLUG' ) ? ADVANCED_USPS_SHIPPING_METHOD_SLUG : 'advanced-usps-shipping-method';

			
			$this->load_dependencies();
			$this->set_locale();
			$this->init();
			$this->define_admin_hooks();
			$this->define_public_hooks();

			//Quick links on plugin list
	        $prefix = is_network_admin() ? 'network_admin_' : '';
	        add_filter(
	            "{$prefix}plugin_action_links_" . ADVANCED_USPS_SHIPPING_METHOD_BASENAME, array(
	            $this,
	            'ausm_plugin_action_links',
	            ), 10, 4 
	        );
		}

		private function init(){
			// Initialize shipping method class
			add_action( 'woocommerce_shipping_init', array( $this, 'ausm_init_shipping_method' ) );
			// Register shipping method
			add_action( 'woocommerce_shipping_methods', array( $this, 'ausm_register_shipping_method_class' ) );
		}

		public function ausm_init_shipping_method(){
			require_once ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . 'includes/ausm-init-shipping-methods.php';
		}

		public function ausm_register_shipping_method_class( $methods ){
			if ( class_exists( 'AUSM_Shipping_Method' ) ) {
				$methods[] = 'AUSM_Shipping_Method';
			}
			return $methods;
		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - Advanced_Usps_Shipping_Method_Loader. Orchestrates the hooks of the plugin.
		 * - Advanced_Usps_Shipping_Method_i18n. Defines internationalization functionality.
		 * - Advanced_Usps_Shipping_Method_Admin. Defines all hooks for the admin area.
		 * - Advanced_Usps_Shipping_Method_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function load_dependencies() {

			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-advanced-usps-shipping-method-loader.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-advanced-usps-shipping-method-i18n.php';

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-advanced-usps-shipping-method-admin.php';

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-advanced-usps-shipping-method-public.php';

			$this->loader = new Advanced_Usps_Shipping_Method_Loader();

		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the Advanced_Usps_Shipping_Method_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function set_locale() {

			$plugin_i18n = new Advanced_Usps_Shipping_Method_i18n();

			$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_admin_hooks() {
			$page         = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			
			$plugin_admin = new Advanced_Usps_Shipping_Method_Admin( $this->get_plugin_name(), $this->get_version() );

			if ( ! empty( $page ) && ( false !== strpos( $page, 'ausm' ) || false !== strpos( $page, 'advanced-usps-shipping-method' ) ) ) {
				$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
				$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
				$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'ausm_pro_admin_footer_review' );
			}

			$this->loader->add_filter( 'woocommerce_get_sections_shipping', $plugin_admin, 'ausm_remove_section' );
			$this->loader->add_action( 'admin_post_submit_form_ausm', $plugin_admin, 'ausm_custom_add_update_options' );
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'ausm_menu_for_setting' );
			$this->loader->add_action( 'admin_head', $plugin_admin, 'ausm_remove_admin_submenus' );
			$this->loader->add_action( 'admin_head', $plugin_admin, 'ausm_admin_menu_icon_style' );
			if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
	    		$this->loader->add_action( 'wp_ajax_usps_api_connection_check', $plugin_admin, 'ausm_api_connection_check__premium_only' );
	    		$this->loader->add_action( 'wp_ajax_ausm_export_plugin_settings', $plugin_admin, 'ausm_export_plugin_settings__premium_only' );
	    		$this->loader->add_action( 'wp_ajax_ausm_import_plugin_settings', $plugin_admin, 'ausm_import_plugin_settings__premium_only' );
			}
			$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'ausm_plugin_row_meta_action_links', 20, 4 );
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_public_hooks() {

			$plugin_public = new Advanced_Usps_Shipping_Method_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		}

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run() {
			$this->loader->run();
		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    Advanced_Usps_Shipping_Method_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader() {
			return $this->loader;
		}

		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @since     1.0.0
		 * @return    string    The version number of the plugin.
		 */
		public function get_version() {
			return $this->version;
		}

		/**
	     * Return the plugin action links.  This will only be called if the plugin
	     * is active.
	     *
	     * @param array $actions associative array of action names to anchor tags
	     *
	     * @return array associative array of plugin action links
	     * @since  1.0.0
	     */
	    public function ausm_plugin_action_links( $actions )
	    {
	        $custom_actions = array(
	        	'support' => sprintf('<a href="%s" target="_blank">%s</a>', esc_url('www.thedotstore.com/support'), __('Support', 'advanced-usps-shipping-method')),
	        	'setting' => sprintf('<a href="%s" target="_blank">%s</a>', esc_url( add_query_arg( array( 'page'=> 'advanced-usps-shipping-method' ), admin_url( 'admin.php' ) ) ), __('Settings', 'advanced-usps-shipping-method')),
	        );
	        // add the links to the front of the actions list
	        return array_merge($custom_actions, $actions);
	    }
	}
}
