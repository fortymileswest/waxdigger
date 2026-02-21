<?php
/**
 * Cookie Consent Component
 *
 * Dark-themed banner that blocks tracking scripts until accepted.
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

// Don't show if already consented
if ( isset( $_COOKIE['fmw_cookie_consent'] ) ) {
	return;
}
?>

<div
	id="cookie-consent"
	x-data="cookieConsent()"
	x-show="show"
	x-cloak
	x-transition:enter="transition ease-out duration-300"
	x-transition:enter-start="opacity-0 translate-y-4"
	x-transition:enter-end="opacity-100 translate-y-0"
	x-transition:leave="transition ease-in duration-200"
	x-transition:leave-start="opacity-100 translate-y-0"
	x-transition:leave-end="opacity-0 translate-y-4"
	class="fixed bottom-0 left-0 right-0 z-[9998] px-6 md:px-[60px] py-5"
	style="background-color: #1a1a1a; border-top: 1px solid rgba(240, 236, 228, 0.125);"
>
	<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 max-w-[1440px] mx-auto">
		<div class="flex-1">
			<p class="font-mono text-[12px] text-cream/70 leading-[1.7] m-0">
				We use cookies to improve your experience and analyse site traffic.
				<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'privacy-policy' ) ) ?: '#' ); ?>" class="text-accent hover:text-accent/80 underline underline-offset-2 transition-colors">Privacy Policy</a>
			</p>
		</div>
		<div class="flex items-center gap-3 shrink-0">
			<button
				type="button"
				@click="reject()"
				class="font-mono text-[10px] font-bold text-cream/50 tracking-wider-2 uppercase bg-transparent border border-cream/30 px-4 py-2.5 hover:border-cream/60 hover:text-cream transition-colors cursor-pointer"
			>REJECT</button>
			<button
				type="button"
				@click="accept()"
				class="font-mono text-[10px] font-bold text-dark tracking-wider-2 uppercase bg-accent px-4 py-2.5 hover:bg-accent/90 transition-colors cursor-pointer border-0"
			>ACCEPT</button>
		</div>
	</div>
</div>

<script>
document.addEventListener('alpine:init', () => {
	Alpine.data('cookieConsent', () => ({
		show: false,

		init() {
			// Show after a short delay if no consent cookie
			if (!this.getCookie('fmw_cookie_consent')) {
				setTimeout(() => { this.show = true; }, 1500);
			} else if (this.getCookie('fmw_cookie_consent') === 'accepted') {
				this.loadTracking();
			}
		},

		accept() {
			this.setCookie('fmw_cookie_consent', 'accepted', 365);
			this.show = false;
			this.loadTracking();
		},

		reject() {
			this.setCookie('fmw_cookie_consent', 'rejected', 365);
			this.show = false;
		},

		loadTracking() {
			// Load Google Analytics or other tracking scripts here
			// Example:
			// if (typeof gtag === 'undefined' && window.fmwTrackingId) {
			//     var script = document.createElement('script');
			//     script.src = 'https://www.googletagmanager.com/gtag/js?id=' + window.fmwTrackingId;
			//     script.async = true;
			//     document.head.appendChild(script);
			//     script.onload = function() {
			//         window.dataLayer = window.dataLayer || [];
			//         function gtag(){dataLayer.push(arguments);}
			//         gtag('js', new Date());
			//         gtag('config', window.fmwTrackingId);
			//     };
			// }

			// Dispatch event for other scripts to listen to
			window.dispatchEvent(new CustomEvent('cookies-accepted'));
		},

		setCookie(name, value, days) {
			var expires = '';
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				expires = '; expires=' + date.toUTCString();
			}
			document.cookie = name + '=' + value + expires + '; path=/; SameSite=Lax';
		},

		getCookie(name) {
			var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
			return match ? match[2] : null;
		}
	}));
});
</script>
