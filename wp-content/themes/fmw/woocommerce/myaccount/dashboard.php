<?php
/**
 * My Account Dashboard
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$orders_count = wc_get_customer_order_count( $current_user->ID );
$recent_orders = wc_get_orders( array(
	'customer_id' => $current_user->ID,
	'limit'       => 3,
	'orderby'     => 'date',
	'order'       => 'DESC',
) );
?>

<div class="dashboard-content">
	<!-- Quick Stats -->
	<div class="dashboard-stats">
		<div class="dashboard-stat">
			<span class="stat-number"><?php echo esc_html( $orders_count ); ?></span>
			<span class="stat-label">Orders</span>
		</div>
		<div class="dashboard-stat">
			<span class="stat-number"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
			<span class="stat-label">Items in Basket</span>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="dashboard-actions">
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="dashboard-action-btn">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z" />
			</svg>
			Browse Records
		</a>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="dashboard-action-btn dashboard-action-btn-outline">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
			</svg>
			View Orders
		</a>
	</div>

	<?php if ( ! empty( $recent_orders ) ) : ?>
	<!-- Recent Orders -->
	<div class="dashboard-section">
		<h2 class="dashboard-section-title">Recent Orders</h2>
		<div class="dashboard-orders">
			<?php foreach ( $recent_orders as $order ) : ?>
				<div class="dashboard-order">
					<div class="order-info">
						<span class="order-number">#<?php echo esc_html( $order->get_order_number() ); ?></span>
						<span class="order-date"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></span>
					</div>
					<div class="order-meta">
						<span class="order-status order-status-<?php echo esc_attr( $order->get_status() ); ?>">
							<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
						</span>
						<span class="order-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
					</div>
					<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="order-view-link">View</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- Account Details Summary -->
	<div class="dashboard-section">
		<h2 class="dashboard-section-title">Account Details</h2>
		<div class="dashboard-details">
			<div class="detail-item">
				<span class="detail-label">Email</span>
				<span class="detail-value"><?php echo esc_html( $current_user->user_email ); ?></span>
			</div>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="detail-edit">Edit Details</a>
		</div>
	</div>
</div>
