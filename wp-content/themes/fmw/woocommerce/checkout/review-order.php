<?php
/**
 * Custom Review Order Table
 *
 * @package FMW
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="checkout-order-items">
	<?php
	do_action( 'woocommerce_review_order_before_cart_contents' );

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			$thumbnail = $_product->get_image( 'thumbnail' );
			$label     = function_exists( 'get_field' ) ? get_field( 'label', $product_id ) : '';
			?>
			<div class="checkout-order-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				<div class="checkout-order-item-image">
					<?php echo $thumbnail; ?>
				</div>
				<div class="checkout-order-item-details">
					<span class="checkout-order-item-name">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
					</span>
					<?php if ( $label ) : ?>
						<span class="checkout-order-item-label"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
				</div>
				<div class="checkout-order-item-total">
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
				</div>
			</div>
			<?php
		}
	}

	do_action( 'woocommerce_review_order_after_cart_contents' );
	?>
</div>

<div class="checkout-order-totals">
	<div class="checkout-totals-row">
		<span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
		<span><?php wc_cart_totals_subtotal_html(); ?></span>
	</div>

	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<div class="checkout-totals-row checkout-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
			<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
			<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
		<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
		<div class="checkout-totals-row">
			<span><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
			<span><?php wc_cart_totals_shipping_html(); ?></span>
		</div>
		<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
	<?php endif; ?>

	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<div class="checkout-totals-row">
			<span><?php echo esc_html( $fee->name ); ?></span>
			<span><?php wc_cart_totals_fee_html( $fee ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
		<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
			<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
				<div class="checkout-totals-row">
					<span><?php echo esc_html( $tax->label ); ?></span>
					<span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="checkout-totals-row">
				<span><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
				<span><?php wc_cart_totals_taxes_total_html(); ?></span>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

	<div class="checkout-totals-row checkout-total">
		<span><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
		<span><?php wc_cart_totals_order_total_html(); ?></span>
	</div>

	<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
</div>
