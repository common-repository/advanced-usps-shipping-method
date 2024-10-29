<?php

//phpcs:ignore
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.thedotstore.com/
 * @since      1.0.0
 *
 * @package    Advanced_Usps_Shipping_Method
 * @subpackage Advanced_Usps_Shipping_Method/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Usps_Shipping_Method
 * @subpackage Advanced_Usps_Shipping_Method/admin
 * @author     theDotstore <support@thedotstore.com>
 */
class Advanced_Usps_Shipping_Method_Admin {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
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
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Advanced_Usps_Shipping_Method_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Advanced_Usps_Shipping_Method_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->plugin_name . '-header-sidebar',
            plugin_dir_url( __FILE__ ) . 'css/ausm-admin-header-sidebar.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/advanced-usps-shipping-method-admin.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-ausm-media',
            plugin_dir_url( __FILE__ ) . 'css/ausm-media.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-font-awesome',
            plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . 'plugin-new-style',
            plugin_dir_url( __FILE__ ) . 'css/plugin-new-style.css',
            array(),
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Advanced_Usps_Shipping_Method_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Advanced_Usps_Shipping_Method_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/advanced-usps-shipping-method-admin.js',
            array('jquery'),
            $this->version,
            false
        );
        wp_localize_script( $this->plugin_name, 'ausm_ajax_object', array(
            'ajax_url'          => admin_url( 'admin-ajax.php' ),
            'ausm_nonce'        => wp_create_nonce( 'ausm-ajax-nonce' ),
            'process_text'      => esc_html__( 'Please wait...', 'advanced-usps-shipping-method' ),
            'import_error_file' => esc_html__( 'Please choose JSON file for import.', 'advanced-usps-shipping-method' ),
        ) );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script(
            $this->plugin_name . '-tiptip',
            plugin_dir_url( __FILE__ ) . 'js/jquery.tipTip.min.js',
            array('jquery'),
            $this->version,
            false
        );
        wp_enqueue_script(
            $this->plugin_name . '-blockui',
            plugin_dir_url( __FILE__ ) . 'js/jquery.blockUI.min.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    public function ausm_menu_for_setting() {
        global $GLOBALS;
        if ( empty( $GLOBALS['admin_page_hooks']['dots_store'] ) ) {
            add_menu_page(
                'DotStore Plugins',
                esc_html__( 'DotStore Plugins', 'advanced-usps-shipping-method' ),
                'null',
                'dots_store',
                array(),
                'dashicons-marker',
                25
            );
        }
        //Configuration page
        add_submenu_page(
            'dots_store',
            'USPS Shipping',
            'USPS Shipping',
            'manage_options',
            'advanced-usps-shipping-method',
            array($this, 'ausm_configuration_page')
        );
        if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
            add_submenu_page(
                'dots_store',
                'Import/Export',
                'Import/Export',
                'manage_options',
                'ausm-import-export',
                array($this, 'ausm_import_export_page')
            );
        }
        add_submenu_page(
            'dots_store',
            'Getting Started',
            'Getting Started',
            'manage_options',
            'ausm-get-started',
            array($this, 'ausm_get_started_page')
        );
        add_submenu_page(
            'dots_store',
            'Quick info',
            'Quick info',
            'manage_options',
            'ausm-information',
            array($this, 'ausm_information_page')
        );
    }

    /**
     * Add custom css for dotstore icon in admin area
     *
     */
    public function ausm_admin_menu_icon_style() {
        echo '<style>
		  .toplevel_page_dots_store .dashicons-marker::after{content:"";border:3px solid;position:absolute;top:14px;left:15px;border-radius:50%;opacity: 0.6;}
		  li.toplevel_page_dots_store:hover .dashicons-marker::after,li.toplevel_page_dots_store.current .dashicons-marker::after{opacity: 1;}
		  @media only screen and (max-width: 960px){
			  .toplevel_page_dots_store .dashicons-marker::after{left:14px;}
		  } </style>';
    }

    /**
     * Create a menu for plugin.
     *
     * @param string $current current page.
     *
     * @since    3.6.1
     */
    public function ausm_pro_menus( $current = 'advanced-usps-shipping-method' ) {
        $wpfp_menus = array(
            'main_menu' => array(
                'pro_menu'  => array(
                    'advanced-usps-shipping-method'         => array(
                        'menu_title' => __( 'Configure USPS', 'advanced-usps-shipping-method' ),
                        'menu_slug'  => 'advanced-usps-shipping-method',
                        'menu_url'   => $this->ausm_pro_plugins_url(
                            '',
                            'advanced-usps-shipping-method',
                            '',
                            '',
                            ''
                        ),
                    ),
                    'ausm-get-started'                      => array(
                        'menu_title' => __( 'Settings', 'advanced-usps-shipping-method' ),
                        'menu_slug'  => 'ausm-get-started',
                        'menu_url'   => $this->ausm_pro_plugins_url(
                            '',
                            'ausm-get-started',
                            '',
                            '',
                            ''
                        ),
                    ),
                    'advanced-usps-shipping-method-account' => array(
                        'menu_title' => __( 'License', 'advanced-usps-shipping-method' ),
                        'menu_slug'  => 'advanced-usps-shipping-method-account',
                        'menu_url'   => esc_url( ausm_fs()->get_account_url() ),
                    ),
                ),
                'free_menu' => array(
                    'advanced-usps-shipping-method' => array(
                        'menu_title' => __( 'Configure USPS', 'advanced-usps-shipping-method' ),
                        'menu_slug'  => 'advanced-usps-shipping-method',
                        'menu_url'   => $this->ausm_pro_plugins_url(
                            '',
                            'advanced-usps-shipping-method',
                            '',
                            '',
                            ''
                        ),
                    ),
                    'ausm-get-started'              => array(
                        'menu_title' => __( 'Settings', 'advanced-usps-shipping-method' ),
                        'menu_slug'  => 'ausm-get-started',
                        'menu_url'   => $this->ausm_pro_plugins_url(
                            '',
                            'ausm-get-started',
                            '',
                            '',
                            ''
                        ),
                    ),
                ),
            ),
        );
        ?>
		<div class="dots-menu-main">
			<nav>
				<ul>
					<?php 
        $main_current = $current;
        $sub_current = $current;
        foreach ( $wpfp_menus['main_menu'] as $main_menu_slug => $main_wpfp_menu ) {
            if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
                if ( 'pro_menu' === $main_menu_slug || 'common_menu' === $main_menu_slug ) {
                    foreach ( $main_wpfp_menu as $menu_slug => $wpfp_menu ) {
                        if ( 'ausm-information' === $main_current || 'ausm-import-export' === $main_current || 'ausm-get-started' === $main_current ) {
                            $main_current = 'ausm-get-started';
                        }
                        $class = ( $menu_slug === $main_current ? 'active' : '' );
                        ?>
									<li>
										<a class="dotstore_plugin <?php 
                        echo esc_attr( $class );
                        ?>"
											href="<?php 
                        echo esc_url( $wpfp_menu['menu_url'] );
                        ?>">
											<?php 
                        echo esc_html( $wpfp_menu['menu_title'] );
                        ?>
										</a>
										<?php 
                        if ( isset( $wpfp_menu['sub_menu'] ) && !empty( $wpfp_menu['sub_menu'] ) ) {
                            ?>
											<ul class="sub-menu">
												<?php 
                            foreach ( $wpfp_menu['sub_menu'] as $sub_menu_slug => $wpfp_sub_menu ) {
                                $sub_class = ( $sub_menu_slug === $sub_current ? 'active' : '' );
                                ?>

													<li>
														<a class="dotstore_plugin <?php 
                                echo esc_attr( $sub_class );
                                ?>"
															href="<?php 
                                echo esc_url( $wpfp_sub_menu['menu_url'] );
                                ?>">
															<?php 
                                echo esc_html( $wpfp_sub_menu['menu_title'] );
                                ?>
														</a>
													</li>
												<?php 
                            }
                            ?>
											</ul>
										<?php 
                        }
                        ?>
									</li>
									<?php 
                    }
                }
            } else {
                if ( 'free_menu' === $main_menu_slug || 'common_menu' === $main_menu_slug ) {
                    foreach ( $main_wpfp_menu as $menu_slug => $wpfp_menu ) {
                        if ( 'ausm-information' === $main_current || 'advanced-usps-shipping-method-account' === $main_current ) {
                            $main_current = 'ausm-get-started';
                        }
                        $class = ( $menu_slug === $main_current ? 'active' : '' );
                        ?>
									<li>
										<a class="dotstore_plugin <?php 
                        echo esc_attr( $class );
                        ?>"
										   href="<?php 
                        echo esc_url( $wpfp_menu['menu_url'] );
                        ?>">
											<?php 
                        echo esc_html( $wpfp_menu['menu_title'] );
                        ?>
										</a>
										<?php 
                        if ( isset( $wpfp_menu['sub_menu'] ) && !empty( $wpfp_menu['sub_menu'] ) ) {
                            ?>
											<ul class="sub-menu">
												<?php 
                            foreach ( $wpfp_menu['sub_menu'] as $sub_menu_slug => $wpfp_sub_menu ) {
                                $sub_class = ( $sub_menu_slug === $sub_current ? 'active' : '' );
                                ?>

													<li>
														<a class="dotstore_plugin <?php 
                                echo esc_attr( $sub_class );
                                ?>"
														   href="<?php 
                                echo esc_url( $wpfp_sub_menu['menu_url'] );
                                ?>">
															<?php 
                                echo esc_html( $wpfp_sub_menu['menu_title'] );
                                ?>
														</a>
													</li>
												<?php 
                            }
                            ?>
											</ul>
										<?php 
                        }
                        ?>
									</li>
									<?php 
                    }
                }
            }
        }
        ?>
				</ul>
			</nav>
		</div>
		<?php 
    }

    /**
     * Plugins URL
     *
     * @since    3.6.1
     */
    public function ausm_pro_plugins_url(
        $id,
        $page,
        $tab,
        $action,
        $nonce
    ) {
        $query_args = array();
        if ( '' !== $page ) {
            $query_args['page'] = $page;
        }
        if ( '' !== $tab ) {
            $query_args['tab'] = $tab;
        }
        if ( '' !== $action ) {
            $query_args['action'] = $action;
        }
        if ( '' !== $id ) {
            $query_args['id'] = $id;
        }
        if ( '' !== $nonce ) {
            $query_args['_wpnonce'] = wp_create_nonce( 'ausmnonce' );
        }
        return esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
    }

    /**
     * Plugin configuration page
     *
     * @since    1.0.0
     */
    public function ausm_configuration_page() {
        require_once ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . 'admin/partials/ausm-configuration-page.php';
    }

    /**
     * Import/Export setting page
     *
     * @since    1.0.0
     */
    public function ausm_import_export_page() {
        require_once ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . 'admin/partials/ausm-import-export-page.php';
    }

    /**
     * Getting started page
     *
     * @since    1.0.0
     */
    public function ausm_get_started_page() {
        require_once ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . 'admin/partials/ausm-get-started-page.php';
    }

    /**
     * Getting started page
     *
     * @since    1.0.0
     */
    public function ausm_information_page() {
        require_once ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . 'admin/partials/ausm-information-page.php';
    }

    public function ausm_custom_add_update_options() {
        $post_wpnonce = filter_input( INPUT_POST, 'ausm_configuration_save', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $post_retrieved_nonce = ( isset( $post_wpnonce ) ? sanitize_text_field( wp_unslash( $post_wpnonce ) ) : '' );
        if ( !wp_verify_nonce( $post_retrieved_nonce, 'ausm_save_action' ) ) {
            //Return message
            $return_arr = array(
                'page'  => 'advanced-usps-shipping-method',
                'error' => true,
            );
        } else {
            //Sanitizing requests
            $get_ausm_status = filter_input( INPUT_POST, 'ausm_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $get_ausm_debug_mode = filter_input( INPUT_POST, 'ausm_debug_mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $get_ausm_user_id = filter_input( INPUT_POST, 'ausm_user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $get_ausm_origin = filter_input( INPUT_POST, 'ausm_origin', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $get_ausm_service_type = filter_input( INPUT_POST, 'ausm_service_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
                $services_filter = array(
                    'ausm_services' => array(
                        'filter' => array(FILTER_VALIDATE_INT, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                        'flags'  => FILTER_REQUIRE_ARRAY,
                    ),
                );
            } else {
                $services_filter = array(
                    'ausm_services' => array(
                        'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                        'flags'  => FILTER_REQUIRE_ARRAY,
                    ),
                );
            }
            $get_ausm_services = filter_input_array( INPUT_POST, $services_filter );
            $get_ausm_default_title = filter_input( INPUT_POST, 'ausm_default_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $get_ausm_default_rate = filter_input(
                INPUT_POST,
                'ausm_default_rate',
                FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION
            );
            $get_ausm_cheapest_rate = filter_input( INPUT_POST, 'ausm_cheapest_rate', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            //Validating requests
            $ausm_status = ( isset( $get_ausm_status ) ? sanitize_text_field( $get_ausm_status ) : '' );
            $ausm_debug_mode = ( isset( $get_ausm_debug_mode ) ? sanitize_text_field( $get_ausm_debug_mode ) : '' );
            $ausm_user_id = ( isset( $get_ausm_user_id ) ? sanitize_text_field( $get_ausm_user_id ) : '' );
            $ausm_origin = ( isset( $get_ausm_origin ) ? sanitize_text_field( $get_ausm_origin ) : '' );
            $ausm_service_type = ( isset( $get_ausm_service_type ) ? sanitize_text_field( $get_ausm_service_type ) : '' );
            $ausm_services = ( isset( $get_ausm_services ) ? $get_ausm_services['ausm_services'] : array() );
            $ausm_default_title = ( isset( $get_ausm_default_title ) ? sanitize_text_field( $get_ausm_default_title ) : '' );
            $ausm_default_rate = ( isset( $get_ausm_default_rate ) ? sanitize_text_field( $get_ausm_default_rate ) : '' );
            $ausm_cheapest_rate = ( isset( $get_ausm_cheapest_rate ) ? sanitize_text_field( $get_ausm_cheapest_rate ) : '' );
            //Storing requests
            $ausm_data = array();
            $ausm_data['ausm_status'] = $ausm_status;
            $ausm_data['ausm_debug_mode'] = $ausm_debug_mode;
            $ausm_data['ausm_user_id'] = $ausm_user_id;
            $ausm_data['ausm_origin'] = $ausm_origin;
            $ausm_data['ausm_service_type'] = $ausm_service_type;
            $ausm_data['ausm_services'] = $ausm_services;
            $ausm_data['ausm_default_title'] = $ausm_default_title;
            $ausm_data['ausm_default_rate'] = $ausm_default_rate;
            $ausm_data['ausm_cheapest_rate'] = $ausm_cheapest_rate;
            update_option( 'ausm_config', $ausm_data );
            //Return message
            $return_arr = array(
                'page'    => 'advanced-usps-shipping-method',
                'success' => true,
            );
        }
        wp_safe_redirect( add_query_arg( $return_arr, admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * Remove section from shipping settings because we have added new menu in admin section
     *
     * @param array $sections
     *
     * @return array $sections
     *
     * @since    1.0.0
     */
    public function ausm_remove_section( $sections ) {
        unset($sections['dots_ausm']);
        return $sections;
    }

    /**
     * Remove submenu from admin screeen
     *
     * @since    1.0.0
     */
    public function ausm_remove_admin_submenus() {
        remove_submenu_page( 'dots_store', 'ausm-import-export' );
        remove_submenu_page( 'dots_store', 'ausm-get-started' );
        remove_submenu_page( 'dots_store', 'ausm-information' );
    }

    /**
     * Admin footer review
     *
     * @since 1.0.0
     */
    public function ausm_pro_admin_footer_review() {
        $html = '';
        $url = '';
        $url = esc_url( 'https://wordpress.org/plugins/advanced-usps-shipping-method/#reviews' );
        $html .= sprintf(
            '%s<strong>%s</strong>%s<a href=%s target="_blank">%s</a>',
            esc_html__( 'If you like installing ', 'advanced-usps-shipping-method' ),
            esc_html__( 'USPS Shipping plugin', 'advanced-usps-shipping-method' ),
            esc_html__( ', please leave us &#9733;&#9733;&#9733;&#9733;&#9733; ratings on ', 'advanced-usps-shipping-method' ),
            $url,
            esc_html__( 'DotStore', 'advanced-usps-shipping-method' )
        );
        echo wp_kses_post( $html );
    }

    /**
     * Add review stars in plugin row meta
     *
     * @since 1.0.0
     */
    public function ausm_plugin_row_meta_action_links(
        $plugin_meta,
        $plugin_file,
        $plugin_data,
        $status
    ) {
        if ( isset( $plugin_data['TextDomain'] ) && $plugin_data['TextDomain'] !== 'advanced-usps-shipping-method' ) {
            return $plugin_meta;
        }
        $url = '';
        $url = esc_url( 'https://wordpress.org/plugins/advanced-usps-shipping-method/#reviews' );
        $plugin_meta[] = sprintf( '<a href="%s" target="_blank" style="color:#f5bb00;">%s</a>', $url, esc_html( '★★★★★' ) );
        return $plugin_meta;
    }

}
