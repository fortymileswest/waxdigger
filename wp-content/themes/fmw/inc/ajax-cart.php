<?php
/**
 * AJAX Cart Handlers
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX add to cart handler for single product pages
 */
function fmw_ajax_add_to_cart() {
	$product_id = absint( $_POST['product_id'] ?? 0 );
	$quantity   = absint( $_POST['quantity'] ?? 1 );

	if ( ! $product_id ) {
		wp_send_json_error();
	}

	$product = wc_get_product( $product_id );

	if ( ! $product ) {
		wp_send_json_error();
	}

	// Check if product is already in cart
	$cart = WC()->cart->get_cart();
	foreach ( $cart as $cart_item ) {
		if ( $cart_item['product_id'] == $product_id ) {
			wp_send_json( array(
				'error'   => true,
				'in_cart' => true,
				'message' => 'Only one available – this is already in your basket.',
			) );
			return;
		}
	}

	$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity );

	if ( $cart_item_key ) {
		do_action( 'woocommerce_ajax_added_to_cart', $product_id );

		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
		}

		WC_AJAX::get_refreshed_fragments();
	} else {
		$data = array(
			'error'       => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
		);

		wp_send_json( $data );
	}
}
add_action( 'wp_ajax_woocommerce_ajax_add_to_cart', 'fmw_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'fmw_ajax_add_to_cart' );

/**
 * Get cart contents via AJAX
 */
function fmw_get_cart_contents() {
	$cart = WC()->cart;

	if ( ! $cart ) {
		wp_send_json_error();
	}

	ob_start();

	if ( $cart->is_empty() ) {
		?>
		<div class="cart-drawer-empty">
			<p>Your basket is empty</p>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="cart-drawer-btn">
				Browse Records
			</a>
		</div>
		<?php
	} else {
		?>
		<div class="cart-drawer-items">
			<?php foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) :
				$product = $cart_item['data'];
				$product_id = $cart_item['product_id'];
				$quantity = $cart_item['quantity'];
				$image_id = $product->get_image_id();
				$label = get_field( 'label', $product_id );
			?>
				<div class="cart-drawer-item">
					<div class="cart-drawer-item-image">
						<?php if ( $image_id ) : ?>
							<?php echo wp_get_attachment_image( $image_id, 'thumbnail' ); ?>
						<?php else : ?>
							<div class="cart-drawer-item-placeholder"></div>
						<?php endif; ?>
					</div>
					<div class="cart-drawer-item-details">
						<h4 class="cart-drawer-item-title"><?php echo esc_html( $product->get_name() ); ?></h4>
						<?php if ( $label ) : ?>
							<p class="cart-drawer-item-label"><?php echo esc_html( $label ); ?></p>
						<?php endif; ?>
						<p class="cart-drawer-item-price"><?php echo $product->get_price_html(); ?></p>
					</div>
					<button
						type="button"
						class="cart-drawer-item-remove"
						data-cart-item="<?php echo esc_attr( $cart_item_key ); ?>"
						onclick="removeCartItem('<?php echo esc_attr( $cart_item_key ); ?>')"
						aria-label="Remove"
					>
						&times;
					</button>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'  => $html,
		'total' => $cart->get_cart_subtotal(),
		'count' => $cart->get_cart_contents_count(),
	) );
}
add_action( 'wp_ajax_fmw_get_cart_contents', 'fmw_get_cart_contents' );
add_action( 'wp_ajax_nopriv_fmw_get_cart_contents', 'fmw_get_cart_contents' );

/**
 * Remove cart item via AJAX
 */
function fmw_remove_cart_item() {
	$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( $_POST['cart_item_key'] ) : '';

	if ( $cart_item_key ) {
		WC()->cart->remove_cart_item( $cart_item_key );
	}

	fmw_get_cart_contents();
}
add_action( 'wp_ajax_fmw_remove_cart_item', 'fmw_remove_cart_item' );
add_action( 'wp_ajax_nopriv_fmw_remove_cart_item', 'fmw_remove_cart_item' );
