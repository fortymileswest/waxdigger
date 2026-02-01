<?php
/**
 * Theme Setup
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme setup
 */
function fmw_setup() {
    // Make theme available for translation
    load_theme_textdomain( 'fmw', FMW_DIR . '/languages' );

    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails
    add_theme_support( 'post-thumbnails' );

    // Custom image sizes
    add_image_size( 'card', 600, 400, true );

    // Register navigation menus
    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'fmw' ),
            'footer'  => __( 'Footer Menu', 'fmw' ),
        )
    );

    // HTML5 support
    add_theme_support(
        'html5',
        array(
            'search-form',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Custom logo support
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 100,
            'width'       => 300,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );

    // WooCommerce support
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'fmw_setup' );

/**
 * Disable Gutenberg for all post types
 */
function fmw_disable_gutenberg( $use_block_editor, $post_type ) {
    return false;
}
add_filter( 'use_block_editor_for_post_type', 'fmw_disable_gutenberg', 10, 2 );

/**
 * Disable comments site-wide
 */
function fmw_disable_comments() {
    // Close comments on the front-end
    add_filter( 'comments_open', '__return_false', 20, 2 );
    add_filter( 'pings_open', '__return_false', 20, 2 );

    // Hide existing comments
    add_filter( 'comments_array', '__return_empty_array', 10, 2 );
}
add_action( 'init', 'fmw_disable_comments' );

/**
 * Remove comments from admin menu
 */
function fmw_remove_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'fmw_remove_comments_admin_menu' );

/**
 * Remove comments from admin bar
 */
function fmw_remove_comments_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'wp_before_admin_bar_render', 'fmw_remove_comments_admin_bar' );

/**
 * Clean up wp_head
 */
function fmw_cleanup_head() {
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'rest_output_link_wp_head' );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'fmw_cleanup_head' );

/**
 * Register Genre taxonomy for products
 */
function fmw_register_genre_taxonomy() {
    $labels = array(
        'name'              => _x( 'Genres', 'taxonomy general name', 'fmw' ),
        'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'fmw' ),
        'search_items'      => __( 'Search Genres', 'fmw' ),
        'all_items'         => __( 'All Genres', 'fmw' ),
        'parent_item'       => __( 'Parent Genre', 'fmw' ),
        'parent_item_colon' => __( 'Parent Genre:', 'fmw' ),
        'edit_item'         => __( 'Edit Genre', 'fmw' ),
        'update_item'       => __( 'Update Genre', 'fmw' ),
        'add_new_item'      => __( 'Add New Genre', 'fmw' ),
        'new_item_name'     => __( 'New Genre Name', 'fmw' ),
        'menu_name'         => __( 'Genres', 'fmw' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'genre', 'with_front' => false ),
    );

    register_taxonomy( 'genre', array( 'product' ), $args );
}
add_action( 'init', 'fmw_register_genre_taxonomy' );
