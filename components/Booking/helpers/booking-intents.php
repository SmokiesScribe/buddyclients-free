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

/**
 * Retrieves the status of a BookingIntent.
 * 
 * @since 1.0.20
 * 
 * @param   int     $booking_intent_id  The ID of the BookingIntent to retrieve.
 */
function buddyc_get_booking_intent_status( $booking_intent_id ) {
    return BookingIntent::get_status( $booking_intent_id );
}

/**
 * Retrieves the IDs of the associated BookingPayments.
 * 
 * @since 1.0.27
 * 
 * @param   int     $booking_intent_id  The ID of the BookingIntent.
 */
function buddyc_get_booking_intent_payment_ids( $booking_intent_id ) {
    return BookingIntent::get_payment_ids( $booking_intent_id );
}

/**
 * Checks whether to send an abandoned booking email.
 * 
 * @since 1.0.27
 * 
 * @param   int $booking_intent_id  The ID of the BookingIntent to check.
 */
function buddyc_abandoned_booking_check( $booking_intent_id ) {
    BookingIntent::abandoned_booking_check( $booking_intent_id );
}
add_action( 'buddyc_scheduled_abandoned_booking', 'buddyc_abandoned_booking_check', 10, 1 );


/**
 * Updates the BookingIntent when all services are complete.
 * 
 * @since 1.0.27
 * 
 * @param   int     $booking_intent_id  The ID of the BookingIntent to update.
 * @param   bool    $services_complete  Optional. Whether all services are complete.
 *                                      Defaults to true.
 * @return  BookingIntent   The updated BookingIntent object.
 */
function buddyc_booking_intent_services_complete( $booking_intent_id, $services_complete = true ) {
    return BookingIntent::update_services_complete( $booking_intent_id, $services_complete );
}
add_action( 'buddyc_all_booking_services_complete', 'buddyc_booking_intent_services_complete', 10, 1 );