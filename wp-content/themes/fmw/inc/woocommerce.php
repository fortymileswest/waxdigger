<?php
/**
 * WooCommerce Support
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Remove default WooCommerce wrappers
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Add custom WooCommerce content wrappers
 */
function fmw_woocommerce_wrapper_before() {
    ?>
    <div class="woocommerce-wrapper">
        <div class="container mx-auto px-4 py-8">
    <?php
}
add_action( 'woocommerce_before_main_content', 'fmw_woocommerce_wrapper_before' );

function fmw_woocommerce_wrapper_after() {
    ?>
        </div>
    </div>
    <?php
}
add_action( 'woocommerce_after_main_content', 'fmw_woocommerce_wrapper_after' );

/**
 * Remove default WooCommerce sidebar
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Cart fragments - Update cart count via AJAX
 */
function fmw_cart_count_fragment( $fragments ) {
    ob_start();
    ?>
    <span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'fmw_cart_count_fragment' );

/**
 * Remove default WooCommerce styles
 */
function fmw_dequeue_woocommerce_styles( $enqueue_styles ) {
    // Keep only the general stylesheet, remove blocks
    unset( $enqueue_styles['woocommerce-general'] );
    unset( $enqueue_styles['woocommerce-layout'] );
    unset( $enqueue_styles['woocommerce-smallscreen'] );

    return $enqueue_styles;
}
// Remove default WooCommerce styles
add_filter( 'woocommerce_enqueue_styles', 'fmw_dequeue_woocommerce_styles' );

/**
 * Change number of products per row
 */
function fmw_products_per_row() {
    return 4;
}
add_filter( 'loop_shop_columns', 'fmw_products_per_row' );

/**
 * Change number of products displayed per page
 */
function fmw_products_per_page() {
    return 24;
}
add_filter( 'loop_shop_per_page', 'fmw_products_per_page', 20 );

/**
 * Remove product meta (SKU, categories, tags)
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

/**
 * Remove related products default output
 * We'll add our own custom related products section
 */
// remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/**
 * Modify breadcrumb defaults
 */
function fmw_woocommerce_breadcrumb_defaults( $defaults ) {
    $defaults['delimiter']   = ' <span class="breadcrumb-separator">/</span> ';
    $defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="Breadcrumb">';
    $defaults['wrap_after']  = '</nav>';

    return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'fmw_woocommerce_breadcrumb_defaults' );
