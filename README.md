# Conditional Checkout Fields for WooCommerce

This enhancement provides the ability to add conditional fields to the WooCommerce checkout page. It is designed to be included in a WordPress theme and is not a standalone plugin.

This implementation demonstrates showing a "Pickup Instructions" text area only when the "Local Pickup" shipping method is selected.

---

## Features

* **Conditional Logic:** Only shows a custom field when a specific shipping method is selected.
* **OOP Structure:** Built using a clean, maintainable, and extensible OOP structure.
* **JavaScript Driven:** Uses JavaScript to dynamically show/hide the field without a page reload.
* **Validation:** The custom field is required only when it is visible.
* **Order Meta:** Saves the custom field data to the order meta.
* **Admin Display:** Displays the collected data in the order details screen in the admin dashboard.

---

## 🛠️ Installation & Usage

Follow these steps to integrate this functionality into your theme:

1.  **Copy Files:**
    Download or clone this repository and copy the entire `wc-conditional-checkout-fields` folder into your active WordPress theme's directory.

    *Example Path:* `wp-content/themes/your-active-theme/wc-conditional-checkout-fields/`

2.  **Include the Loader:**
    Open your theme's `functions.php` file and add the following line of PHP code at the end:

    ```php
    // Load the conditional checkout functionality
    require_once get_template_directory() . '/wc-conditional-checkout-fields/load.php';
    ```

That's it! The functionality is now active.

---

## 🔧 Configuration

### How to Change the Target Shipping Method

By default, the field appears for **Local Pickup** (`local_pickup:1`). You may need to change this.

1.  **Find Your Shipping Method ID:**
    * Go to your WordPress Admin Dashboard.
    * Navigate to **WooCommerce > Settings > Shipping > Shipping Zones**.
    * Select the shipping zone containing your desired method.
    * Hover your mouse over the shipping method (e.g., "Flat Rate", "Free Shipping"). The ID will be displayed in the URL or via browser inspection. It usually looks like `flat_rate:2`, `free_shipping:3`, etc.

2.  **Update the Code:**
    * Open the file: `conditional-checkout-fields/includes/class-hussainas-checkout-core.php`.
    * Find the following property (around line 26):

        ```php
        private $target_shipping_method = 'local_pickup:1';
        ```

    * Change `'local_pickup:1'` to your new shipping method ID.

### How to Change the Custom Field

All logic for the custom field (rendering, validation, saving) is located in the `class-hussainas-checkout-core.php` file. You can modify the following methods to change the field type, label, or validation rules:

* `hussainas_render_custom_field()`
* `hussainas_validate_custom_field()`
* `hussainas_save_custom_field_meta()`

---

## License

This project is licensed under the GPL v2.0 or later.
