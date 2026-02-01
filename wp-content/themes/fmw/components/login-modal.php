<?php
/**
 * Login Modal Component
 *
 * Dark background with light elements
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

// Don't show if already logged in
if ( is_user_logged_in() ) {
	return;
}
?>

<div
	id="login-modal"
	class="login-modal"
	x-data="loginModal()"
	x-show="open"
	x-cloak
	@keydown.escape.window="open = false"
	@login-modal.window="open = true"
>
	<!-- Backdrop -->
	<div
		class="login-modal-backdrop"
		@click="open = false"
		x-show="open"
		x-transition:enter="transition ease-out duration-300"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
	></div>

	<!-- Modal Panel -->
	<div
		class="login-modal-panel"
		x-show="open"
		x-transition:enter="transition ease-out duration-300"
		x-transition:enter-start="opacity-0 scale-95"
		x-transition:enter-end="opacity-100 scale-100"
		x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100 scale-100"
		x-transition:leave-end="opacity-0 scale-95"
		@click.away="open = false"
	>
		<!-- Close Button -->
		<button
			type="button"
			class="login-modal-close"
			@click="open = false"
			aria-label="<?php esc_attr_e( 'Close', 'fmw' ); ?>"
		>
			<?php fmw_icon( 'close', 'icon' ); ?>
		</button>

		<!-- Login Form -->
		<div class="login-modal-content" x-show="!showRegister">
			<h2 class="login-modal-title">Sign In</h2>
			<p class="login-modal-subtitle">Welcome back to Waxdigger</p>

			<form class="login-modal-form" @submit.prevent="handleLogin">
				<div class="login-field">
					<label for="login-email">Email address</label>
					<input
						type="email"
						id="login-email"
						name="email"
						x-model="email"
						required
						autocomplete="email"
					>
				</div>

				<div class="login-field">
					<label for="login-password">Password</label>
					<input
						type="password"
						id="login-password"
						name="password"
						x-model="password"
						required
						autocomplete="current-password"
					>
				</div>

				<div class="login-options">
					<label class="login-remember">
						<input type="checkbox" name="remember" x-model="remember">
						<span>Remember me</span>
					</label>
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="login-forgot">
						Forgot password?
					</a>
				</div>

				<div class="login-error" x-show="error" x-text="error"></div>

				<button type="submit" class="login-submit" :disabled="loading">
					<span x-show="!loading">Sign In</span>
					<span x-show="loading">Signing in...</span>
				</button>
			</form>

			<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
				<p class="login-switch">
					Don't have an account?
					<button type="button" @click="showRegister = true" class="login-switch-btn">Create one</button>
				</p>
			<?php endif; ?>
		</div>

		<!-- Register Form -->
		<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
			<div class="login-modal-content" x-show="showRegister">
				<h2 class="login-modal-title">Create Account</h2>
				<p class="login-modal-subtitle">Join Waxdigger</p>

				<form class="login-modal-form" @submit.prevent="handleRegister">
					<div class="login-field">
						<label for="register-email">Email address</label>
						<input
							type="email"
							id="register-email"
							name="email"
							x-model="regEmail"
							required
							autocomplete="email"
						>
					</div>

					<div class="login-field">
						<label for="register-password">Password</label>
						<input
							type="password"
							id="register-password"
							name="password"
							x-model="regPassword"
							required
							autocomplete="new-password"
						>
					</div>

					<div class="login-error" x-show="error" x-text="error"></div>

					<button type="submit" class="login-submit" :disabled="loading">
						<span x-show="!loading">Create Account</span>
						<span x-show="loading">Creating account...</span>
					</button>
				</form>

				<p class="login-switch">
					Already have an account?
					<button type="button" @click="showRegister = false" class="login-switch-btn">Sign in</button>
				</p>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
document.addEventListener('alpine:init', () => {
	Alpine.data('loginModal', () => ({
		open: false,
		showRegister: false,
		email: '',
		password: '',
		remember: false,
		regEmail: '',
		regPassword: '',
		loading: false,
		error: '',

		async handleLogin() {
			this.loading = true;
			this.error = '';

			try {
				const formData = new FormData();
				formData.append('action', 'fmw_ajax_login');
				formData.append('email', this.email);
				formData.append('password', this.password);
				formData.append('remember', this.remember ? '1' : '0');
				formData.append('nonce', fmw.nonce);

				const response = await fetch(fmw.ajaxUrl, {
					method: 'POST',
					body: formData
				});

				const data = await response.json();

				if (data.success) {
					window.location.href = data.data.redirect;
				} else {
					this.error = data.data.message || 'Login failed. Please try again.';
				}
			} catch (err) {
				this.error = 'An error occurred. Please try again.';
			}

			this.loading = false;
		},

		async handleRegister() {
			this.loading = true;
			this.error = '';

			try {
				const formData = new FormData();
				formData.append('action', 'fmw_ajax_register');
				formData.append('email', this.regEmail);
				formData.append('password', this.regPassword);
				formData.append('nonce', fmw.nonce);

				const response = await fetch(fmw.ajaxUrl, {
					method: 'POST',
					body: formData
				});

				const data = await response.json();

				if (data.success) {
					window.location.href = data.data.redirect;
				} else {
					this.error = data.data.message || 'Registration failed. Please try again.';
				}
			} catch (err) {
				this.error = 'An error occurred. Please try again.';
			}

			this.loading = false;
		}
	}));
});
</script>
