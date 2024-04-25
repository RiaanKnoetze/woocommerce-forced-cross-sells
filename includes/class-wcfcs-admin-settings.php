<?php
/**
 * Admin settings for custom WooCommerce Cross-Sells.
 *
 * @package WooCommerce_Forced_Cross_Sells
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WCFCS_Admin_Settings
 *
 * Adds custom settings for managing cross-sell products in WooCommerce.
 */
class WCFCS_Admin_Settings {
	/**
	 * Constructor for the WCFCS_Admin_Settings class.
	 *
	 * Sets up hooks for adding settings and scripts.
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_settings_section' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_select_scripts' ) );
	}

	/**
	 * Adds a new section to the WooCommerce Products settings tab.
	 *
	 * @param array $sections Array of existing sections.
	 * @return array Modified array of sections.
	 */
	public function add_settings_section( $sections ) {
		$sections['cross_sells'] = __( 'Cross-sells', 'woocommerce-forced-cross-sells' );
		return $sections;
	}

	/**
	 * Adds settings for the Cross-sells section.
	 *
	 * @param array  $settings        Array of existing settings for the current section.
	 * @param string $current_section The current section identifier.
	 * @return array Modified array of settings for the current section.
	 */
	public function add_settings( $settings, $current_section ) {
		if ( 'cross_sells' === $current_section ) {
			$forced_settings = array(
				array(
					'title' => __( 'Cross-sell Products', 'woocommerce-forced-cross-sells' ),
					'desc'  => __( 'Choose products to always display as cross-sells in the cart.', 'woocommerce-forced-cross-sells' ),
					'type'  => 'title',
					'id'    => 'wcfcs_cross_sells_options',
				),
				array(
					'title'             => __( 'Cross-sell Products', 'woocommerce-forced-cross-sells' ),
					'desc'              => __( 'Select products', 'woocommerce-forced-cross-sells' ),
					'id'                => 'wcfcs_forced_cross_sell_ids',
					'type'              => 'wcfcs_product_selector',
					'default'           => '',
					'desc_tip'          => false,
					'class'             => 'wc-enhanced-select',
					'css'               => 'min-width:300px;',
					'forced_attributes' => array(
						'data-placeholder' => __( 'Search for a product&hellip;', 'woocommerce-forced-cross-sells' ),
						'data-multiple'    => true,
						'data-action'      => 'woocommerce_json_search_products_and_variations',
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'wcfcs_cross_sells_options',
				),
			);
			return $forced_settings;
		}

		return $settings;
	}

	/**
	 * Enqueues scripts and styles for the selectWoo functionality.
	 *
	 * @param string $hook_suffix The current page hook suffix.
	 */
	public function enqueue_select_scripts( $hook_suffix ) {
		if ( 'woocommerce_page_wc-settings' !== $hook_suffix ) {
			return;
		}

		// Add nonce verification.
		if ( ! isset( $_GET['section'] ) || ! isset( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		// Sanitize the nonce field.
		$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'woocommerce-settings' ) ) {
			return;
		}

		$section = sanitize_text_field( wp_unslash( $_GET['section'] ) );
		if ( 'cross_sells' === $section ) {
			wp_enqueue_script( 'selectWoo' );
			wp_enqueue_style( 'select2' );
			wc_enqueue_js(
				"
                jQuery( function( $ ) {
                    function initSelect2() {
                        $( ':input.wc-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
                            var select2_args = {
                                minimumInputLength: 1,
                                allowClear:  true,
                                placeholder: $( this ).data( 'placeholder' )
                            };
                            $( this ).selectWoo( select2_args ).addClass( 'enhanced' );
                        });
                    }
                    initSelect2();
                });
            "
			);
		}
	}
}

new WCFCS_Admin_Settings();

/**
 * Renders a custom product selector field.
 *
 * @param array $value The field value parameters.
 */
function wcfcs_product_selector_field( $value ) {
	$product_ids = (array) get_option( $value['id'], array() );
	?>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			<?php echo wp_kses_post( wc_help_tip( $value['desc'] ) ); ?>
		</th>
		<td class="forminp">
			<select class="wc-product-search" multiple="multiple" style="<?php echo esc_attr( $value['css'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>[]" data-placeholder="<?php echo esc_attr( $value['forced_attributes']['data-placeholder'] ); ?>" data-action="<?php echo esc_attr( $value['forced_attributes']['data-action'] ); ?>">
				<?php
				foreach ( $product_ids as $product_id ) {
					$product = wc_get_product( $product_id );
					if ( is_object( $product ) ) {
						echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $product->get_formatted_name() ) . '</option>';
					}
				}
				?>
			</select>
		</td>
	</tr>
	<?php
}
add_filter( 'woocommerce_admin_field_wcfcs_product_selector', 'wcfcs_product_selector_field', 10, 1 );

/**
 * Saves the settings for the Cross-sells section.
 */
function wcfcs_save_settings() {
	// Check if our nonce is set and verify it.
	if ( ! isset( $_POST['_wpnonce'] ) ) {
		wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce-forced-cross-sells' ) );
	}

	// Sanitize the nonce field.
	$nonce = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'woocommerce-settings' ) ) {
		wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce-forced-cross-sells' ) );
	}

	// Check if the cross-sell IDs are set. If not, save an empty array.
	if ( isset( $_POST['wcfcs_forced_cross_sell_ids'] ) ) {
		$forced_cross_sell_ids = array_map( 'intval', (array) wp_unslash( $_POST['wcfcs_forced_cross_sell_ids'] ) );
	} else {
		// No products have been selected, so we save an empty array.
		$forced_cross_sell_ids = array();
	}
	update_option( 'wcfcs_forced_cross_sell_ids', $forced_cross_sell_ids );
}
add_action( 'woocommerce_update_options_products_cross_sells', 'wcfcs_save_settings' );
