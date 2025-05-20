# LabKings Tooltips for WooCommerce Products

Dynamically displays product prices in tooltips on specified product-related buttons or links within your WordPress pages. This plugin helps improve user experience by providing quick price information without navigating to the product page.

**Plugin Version:** 1.3.0 (or current version)
**Author:** Michel Many
**Author URI:** https://michelmany.com
**Requires PHP:** 7.4 or higher
**Requires WordPress:** 5.0 or higher
**License:** GPL-2.0-or-later

## Description

The LabKings Tooltips plugin enhances your WooCommerce product listings or related content by adding interactive tooltips. When a user hovers over a designated button or link associated with a product, a tooltip appears, dynamically fetching and displaying the product's price using an AJAX request.

The pages where this functionality is active can be easily configured through a dedicated settings panel in the WordPress admin area, built using Carbon Fields.

## Features

*   **Dynamic Price Tooltips:** Shows product prices on mouseover for selected elements.
*   **AJAX Powered:** Fetches product prices asynchronously without page reloads.
*   **Customizable Page Activation:** Use the admin settings panel (Carbon Fields Association field) to select specific WordPress pages where tooltips should be enabled.
*   **SKU Detection:**
    *   Detects product SKUs from the text of a preceding table cell (`<td>`).
    *   For specific, predefined page IDs, it can detect SKUs from the text content of a `<button>` element within the link.
*   **Loading & Error States:** Displays "Loading price..." while fetching and "Click here" (or similar, configurable) if an error occurs.
*   **Easy Administration:** Simple settings page to manage tooltip-enabled pages.
*   **Modern Development:** Uses Composer for dependency management (Carbon Fields) and PSR-4 autoloading for cleaner code structure.

## Installation

### Using Composer (Recommended for Development)

1.  Clone the repository or download the source code into your `wp-content/plugins/` directory.
    ```bash
    git clone https://github.com/your-username/labkings-tooltips.git
    ```
2.  Navigate to the plugin directory:
    ```bash
    cd labkings-tooltips
    ```
3.  Install dependencies using Composer:
    ```bash
    composer install
    ```
4.  Activate the plugin through the 'Plugins' menu in WordPress.

### Manual Installation (Production Release ZIP)

1.  Download the latest release ZIP file from the [GitHub Releases page](https://github.com/your-username/labkings-tooltips/releases) (replace with your actual releases link).
2.  In your WordPress admin panel, go to **Plugins > Add New**.
3.  Click **Upload Plugin** at the top.
4.  Upload the ZIP file and click **Install Now**.
5.  Activate the plugin.

## Configuration

Once activated, a new admin menu item **"LabKings Tooltips"** will appear in your WordPress dashboard.

1.  Navigate to **LabKings Tooltips > LabKings Tooltips Settings**.
2.  In the "Enable Tooltips on Pages" section, use the association field to search for and select the pages where you want the tooltip functionality to be active.
3.  Click **Save Changes**.

The tooltips will now only appear on the selected pages for elements matching the predefined HTML structure (see "How it Works" below).

## How it Works

The plugin primarily targets links (`<a>` tags) within tables that are part of a `.wpb_text_column` or a `.wpb_text_column` immediately following a `.wpb_single_image` (common WPBakery Page Builder structures, but adaptable).

1.  **Body Class:** On the pages selected in the admin settings, the plugin adds a `apply-labkings-tooltips` class to the `<body>` tag.
2.  **CSS Styling:** Specific CSS rules target elements within `apply-labkings-tooltips` to style the tooltips.
3.  **JavaScript Logic (`scripts.js`):**
    *   Identifies target anchor tags (`<a>`) based on the CSS selectors defined in `anchorSelector`.
    *   On `mouseenter` over a target anchor:
        *   It determines the product SKU.
            *   By default, it looks for the text content of the `<td>` element immediately preceding the parent `<td>` of the hovered anchor.
            *   If the current page's ID is in a predefined list (`pagesSkuInTheButton`), it attempts to find a `<button>` inside the anchor and uses its text content as the SKU.
        *   It makes an AJAX GET request to a custom WordPress REST API endpoint (`/wp-json/labkings/v1/product/{sku}`) to fetch product details (specifically the price, formatted as HTML).
        *   The fetched price (or an error message) is displayed in a tooltip element that was prepended to the anchor tag.

## Customization & Extension

*   **CSS Selectors:** The primary CSS selectors for identifying target links are in `assets/js/scripts.js` (`anchorSelector`). You may need to adjust these if your HTML structure differs significantly.
*   **SKU Detection Logic:** The SKU detection logic in `scripts.js` can be modified to suit different HTML structures or data sources.
*   **REST API Endpoint:** The plugin assumes a REST API endpoint `/wp-json/labkings/v1/product/{sku}` is available to fetch product data. This endpoint needs to be implemented separately (e.g., in your theme or another plugin) and should return the formatted price HTML.
*   **Styling:** Tooltip styles can be customized in `assets/css/style.css`.

## REST API Endpoint Example (Conceptual)

You'll need to implement an endpoint that, given a SKU, returns the product's price. Here's a conceptual example of how such an endpoint might be registered in your theme's `functions.php` or a custom plugin:

```php
<?php
add_action( 'rest_api_init', function () {
    register_rest_route( 'labkings/v1', '/product/(?P<sku>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'get_product_details_by_sku_for_tooltip',
        'permission_callback' => '__return_true', // Adjust permissions as needed
    ) );
} );

function get_product_details_by_sku_for_tooltip( $data ) {
    $sku = $data['sku'];
    $product_id = wc_get_product_id_by_sku( $sku );

    if ( ! $product_id ) {
        return new WP_REST_Response( 'Product not found', 404 );
    }

    $product = wc_get_product( $product_id );

    if ( ! $product ) {
        return new WP_REST_Response( 'Product not found', 404 );
    }

    // Example: Return formatted price. Adjust HTML as needed for your tooltip.
    // Consider adding a class for specific styling.
    $price_html = '<span class="labkings-formatted-price">' . $product->get_price_html() . '</span>';

    return new WP_REST_Response( $price_html, 200 );
}
