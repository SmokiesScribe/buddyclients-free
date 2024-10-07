<?php
use BuddyClients\Components\Booking\BookingIntent;
/**
 * Handles booking deletions.
 * 
 * @since 0.2.4
 */
function bc_handle_delete_booking() {
    $action = bc_get_param( 'action' );

    // Check if action is delete booking
    if ( $action === 'delete_booking' ) {
        
        // Ensure booking_id is set
        $booking_id = bc_get_param( 'booking_id' );
        if ( is_numeric( $booking_id ) && $booking_id > 0 ) {
            // Cast to integer
            $booking_id = intval( $booking_id );
            
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