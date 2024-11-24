<?php
use BuddyClients\Components\Booking\BookingIntent as BookingIntent;
/**
 * Retrieves a BookingIntent object.
 * 
 * @since 0.2.6
 * 
 * @param   int     $booking_intent_id  The ID of the BookingIntent to retrieve.
 */
function buddyc_get_booking_intent( $booking_intent_id ) {
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