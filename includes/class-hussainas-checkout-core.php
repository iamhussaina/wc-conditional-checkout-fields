<?php
/**
 * Core class for Conditional Checkout Fields.
 *
 * Handles script enqueuing, field rendering, validation, and saving.
 *
 * @package Hussainas_Conditional_Checkout
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     GPL-2.0-or-later
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 */

// --- Direct access check ---
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Final class Hussainas_Checkout_Core.
 *
 * This class orchestrates the conditional checkout logic.
 */
final class Hussainas_Checkout_Core {

	/**
	 * The target shipping method ID that triggers the conditional field.
	 *
	 * Note: Find your method ID in WooCommerce > Settings > Shipping > Shipping Zones.
	 * Hover over a method, and you'll see the ID (e.g., 'local_pickup:1').
	 *
	 * @var string
	 */
	private $target_shipping_method = 'local_pickup:1';

	/**
	 * The ID of our custom field.
	 *
	 * @var string
	 */
	private $custom_field_key = 'hussainas_pickup_instructions';

	/**
	 * Constructor.
	 *
	 * Sets up all necessary action and filter hooks.
	 */
	public function __construct() {
		// Enqueue scripts and styles for the checkout page.
		add_action( 'wp_enqueue_scripts', array( $this, 'hussainas_enqueue_checkout_assets' ) );

		// Render the custom field on the checkout page.
		add_action( 'woocommerce_after_order_notes', array( $this, 'hussainas_render_custom_field' ) );

		// Validate the custom field during checkout processing.
		add_action( 'woocommerce_checkout_process', array( $this, 'hussainas_validate_custom_field' ) );

		// Save the custom field data to the order.
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'hussainas_save_custom_field_meta' ) );

		// Display the custom field data in the order admin view.
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'hussainas_display_custom_field_admin' ), 10, 1 );

		// Load text domain for translations.
		add_action( 'init', array( $this, 'hussainas_load_textdomain' ) );
	}

	/**
	 * Load text domain for translation.
	 */
	public function hussainas_load_textdomain() {
		load_plugin_textdomain(
			HUSSAINAS_HCC_TEXT_DOMAIN,
			false,
			dirname( plugin_basename( HUSSAINAS_HCC_PATH ) ) . '/languages/'
		);
	}

	/**
	 * Enqueue scripts and styles for the checkout page.
	 *
	 * Only loads assets on the checkout page to optimize performance.
	 */
	public function hussainas_enqueue_checkout_assets() {
		// Only enqueue on the checkout page.
		if ( ! is_checkout() ) {
			return;
		}

		// Enqueue the CSS file.
		wp_enqueue_style(
			'hussainas-checkout',
			HUSSAINAS_HCC_URL . 'assets/css/hussainas-checkout.css',
			array(),
			HUSSAINAS_HCC_VERSION
		);

		// Enqueue the JavaScript file.
		wp_enqueue_script(
			'hussainas-checkout',
			HUSSAINAS_HCC_URL . 'assets/js/hussainas-checkout.js',
			array( 'jquery', 'woocommerce' ), // Dependencies.
			HUSSAINAS_HCC_VERSION,
			true // Load in footer.
		);

		// Pass data from PHP to JavaScript.
		wp_localize_script(
			'hussainas-checkout',
			'hussainas_checkout_params',
			array(
				'target_shipping_method' => $this->target_shipping_method,
				'target_field_wrapper'   => '#hussainas_custom_field_wrapper',
			)
		);
	}

	/**
	 * Render the custom conditional field on the checkout page.
	 *
	 * This field will be hidden by default via CSS.
	 *
	 * @param array $checkout The checkout object.
	 */
	public function hussainas_render_custom_field( $checkout ) {
		echo '<div id="hussainas_custom_field_wrapper" class="hussainas-checkout-field-wrapper">';

		woocommerce_form_field(
			$this->custom_field_key,
			array(
				'type'        => 'textarea',
				'class'       => array( 'form-row-wide', 'hussainas-custom-field' ),
				'label'       => esc_html__( 'Pickup Instructions', HUSSAINAS_HCC_TEXT_DOMAIN ),
				'placeholder' => esc_html__( 'Please provide any specific pickup details.', HUSSAINAS_HCC_TEXT_DOMAIN ),
				'required'    => true, // This field will be conditionally required.
			),
			$checkout->get_value( $this->custom_field_key )
		);

		echo '</div>';
	}

	/**
	 * Validate the custom field when the order is placed.
	 *
	 * The field is only required if the target shipping method is selected.
	 */
	public function hussainas_validate_custom_field() {
		// Get the chosen shipping method.
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		$chosen_shipping_method  = ( ! empty( $chosen_shipping_methods ) ) ? $chosen_shipping_methods[0] : '';

		// Check if our target shipping method is selected.
		if ( $chosen_shipping_method === $this->target_shipping_method ) {
			// If the target method is selected, check if our custom field is empty.
			if ( empty( $_POST[ $this->custom_field_key ] ) ) {
				wc_add_notice(
					esc_html__( 'Please provide pickup instructions.', HUSSAINAS_HCC_TEXT_DOMAIN ),
					'error'
				);
			}
		}
	}

	/**
	 * Save the custom field value to the order meta.
	 *
	 * @param int $order_id The ID of the order being processed.
	 */
	public function hussainas_save_custom_field_meta( $order_id ) {
		if ( ! empty( $_POST[ $this->custom_field_key ] ) ) {
			$order = wc_get_order( $order_id );
			$order->update_meta_data(
				$this->custom_field_key,
				sanitize_textarea_field( $_POST[ $this->custom_field_key ] )
			);
		}
	}

	/**
	 * Display the custom field data in the order admin panel.
	 *
	 * @param WC_Order $order The order object.
	 */
	public function hussainas_display_custom_field_admin( $order ) {
		$pickup_instructions = $order->get_meta( $this->custom_field_key );

		if ( ! empty( $pickup_instructions ) ) {
			echo '<div class="order_data_column">';
			echo '<h3>' . esc_html__( 'Pickup Instructions', HUSSAINAS_HCC_TEXT_DOMAIN ) . '</h3>';
			echo '<p>' . esc_html( $pickup_instructions ) . '</p>';
			echo '</div>';
		}
	}

} // End of class Hussainas_Checkout_Core
