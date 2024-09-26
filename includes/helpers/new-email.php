<?php
/**
 * Handles AJAX calls to update BookingIntent with newly entered email.
 * 
 * @since 0.1.0
 * @updated 0.4.0
 */
function bc_update_booking_intent_email() {
    $email = $_POST['email'] ?? null;
    $booking_intent_id = $_POST['booking_intent_id'] ?? null;
    $registration_intent_id = $_POST['registration_intent_id'] ?? null;

    // Update BookingIntent with email
    if ( $booking_intent_id && $booking_intent_id !== '' ) {
        BuddyClients\Components\Booking\BookingIntent::update_client_email( $booking_intent_id, $email );
    }
    
    // Update RegistrationIntent with email
    if ( $registration_intent_id && $registration_intent_id !== '' ) {
        BuddyEvents\Includes\Registration\RegistrationIntent::update_attendee_email( $registration_intent_id, $email );
    }
    
    wp_die(); // Terminate
}
add_action('wp_ajax_bc_update_booking_intent_email', 'bc_update_booking_intent_email'); // For logged-in users
add_action('wp_ajax_nopriv_bc_update_booking_intent_email', 'bc_update_booking_intent_email'); // For logged-out users