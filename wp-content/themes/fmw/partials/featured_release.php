<?php
/**
 * Partial: Featured Release
 *
 * Bleep-style hero: gray background, large typography,
 * layered product images, carousel navigation.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get fields
$product_id = isset( $product_id ) ? $product_id : fmw_get_sub_field( 'product' );
$eyebrow    = isset( $eyebrow ) ? $eyebrow : fmw_get_sub_field( 'eyebrow', 'Record of the Month:' );
$details    = isset( $details ) ? $details : fmw_get_sub_field( 'details' );

// Placeholder if no product
if ( ! $product_id || ! function_exists( 'wc_get_product' ) ) {
    ?>
    <section class="featured-release">
        <div class="featured-release-inner">
            <div class="featured-release-content">
                <button class="featured-release-nav featured-release-prev" aria-label="Previous">
                    <?php fmw_icon( 'chevron-left', 'w-5 h-5' ); ?>
                </button>

                <div class="featured-release-text">
                    <h2 class="featured-release-eyebrow">Record of the Month:</h2>
                    <p class="featured-release-title">Artist Name - Album Title</p>
                    <p class="featured-release-label">(Label)</p>
                    <ul class="featured-release-details">
                        <li>Limited edition pressing</li>
                        <li>180g vinyl</li>
                    </ul>
                    <div class="featured-release-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>

            <div class="featured-release-images">
                <div class="featured-release-image-stack">
                    <div class="featured-release-cover">
                        <div class="placeholder-cover"></div>
                    </div>
                    <div class="featured-release-vinyl">
                        <div class="placeholder-vinyl"></div>
                    </div>
                    <div class="featured-release-extra">
                        <div class="placeholder-extra"></div>
                    </div>
                </div>

                <button class="featured-release-nav featured-release-next" aria-label="Next">
                    <?php fmw_icon( 'chevron-right', 'w-5 h-5' ); ?>
                </button>
            </div>
        </div>
    </section>
    <?php
    return;
}

// Get product
$product = wc_get_product( $product_id );

if ( ! $product ) {
    return;
}

// Product data
$title      = $product->get_name();
$permalink  = $product->get_permalink();
$image_id   = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();

// ACF fields from product
$artist = get_field( 'artist', $product_id );
$label  = get_field( 'label', $product_id );

// Display title
$display_title = $artist ? $artist . ' - ' . $title : $title;
?>

<section class="featured-release">
    <div class="featured-release-inner">
        <!-- Left: Text content with prev arrow -->
        <div class="featured-release-content">
            <button class="featured-release-nav featured-release-prev" aria-label="Previous">
                <?php fmw_icon( 'chevron-left', 'w-5 h-5' ); ?>
            </button>

            <div class="featured-release-text">
                <?php if ( $eyebrow ) : ?>
                    <h2 class="featured-release-eyebrow"><?php echo esc_html( $eyebrow ); ?></h2>
                <?php endif; ?>

                <p class="featured-release-title">
                    <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $display_title ); ?></a>
                </p>

                <?php if ( $label ) : ?>
                    <p class="featured-release-label">(<?php echo esc_html( $label ); ?>)</p>
                <?php endif; ?>

                <?php if ( $details && is_array( $details ) ) : ?>
                    <ul class="featured-release-details">
                        <?php foreach ( $details as $detail ) : ?>
                            <li><?php echo esc_html( $detail['detail'] ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="featured-release-dots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>
        </div>

        <!-- Right: Layered images with next arrow -->
        <div class="featured-release-images">
            <div class="featured-release-image-stack">
                <?php if ( $image_id ) : ?>
                    <div class="featured-release-cover">
                        <?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'cover-image' ) ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $gallery_ids[0] ) ) : ?>
                    <div class="featured-release-vinyl">
                        <?php echo wp_get_attachment_image( $gallery_ids[0], 'medium', false, array( 'class' => 'vinyl-image' ) ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $gallery_ids[1] ) ) : ?>
                    <div class="featured-release-extra">
                        <?php echo wp_get_attachment_image( $gallery_ids[1], 'medium', false, array( 'class' => 'extra-image' ) ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <button class="featured-release-nav featured-release-next" aria-label="Next">
                <?php fmw_icon( 'chevron-right', 'w-5 h-5' ); ?>
            </button>
        </div>
    </div>
</section>
