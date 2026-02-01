<?php
/**
 * My Account page
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
?>

<div class="fmw-account-wrapper">
	<div class="account-header">
		<h1 class="account-page-title">My Account</h1>
		<p class="account-welcome">Welcome back, <strong><?php echo esc_html( $current_user->display_name ); ?></strong></p>
	</div>

	<div class="account-layout">
		<aside class="account-sidebar">
			<?php do_action( 'woocommerce_account_navigation' ); ?>
		</aside>

		<main class="account-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</main>
	</div>
</div>
