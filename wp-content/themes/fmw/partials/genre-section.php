<?php
/**
 * Partial: Genre Section
 *
 * Dark background with genre cards from taxonomy in a slider.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get all genres with product counts
$genres = get_terms( array(
    'taxonomy'   => 'genre',
    'hide_empty' => false,
    'orderby'    => 'count',
    'order'      => 'DESC',
) );

if ( is_wp_error( $genres ) ) {
    $genres = array();
}

$genre_count = count( $genres );
$show_arrows = $genre_count > 4;
?>

<section
    class="bg-dark py-20 px-6 md:px-[60px]"
    data-cursor-light
    x-data="{
        touchStartX: 0,
        slideBy(dir) {
            const el = this.$refs.genreSlider;
            const card = el.querySelector('a');
            if (!card) return;
            const cardWidth = card.offsetWidth + 2;
            el.scrollBy({ left: dir * cardWidth * 4, behavior: 'smooth' });
        }
    }"
>

    <!-- Section Header -->
    <div class="flex items-end justify-between mb-12">
        <div class="flex flex-col gap-2">
            <span class="font-mono text-xs font-bold text-accent tracking-wider-2">02</span>
            <h2 class="font-display text-[32px] md:text-[40px] font-bold text-cream tracking-[-1px]">DIG BY GENRE</h2>
        </div>
        <div class="flex items-center gap-5 py-3">
            <span class="font-mono text-xs text-cream/50 tracking-[1px]">Find your frequency</span>
            <?php if ( $show_arrows ) : ?>
                <button
                    @click="slideBy(-1)"
                    class="w-[38px] h-[38px] rounded-full border border-cream/50 flex items-center justify-center hover:border-cream transition-colors"
                    aria-label="Previous"
                >
                    <?php fmw_icon( 'chevron-left', 'w-4 h-4 text-cream' ); ?>
                </button>
                <button
                    @click="slideBy(1)"
                    class="w-[38px] h-[38px] rounded-full border-[1.5px] border-cream flex items-center justify-center hover:border-cream transition-colors"
                    aria-label="Next"
                >
                    <?php fmw_icon( 'chevron-right', 'w-4 h-4 text-cream' ); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Divider -->
    <div class="w-full h-px bg-cream/[0.125] mb-12"></div>

    <!-- Genre Slider -->
    <?php if ( ! empty( $genres ) ) : ?>
        <div
            x-ref="genreSlider"
            class="flex gap-0.5 overflow-x-auto scroll-smooth scrollbar-hide snap-x snap-mandatory"
            @touchstart="touchStartX = $event.touches[0].clientX"
            @touchend="
                let diff = touchStartX - $event.changedTouches[0].clientX;
                if (Math.abs(diff) > 50) { slideBy(diff > 0 ? 1 : -1); }
            "
        >
            <?php foreach ( $genres as $genre ) :
                $genre_link  = get_term_link( $genre );
                $count       = $genre->count;
                $genre_img   = get_term_meta( $genre->term_id, 'thumbnail_id', true );
                $genre_img_url = $genre_img ? wp_get_attachment_image_url( $genre_img, 'large' ) : '';

                // Fallback to theme genre images
                if ( ! $genre_img_url ) {
                    $fallback = get_template_directory() . '/assets/images/genre-' . $genre->slug . '.webp';
                    if ( file_exists( $fallback ) ) {
                        $genre_img_url = get_template_directory_uri() . '/assets/images/genre-' . $genre->slug . '.webp';
                    }
                }
            ?>
                <a href="<?php echo esc_url( $genre_link ); ?>" class="group relative flex-none w-[calc(50%-1px)] sm:w-[calc(33.333%-1px)] lg:w-[calc(25%-1px)] h-[400px] overflow-hidden border border-cream/[0.125] block no-transition snap-start">
                    <!-- Background Image -->
                    <?php if ( $genre_img_url ) : ?>
                        <div class="absolute inset-0">
                            <img src="<?php echo esc_url( $genre_img_url ); ?>" alt="" class="w-full h-full object-cover opacity-25 group-hover:opacity-40 transition-opacity duration-500" loading="lazy">
                        </div>
                    <?php endif; ?>

                    <!-- Count -->
                    <span class="absolute top-6 left-5 font-mono text-[10px] font-semibold text-accent tracking-wider-2 uppercase"><?php echo esc_html( $count ); ?> RELEASES</span>

                    <!-- Genre Name -->
                    <span class="absolute bottom-5 left-5 font-display text-[28px] font-bold text-cream"><?php echo esc_html( $genre->name ); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="font-mono text-sm text-cream/50">No genres available yet.</p>
    <?php endif; ?>

</section>
