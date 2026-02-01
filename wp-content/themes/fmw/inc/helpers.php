<?php
/**
 * Helper Functions
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Output an SVG icon from the icons folder
 *
 * @param string $name   Icon name (filename without extension).
 * @param string $class  Additional CSS classes.
 * @param array  $attrs  Additional HTML attributes.
 */
function fmw_icon( $name, $class = '', $attrs = array() ) {
    echo fmw_get_icon( $name, $class, $attrs );
}

/**
 * Get an SVG icon from the icons folder
 *
 * @param string $name   Icon name (filename without extension).
 * @param string $class  Additional CSS classes.
 * @param array  $attrs  Additional HTML attributes.
 * @return string
 */
function fmw_get_icon( $name, $class = '', $attrs = array() ) {
    $path = FMW_DIR . '/assets/icons/' . sanitize_file_name( $name ) . '.svg';

    if ( ! file_exists( $path ) ) {
        return '';
    }

    $svg = file_get_contents( $path );

    // Add class if provided
    if ( ! empty( $class ) ) {
        $svg = preg_replace( '/<svg/', '<svg class="' . esc_attr( $class ) . '"', $svg, 1 );
    }

    // Add additional attributes
    if ( ! empty( $attrs ) ) {
        $attr_string = '';
        foreach ( $attrs as $key => $value ) {
            $attr_string .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
        }
        $svg = preg_replace( '/<svg/', '<svg' . $attr_string, $svg, 1 );
    }

    return $svg;
}

/**
 * Output a responsive image with proper srcset
 *
 * @param int|array $image    Image ID or ACF image array.
 * @param string    $size     Image size.
 * @param string    $class    Additional CSS classes.
 * @param array     $attrs    Additional HTML attributes.
 */
function fmw_image( $image, $size = 'large', $class = '', $attrs = array() ) {
    echo fmw_get_image( $image, $size, $class, $attrs );
}

/**
 * Get a responsive image with proper srcset
 *
 * @param int|array $image    Image ID or ACF image array.
 * @param string    $size     Image size.
 * @param string    $class    Additional CSS classes.
 * @param array     $attrs    Additional HTML attributes.
 * @return string
 */
function fmw_get_image( $image, $size = 'large', $class = '', $attrs = array() ) {
    // Get image ID
    $image_id = is_array( $image ) ? $image['ID'] : $image;

    if ( empty( $image_id ) ) {
        return '';
    }

    // Build attributes array
    $default_attrs = array(
        'class'   => $class,
        'loading' => 'lazy',
    );

    $attrs = array_merge( $default_attrs, $attrs );

    return wp_get_attachment_image( $image_id, $size, false, $attrs );
}

/**
 * Get image URL from ID or ACF array
 *
 * @param int|array $image  Image ID or ACF image array.
 * @param string    $size   Image size.
 * @return string
 */
function fmw_get_image_url( $image, $size = 'large' ) {
    $image_id = is_array( $image ) ? $image['ID'] : $image;

    if ( empty( $image_id ) ) {
        return '';
    }

    $src = wp_get_attachment_image_src( $image_id, $size );
    return $src ? $src[0] : '';
}

/**
 * Truncate text to a specified length
 *
 * @param string $text    The text to truncate.
 * @param int    $length  Maximum length.
 * @param string $suffix  Suffix to append if truncated.
 * @return string
 */
function fmw_truncate( $text, $length = 100, $suffix = '...' ) {
    $text = wp_strip_all_tags( $text );

    if ( strlen( $text ) <= $length ) {
        return $text;
    }

    return rtrim( substr( $text, 0, $length ) ) . $suffix;
}

/**
 * Get theme asset URL with cache busting
 *
 * @param string $path  Path relative to theme assets folder.
 * @return string
 */
function fmw_asset( $path ) {
    $file_path = FMW_DIR . '/assets/' . ltrim( $path, '/' );

    if ( file_exists( $file_path ) ) {
        return FMW_URI . '/assets/' . ltrim( $path, '/' ) . '?v=' . filemtime( $file_path );
    }

    return FMW_URI . '/assets/' . ltrim( $path, '/' );
}

/**
 * Include a component with passed data
 *
 * @param string $name  Component name.
 * @param array  $args  Arguments to pass to the component.
 */
function fmw_component( $name, $args = array() ) {
    $path = FMW_DIR . '/components/' . sanitize_file_name( $name ) . '.php';

    if ( file_exists( $path ) ) {
        extract( $args );
        include $path;
    }
}

/**
 * Include a partial with passed data
 *
 * @param string $name  Partial name.
 * @param array  $args  Arguments to pass to the partial.
 */
function fmw_partial( $name, $args = array() ) {
    $path = FMW_DIR . '/partials/' . sanitize_file_name( $name ) . '.php';

    if ( file_exists( $path ) ) {
        extract( $args );
        include $path;
    }
}
