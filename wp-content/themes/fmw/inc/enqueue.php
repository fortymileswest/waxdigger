<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue front-end assets
 */
function fmw_enqueue_assets() {
    // Main stylesheet (Tailwind output)
    wp_enqueue_style(
        'fmw-styles',
        FMW_URI . '/assets/css/output.css',
        array(),
        filemtime( FMW_DIR . '/assets/css/output.css' )
    );

    // Alpine.js
    wp_enqueue_script(
        'alpine',
        FMW_URI . '/assets/js/vendor/alpine.min.js',
        array(),
        filemtime( FMW_DIR . '/assets/js/vendor/alpine.min.js' ),
        true
    );

    // Add defer attribute to Alpine
    add_filter( 'script_loader_tag', function( $tag, $handle ) {
        if ( 'alpine' === $handle ) {
            return str_replace( ' src', ' defer src', $tag );
        }
        return $tag;
    }, 10, 2 );

    // GSAP
    wp_enqueue_script(
        'gsap',
        FMW_URI . '/assets/js/vendor/gsap.min.js',
        array(),
        filemtime( FMW_DIR . '/assets/js/vendor/gsap.min.js' ),
        true
    );

    // GSAP ScrollTrigger
    wp_enqueue_script(
        'gsap-scrolltrigger',
        FMW_URI . '/assets/js/vendor/ScrollTrigger.min.js',
        array( 'gsap' ),
        filemtime( FMW_DIR . '/assets/js/vendor/ScrollTrigger.min.js' ),
        true
    );

    // Main JavaScript
    wp_enqueue_script(
        'fmw-scripts',
        FMW_URI . '/assets/js/app.js',
        array( 'alpine', 'gsap', 'gsap-scrolltrigger' ),
        filemtime( FMW_DIR . '/assets/js/app.js' ),
        true
    );

    // Localise script with data
    wp_localize_script(
        'fmw-scripts',
        'fmw',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'fmw_nonce' ),
            'siteUrl' => home_url(),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'fmw_enqueue_assets' );

/**
 * Dequeue Gutenberg block styles
 */
function fmw_dequeue_block_styles() {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' );
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'fmw_dequeue_block_styles', 100 );

/**
 * Remove SVG and global styles
 */
function fmw_remove_svg_global_styles() {
    remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
    remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
}
add_action( 'init', 'fmw_remove_svg_global_styles' );

/**
 * Enqueue admin assets
 */
function fmw_enqueue_admin_assets() {
    // Add admin-specific styles if needed
}
add_action( 'admin_enqueue_scripts', 'fmw_enqueue_admin_assets' );
