<?php
/**
 * Exit Intent Popup Component
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

// Don't show to logged-in users or if already subscribed
if ( is_user_logged_in() || isset( $_COOKIE['fmw_subscribed'] ) ) {
	return;
}
?>

<div id="exit-popup" class="exit-popup" x-data="exitPopup()" x-show="open" x-cloak
	@keydown.escape.window="open = false"
	x-transition:enter="transition ease-out duration-300"
	x-transition:enter-start="opacity-0"
	x-transition:enter-end="opacity-100"
	x-transition:leave="transition ease-in duration-200"
	x-transition:leave-start="opacity-100"
	x-transition:leave-end="opacity-0">

	<div class="exit-popup-backdrop" @click="open = false"></div>

	<div class="exit-popup-content"
		x-transition:enter="transition ease-out duration-300 delay-100"
		x-transition:enter-start="opacity-0 scale-95"
		x-transition:enter-end="opacity-100 scale-100">

		<button type="button" class="exit-popup-close" @click="open = false" aria-label="Close">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
				<line x1="18" y1="6" x2="6" y2="18"></line>
				<line x1="6" y1="6" x2="18" y2="18"></line>
			</svg>
		</button>

		<!-- Email Form -->
		<div x-show="!submitted" class="exit-popup-form-wrapper">
			<h3 class="exit-popup-title">Want 10% off your first order?</h3>
			<p class="exit-popup-text">Join our mailing list for exclusive offers and new arrivals.</p>

			<form @submit.prevent="submitEmail" class="exit-popup-form">
				<div class="exit-popup-input-wrapper">
					<input
						type="email"
						x-model="email"
						placeholder="Enter your email"
						required
						class="exit-popup-input"
					>
					<button type="submit" class="exit-popup-submit" :disabled="loading">
						<span x-show="!loading">Get Code</span>
						<span x-show="loading">...</span>
					</button>
				</div>
				<p x-show="error" x-text="error" class="exit-popup-error"></p>
			</form>

			<p class="exit-popup-disclaimer">No spam, unsubscribe anytime.</p>
		</div>

		<!-- Success / Code Display -->
		<div x-show="submitted" class="exit-popup-success">
			<h3 class="exit-popup-title">Your discount code</h3>
			<div class="exit-popup-code">WELCOME10</div>
			<p class="exit-popup-text">Use this code at checkout for 10% off your first order.</p>
			<button type="button" class="exit-popup-shop-btn" @click="open = false">
				Start Shopping
			</button>
		</div>
	</div>
</div>

<script>
document.addEventListener('alpine:init', function() {
	Alpine.data('exitPopup', function() {
		return {
			open: false,
			email: '',
			loading: false,
			error: '',
			submitted: false,
			triggered: false,

			init() {
				// Check if already shown this session
				if (sessionStorage.getItem('exitPopupShown')) {
					return;
				}

				// Exit intent detection
				document.addEventListener('mouseout', (e) => {
					if (this.triggered) return;

					// Only trigger when mouse leaves through top of viewport
					if (e.clientY < 10 && e.relatedTarget === null) {
						this.showPopup();
					}
				});

				// Mobile: trigger on scroll up after scrolling down
				let lastScrollY = window.scrollY;
				let scrolledDown = false;

				window.addEventListener('scroll', () => {
					if (this.triggered) return;

					const currentScrollY = window.scrollY;

					if (currentScrollY > 300) {
						scrolledDown = true;
					}

					if (scrolledDown && currentScrollY < lastScrollY - 50) {
						this.showPopup();
					}

					lastScrollY = currentScrollY;
				});
			},

			showPopup() {
				if (this.triggered) return;
				this.triggered = true;
				this.open = true;
				sessionStorage.setItem('exitPopupShown', 'true');
			},

			async submitEmail() {
				if (!this.email || this.loading) return;

				this.loading = true;
				this.error = '';

				try {
					const formData = new FormData();
					formData.append('action', 'fmw_subscribe_email');
					formData.append('email', this.email);
					formData.append('nonce', fmw.nonce);

					const response = await fetch(fmw.ajaxUrl, {
						method: 'POST',
						body: formData
					});

					const data = await response.json();

					if (data.success) {
						this.submitted = true;
						// Set cookie to not show popup again
						document.cookie = 'fmw_subscribed=1; path=/; max-age=' + (365 * 24 * 60 * 60);
					} else {
						this.error = data.data.message || 'Something went wrong. Please try again.';
					}
				} catch (err) {
					this.error = 'Something went wrong. Please try again.';
				} finally {
					this.loading = false;
				}
			}
		};
	});
});
</script>
