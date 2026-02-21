<?php
/**
 * Partial: Newsletter Section
 *
 * Dark background, decorative typography left, email form right.
 * Hardcoded content matching design.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="bg-dark border-y border-cream/[0.125]" data-cursor-light>
    <div class="flex flex-col lg:flex-row">

        <!-- Left — Decorative Type -->
        <div class="relative hidden lg:block flex-1 h-[480px] overflow-hidden">
            <!-- Grid Lines -->
            <div class="absolute top-[160px] left-0 right-0 h-px bg-cream/[0.03]"></div>
            <div class="absolute top-[320px] left-0 right-0 h-px bg-cream/[0.03]"></div>

            <!-- Big Type -->
            <span class="absolute left-[224px] top-[111px] font-display text-[96px] font-bold text-cream/[0.07] leading-[0.9] tracking-[-2px]">STAY<br>IN THE<br>LOOP.</span>
        </div>

        <!-- Right — Form -->
        <div class="flex flex-col gap-8 justify-center flex-1 py-20 px-6 md:px-[60px] lg:border-l lg:border-cream/[0.125]">

            <div class="flex items-center gap-2">
                <?php fmw_icon( 'compass', 'w-4 h-4 text-accent' ); ?>
                <span class="font-mono text-xs font-bold text-accent tracking-wider-2">04</span>
            </div>
            <h2 class="font-display text-[28px] md:text-[32px] font-bold text-cream">JOIN THE DIG LIST</h2>
            <p class="font-mono text-[13px] text-cream/50 leading-[1.7] max-w-[480px]">New drops, restocks, exclusive pre-orders and<br class="hidden md:block">underground selections — delivered weekly.</p>

            <!-- Form -->
            <form class="flex w-full" data-ajax-form data-action="fmw_newsletter">
                <input
                    type="email"
                    name="email"
                    placeholder="YOUR@EMAIL.COM"
                    required
                    class="flex-1 h-[52px] bg-transparent border border-cream/[0.125] px-4 font-mono text-xs text-cream font-medium tracking-wider-2 placeholder:text-cream/50 focus:border-accent focus:outline-none transition-colors"
                >
                <button type="submit" class="flex items-center justify-center gap-2 h-[52px] bg-accent px-7 shrink-0">
                    <span class="font-mono text-xs font-bold text-dark tracking-wider-2 uppercase">SUBSCRIBE</span>
                    <?php fmw_icon( 'arrow-right', 'w-4 h-4 text-dark' ); ?>
                </button>
            </form>
            <div data-form-message></div>

            <p class="font-mono text-[10px] text-cream/50 tracking-[1px]">No spam. Unsubscribe anytime. We respect the craft.</p>
        </div>

    </div>
</section>
