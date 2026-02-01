<?php
/**
 * Cart Drawer Component
 *
 * Slide-in cart that appears when items are added
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;
?>

<div
	id="cart-drawer"
	class="cart-drawer"
	x-data="cartDrawer()"
	x-show="open"
	x-cloak
	@keydown.escape.window="open = false"
	@cart-updated.window="refreshCart(); open = true"
>
	<!-- Backdrop -->
	<div
		class="cart-drawer-backdrop"
		@click="open = false"
		x-show="open"
		x-transition:enter="transition ease-out duration-300"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
	></div>

	<!-- Drawer Panel -->
	<div
		class="cart-drawer-panel"
		x-show="open"
		x-transition:enter="transition ease-out duration-300"
		x-transition:enter-start="translate-x-full"
		x-transition:enter-end="translate-x-0"
		x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="translate-x-0"
		x-transition:leave-end="translate-x-full"
	>
		<!-- Header -->
		<div class="cart-drawer-header">
			<h2 class="cart-drawer-title">Your Basket</h2>
			<button
				type="button"
				class="cart-drawer-close"
				@click="open = false"
				aria-label="<?php esc_attr_e( 'Close', 'fmw' ); ?>"
			>
				<?php fmw_icon( 'close', 'w-5 h-5' ); ?>
			</button>
		</div>

		<!-- Cart Contents -->
		<div class="cart-drawer-content" x-html="cartHtml">
			<div class="cart-drawer-loading">
				<span>Loading...</span>
			</div>
		</div>

		<!-- Footer -->
		<div class="cart-drawer-footer" x-show="itemCount > 0">
			<div class="cart-drawer-total">
				<span>Subtotal</span>
				<span x-html="cartTotal"></span>
			</div>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-drawer-btn cart-drawer-btn-outline">
				View Basket
			</a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="cart-drawer-btn">
				Checkout
			</a>
		</div>
	</div>
</div>

<script>
document.addEventListener('alpine:init', () => {
	Alpine.data('cartDrawer', () => ({
		open: false,
		cartHtml: '',
		cartTotal: '',
		itemCount: 0,

		init() {
			this.refreshCart();
		},

		refreshCart() {
			fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>?action=fmw_get_cart_contents')
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						this.cartHtml = data.data.html;
						this.cartTotal = data.data.total;
						this.itemCount = data.data.count;

						// Update header cart count
						const countEl = document.querySelector('.cart-count');
						if (countEl) {
							countEl.textContent = data.data.count;
							countEl.style.display = data.data.count > 0 ? '' : 'none';
						} else if (data.data.count > 0) {
							const cartLink = document.querySelector('.header-cart');
							if (cartLink) {
								const span = document.createElement('span');
								span.className = 'cart-count';
								span.textContent = data.data.count;
								cartLink.appendChild(span);
							}
						}
					}
				});
		}
	}));
});

// Intercept WooCommerce add to cart (archive pages)
jQuery(document).ready(function($) {
	$(document.body).on('added_to_cart', function(e, fragments, cart_hash, $button) {
		window.dispatchEvent(new CustomEvent('cart-updated'));
	});

	// AJAX add to cart for single product pages
	$('form.cart').on('submit', function(e) {
		var $form = $(this);
		var $button = $form.find('button[type="submit"]');

		// Only handle simple products with add-to-cart button
		if (!$button.attr('name') || $button.attr('name') !== 'add-to-cart') {
			return true;
		}

		e.preventDefault();

		var productId = $button.val();
		var originalText = $button.text();

		$button.text('Adding...').prop('disabled', true);

		$.ajax({
			url: wc_add_to_cart_params.ajax_url,
			type: 'POST',
			data: {
				action: 'woocommerce_ajax_add_to_cart',
				product_id: productId,
				quantity: 1
			},
			success: function(response) {
				if (response.error) {
					$button.text(originalText).prop('disabled', false);

					// Show toast if already in cart
					if (response.in_cart) {
						showToast(response.message);
					}
					return;
				}

				$button.text('Added!');
				setTimeout(function() {
					$button.text(originalText).prop('disabled', false);
				}, 2000);

				$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
			},
			error: function() {
				$button.text(originalText).prop('disabled', false);
			}
		});
	});
});

// Toast notification
function showToast(message) {
	// Remove existing toast
	var existing = document.querySelector('.fmw-toast');
	if (existing) existing.remove();

	// Create toast
	var toast = document.createElement('div');
	toast.className = 'fmw-toast';
	toast.innerHTML = '<div class="fmw-toast-content">' + message + '</div>';
	document.body.appendChild(toast);

	// Animate in
	setTimeout(function() {
		toast.classList.add('is-visible');
	}, 10);

	// Remove after delay
	setTimeout(function() {
		toast.classList.remove('is-visible');
		setTimeout(function() {
			toast.remove();
		}, 300);
	}, 4000);
}

// Remove cart item
function removeCartItem(cartItemKey) {
	fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: 'action=fmw_remove_cart_item&cart_item_key=' + cartItemKey
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			window.dispatchEvent(new CustomEvent('cart-updated'));
		}
	});
}
</script>
