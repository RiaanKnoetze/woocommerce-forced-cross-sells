<?php
/**
 * Plugin Name: WooCommerce Forced Cross-Sells
 * Plugin URI: https://woocommerce.com
 * Description: A plugin to ensure a specific list of products always show as cross-sells on the cart page, overriding any individual product cross-sell configurations.
 * Version: 0.1
 * Author: Riaan Knoetze
 * Author URI: https://woocommerce.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: woocommerce-forced-cross-sells
 * Domain Path: /languages
 *
 * WC requires at least: 7.4
 * WC tested up to: 8.7
 *
 * @package WooCommerce_Forced_Cross_Sells
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WCCS_PLUGIN_FILE.
if ( ! defined( 'WCFCS_PLUGIN_FILE' ) ) {
	define( 'WCFCS_PLUGIN_FILE', __FILE__ );
}

// Include the main classes responsible for core functionality.
if ( ! class_exists( 'WCFCS_Cross_Sells' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wcfcs-cross-sells.php';
}

if ( ! class_exists( 'WCCS_Admin_Settings' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wcfcs-admin-settings.php';
}

// Initialize the plugin.
add_action( 'plugins_loaded', 'woocommerce_forced_cross_sells_init' );

/**
 * Initialize the WooCommerce Forced Cross-Sells plugin.
 *
 * Loads the plugin's text domain and initializes plugin components.
 */
function woocommerce_forced_cross_sells_init() {
	// Load plugin text domain.
	load_plugin_textdomain( 'woocommerce-forced-cross-sells', false, basename( dirname( __FILE__ ) ) . '/languages' );

	// Initialize plugin parts.
	$wccs_cross_sells    = new WCFCS_Cross_Sells();
	$wccs_admin_settings = new WCFCS_Admin_Settings();
}
