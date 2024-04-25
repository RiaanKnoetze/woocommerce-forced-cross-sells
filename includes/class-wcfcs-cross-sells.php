<?php
/**
 * Handles the cross-sell functionality in WooCommerce.
 *
 * @package WooCommerce_Forced_Cross_Sells
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WCFCS_Cross_Sells
 *
 * Manages the custom cross-sell products functionality.
 */
class WCFCS_Cross_Sells {
	/**
	 * Constructor for the WCFCS_Cross_Sells class.
	 *
	 * Sets up the filter hook for customizing cross-sell products.
	 */
	public function __construct() {
		add_filter( 'woocommerce_cart_crosssell_ids', array( $this, 'wcfcs_filter_crosssell_ids' ), 10, 2 );
	}

	/**
	 * Custom filter for WooCommerce cart cross-sell IDs.
	 *
	 * Overrides the default cross-sell products with custom IDs defined in plugin settings.
	 *
	 * @param array   $cross_sell_ids The current array of cross-sell product IDs.
	 * @param WC_Cart $cart The WooCommerce cart object.
	 * @return array The modified array of cross-sell product IDs.
	 */
	public function wcfcs_filter_crosssell_ids( $cross_sell_ids, $cart ) {
		// Retrieve custom cross-sell product IDs from the plugin settings.
		$custom_cross_sell_ids = get_option( 'wcfcs_forced_cross_sell_ids' );

		// Guard clause: Only proceed if $custom_cross_sell_ids exists and is not empty.
		if ( empty( $custom_cross_sell_ids ) || ! is_array( $custom_cross_sell_ids ) ) {
			return $cross_sell_ids;
		}

		return $custom_cross_sell_ids;
	}
}
