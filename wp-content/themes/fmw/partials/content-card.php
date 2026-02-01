<?php
/**
 * Partial: Content Card
 *
 * ACF Fields:
 * - content_card_heading (text)
 * - content_card_cards (repeater)
 *   - card_image (image)
 *   - card_title (text)
 *   - card_text (textarea)
 *   - card_link (link)
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get fields
$heading = fmw_get_sub_field( 'content_card_heading' );
$cards   = fmw_get_sub_field( 'content_card_cards' );
?>

<section class="content-card-section">
    <div class="container mx-auto px-4">
        <?php if ( $heading ) : ?>
            <h2><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>

        <?php if ( $cards ) : ?>
            <div class="cards-grid">
                <?php foreach ( $cards as $card ) : ?>
                    <?php
                    fmw_component(
                        'card',
                        array(
                            'image' => $card['card_image'] ?? null,
                            'title' => $card['card_title'] ?? '',
                            'text'  => $card['card_text'] ?? '',
                            'link'  => $card['card_link'] ?? null,
                        )
                    );
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
