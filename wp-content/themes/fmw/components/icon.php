<?php
/**
 * Component: Icon
 *
 * Wrapper component for the fmw_icon helper function.
 *
 * @param string $name   Icon name (filename without extension)
 * @param string $class  Additional CSS classes
 * @param string $size   Icon size (sm, md, lg)
 * @param array  $attrs  Additional HTML attributes
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Defaults
$name  = $name ?? '';
$class = $class ?? '';
$size  = $size ?? 'md';
$attrs = $attrs ?? array();

if ( empty( $name ) ) {
    return;
}

// Build classes
$classes = array( 'icon', 'icon-' . $size );
if ( ! empty( $class ) ) {
    $classes[] = $class;
}

// Output icon
fmw_icon( $name, implode( ' ', $classes ), $attrs );
