<?php
/**
 * Forty Miles West Theme Functions
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Theme constants
define( 'FMW_VERSION', '1.0.0' );
define( 'FMW_DIR', get_template_directory() );
define( 'FMW_URI', get_template_directory_uri() );

// Include theme files
require_once FMW_DIR . '/inc/setup.php';
require_once FMW_DIR . '/inc/enqueue.php';
require_once FMW_DIR . '/inc/acf.php';
require_once FMW_DIR . '/inc/helpers.php';
require_once FMW_DIR . '/inc/form-handler.php';

// WooCommerce support (only if WooCommerce is active)
if ( class_exists( 'WooCommerce' ) ) {
    require_once FMW_DIR . '/inc/woocommerce.php';
    require_once FMW_DIR . '/inc/ajax-auth.php';
    require_once FMW_DIR . '/inc/ajax-cart.php';
    require_once FMW_DIR . '/inc/advanced-search.php';
}

// WP-CLI commands
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once FMW_DIR . '/inc/import-records.php';
    require_once FMW_DIR . '/inc/discogs-scraper.php';
}
