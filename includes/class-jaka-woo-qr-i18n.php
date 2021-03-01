<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/JaKaspar
 * @since      1.0.0
 *
 * @package    Jaka_Woo_Qr
 * @subpackage Jaka_Woo_Qr/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Jaka_Woo_Qr
 * @subpackage Jaka_Woo_Qr/includes
 * @author     JaKaspar <j.kaspar.gm@gmail.com>
 */
class Jaka_Woo_Qr_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'jaka-woo-qr',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
