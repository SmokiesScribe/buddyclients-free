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
            wp_redirect( admin_url( 'admin.php?page=bc-dashboard&booking_deleted=true' ) );
            exit;
        } else {
            wp_die( 'Booking ID not specified.' );
        }
    }
}
add_action( 'admin_init', 'bc_handle_delete_booking' );

/**
 * Outputs success message on booking deletion.
 * 
 * @since 1.0.17
 */
function bc_delete_booking_success() {
    $deleted = bc_get_param( 'booking_deleted' );

    // Check if booking was successfully deleted
    if ( $deleted === 'true' ) {

        $message = "Booking deleted!";
        $alert = '<script type="text/javascript">';
        $alert .= 'alert("' . esc_js( $message ) . '");';
        $alert .= '</script>';

        $allowed_html = ['script' => []];

        echo wp_kses( $alert, $allowed_html );
    }
}
add_action( 'admin_init', 'bc_delete_booking_success' );