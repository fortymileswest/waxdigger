<?php
/**
 * Component: Card
 *
 * @param array  $image  ACF image array or image ID
 * @param string $title  Card title
 * @param string $text   Card text/description
 * @param array  $link   ACF link array (optional)
 * @param string $class  Additional CSS classes
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Defaults
$image = $image ?? null;
$title = $title ?? '';
$text  = $text ?? '';
$link  = $link ?? null;
$class = $class ?? '';

// Build classes
$classes = array( 'card' );
if ( ! empty( $class ) ) {
    $classes[] = $class;
}

// Determine if card is clickable
$is_clickable = ! empty( $link['url'] );
$tag          = $is_clickable ? 'a' : 'div';
?>

<<?php echo $tag; ?>
    class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
    <?php if ( $is_clickable ) : ?>
        href="<?php echo esc_url( $link['url'] ); ?>"
        <?php if ( ! empty( $link['target'] ) && '_blank' === $link['target'] ) : ?>
            target="_blank"
            rel="noopener noreferrer"
        <?php endif; ?>
    <?php endif; ?>
>
    <?php if ( $image ) : ?>
        <div class="card-image">
            <?php fmw_image( $image, 'card' ); ?>
        </div>
    <?php endif; ?>

    <div class="card-content">
        <?php if ( $title ) : ?>
            <h3 class="card-title"><?php echo esc_html( $title ); ?></h3>
        <?php endif; ?>

        <?php if ( $text ) : ?>
            <p class="card-text"><?php echo esc_html( $text ); ?></p>
        <?php endif; ?>
    </div>
</<?php echo $tag; ?>>
