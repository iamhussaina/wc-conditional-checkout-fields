<?php
/**
 * Main loader for the Conditional Checkout Fields.
 *
 * This file defines constants, includes the core class, and initializes the functionality.
 *
 * @package Hussainas_Conditional_Checkout
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     MIT  GPL-2.0-or-later
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 */

// --- Direct access check ---
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// --- Define essential constants ---
define( 'HUSSAINAS_HCC_VERSION', '1.0.0' );
define( 'HUSSAINAS_HCC_PATH', plugin_dir_path( __FILE__ ) );
define( 'HUSSAINAS_HCC_URL', plugin_dir_url( __FILE__ ) );
define( 'HUSSAINAS_HCC_TEXT_DOMAIN', 'hussainas' );

// --- Include the core class ---
require_once HUSSAINAS_HCC_PATH . 'includes/class-hussainas-checkout-core.php';

/**
 * Initializes the core functionality.
 *
 * Loads the main class and starts the process.
 *
 * @since 1.0.0
 */
function hussainas_run_checkout_core() {
	new Hussainas_Checkout_Core();
}

// Load the functionality on the 'plugins_loaded' hook to ensure WooCommerce is available.
add_action( 'woocommerce_init', 'hussainas_run_checkout_core' );
