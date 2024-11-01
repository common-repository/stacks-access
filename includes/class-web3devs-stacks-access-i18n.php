<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://stacksaccess.com
 * @since      1.0.0
 *
 * @package    Web3devs_Stacks_Access
 * @subpackage Web3devs_Stacks_Access/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Web3devs_Stacks_Access
 * @subpackage Web3devs_Stacks_Access/includes
 * @author     Web3devs <wordpress@web3devs.com>
 */
class Web3devs_Stacks_Access_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'web3devs-stacks-access',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
