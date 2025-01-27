<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookedService\Payment;
use BuddyClients\Components\Booking\BookedService\BookedService;

/**
 * Deletes all Payments associated with a BookingIntent.
 * 
 * @since 1.0.21
 * 
 * @param   int     $booking_intent_id      The ID of the BookingIntent.
 */
function buddyc_delete_booking_intent_payments( $booking_intent_id ) {
    Payment::delete_intent_payments( $booking_intent_id );
}

/**
 * Deletes all BookedServices associated with a BookingIntent.
 * 
 * @since 1.0.21
 * 
 * @param   int     $booking_intent_id      The ID of the BookingIntent.
 */
function buddyc_delete_booking_intent_booked_services( $booking_intent_id ) {
    BookedService::delete_intent_booked_services( $booking_intent_id );
}

/**
 * Retrieves all BookedServices for a specific property.
 * 
 * @since 1.0.21
 * 
 * @param   string  $property   The property to search by.
 * @param   mixed   $value      The value to filter by.
 */
function buddyc_get_booked_services_by( $property, $value ) {
    return BookedService::get_services_by( $property, $value );
}