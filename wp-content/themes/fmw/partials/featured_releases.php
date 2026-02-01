<?php
/**
 * Partial: Featured Releases Slider
 *
 * Bleep-style carousel: gray background, large typography,
 * layered product images, prev/next navigation, dots.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get products - from args or ACF repeater
$products = isset( $products ) ? $products : array();

// If using ACF flexible content, get from repeater
if ( empty( $products ) && function_exists( 'have_rows' ) && have_rows( 'slides' ) ) {
    while ( have_rows( 'slides' ) ) {
        the_row();
        $products[] = array(
            'product_id' => get_sub_field( 'product' ),
            'eyebrow'    => get_sub_field( 'eyebrow' ),
            'details'    => get_sub_field( 'details' ),
        );
    }
}

// Fallback: get latest products if none specified
if ( empty( $products ) && function_exists( 'wc_get_products' ) ) {
    $wc_products = wc_get_products( array(
        'limit'   => 3,
        'status'  => 'publish',
        'orderby' => 'date',
        'order'   => 'DESC',
    ) );

    foreach ( $wc_products as $wc_product ) {
        $products[] = array(
            'product_id' => $wc_product->get_id(),
            'eyebrow'    => 'Featured Release:',
            'details'    => array(),
        );
    }
}

// Still empty? Show placeholder
if ( empty( $products ) ) {
    $products = array(
        array(
            'product_id' => null,
            'eyebrow'    => 'Record of the Month:',
            'details'    => array(
                array( 'detail' => 'Limited edition pressing' ),
                array( 'detail' => '180g vinyl' ),
            ),
        ),
    );
}

$total_slides = count( $products );
?>

<section
    class="featured-releases"
    x-data="{
        current: 0,
        total: <?php echo esc_attr( $total_slides ); ?>,
        ready: false
    }"
    x-init="$nextTick(() => { ready = true })"
    :class="{ 'is-ready': ready }"
>
    <div class="featured-releases-inner">

        <!-- Prev Arrow -->
        <button
            class="featured-releases-nav featured-releases-prev"
            @click="current = current > 0 ? current - 1 : total - 1"
            aria-label="Previous slide"
        >
            <?php fmw_icon( 'chevron-left', 'w-6 h-6' ); ?>
        </button>

        <!-- Slides Container -->
        <div class="featured-releases-slides">
            <?php foreach ( $products as $index => $slide ) :
                $product_id = $slide['product_id'] ?? null;
                $eyebrow    = $slide['eyebrow'] ?? 'Featured Release:';
                $details    = $slide['details'] ?? array();

                // Get product data if available
                $product      = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
                $title        = $product ? $product->get_name() : 'Album Title';
                $permalink    = $product ? $product->get_permalink() : '#';
                $image_id     = $product ? $product->get_image_id() : null;

                // ACF fields from product
                $label = $product_id ? get_field( 'label', $product_id ) : 'Label';

                // Product name already includes "Artist - Title" from import
                $display_title = $title;
            ?>
            <div
                class="featured-slide"
                x-show="current === <?php echo esc_attr( $index ); ?>"
                x-transition:enter="transition-opacity duration-500 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-300 ease-in absolute inset-0"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                <?php if ( $index !== 0 ) : ?>x-cloak<?php endif; ?>
            >
                <!-- Text -->
                <div class="featured-slide-content">
                    <div class="featured-slide-text">
                        <p class="featured-slide-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
                        <p class="featured-slide-title">
                            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $display_title ); ?></a>
                        </p>
                        <?php if ( $label ) : ?>
                            <p class="featured-slide-label">(<?php echo esc_html( $label ); ?>)</p>
                        <?php endif; ?>

                        <?php if ( ! empty( $details ) ) : ?>
                            <ul class="featured-slide-details">
                                <?php foreach ( $details as $detail ) : ?>
                                    <li><?php echo esc_html( $detail['detail'] ?? $detail ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Image -->
                <div class="featured-slide-images">
                    <?php if ( $image_id ) : ?>
                        <?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'featured-slide-cover' ) ); ?>
                    <?php else : ?>
                        <div class="featured-slide-cover placeholder-cover"></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Next Arrow -->
        <button
            class="featured-releases-nav featured-releases-next"
            @click="current = current < total - 1 ? current + 1 : 0"
            aria-label="Next slide"
        >
            <?php fmw_icon( 'chevron-right', 'w-6 h-6' ); ?>
        </button>

        <!-- Dots -->
        <?php if ( $total_slides > 1 ) : ?>
            <div class="featured-releases-dots">
                <?php for ( $i = 0; $i < $total_slides; $i++ ) : ?>
                    <button
                        @click="current = <?php echo esc_attr( $i ); ?>"
                        :class="{ 'active': current === <?php echo esc_attr( $i ); ?> }"
                        aria-label="Go to slide <?php echo esc_attr( $i + 1 ); ?>"
                    ></button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
