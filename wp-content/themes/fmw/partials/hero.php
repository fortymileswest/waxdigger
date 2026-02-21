<?php
/**
 * Partial: Hero Section
 *
 * Dark hero with background image, gradient overlay, featured release slider.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Build featured products array — SCF option first, then pad with latest products
$featured_products = array();

// Try SCF featured product
$featured_id = function_exists( 'get_field' ) ? get_field( 'featured_product', 'option' ) : null;
if ( $featured_id && function_exists( 'wc_get_product' ) ) {
    $product = wc_get_product( $featured_id );
    if ( $product ) {
        $featured_products[] = $product;
    }
}

// Pad with latest products (up to 5 total)
if ( function_exists( 'wc_get_products' ) ) {
    $exclude_ids = array_map( function( $p ) { return $p->get_id(); }, $featured_products );
    $latest = wc_get_products( array(
        'limit'   => 5 - count( $featured_products ),
        'status'  => 'publish',
        'orderby' => 'date',
        'order'   => 'DESC',
        'exclude' => $exclude_ids,
    ) );
    $featured_products = array_merge( $featured_products, $latest );
}

// Build slides data for Alpine.js
$slides = array();
foreach ( $featured_products as $product ) {
    $name   = $product->get_name();
    $parts  = explode( ' - ', $name, 2 );
    $artist = strtoupper( $parts[0] ?? $name );
    $title  = $parts[1] ?? $name;
    $label  = fmw_get_product_label( $product->get_id() );
    $img_id = $product->get_image_id();

    $slides[] = array(
        'id'     => $product->get_id(),
        'artist' => $artist,
        'title'  => $title,
        'label'  => $label,
        'price'  => strip_tags( $product->get_price_html() ),
        'link'   => $product->get_permalink(),
        'image'  => $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '',
        'cart'   => $product->is_purchasable() && $product->is_in_stock() ? '?add-to-cart=' . $product->get_id() : '',
    );
}

// Background image
$bg_url = get_template_directory_uri() . '/assets/images/hero-records-in-store.webp';
?>

<section class="relative overflow-hidden bg-dark min-h-[680px]" data-cursor-light>

    <!-- Background Image -->
    <div class="absolute inset-0 w-full h-full">
        <img src="<?php echo esc_url( $bg_url ); ?>" alt="" class="w-full h-full object-cover opacity-25" loading="eager">
    </div>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-dark/[0.93] via-dark/60 to-dark/20"></div>

    <!-- Decorative Grid Lines -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 bottom-0 left-[60px] w-px bg-cream/[0.03]"></div>
        <div class="absolute top-0 bottom-0 left-[25%] w-px bg-cream/[0.03]"></div>
        <div class="absolute top-0 bottom-0 left-1/2 w-px bg-cream/[0.03]"></div>
        <div class="absolute top-0 bottom-0 left-[75%] w-px bg-cream/[0.03]"></div>
        <div class="absolute top-0 bottom-0 right-[60px] w-px bg-cream/[0.03]"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative flex min-h-[680px]">

        <!-- Left Content -->
        <div class="flex flex-col justify-end flex-1 px-6 md:px-[60px] pt-[120px] pb-[80px] gap-6">

            <!-- Label -->
            <div class="flex items-center gap-2.5">
                <?php fmw_icon( 'compass', 'w-3.5 h-3.5 text-accent' ); ?>
                <span class="font-mono text-[11px] font-semibold text-accent tracking-wider-3 uppercase">EST. 2024 — UNDERGROUND MUSIC</span>
            </div>

            <!-- Headline -->
            <h1 class="font-display text-[72px] md:text-[120px] font-bold text-cream leading-[0.9] tracking-[-3px]">DIG<br>DEEPER.</h1>

            <!-- Subtitle -->
            <p class="font-mono text-sm text-cream/50 leading-[1.7] max-w-[520px]">Curated vinyl for heads who know. Rave, jungle,<br class="hidden md:block">hip hop &amp; the sounds that shaped the underground.</p>

            <!-- CTAs -->
            <div class="flex items-center gap-4 flex-wrap">
                <a href="<?php echo esc_url( function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : '#' ); ?>" class="flex items-center gap-2 bg-accent px-7 py-3.5 no-transition">
                    <span class="font-mono text-xs font-bold text-dark tracking-wider-2 uppercase">BROWSE CATALOGUE</span>
                    <?php fmw_icon( 'arrow-right', 'w-4 h-4 text-dark' ); ?>
                </a>
                <a href="#new-arrivals" class="flex items-center gap-2 border-2 border-cream px-7 py-3.5">
                    <span class="font-mono text-xs font-bold text-cream tracking-wider-2 uppercase">NEW ARRIVALS</span>
                </a>
            </div>
        </div>

        <!-- Featured Release Slider -->
        <?php if ( ! empty( $slides ) ) : ?>
            <div
                class="hidden lg:flex flex-col justify-center items-center w-[500px] px-[60px] gap-6"
                x-data='{
                    current: 0,
                    fading: false,
                    total: <?php echo count( $slides ); ?>,
                    slides: <?php echo wp_json_encode( $slides ); ?>,
                    touchStartX: 0,
                    fade(newIndex) {
                        if (this.fading || newIndex === this.current) return;
                        this.fading = true;
                        setTimeout(() => {
                            this.current = newIndex;
                            setTimeout(() => { this.fading = false; }, 50);
                        }, 300);
                    },
                    next() { this.fade((this.current + 1) % this.total); },
                    prev() { this.fade((this.current - 1 + this.total) % this.total); }
                }'
                @touchstart="touchStartX = $event.touches[0].clientX"
                @touchend="
                    let diff = touchStartX - $event.changedTouches[0].clientX;
                    if (Math.abs(diff) > 50) {
                        diff > 0 ? next() : prev();
                    }
                "
            >
                <!-- Cover Image -->
                <a :href="slides[current].link" class="block w-[320px] h-[320px] rounded overflow-hidden no-transition">
                    <img
                        :src="slides[current].image"
                        :alt="slides[current].artist + ' - ' + slides[current].title"
                        class="w-full h-full object-cover transition-opacity duration-300"
                        :class="fading ? 'opacity-0' : 'opacity-100'"
                        loading="eager"
                    >
                </a>

                <!-- Release Info -->
                <div
                    class="flex flex-col items-center gap-2 w-full text-center transition-opacity duration-300"
                    :class="fading ? 'opacity-0' : 'opacity-100'"
                >
                    <span class="font-mono text-[10px] font-semibold text-accent tracking-wider-3 uppercase">FEATURED RELEASE</span>
                    <span class="font-display text-[28px] font-bold text-cream" x-text="slides[current].artist"></span>
                    <span class="font-mono text-[13px] text-cream/50" x-text="slides[current].title"></span>
                    <span class="font-mono text-[11px] font-semibold text-accent tracking-[1px]">
                        <span x-text="slides[current].label"></span> / <span x-text="slides[current].price"></span>
                    </span>
                </div>

                <!-- Add to Cart -->
                <button
                    @click="slides[current].cart ? fmwAddToCart(slides[current].id, $el) : window.location.href = slides[current].link"
                    class="font-mono text-[10px] font-bold text-cream tracking-wider-3 uppercase border border-cream/50 px-5 py-2.5 hover:border-cream transition-colors bg-transparent cursor-pointer"
                    x-text="slides[current].cart ? '[ ADD TO CART ]' : '[ VIEW RELEASE ]'"
                ></button>

                <!-- Slider Nav -->
                <div class="flex items-center gap-4">
                    <button @click="prev()" class="w-11 h-11 rounded-full border border-cream/50 flex items-center justify-center hover:border-cream transition-colors" aria-label="Previous">
                        <?php fmw_icon( 'chevron-left', 'w-4 h-4 text-cream' ); ?>
                    </button>
                    <span class="font-mono text-[10px] text-cream/50 tracking-wider-2">
                        <span x-text="current + 1"></span> / <span x-text="total"></span>
                    </span>
                    <button @click="next()" class="w-11 h-11 rounded-full border-[1.5px] border-cream flex items-center justify-center hover:border-cream transition-colors" aria-label="Next">
                        <?php fmw_icon( 'chevron-right', 'w-4 h-4 text-cream' ); ?>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scroll Label -->
    <span class="absolute bottom-10 right-[60px] font-mono text-[10px] font-semibold text-cream/50 tracking-wider-3 uppercase -rotate-90 origin-bottom-right hidden lg:block">SCROLL</span>

</section>
