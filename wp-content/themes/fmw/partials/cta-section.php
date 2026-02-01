<?php
/**
 * Partial: CTA Section
 *
 * ACF Fields:
 * - cta_heading (text)
 * - cta_text (textarea)
 * - cta_button_text (text)
 * - cta_button_link (link)
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get fields
$heading     = fmw_get_sub_field( 'cta_heading' );
$text        = fmw_get_sub_field( 'cta_text' );
$button_text = fmw_get_sub_field( 'cta_button_text' );
$button_link = fmw_get_sub_field( 'cta_button_link' );
?>

<section class="cta-section">
    <div class="container mx-auto px-4">
        <?php if ( $heading ) : ?>
            <h2><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>

        <?php if ( $text ) : ?>
            <p><?php echo esc_html( $text ); ?></p>
        <?php endif; ?>

        <?php if ( $button_link && $button_text ) : ?>
            <?php
            fmw_component(
                'button',
                array(
                    'text'   => $button_text,
                    'url'    => $button_link['url'],
                    'target' => $button_link['target'] ?? '_self',
                )
            );
            ?>
        <?php endif; ?>
    </div>
</section>
