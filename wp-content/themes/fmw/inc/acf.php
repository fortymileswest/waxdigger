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
            'redirect'   => true,
            'icon_url'   => 'dashicons-admin-settings',
            'position'   => 2,
        )
    );

    acf_add_options_sub_page(
        array(
            'page_title'  => __( 'Homepage', 'fmw' ),
            'menu_title'  => __( 'Homepage', 'fmw' ),
            'menu_slug'   => 'site-settings-homepage',
            'parent_slug' => 'site-settings',
        )
    );
}
add_action( 'acf/init', 'fmw_acf_options_page' );

/**
 * Register Homepage field groups programmatically
 */
function fmw_register_homepage_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    // Featured Release — Hero slider
    acf_add_local_field_group( array(
        'key'      => 'group_homepage_featured',
        'title'    => 'Featured Release',
        'fields'   => array(
            array(
                'key'           => 'field_featured_product',
                'label'         => 'Featured Product',
                'name'          => 'featured_product',
                'type'          => 'post_object',
                'post_type'     => array( 'product' ),
                'return_format' => 'id',
                'ui'            => 1,
                'instructions'  => 'Select the product to feature in the hero slider.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => 'site-settings-homepage',
                ),
            ),
        ),
        'menu_order' => 0,
    ) );

    // Staff Picks
    acf_add_local_field_group( array(
        'key'      => 'group_homepage_staff_picks',
        'title'    => 'Staff Picks',
        'fields'   => array(
            array(
                'key'           => 'field_staff_pick_featured',
                'label'         => 'Featured Pick',
                'name'          => 'staff_pick_featured',
                'type'          => 'post_object',
                'post_type'     => array( 'product' ),
                'return_format' => 'id',
                'ui'            => 1,
                'instructions'  => 'The large featured pick shown on the left.',
            ),
            array(
                'key'          => 'field_staff_pick_description',
                'label'        => 'Featured Pick Description',
                'name'         => 'staff_pick_description',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => 'Short description for the featured pick.',
            ),
            array(
                'key'          => 'field_staff_picks_list',
                'label'        => 'Picks List',
                'name'         => 'staff_picks_list',
                'type'         => 'repeater',
                'min'          => 0,
                'max'          => 5,
                'layout'       => 'table',
                'button_label' => 'Add Pick',
                'sub_fields'   => array(
                    array(
                        'key'           => 'field_staff_pick_product',
                        'label'         => 'Product',
                        'name'          => 'product',
                        'type'          => 'post_object',
                        'post_type'     => array( 'product' ),
                        'return_format' => 'id',
                        'ui'            => 1,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => 'site-settings-homepage',
                ),
            ),
        ),
        'menu_order' => 1,
    ) );
}
add_action( 'acf/init', 'fmw_register_homepage_fields' );

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
        } elseif ( ! empty( $existing['modified'] ) ) {
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
