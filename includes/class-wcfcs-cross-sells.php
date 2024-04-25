<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WCFCS_Cross_Sells {
	public function __construct() {
		add_filter( 'woocommerce_cart_crosssell_ids', array( $this, 'custom_filter_woocommerce_cart_cross_sells' ), 10, 2 );
	}

	public function custom_filter_woocommerce_cart_cross_sells( $cross_sell_ids, $cart ) {
		// Retrieve custom cross-sell product IDs from the plugin settings.
		$custom_cross_sell_ids = get_option( 'wcfcs_custom_cross_sell_ids', array() );
		return $custom_cross_sell_ids;
	}
}
