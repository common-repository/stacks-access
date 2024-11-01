<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://stacksaccess.com
 * @since             1.0.0
 * @package           Web3devs_Stacks_Access
 *
 * @wordpress-plugin
 * Plugin Name:       Stacks Access
 * Plugin URI:        https://stacksaccess.com/
 * Description:       Restrict accesss to pages for users holding specified Stacks-based tokens (NFTs, tokens, STX, etc.)
 * Version:           1.0.3
 * Author:            Web3devs
 * Author URI:        https://web3devs.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       web3devs-stacks-access
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WEB3DEVS_STACKS_ACCESS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-web3devs-stacks-access-activator.php
 */
function activate_web3devs_stacks_access() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-web3devs-stacks-access-activator.php';
	Web3devs_Stacks_Access_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-web3devs-stacks-access-deactivator.php
 */
function deactivate_web3devs_stacks_access() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-web3devs-stacks-access-deactivator.php';
	Web3devs_Stacks_Access_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_web3devs_stacks_access' );
register_deactivation_hook( __FILE__, 'deactivate_web3devs_stacks_access' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-web3devs-stacks-access.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_web3devs_stacks_access() {

	$plugin = new Web3devs_Stacks_Access();
	$plugin->run();

}
run_web3devs_stacks_access();
