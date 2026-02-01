<?php
/**
 * Partial: Hero Section
 *
 * ACF Fields:
 * - hero_heading (text)
 * - hero_subheading (textarea)
 * - hero_image (image)
 * - hero_cta_text (text)
 * - hero_cta_link (link)
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get fields
$heading    = fmw_get_sub_field( 'hero_heading' );
$subheading = fmw_get_sub_field( 'hero_subheading' );
$image      = fmw_get_sub_field( 'hero_image' );
$cta_text   = fmw_get_sub_field( 'hero_cta_text' );
$cta_link   = fmw_get_sub_field( 'hero_cta_link' );
?>

<section class="hero-section">
    <div class="container mx-auto px-4">
        <?php if ( $heading ) : ?>
            <h1><?php echo esc_html( $heading ); ?></h1>
        <?php endif; ?>

        <?php if ( $subheading ) : ?>
            <p><?php echo esc_html( $subheading ); ?></p>
        <?php endif; ?>

        <?php if ( $cta_link && $cta_text ) : ?>
            <?php
            fmw_component(
                'button',
                array(
                    'text' => $cta_text,
                    'url'  => $cta_link['url'],
                    'target' => $cta_link['target'] ?? '_self',
                )
            );
            ?>
        <?php endif; ?>
    </div>

    <?php if ( $image ) : ?>
        <div class="hero-image">
            <?php fmw_image( $image, 'large' ); ?>
        </div>
    <?php endif; ?>
</section>
