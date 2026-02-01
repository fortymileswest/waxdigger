<?php
/**
 * Component: Button
 *
 * @param string $text    Button text
 * @param string $url     Button URL
 * @param string $target  Link target (_self, _blank)
 * @param string $type    Button type (primary, secondary)
 * @param string $class   Additional CSS classes
 * @param array  $attrs   Additional HTML attributes
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Defaults
$text   = $text ?? '';
$url    = $url ?? '#';
$target = $target ?? '_self';
$type   = $type ?? 'primary';
$class  = $class ?? '';
$attrs  = $attrs ?? array();

if ( empty( $text ) ) {
    return;
}

// Build classes
$classes = array( 'btn', 'btn-' . $type );
if ( ! empty( $class ) ) {
    $classes[] = $class;
}

// Build attributes string
$attr_string = '';
foreach ( $attrs as $key => $value ) {
    $attr_string .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
}
?>

<a
    href="<?php echo esc_url( $url ); ?>"
    class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
    <?php if ( '_blank' === $target ) : ?>
        target="_blank"
        rel="noopener noreferrer"
    <?php endif; ?>
    <?php echo $attr_string; ?>
>
    <?php echo esc_html( $text ); ?>
</a>
