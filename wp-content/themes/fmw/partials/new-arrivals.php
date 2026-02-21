<?php
/**
 * Partial: New Arrivals Section
 *
 * Cream background, latest WooCommerce products in sliding card grid.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get latest products (8 for the slider, showing 4 at a time)
$products = array();
if ( function_exists( 'wc_get_products' ) ) {
    $products = wc_get_products( array(
        'limit'   => 12,
        'status'  => 'publish',
        'orderby' => 'date',
        'order'   => 'DESC',
    ) );
}

$total = count( $products );
?>

<section
    id="new-arrivals"
    class="bg-cream py-20 px-6 md:px-[60px]"
    x-data="{
        touchStartX: 0,
        slideBy(dir) {
            const el = this.$refs.arrivals;
            const cardWidth = el.querySelector('article').offsetWidth + 24;
            const cols = window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 3 : 2;
            el.scrollBy({ left: dir * cardWidth * cols, behavior: 'smooth' });
        }
    }"
>

    <!-- Section Header -->
    <div class="flex items-end justify-between mb-12">
        <div class="flex flex-col gap-2">
            <span class="font-mono text-xs font-bold text-accent tracking-wider-2">01</span>
            <h2 class="font-display text-[32px] md:text-[40px] font-bold text-dark tracking-[-1px]">NEW ARRIVALS</h2>
        </div>
        <div class="flex items-center gap-5 py-3">
            <a href="<?php echo esc_url( function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : '#' ); ?>" class="font-mono text-[11px] font-bold text-dark tracking-wider-2 uppercase hover:text-teal-dark transition-colors">VIEW ALL</a>
            <button
                @click="slideBy(-1)"
                class="w-[38px] h-[38px] rounded-full border border-dark/50 flex items-center justify-center hover:border-dark transition-colors"
                aria-label="Previous"
            >
                <?php fmw_icon( 'chevron-left', 'w-4 h-4 text-dark' ); ?>
            </button>
            <button
                @click="slideBy(1)"
                class="w-[38px] h-[38px] rounded-full border-[1.5px] border-dark flex items-center justify-center hover:border-dark transition-colors"
                aria-label="Next"
            >
                <?php fmw_icon( 'chevron-right', 'w-4 h-4 text-dark' ); ?>
            </button>
        </div>
    </div>

    <!-- Divider -->
    <div class="w-full h-px bg-dark/[0.125] mb-12"></div>

    <!-- Cards Slider -->
    <?php if ( ! empty( $products ) ) : ?>
        <div
            x-ref="arrivals"
            class="flex gap-6 overflow-x-auto scroll-smooth pb-4 snap-x snap-mandatory scrollbar-hide"
            @touchstart="touchStartX = $event.touches[0].clientX"
            @touchend="
                let diff = touchStartX - $event.changedTouches[0].clientX;
                if (Math.abs(diff) > 50) { slideBy(diff > 0 ? 1 : -1); }
            "
        >
            <?php foreach ( $products as $product ) :
                $prod_id    = $product->get_id();
                $prod_name  = $product->get_name();
                $prod_price = strip_tags( $product->get_price_html() );
                $prod_link  = $product->get_permalink();
                $prod_img   = $product->get_image_id();
                $prod_label = fmw_get_product_label( $prod_id );

                // Split name into artist/title
                $name_parts  = explode( ' - ', $prod_name, 2 );
                $artist      = strtoupper( $name_parts[0] ?? $prod_name );
                $title       = $name_parts[1] ?? $prod_name;

                // Get format from product attributes or fallback
                $format = $product->get_attribute( 'format' ) ?: '12" VINYL';

                // Cart URL
                $cart_url = $product->is_purchasable() && $product->is_in_stock()
                    ? esc_url( $prod_link . '?add-to-cart=' . $prod_id )
                    : '';
            ?>
                <article class="flex flex-col flex-none w-[calc(50%-12px)] sm:w-[calc(33.333%-16px)] lg:w-[calc(25%-18px)] snap-start">
                    <!-- Cover Image -->
                    <a href="<?php echo esc_url( $prod_link ); ?>" class="block w-full aspect-square overflow-hidden bg-card-dark no-transition">
                        <?php if ( $prod_img ) : ?>
                            <?php echo wp_get_attachment_image( $prod_img, 'medium', false, array( 'class' => 'w-full h-full object-cover' ) ); ?>
                        <?php else : ?>
                            <div class="w-full h-full bg-card-dark"></div>
                        <?php endif; ?>
                    </a>

                    <!-- Card Info -->
                    <div class="flex flex-col gap-2 pt-4">
                        <span class="font-mono text-xs font-extrabold text-teal-dark tracking-wider-3 uppercase"><?php echo esc_html( $artist ); ?></span>
                        <a href="<?php echo esc_url( $prod_link ); ?>" class="font-display text-base font-bold text-dark hover:text-teal-dark transition-colors no-transition"><?php echo esc_html( $title ); ?></a>
                        <div class="flex items-center gap-3 font-mono text-[11px] text-dark/50">
                            <?php if ( $prod_label ) : ?>
                                <span><?php echo esc_html( $prod_label ); ?></span>
                                <span>/</span>
                            <?php endif; ?>
                            <span><?php echo esc_html( $format ); ?></span>
                        </div>
                        <div class="flex items-center justify-between w-full mt-1">
                            <span class="font-mono text-sm font-bold text-dark"><?php echo esc_html( $prod_price ); ?></span>
                            <?php if ( $cart_url ) : ?>
                                <button type="button" onclick="fmwAddToCart(<?php echo esc_attr( $prod_id ); ?>, this)" class="font-mono text-[11px] font-bold text-dark tracking-wider-2 bg-transparent border-0 cursor-pointer p-0 hover:text-teal-dark transition-colors">[ + ]</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="font-mono text-sm text-dark/50">No products available yet.</p>
    <?php endif; ?>

</section>
