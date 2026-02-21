<?php
/**
 * Partial: About Section
 *
 * Cream background, two columns: left text/quote, right stats + image.
 * Hardcoded content matching design.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="bg-cream py-20 px-6 md:px-[60px] border-t border-dark/[0.125]">

    <div class="flex flex-col lg:flex-row gap-[60px]">

        <!-- Left Column -->
        <div class="flex flex-col gap-8 justify-center flex-1">

            <!-- Header -->
            <div class="flex flex-col gap-2">
                <span class="font-mono text-xs font-bold text-teal-dark tracking-wider-2">05</span>
                <h2 class="font-display text-[48px] md:text-[72px] font-bold text-dark leading-[0.9] tracking-[-2px]">THE<br>CRATE.</h2>
            </div>

            <!-- Divider -->
            <div class="w-[60px] h-[3px] bg-teal-dark"></div>

            <!-- Body Text -->
            <p class="font-mono text-[13px] text-dark/50 leading-[1.8]">Waxdigger is an independent vinyl record shop specialising in underground music. From jungle and drum &amp; bass to hip hop, rave, techno and beyond — we stock hand-selected pressings for serious collectors and casual listeners alike.</p>
            <p class="font-mono text-[13px] text-dark/50 leading-[1.8]">Founded by crate diggers, for crate diggers. Every record in our catalogue has been listened to, vetted and approved. We believe in the art of the physical format — the warmth of analogue, the ritual of the needle drop, and the culture that surrounds it.</p>

            <!-- Quote -->
            <div class="flex gap-3 pt-6 items-start">
                <?php fmw_icon( 'compass', 'w-5 h-5 text-teal-dark shrink-0 mt-0.5' ); ?>
                <p class="font-display text-lg font-bold text-dark leading-[1.4]">We don't just sell records.<br>We preserve culture.</p>
            </div>
        </div>

        <!-- Right Column -->
        <div class="flex flex-col gap-6 flex-1">

            <!-- Stats Row 1 -->
            <div class="flex gap-6">
                <div class="flex flex-col gap-2 bg-dark rounded py-8 px-6 flex-1">
                    <span class="font-display text-4xl font-bold text-accent">4,200+</span>
                    <span class="font-mono text-[10px] font-semibold text-cream/50 tracking-wider-2 uppercase">VINYL IN STOCK</span>
                </div>
                <div class="flex flex-col gap-2 bg-dark rounded py-8 px-6 flex-1">
                    <span class="font-display text-4xl font-bold text-accent">12</span>
                    <span class="font-mono text-[10px] font-semibold text-cream/50 tracking-wider-2 uppercase">YEARS DIGGING</span>
                </div>
            </div>

            <!-- Stats Row 2 -->
            <div class="flex gap-6">
                <div class="flex flex-col gap-2 bg-dark rounded py-8 px-6 flex-1">
                    <span class="font-display text-4xl font-bold text-accent">48</span>
                    <span class="font-mono text-[10px] font-semibold text-cream/50 tracking-wider-2 uppercase">COUNTRIES SHIPPED</span>
                </div>
                <div class="flex flex-col gap-2 bg-dark rounded py-8 px-6 flex-1">
                    <span class="font-display text-4xl font-bold text-accent">100%</span>
                    <span class="font-mono text-[10px] font-semibold text-cream/50 tracking-wider-2 uppercase">VINYL ONLY</span>
                </div>
            </div>

            <!-- Image -->
            <div class="w-full h-[280px] rounded overflow-hidden">
                <img src="https://images.unsplash.com/photo-1575475929146-b1fdf3e1ac1a?w=800&q=80" alt="Vinyl records" class="w-full h-full object-cover" loading="lazy">
            </div>
        </div>

    </div>
</section>
