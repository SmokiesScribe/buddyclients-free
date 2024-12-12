<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingIntent;
use BuddyEvents\Includes\Registration\RegistrationIntent;
/**
 * Handles AJAX calls to update BookingIntent with newly entered email.
 * 
 * @since 0.1.0
 * @updated 0.4.0
 */
function buddyc_update_booking_intent_email() {
    
    // Log the nonce being sent in the AJAX request
    $nonce = isset( $_POST['nonce'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ) : null;
    $nonce_action = isset( $_POST['nonceAction'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonceAction'] ) ) ) : null;

    // Verify nonce
    if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
        return;
    }

    // Get email
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : null;
    if ( ! is_email( $email ) ) {
        return;
    }

    // Get intent id
    $booking_intent_id = isset( $_POST['booking_intent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_intent_id'] ) ) : null;
    $registration_intent_id = isset( $_POST['registration_intent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['registration_intent_id'] ) ) : null;

    // Update BookingIntent with email
    if ( $booking_intent_id && $booking_intent_id !== '' ) {
        BookingIntent::update_client_email( $booking_intent_id, $email );
    }
    
    // Update RegistrationIntent with email
    if ( $registration_intent_id && $registration_intent_id !== '' ) {
        RegistrationIntent::update_attendee_email( $registration_intent_id, $email );
    }
    
    wp_die(); // Terminate
}
add_action('wp_ajax_buddyc_update_booking_intent_email', 'buddyc_update_booking_intent_email'); // For logged-in users
add_action('wp_ajax_nopriv_buddyc_update_booking_intent_email', 'buddyc_update_booking_intent_email'); // For logged-out users