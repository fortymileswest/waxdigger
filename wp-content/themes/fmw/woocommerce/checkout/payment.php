<?php
/**
 * Custom Checkout Payment Section
 *
 * @package FMW
 * @version 9.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>

<div id="payment" class="checkout-payment">
	<?php if ( WC()->cart && WC()->cart->needs_payment() ) : ?>
		<h3 class="checkout-payment-title">Payment Method</h3>
		<div class="checkout-payment-methods">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ), 'notice' );
			}
			?>
		</div>
	<?php endif; ?>

	<div class="checkout-place-order">
		<noscript>
			<?php
			printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order.', 'woocommerce' ), '<em>', '</em>' );
			?>
			<br/>
			<button type="submit" class="checkout-update-btn" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>">
				<?php esc_html_e( 'Update totals', 'woocommerce' ); ?>
			</button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<button type="submit" class="checkout-submit-btn" name="woocommerce_checkout_place_order" id="place_order" value="<?php echo esc_attr( $order_button_text ); ?>" data-value="<?php echo esc_attr( $order_button_text ); ?>">
			<?php echo esc_html( $order_button_text ); ?>
		</button>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>
</div>

<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
?>
