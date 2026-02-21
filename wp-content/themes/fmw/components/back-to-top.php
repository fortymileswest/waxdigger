<?php
/**
 * Back to Top Button Component
 *
 * Appears after scrolling down, smooth-scrolls to top.
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;
?>

<button
	id="back-to-top"
	type="button"
	x-data="{ visible: false }"
	x-show="visible"
	x-cloak
	x-transition:enter="transition ease-out duration-300"
	x-transition:enter-start="opacity-0 scale-90"
	x-transition:enter-end="opacity-100 scale-100"
	x-transition:leave="transition ease-in duration-200"
	x-transition:leave-start="opacity-100 scale-100"
	x-transition:leave-end="opacity-0 scale-90"
	@scroll.window="visible = window.scrollY > 500"
	@click="window.scrollTo({ top: 0, behavior: 'smooth' })"
	class="fixed bottom-6 right-6 z-[9990] w-11 h-11 rounded-full flex items-center justify-center border border-cream/50 bg-dark/80 backdrop-blur-sm hover:border-accent hover:bg-dark transition-all cursor-pointer"
	aria-label="<?php esc_attr_e( 'Back to top', 'fmw' ); ?>"
>
	<?php fmw_icon( 'chevron-up', 'w-4 h-4 text-cream' ); ?>
</button>
