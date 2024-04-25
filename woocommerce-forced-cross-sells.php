<?php
/**
 * Plugin Name: WooCommerce Forced Cross-Sells
 * Plugin URI: https://woocommerce.com
 * Description: A plugin to ensure a specific list of products always show as cross-sells on the cart page, overriding any individual product cross-sell configurations.
 * Version: 1.0
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

// Define WCFCS_PLUGIN_FILE.
if ( ! defined( 'WCFCS_PLUGIN_FILE' ) ) {
	define( 'WCFCS_PLUGIN_FILE', __FILE__ );
}

/**
 * Main plugin class for WooCommerce Forced Cross-Sells.
 *
 * This is the primary class responsible for initializing the plugin,
 * setting up hooks, and handling core plugin functionality.
 */
class WCFCS_Plugin {

	/**
	 * Constructor method, used to initialize the plugin.
	 */
	public function __construct() {
		// Include the main classes responsible for core functionality.
		include_once dirname( __FILE__ ) . '/includes/class-wcfcs-cross-sells.php';
		include_once dirname( __FILE__ ) . '/includes/class-wcfcs-admin-settings.php';

		// Initialize plugin components.
		$this->init_hooks();
	}

	/**
	 * Initializes WordPress hooks.
	 */
	private function init_hooks() {
		// Load plugin text domain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Initialize plugin parts.
		$wccs_cross_sells    = new WCFCS_Cross_Sells();
		$wccs_admin_settings = new WCFCS_Admin_Settings();

		// Add the settings link to the plugin list table.
		add_filter( 'plugin_action_links_' . plugin_basename( WCFCS_PLUGIN_FILE ), array( $this, 'add_action_links' ) );

		// Declare WC Feature compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatible' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_wc_block_compatibility' ) );
	}

	/**
	 * Load plugin text domain for translation.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-forced-cross-sells', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Adds a 'Settings' link to the plugin action links.
	 *
	 * @param array $links An array of plugin action links.
	 * @return array Updated array of plugin action links.
	 */
	public function add_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=wc-settings&tab=products&section=cross-sells' ) ) . '">' . esc_html__( 'Settings', 'woocommerce-forced-cross-sells' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Declare HPOS compatibility.
	 */
	public function declare_hpos_compatible() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}

	/**
	 * Declare Cart/Checkout Block compatibility.
	 *
	 * @since 2.3
	 */
	public function declare_wc_block_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
}

// Instantiate the plugin class.
new WCFCS_Plugin();
