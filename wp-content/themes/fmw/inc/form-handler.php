<?php
/**
 * Form Handler
 *
 * Handles AJAX form submissions with nonce verification and sanitisation.
 *
 * @package FMW
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle contact form submission
 */
function fmw_handle_contact_form() {
    // Verify nonce
    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
    if ( ! wp_verify_nonce( $nonce, 'fmw_nonce' ) && ! wp_verify_nonce( $nonce, 'fmw_contact_nonce' ) ) {
        wp_send_json_error(
            array(
                'message' => __( 'Security check failed. Please refresh the page and try again.', 'fmw' ),
            )
        );
    }

    // Honeypot check
    if ( ! empty( $_POST['website'] ) ) {
        wp_send_json_error(
            array(
                'message' => __( 'Spam detected.', 'fmw' ),
            )
        );
    }

    // Sanitise input
    $form_type = isset( $_POST['form_type'] ) ? sanitize_text_field( $_POST['form_type'] ) : 'customer';
    $name      = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $email     = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $phone     = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
    $subject   = isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
    $location  = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
    $message   = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

    // Validate required fields
    $errors = array();

    if ( empty( $name ) ) {
        $errors['name'] = __( 'Name is required.', 'fmw' );
    }

    if ( empty( $email ) || ! is_email( $email ) ) {
        $errors['email'] = __( 'A valid email address is required.', 'fmw' );
    }

    if ( empty( $message ) ) {
        $errors['message'] = __( 'Message is required.', 'fmw' );
    }

    if ( ! empty( $errors ) ) {
        wp_send_json_error(
            array(
                'message' => __( 'Please fill in all required fields.', 'fmw' ),
                'errors'  => $errors,
            )
        );
    }

    // Get recipient email from ACF options or use admin email
    $to = fmw_get_option( 'contact_email', get_option( 'admin_email' ) );

    // Build email subject based on form type
    if ( $form_type === 'supplier' ) {
        $email_subject = sprintf( '[%s] Supplier Enquiry from %s', get_bloginfo( 'name' ), $name );
    } else {
        $subject_labels = array(
            'order'    => 'Order Enquiry',
            'shipping' => 'Shipping Question',
            'returns'  => 'Returns & Refunds',
            'product'  => 'Product Question',
            'other'    => 'General Enquiry',
        );
        $subject_text = isset( $subject_labels[ $subject ] ) ? $subject_labels[ $subject ] : 'Contact Form';
        $email_subject = sprintf( '[%s] %s from %s', get_bloginfo( 'name' ), $subject_text, $name );
    }

    // Build email body
    $body  = "Form Type: " . ucfirst( $form_type ) . "\n\n";
    $body .= "Name: {$name}\n";
    $body .= "Email: {$email}\n";
    if ( ! empty( $phone ) ) {
        $body .= "Phone: {$phone}\n";
    }
    if ( ! empty( $subject ) ) {
        $body .= "Subject: {$subject}\n";
    }
    if ( ! empty( $location ) ) {
        $body .= "Location: {$location}\n";
    }
    $body .= "\nMessage:\n" . $message;

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    // Send email
    $sent = wp_mail( $to, $email_subject, $body, $headers );

    if ( $sent ) {
        wp_send_json_success(
            array(
                'message' => __( 'Thanks for your message. We\'ll be in touch soon.', 'fmw' ),
            )
        );
    } else {
        wp_send_json_error(
            array(
                'message' => __( 'There was a problem sending your message. Please try again later.', 'fmw' ),
            )
        );
    }
}
add_action( 'wp_ajax_fmw_contact_form', 'fmw_handle_contact_form' );
add_action( 'wp_ajax_nopriv_fmw_contact_form', 'fmw_handle_contact_form' );

/**
 * Handle email subscription from exit popup
 */
function fmw_handle_subscribe_email() {
	// Verify nonce
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
	if ( ! wp_verify_nonce( $nonce, 'fmw_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Security check failed. Please refresh the page and try again.', 'fmw' ),
			)
		);
	}

	// Sanitise email
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

	// Validate email
	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please enter a valid email address.', 'fmw' ),
			)
		);
	}

	// Get existing subscribers
	$subscribers = get_option( 'fmw_email_subscribers', array() );

	// Check if already subscribed
	if ( in_array( $email, $subscribers, true ) ) {
		// Still return success - they get the code anyway
		wp_send_json_success(
			array(
				'message' => __( 'You\'re already subscribed!', 'fmw' ),
				'code'    => 'WELCOME10',
			)
		);
	}

	// Add new subscriber
	$subscribers[] = $email;
	update_option( 'fmw_email_subscribers', $subscribers );

	// Optional: Send welcome email with code
	$subject = sprintf( __( 'Your %s Discount Code', 'fmw' ), get_bloginfo( 'name' ) );
	$message = sprintf(
		__(
			"Thanks for subscribing to %s!\n\nYour discount code is: WELCOME10\n\nUse this code at checkout for 10%% off your first order.\n\nHappy digging!\n%s",
			'fmw'
		),
		get_bloginfo( 'name' ),
		home_url()
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	wp_mail( $email, $subject, $message, $headers );

	wp_send_json_success(
		array(
			'message' => __( 'Thanks for subscribing!', 'fmw' ),
			'code'    => 'WELCOME10',
		)
	);
}
add_action( 'wp_ajax_fmw_subscribe_email', 'fmw_handle_subscribe_email' );
add_action( 'wp_ajax_nopriv_fmw_subscribe_email', 'fmw_handle_subscribe_email' );
