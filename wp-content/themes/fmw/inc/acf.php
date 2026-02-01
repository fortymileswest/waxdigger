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

/**
 * Auto-sync ACF JSON field groups on admin load
 *
 * Automatically imports any field groups from JSON that are
 * newer than the database version or don't exist yet.
 */
function fmw_acf_auto_sync() {
    // Only run in admin and if ACF is active
    if ( ! is_admin() || ! function_exists( 'acf_get_field_group' ) ) {
        return;
    }

    // Don't run during AJAX requests
    if ( wp_doing_ajax() ) {
        return;
    }

    // Get sync-able field groups
    $groups = acf_get_field_groups();
    $sync   = array();

    // Check JSON folder for field groups
    $json_path = FMW_DIR . '/acf-json';
    if ( ! is_dir( $json_path ) ) {
        return;
    }

    $files = glob( $json_path . '/*.json' );
    if ( empty( $files ) ) {
        return;
    }

    foreach ( $files as $file ) {
        $json = json_decode( file_get_contents( $file ), true );
        if ( ! is_array( $json ) || ! isset( $json['key'] ) ) {
            continue;
        }

        $key = $json['key'];

        // Check if this group exists in DB
        $existing = acf_get_field_group( $key );

        if ( ! $existing ) {
            // Group doesn't exist - needs import
            $sync[ $key ] = $json;
        } else {
            // Check if JSON is newer (compare modified times)
            $json_modified = filemtime( $file );
            $db_modified   = strtotime( $existing['modified'] );

            if ( $json_modified > $db_modified ) {
                $sync[ $key ] = $json;
            }
        }
    }

    // Import any groups that need syncing
    if ( ! empty( $sync ) ) {
        foreach ( $sync as $key => $field_group ) {
            // Import the field group
            $field_group['ID'] = 0;
            $result = acf_import_field_group( $field_group );
        }

        // Clear ACF cache
        if ( function_exists( 'acf_reset_cache' ) ) {
            acf_reset_cache();
        }
    }
}
add_action( 'admin_init', 'fmw_acf_auto_sync' );
