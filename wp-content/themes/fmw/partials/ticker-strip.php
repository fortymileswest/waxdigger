<?php
/**
 * Partial: Ticker Strip
 *
 * Teal horizontal scrolling marquee with USP messages.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="bg-accent overflow-hidden">
    <div class="flex items-center py-3.5 px-[60px] whitespace-nowrap ticker-track">
        <?php for ( $i = 0; $i < 3; $i++ ) : ?>
        <div class="flex items-center gap-8 ticker-content">
            <span class="font-mono text-[11px] font-bold text-dark tracking-wider-2 uppercase">FREE UK SHIPPING OVER £50</span>
            <?php fmw_icon( 'compass', 'w-3 h-3 text-dark/30' ); ?>
            <span class="font-mono text-[11px] font-bold text-dark tracking-wider-2 uppercase">NEW DROPS EVERY FRIDAY</span>
            <?php fmw_icon( 'compass', 'w-3 h-3 text-dark/30' ); ?>
            <span class="font-mono text-[11px] font-bold text-dark tracking-wider-2 uppercase">WORLDWIDE DELIVERY</span>
            <?php fmw_icon( 'compass', 'w-3 h-3 text-dark/30' ); ?>
            <span class="font-mono text-[11px] font-bold text-dark tracking-wider-2 uppercase">VINYL ONLY</span>
            <span class="mr-8"><?php fmw_icon( 'compass', 'w-3 h-3 text-dark/30' ); ?></span>
        </div>
        <?php endfor; ?>
    </div>
</section>
