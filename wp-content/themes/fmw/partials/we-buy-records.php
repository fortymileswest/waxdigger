<?php
/**
 * Partial: We Buy Records Section
 *
 * Teal background, background text, USP cards + testimonial.
 * Hardcoded content matching design.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="relative bg-accent overflow-hidden min-h-[560px]">

    <!-- Background Text -->
    <div class="absolute -left-5 top-[120px] pointer-events-none select-none">
        <span class="font-display text-[120px] md:text-[220px] font-bold text-black/[0.06] leading-[0.85] tracking-[-6px] whitespace-nowrap">WE BUY<br>VINYL.</span>
    </div>

    <!-- Content -->
    <div class="relative flex flex-col lg:flex-row items-center gap-20 py-20 px-6 md:px-[60px] min-h-[560px]">

        <!-- Left Column -->
        <div class="flex flex-col gap-8 flex-1 justify-center">

            <!-- Label -->
            <div class="flex items-center gap-2">
                <?php fmw_icon( 'pound', 'w-[18px] h-[18px] text-dark' ); ?>
                <span class="font-mono text-xs font-extrabold text-dark tracking-wider-3 uppercase">CASH PAID</span>
            </div>

            <!-- Headline -->
            <h2 class="font-display text-[48px] md:text-[64px] font-bold text-dark leading-[0.9] tracking-[-2px]">WE BUY<br>RECORDS.</h2>

            <!-- Body -->
            <p class="font-mono text-[13px] text-dark/80 leading-[1.8]">Got a collection gathering dust? We pay cash for quality vinyl — jungle, hip hop, rave, techno, soul, funk and more. Large or small, we'll come to you.</p>

            <!-- CTAs -->
            <div class="flex items-center gap-4 flex-wrap">
                <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="flex items-center gap-2 bg-dark px-7 py-4 no-transition">
                    <span class="font-mono text-xs font-bold text-accent tracking-wider-2 uppercase">GET A QUOTE</span>
                    <?php fmw_icon( 'arrow-right', 'w-4 h-4 text-accent' ); ?>
                </a>
                <span class="font-mono text-xs font-bold text-dark tracking-wider-2 uppercase">[ CALL US ]</span>
            </div>
        </div>

        <!-- Right Column -->
        <div class="flex flex-col gap-6 flex-1 w-full">

            <!-- USP Cards -->
            <div class="flex items-center gap-4 bg-black/[0.09] rounded px-6 py-5">
                <?php fmw_icon( 'banknote', 'w-6 h-6 text-dark shrink-0' ); ?>
                <div class="flex flex-col gap-0.5">
                    <span class="font-mono text-[11px] font-extrabold text-dark tracking-wider-2 uppercase">CASH ON COLLECTION</span>
                    <span class="font-mono text-[11px] text-dark/65">Instant payment, no waiting around.</span>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-black/[0.09] rounded px-6 py-5">
                <?php fmw_icon( 'truck', 'w-6 h-6 text-dark shrink-0' ); ?>
                <div class="flex flex-col gap-0.5">
                    <span class="font-mono text-[11px] font-extrabold text-dark tracking-wider-2 uppercase">WE COLLECT</span>
                    <span class="font-mono text-[11px] text-dark/65">Nationwide collection for large lots.</span>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-black/[0.09] rounded px-6 py-5">
                <?php fmw_icon( 'disc', 'w-6 h-6 text-dark shrink-0' ); ?>
                <div class="flex flex-col gap-0.5">
                    <span class="font-mono text-[11px] font-extrabold text-dark tracking-wider-2 uppercase">ALL GENRES WANTED</span>
                    <span class="font-mono text-[11px] text-dark/65">Jungle, hip hop, rave, soul, funk, techno.</span>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-black/[0.09] rounded px-6 py-5">
                <?php fmw_icon( 'shield-check', 'w-6 h-6 text-dark shrink-0' ); ?>
                <div class="flex flex-col gap-0.5">
                    <span class="font-mono text-[11px] font-extrabold text-dark tracking-wider-2 uppercase">FAIR HONEST PRICES</span>
                    <span class="font-mono text-[11px] text-dark/65">We know what records are worth.</span>
                </div>
            </div>

            <!-- Testimonial -->
            <div class="bg-dark rounded p-6">
                <div class="flex items-center gap-2 mb-3">
                    <?php fmw_icon( 'compass', 'w-3.5 h-3.5 text-accent' ); ?>
                    <span class="font-mono text-[10px] font-bold text-accent tracking-wider-2 uppercase">CUSTOMER REVIEW</span>
                </div>
                <p class="font-mono text-[11px] text-cream leading-[1.7]">&ldquo;Sold my dad's collection of 2,000+ records. They came, graded everything fairly and paid on the spot. Couldn't recommend more.&rdquo;</p>
            </div>
        </div>

    </div>
</section>
