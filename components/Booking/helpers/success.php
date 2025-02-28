<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\SuccessfulBooking;
use BuddyClients\Components\Booking\SuccessfulBookingPayment;

/**
 * Handles a successful booking event.
 * 
 * @since 1.0.27
 * 
 * @param   int     $booking_intent_id  The ID of the successful BookingIntent.
 * @param   string  $status             Optional. The new status for the BookingIntent.
 *                                      Defaults to 'succeeded'.
 */
function buddyc_booking_success( $booking_intent_id, $status = 'succeeded' ) {
    new SuccessfulBooking( $booking_intent_id, $status );
}

/**
 * Handles a successful payment event.
 * 
 * @since 1.0.27
 * 
 * @param   int             $booking_payment_id  The ID of the BookingPayment.
 * @param   PaymentIntent   $payment_intent      Optional. The Stripe PaymentIntent.
 */
function buddyc_payment_success( $booking_payment_id = null, $payment_intent = null ) {
    $args = [
        'payment_id'        => $booking_payment_id,
        'payment_intent'    => $payment_intent,
        'succeed'           => true
    ];
    new SuccessfulBookingPayment( $args );
}