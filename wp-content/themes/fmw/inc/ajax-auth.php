<?php
/**
 * AJAX Authentication Handlers
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX Login Handler
 */
function fmw_ajax_login() {
	// Verify nonce
	if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'fmw_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed.' ) );
	}

	$email    = sanitize_email( $_POST['email'] ?? '' );
	$password = $_POST['password'] ?? '';
	$remember = ( $_POST['remember'] ?? '' ) === '1';

	if ( empty( $email ) || empty( $password ) ) {
		wp_send_json_error( array( 'message' => 'Please enter your email and password.' ) );
	}

	// Get user by email
	$user = get_user_by( 'email', $email );

	if ( ! $user ) {
		wp_send_json_error( array( 'message' => 'Invalid email or password.' ) );
	}

	// Attempt login
	$creds = array(
		'user_login'    => $user->user_login,
		'user_password' => $password,
		'remember'      => $remember,
	);

	$login = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $login ) ) {
		wp_send_json_error( array( 'message' => 'Invalid email or password.' ) );
	}

	// Get redirect URL
	$redirect = wc_get_page_permalink( 'myaccount' );

	wp_send_json_success( array(
		'redirect' => $redirect,
		'message'  => 'Login successful.',
	) );
}
add_action( 'wp_ajax_nopriv_fmw_ajax_login', 'fmw_ajax_login' );
add_action( 'wp_ajax_fmw_ajax_login', 'fmw_ajax_login' );

/**
 * AJAX Registration Handler
 */
function fmw_ajax_register() {
	// Verify nonce
	if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'fmw_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed.' ) );
	}

	// Check if registration is enabled
	if ( get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) {
		wp_send_json_error( array( 'message' => 'Registration is disabled.' ) );
	}

	$email    = sanitize_email( $_POST['email'] ?? '' );
	$password = $_POST['password'] ?? '';

	if ( empty( $email ) || empty( $password ) ) {
		wp_send_json_error( array( 'message' => 'Please enter your email and password.' ) );
	}

	// Validate email
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
	}

	// Check if email exists
	if ( email_exists( $email ) ) {
		wp_send_json_error( array( 'message' => 'An account with this email already exists.' ) );
	}

	// Validate password strength
	if ( strlen( $password ) < 8 ) {
		wp_send_json_error( array( 'message' => 'Password must be at least 8 characters.' ) );
	}

	// Create user
	$username = sanitize_user( current( explode( '@', $email ) ), true );

	// Ensure unique username
	if ( username_exists( $username ) ) {
		$username = $username . wp_rand( 100, 999 );
	}

	$user_id = wc_create_new_customer( $email, $username, $password );

	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
	}

	// Auto login
	wc_set_customer_auth_cookie( $user_id );

	// Get redirect URL
	$redirect = wc_get_page_permalink( 'myaccount' );

	wp_send_json_success( array(
		'redirect' => $redirect,
		'message'  => 'Account created successfully.',
	) );
}
add_action( 'wp_ajax_nopriv_fmw_ajax_register', 'fmw_ajax_register' );
add_action( 'wp_ajax_fmw_ajax_register', 'fmw_ajax_register' );
