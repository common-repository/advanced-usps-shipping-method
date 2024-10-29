<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.thedotstore.com/
 * @since      1.0.0
 *
 * @package    Advanced_Usps_Shipping_Method
 * @subpackage Advanced_Usps_Shipping_Method/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Advanced_Usps_Shipping_Method
 * @subpackage Advanced_Usps_Shipping_Method/includes
 * @author     theDotstore <support@thedotstore.com>
 */
class Advanced_Usps_Shipping_Method_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if (is_multisite() ) {
            if(is_main_site()){
				if( !is_plugin_active_for_network('woocommerce/woocommerce.php') ) {
					wp_die( sprintf(wp_kses_post('<strong>%2$s</strong> must requires below plugins on your main site: <ul><li><strong>WooCommerce</strong></li></ul> Return to <a href=%1$s>Plugins page</a>.', 'multi-vendor-shipping-addon'), esc_url(get_admin_url(null, 'plugins.php')), esc_html(ADVANCED_USPS_SHIPPING_METHOD_NAME)) );
				}
			} else {
				$active_plugins = get_option( 'active_plugins', array() );
                if ( is_multisite() ) {
                    $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
                    $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
                }
				if (!in_array( 'woocommerce/woocommerce.php', apply_filters('active_plugins', $active_plugins), true ) ) {
					wp_die( sprintf(wp_kses_post('<strong>%2$s</strong> must requires below plugins on your sub site: <ul><li><strong>WooCommerce</strong></li></ul> Return to <a href=%1$s>Plugins page</a>.', 'multi-vendor-shipping-addon'), esc_url(get_admin_url(null, 'plugins.php')), esc_html(ADVANCED_USPS_SHIPPING_METHOD_NAME)) );
				} 
			}
        } else {
            if ( !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true) ) {
                   wp_die(sprintf(wp_kses_post('<strong>%2$s</strong> must requires below plugins: <ul><li><strong>WooCommerce</strong></li></ul> Return to <a href=%1$s>Plugins page</a>.', 'multi-vendor-shipping-addon'), esc_url(get_admin_url(null, 'plugins.php')), esc_html(ADVANCED_USPS_SHIPPING_METHOD_NAME)));
            }
        }
	}

}
