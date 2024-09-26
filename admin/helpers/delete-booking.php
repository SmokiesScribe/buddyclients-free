<?php
use BuddyClients\Components\Booking\BookingIntent as BookingIntent;
/**
 * Handles booking deletions.
 * 
 * @since 0.2.4
 */
function bc_handle_delete_booking() {
    // Check if action is set and matches your action
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete_booking' ) {
        // Ensure booking_id is set
        if ( isset( $_GET['booking_id'] ) && is_numeric( $_GET['booking_id'] ) && $_GET['booking_id'] > 0 ) {
            $booking_id = intval( $_GET['booking_id'] );
            
            // Delete booking intent
            BookingIntent::delete_booking_intent( $booking_id );
    
            // Redirect back to the admin page after deletion
            wp_redirect( admin_url( 'admin.php?page=bc-dashboard&deleted=true' ) );
            exit;
        } else {
            wp_die( 'Booking ID not specified.' );
        }
    }
}
add_action( 'admin_init', 'bc_handle_delete_booking' );