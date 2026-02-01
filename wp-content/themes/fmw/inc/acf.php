<?php
/**
 * ACF Configuration
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ACF JSON save point
 */
function fmw_acf_json_save_point( $path ) {
    return FMW_DIR . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'fmw_acf_json_save_point' );

/**
 * ACF JSON load point
 */
function fmw_acf_json_load_point( $paths ) {
    unset( $paths[0] );
    $paths[] = FMW_DIR . '/acf-json';
    return $paths;
}
add_filter( 'acf/settings/load_json', 'fmw_acf_json_load_point' );

/**
 * Register ACF Options Page
 */
function fmw_acf_options_page() {
    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }

    acf_add_options_page(
        array(
            'page_title' => __( 'Site Settings', 'fmw' ),
            'menu_title' => __( 'Site Settings', 'fmw' ),
            'menu_slug'  => 'site-settings',
            'capability' => 'edit_posts',
            'redirect'   => false,
            'icon_url'   => 'dashicons-admin-settings',
            'position'   => 2,
        )
    );
}
add_action( 'acf/init', 'fmw_acf_options_page' );

/**
 * Get ACF field with fallback
 *
 * @param string $field_name  The field name.
 * @param mixed  $post_id     The post ID (optional).
 * @param mixed  $default     Default value if field is empty.
 * @return mixed
 */
function fmw_get_field( $field_name, $post_id = false, $default = '' ) {
    if ( ! function_exists( 'get_field' ) ) {
        return $default;
    }

    $value = get_field( $field_name, $post_id );
    return ! empty( $value ) ? $value : $default;
}

/**
 * Get ACF sub field with fallback
 *
 * @param string $field_name  The field name.
 * @param mixed  $default     Default value if field is empty.
 * @return mixed
 */
function fmw_get_sub_field( $field_name, $default = '' ) {
    if ( ! function_exists( 'get_sub_field' ) ) {
        return $default;
    }

    $value = get_sub_field( $field_name );
    return ! empty( $value ) ? $value : $default;
}

/**
 * Get ACF option field with fallback
 *
 * @param string $field_name  The field name.
 * @param mixed  $default     Default value if field is empty.
 * @return mixed
 */
function fmw_get_option( $field_name, $default = '' ) {
    return fmw_get_field( $field_name, 'option', $default );
}
