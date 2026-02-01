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
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'fmw_nonce' ) ) {
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
    $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

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
                'message' => __( 'Please correct the errors below.', 'fmw' ),
                'errors'  => $errors,
            )
        );
    }

    // Get recipient email from ACF options or use admin email
    $to = fmw_get_option( 'contact_email', get_option( 'admin_email' ) );

    // Build email
    $subject = sprintf(
        /* translators: %s: site name */
        __( 'Contact form submission from %s', 'fmw' ),
        get_bloginfo( 'name' )
    );

    $body  = sprintf( __( 'Name: %s', 'fmw' ), $name ) . "\n";
    $body .= sprintf( __( 'Email: %s', 'fmw' ), $email ) . "\n";
    if ( ! empty( $phone ) ) {
        $body .= sprintf( __( 'Phone: %s', 'fmw' ), $phone ) . "\n";
    }
    $body .= "\n" . __( 'Message:', 'fmw' ) . "\n" . $message;

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    // Send email
    $sent = wp_mail( $to, $subject, $body, $headers );

    if ( $sent ) {
        wp_send_json_success(
            array(
                'message' => __( 'Thank you for your message. We will be in touch soon.', 'fmw' ),
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
