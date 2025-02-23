<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingIntent;

/**
 * Handles booking actions (delete/update).
 * 
 * @since 0.2.4
 */
function buddyc_handle_booking_action() {
    $action = buddyc_get_param( 'action' );
    if ( ! $action ) return;

    // Get booking ID
    $booking_id = buddyc_get_param( 'booking_id' );
    if ( ! is_numeric( $booking_id ) || $booking_id <= 0 ) return;
    $booking_id = intval( $booking_id );

    // Initialize redirect param
    $redirect_param = 'false';
    $redirect_key = '';

    // Delete booking
    if ( $action === 'delete_booking' ) {
        $deleted = BookingIntent::delete_booking_intent( $booking_id );
        $redirect_param = $deleted ? 'true' : 'false';
        $redirect_key = 'booking_deleted';

    // Update booking
    } elseif ( $action === 'update_booking' ) {
        $property = buddyc_get_param( 'booking_property' );
        $value = buddyc_get_param( 'booking_value' );
        if ( empty( $property ) || empty( $value ) ) return;
        
        $updated = BookingIntent::update_booking_intent( $booking_id, $property, $value );
        $redirect_param = $updated ? 'true' : 'false';
        $redirect_key = 'booking_updated';
    }

    // Redirect and exit
    if ( $redirect_key ) {
        wp_redirect( admin_url( sprintf( 'admin.php?page=buddyc-dashboard&%s=%s', $redirect_key, $redirect_param ) ) );
        exit;
    }
}
add_action( 'admin_init', 'buddyc_handle_booking_action' );

/**
 * Outputs success messages for booking actions.
 * 
 * @since 1.0.17
 */
function buddyc_booking_action_success() {
    $messages = [
        'booking_deleted' => [ 'true' => __( 'Booking deleted!' ), 'false' => __( 'Error: Booking not deleted.' ) ],
        'booking_updated' => [ 'true' => __( 'Booking updated!' ), 'false' => __( 'Error: Booking not updated.' ) ],
    ];
    
    foreach ( $messages as $param => $texts ) {
        // Check for param
        $status = buddyc_get_param( $param );

        // Output alert
        if ( isset( $texts[ $status ] ) ) {
            buddyc_js_alert( $texts[ $status ], true );
        }
    }
}
add_action( 'admin_init', 'buddyc_booking_action_success' );