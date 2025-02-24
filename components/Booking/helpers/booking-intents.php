<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingIntent;
/**
 * Retrieves a BookingIntent object.
 * 
 * @since 0.2.6
 * 
 * @param   int     $booking_intent_id  The ID of the BookingIntent to retrieve.
 */
function buddyc_get_booking_intent( int $booking_intent_id ) {
    return BookingIntent::get_booking_intent( $booking_intent_id );
}

/**
 * Updates a BookingIntent object.
 * 
 * @since 0.2.6
 * 
 * @param   int     $ID         The BookingIntent ID.
 * @param   string  $property   The property to update.
 * @param   mixed   $value      The new value for the property.
 */
function buddyc_update_booking_intent( $ID, $property, $value ) {
    return BookingIntent::update_booking_intent( $ID, $property, $value );
}

/**
 * Retrieves the status of a BookingIntent.
 * 
 * @since 1.0.20
 * 
 * @param   int     $booking_intent_id  The ID of the BookingIntent to retrieve.
 */
function buddyc_get_booking_intent_status( $booking_intent_id ) {
    $booking_intent = BookingIntent::get_booking_intent( $booking_intent_id );
    return $booking_intent->status ?? null;
}