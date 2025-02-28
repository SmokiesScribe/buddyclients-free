<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookedService\Payment;
/**
 * Updates payment status to eligible.
 * 
 * @since 0.1.0
 * 
 * @param   int     $payment_id             The ID of the Payment.
 * @param   string  $cancellation_window    The cancellation window setting at the time of scheduling.
 * @param   string  $time_scheduled         The time the function was scheduled.
 */
function buddyc_payment_eligible( $payment_id, $cancellation_window, $time_scheduled ) {
    
    // Get the Payment
    $payment = Payment::get_payment( $payment_id );
    
    // Get the current cancellation window setting
    $curr_cancellation_window = buddyc_get_setting( 'booking', 'cancellation_window' );
    
    // Check if the cancellation window has changed
    if ( $curr_cancellation_window !== $cancellation_window ) {
        
        // Calculate new date
        $new_scheduled_date = strtotime('+' . $curr_cancellation_window . ' days', $time_scheduled );
        
        // Check if the new window has passed
        if ( $new_scheduled_date > time() ) {
            // Schedule for the date in the future
            wp_schedule_single_event( $new_scheduled_date, 'buddyc_payment_eligible', array( $payment_id, $curr_cancellation_window, $time_scheduled ) );
            return;
        }
    }
    
    // Make sure the Payment is still pending
    if ( $payment->status === 'pending' ) {
        // Call the update_status function
        Payment::update_status( $payment_id, 'eligible' );
    }
}
add_action('buddyc_payment_eligible', 'buddyc_payment_eligible', 10, 3);

/**
 * Checks if all services for a booking intent are complete.
 * 
 * @since 1.0.27
 * 
 * @param   BookedService   $booked_service The last updated BookedService object.
 */
function buddyc_check_booking_status( $booked_service ) {
    $booking_intent_id = $booked_service->ID;

}
add_action( 'buddyc_service_status_complete', 'buddyc_check_booking_status', 10, 1 );