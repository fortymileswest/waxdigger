<?php
/**
 * Custom Checkout Form
 *
 * @package FMW
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<div class="fmw-checkout-wrapper">
	<h1 class="checkout-page-title">Checkout</h1>

	<form name="checkout" method="post" class="checkout woocommerce-checkout fmw-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

		<div class="checkout-columns">
			<!-- Left Column: Customer Details -->
			<div class="checkout-details">
				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<!-- Billing -->
					<div class="checkout-section" id="customer_details">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>

					<!-- Shipping -->
					<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
						<div class="checkout-section">
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						</div>
					<?php endif; ?>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>

				<!-- Additional Information / Order Notes -->
				<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>
					<div class="checkout-section checkout-notes">
						<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

						<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>
							<h3><?php esc_html_e( 'Additional information', 'woocommerce' ); ?></h3>
						<?php endif; ?>

						<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
							<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
						<?php endforeach; ?>

						<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Right Column: Order Review -->
			<div class="checkout-order-review">
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

				<h2 class="checkout-section-title">Your Order</h2>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</div>

	</form>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
