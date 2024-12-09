<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingIntent;
/**
 * Handles booking deletions.
 * 
 * @since 0.2.4
 */
function buddyc_handle_delete_booking() {
    $action = buddyc_get_param( 'action' );

    // Check if action is delete booking
    if ( $action === 'delete_booking' ) {
        
        // Ensure booking_id is set
        $booking_id = buddyc_get_param( 'booking_id' );

        if ( is_numeric( $booking_id ) && $booking_id > 0 ) {
            // Cast to integer
            $booking_id = intval( $booking_id );
            
            // Delete booking intent
            BookingIntent::delete_booking_intent( $booking_id );
    
            // Redirect back to the admin page after deletion
            wp_redirect( admin_url( 'admin.php?page=buddyc-dashboard&booking_deleted=true' ) );
            exit;
        } else {
            wp_die( 'Booking ID not specified.' );
        }
    }
}
add_action( 'admin_init', 'buddyc_handle_delete_booking' );

/**
 * Outputs success message on booking deletion.
 * 
 * @since 1.0.17
 */
function buddyc_delete_booking_success() {
    $deleted = buddyc_get_param( 'booking_deleted' );

    // Check if booking was successfully deleted
    if ( $deleted === 'true' ) {
        $message = "Booking deleted!";
        buddyc_js_alert( $message, $admin = true );
    }
}
add_action( 'admin_init', 'buddyc_delete_booking_success' );

/**
 * Handles updates to bookings.
 * 
 * @since 1.0.20
 */
function buddyc_handle_update_booking() {
    $action = buddyc_get_param( 'action' );

    // Check if action is delete booking
    if ( $action === 'update_booking' ) {
        
        // Ensure booking_id is set
        $booking_id = buddyc_get_param( 'booking_id' );
        $property = buddyc_get_param( 'booking_property' );
        $value = buddyc_get_param( 'booking_value' );

        if ( is_numeric( $booking_id ) && $booking_id > 0 && ! empty( $property ) && ! empty( $value ) ) {
            // Cast to integer
            $booking_id = intval( $booking_id );
            
            // Update booking intent
            $updated = BookingIntent::update_booking_intent( $booking_id, $property, $value );

            if ( $updated ) {    
                // Redirect back to the admin page after deletion
                wp_redirect( admin_url( 'admin.php?page=buddyc-dashboard&booking_updated=true' ) );
                exit;
            } else {
                wp_die( 'Booking not updated.' );
                exit;
            }
        } else {
            wp_die( 'Booking ID or property not specified.' );
        }
    }
}
add_action( 'admin_init', 'buddyc_handle_update_booking' );

/**
 * Outputs success message on booking update.
 * 
 * @since 1.0.17
 */
function buddyc_update_booking_success() {
    $deleted = buddyc_get_param( 'booking_updated' );

    // Check if booking was successfully deleted
    if ( $deleted === 'true' ) {
        $message = "Booking updated!";
        buddyc_js_alert( $message, $admin = true );
    }
}
add_action( 'admin_init', 'buddyc_update_booking_success' );