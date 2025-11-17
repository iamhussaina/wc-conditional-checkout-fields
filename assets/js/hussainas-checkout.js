/*
 * Hussainas Conditional Checkout Field Handler
 *
 * This script handles the visibility of a custom checkout field based on
 * the selected shipping method.
 *
 * @package Hussainas_Conditional_Checkout
 */

(function($) {
    'use strict';

    /**
     * Main handler for the checkout logic.
     */
    var HussainasCheckoutHandler = {

        /**
         * Initialize the script.
         *
         * Binds events and runs the initial check.
         */
        init: function() {
            // Bind the update event. This is triggered by WooCommerce on checkout updates.
            $(document.body).on('updated_checkout', this.toggleFieldVisibility);

            // Run on page load as well.
            this.toggleFieldVisibility();
        },

        /**
         * Check the selected shipping method and toggle the custom field.
         */
        toggleFieldVisibility: function() {
            // Get the value of the selected shipping method.
            // WooCommerce shipping methods are radio buttons.
            var selectedShippingMethod = $('input[name^="shipping_method"]:checked').val();

            // Get the target elements from the localized parameters.
            var targetMethod = hussainas_checkout_params.target_shipping_method;
            var $targetWrapper = $(hussainas_checkout_params.target_field_wrapper);

            if (!$targetWrapper.length) {
                // Safety check if the wrapper doesn't exist.
                return;
            }

            // Compare the selected method with our target method.
            if (selectedShippingMethod === targetMethod) {
                // Show the field.
                $targetWrapper.slideDown();
            } else {
                // Hide the field.
                $targetWrapper.slideUp();
            }
        }
    };

    // Initialize the handler when the document is ready.
    $(function() {
        HussainasCheckoutHandler.init();
    });

})(jQuery);
