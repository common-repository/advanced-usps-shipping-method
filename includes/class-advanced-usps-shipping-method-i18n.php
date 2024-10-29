<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.thedotstore.com/
 * @since      1.0.0
 *
 * @package    Advanced_Usps_Shipping_Method
 * @subpackage Advanced_Usps_Shipping_Method/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Advanced_Usps_Shipping_Method
 * @subpackage Advanced_Usps_Shipping_Method/includes
 * @author     theDotstore <support@thedotstore.com>
 */
class Advanced_Usps_Shipping_Method_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'advanced-usps-shipping-method',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
