<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.thedotstore.com/
 * @since             1.0.0
 * @package           Advanced_Usps_Shipping_Method
 *
 * @wordpress-plugin
 * Plugin Name: USPS Shipping for WooCommerce – Live Rates
 * Plugin URI:        https://www.thedotstore.com/
 * Description:       This plugin will help you to connect with USPS API and fetch shipping services rate.
 * Version:           1.0.4
 * Author:            theDotstore
 * Author URI:        https://www.thedotstore.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-usps-shipping-method
 * Domain Path:       /languages
 * 
 * WC requires at least: 3.0
 * WP tested up to:     6.5.3
 * WC tested up to:     8.8.3
 * Requires PHP:        7.4
 * Requires at least:   5.9
 * 
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( function_exists( 'ausm_fs' ) ) {
    ausm_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if ( !function_exists( 'ausm_fs' ) ) {
        // Create a helper function for easy SDK access.
        function ausm_fs() {
            global $ausm_fs;
            if ( !isset( $ausm_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_9804_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_9804_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $ausm_fs = fs_dynamic_init( array(
                    'id'             => '9804',
                    'slug'           => 'advanced-usps-shipping-method',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_7e43862490e02b99c78fa62f0c5e6',
                    'is_premium'     => false,
                    'premium_suffix' => 'undefined',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 14,
                        'is_require_payment' => true,
                    ),
                    'menu'           => array(
                        'slug'       => 'advanced-usps-shipping-method',
                        'first-path' => 'admin.php?page=advanced-usps-shipping-method',
                        'support'    => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $ausm_fs;
        }

        // Init Freemius.
        ausm_fs();
        // Signal that SDK was initiated.
        do_action( 'ausm_fs_loaded' );
    }
    // ... Your plugin's main file logic ...
}
/**
 * Currently plugin version.
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_VERSION' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_VERSION', '1.0.4' );
}
/**
 * Define plguin name
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_NAME' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_NAME', __( 'USPS Shipping', 'advanced-usps-shipping-method' ) );
}
/**
 * Define plguin description
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_DESC' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_DESC', __( 'This plugin will help you to connect with USPS API and fetch shipping services rate.', 'advanced-usps-shipping-method' ) );
}
/**
 * Define plguin description
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_SLUG' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_SLUG', 'advanced-usps-shipping-method' );
}
/**
 * Define plugin URL
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_URL' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_URL', plugin_dir_url( __FILE__ ) );
}
/**
 * Define plugin directory url
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_DIR' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_DIR', dirname( __FILE__ ) );
}
/**
 * Define plugin base name like first file call in plugin
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_BASENAME' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_BASENAME', plugin_basename( __FILE__ ) );
}
/**
 * Define plugin directory path
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
/**
 * Define plugin status name on whole plugin
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_VERSION_TYPE' ) ) {
    if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
        define( 'ADVANCED_USPS_SHIPPING_METHOD_VERSION_TYPE', __( 'Pro', 'advanced-usps-shipping-method' ) );
    } else {
        define( 'ADVANCED_USPS_SHIPPING_METHOD_VERSION_TYPE', __( 'Free', 'advanced-usps-shipping-method' ) );
    }
}
/**
 * Define plugin logo URL
 */
if ( !defined( 'ADVANCED_USPS_SHIPPING_METHOD_LOGO' ) ) {
    define( 'ADVANCED_USPS_SHIPPING_METHOD_LOGO', ADVANCED_USPS_SHIPPING_METHOD_URL . 'admin/images/advanced-usps-shipping-method.png' );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-advanced-usps-shipping-method-activator.php
 */
if ( !function_exists( 'activate_advanced_usps_shipping_method' ) ) {
    function activate_advanced_usps_shipping_method() {
        require_once ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . 'includes/class-advanced-usps-shipping-method-activator.php';
        Advanced_Usps_Shipping_Method_Activator::activate();
    }

}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-advanced-usps-shipping-method-deactivator.php
 */
if ( !function_exists( 'deactivate_advanced_usps_shipping_method' ) ) {
    function deactivate_advanced_usps_shipping_method() {
        require_once ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . 'includes/class-advanced-usps-shipping-method-deactivator.php';
        Advanced_Usps_Shipping_Method_Deactivator::deactivate();
    }

}
register_activation_hook( __FILE__, 'activate_advanced_usps_shipping_method' );
register_deactivation_hook( __FILE__, 'deactivate_advanced_usps_shipping_method' );
/**
 * Deactivate addon if woocommerce plugin deactivated or not exist
 */
add_action( 'admin_init', 'ausm_deactivate_plugin' );
if ( !function_exists( 'ausm_deactivate_plugin' ) ) {
    function ausm_deactivate_plugin() {
        if ( is_multisite() ) {
            if ( is_main_site() ) {
                if ( !is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
                    deactivate_plugins( '/advanced-usps-shipping-method/advanced-usps-shipping-method.php', true );
                }
            } else {
                $active_plugins = get_option( 'active_plugins', array() );
                if ( is_multisite() ) {
                    $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
                    $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
                }
                if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                    deactivate_plugins( '/advanced-usps-shipping-method/advanced-usps-shipping-method.php', true );
                }
            }
        } else {
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
                deactivate_plugins( '/advanced-usps-shipping-method/advanced-usps-shipping-method.php', true );
            }
        }
    }

}
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-advanced-usps-shipping-method.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if ( !function_exists( 'run_advanced_usps_shipping_method' ) ) {
    function run_advanced_usps_shipping_method() {
        $plugin = new Advanced_Usps_Shipping_Method();
        $plugin->run();
    }

}
run_advanced_usps_shipping_method();
/**
 * Hide freemius account tab
 *
 * @since    3.9.3
 */
if ( !function_exists( 'ausm_hide_account_tab' ) ) {
    function ausm_hide_account_tab() {
        return true;
    }

    ausm_fs()->add_filter( 'hide_account_tabs', 'ausm_hide_account_tab' );
}
/**
 * Include plugin header on freemius account page
 *
 * @since    1.0.0
 */
if ( !function_exists( 'ausm_load_plugin_header_after_account' ) ) {
    function ausm_load_plugin_header_after_account() {
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-header.php';
        ?>
        </div>
        </div>
        </div>
        </div>
        <?php 
    }

    ausm_fs()->add_action( 'after_account_details', 'ausm_load_plugin_header_after_account' );
}
/**
 * Hide billing and payments details from freemius account page
 *
 * @since    3.9.3
 */
if ( !function_exists( 'ausm_hide_billing_and_payments_info' ) ) {
    function ausm_hide_billing_and_payments_info() {
        return true;
    }

    ausm_fs()->add_action( 'hide_billing_and_payments_info', 'ausm_hide_billing_and_payments_info' );
}
/**
 * Hide powerd by popup from freemius account page
 *
 * @since    3.9.3
 */
if ( !function_exists( 'ausm_hide_freemius_powered_by' ) ) {
    function ausm_hide_freemius_powered_by() {
        return true;
    }

    ausm_fs()->add_action( 'hide_freemius_powered_by', 'ausm_hide_freemius_powered_by' );
}
/**
 * Plugin compability with WooCommerce HPOS
 *
 * @since 1.0.0
 */
add_action( 'before_woocommerce_init', function () {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );