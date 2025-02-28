<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingPayment;
use BuddyClients\Components\Booking\SuccessfulBookingPayment;

/**
 * Checks whether deposits are enabled.
 * 
 * @since 1.0.27
 * 
 * @return  bool    True if deposits are enabled, false if not.
 */
function buddyc_deposits_enabled() {
    $enabled = buddyc_get_setting( 'booking', 'enable_deposits' );
    return $enabled === 'enable';
}

/**
 * Retrieves a BookingPayment by ID.
 * 
 * @since 1.0.27
 * 
 * @param   int   $payment_id The ID of the BookingPayment.
 * @return  BookingPayment  The BookingPayment object.
 */
function buddyc_get_booking_payment( $payment_id ) {
    return BookingPayment::get_payment( $payment_id );
}

/**
 * Updates a single property of a BookingPayment.
 * 
 * @since 1.0.27
 * 
 * @param   int     $ID         The BookingPayment ID.
 * @param   string  $property   The property to update.
 * @param   mixed   $value      The new value for the property.
 */
function buddyc_update_booking_payment( $ID, $property, $value ) {
    return BookingPayment::update_payment( $ID, $property, $value );
}

/**
 * Updates a single property of multiple BookingPayment objects.
 * 
 * @since 1.0.27
 * 
 * @param   array   $payment_ids    The BookingPayment IDs.
 * @param   string  $property       The property to update.
 * @param   mixed   $value          The new value for the property.
 */
function buddyc_update_booking_payments( $payment_ids, $property, $value ) {
    return BookingPayment::update_payments( $payment_ids, $property, $value );
}

/**
 * Creates a new BookingPayment.
 * 
 * @since 1.0.27
 * 
 * @param   BookingIntent   $booking_intent The BookingIntent object.
 * @param   string          $type           The type of BookingPayment to create.
 *                                          Accepts 'deposit' and 'final'.
 */
function buddyc_new_booking_payment( $booking_intent, $type ) {
    return new BookingPayment( $booking_intent, $type );
}

/**
 * Returns the ID of a new BookingPayment.
 * 
 * @since 1.0.27
 * 
 * @param   BookingIntent   $booking_intent The BookingIntent object.
 * @param   string          $type           The type of BookingPayment to create.
 *                                          Accepts 'deposit' and 'final'.
 */
function buddyc_new_booking_payment_id( $booking_intent, $type ) {
    $payment = buddyc_new_booking_payment( $booking_intent, $type );
    if ( $payment && isset( $payment->ID ) ) {
        return $payment->ID;
    }
}

/**
 * Builds the url where clients can pay the fee.
 * 
 * @since 1.0.27
 * 
 * @param   int     $payment_id The ID of the BookingPayment.
 * @param   int     $booking_intent_id  Optional. The ID of the BookingIntent.
 * @return  string  The url to pay.
 */
function buddyc_build_pay_link( $payment_id, $booking_intent_id = null ) {
    return BookingPayment::build_pay_link( $payment_id, $booking_intent_id );
}

/**
 * Builds an array of data for the open BookingPayments for a single BookingIntent.
 * 
 * @since 1.0.27
 * 
 * @param   int     $booking_intent_id  The ID of the BookingIntent.
 * @return  array   {
 *     An array of data for unpaid payments for the BookingIntent.
 * 
 *     @type    string  $pay_link   The url to submit payment.
 *     @type    string  $type       The type of payment (final or deposit).
 *     @type    string  $type_label The formatted label for the type.
 *     @type    float   $amount     The amount of the payment.
 *     @status  string  $status     The status of the payment (paid or unpaid).
 * }
 */
function buddyc_get_unpaid_payment_data( $booking_intent_id ) {
    return BookingPayment::get_unpaid_payment_data( $booking_intent_id );
}

/**
 * Retrieves the status of the BookingIntent attached to a BookingPayment.
 * 
 * @since 1.0.27
 * 
 * @param   int $payment_id The ID of the BookingPayment.
 */
function buddyc_get_payment_booking_status( $payment_id ) {
    return BookingPayment::get_booking_status( $payment_id );
}

/**
 * Deletes all BookingPayment objects associated with a BookingIntent.
 * 
 * @since 1.0.27
 * 
 * @param   int     $booking_intent_id      The ID of the BookingIntent.
 */
function buddyc_delete_booking_intent_booking_payments( $booking_intent_id ) {
    BookingPayment::delete_intent_payments( $booking_intent_id );
}

/**
 * Updates a BookingPayment on successful Stripe payment.
 * 
 * @since 1.0.27
 * @deprecated
 * 
 * @param   PaymentIntent   $payment_intent The PaymentIntent from Stripe.
 */
function buddyc_succeed_booking_payment( $payment_intent ) {
    BookingPayment::succeed( $payment_intent );
}

/**
 * Marks a BookingPayment as due.
 * 
 * @since 1.0.27
 * 
 * @param   int   $booking_intent_id The ID of the BookingIntent.
 * @param   bool  $due               Optional. Whether the BookingPayments should be marked as due.
 *                                   Defaults to true.
 */
function buddyc_booking_payments_due( $booking_intent_id, $due = true ) {
    $payment_ids = buddyc_get_booking_intent_payment_ids( $booking_intent_id );
    if ( $payment_ids ) {
        foreach ( $payment_ids as $payment_id ) {
            if ( $due ) {
                BookingPayment::make_due( $payment_id );
            } else {
                BookingPayment::make_not_due( $payment_id );
            }
        }
    }
}

/**
 * Unsucceeds a BookingPayment. Used manually in the admin area.
 * 
 * @since 1.0.25
 */
function buddyc_unsucceed_booking_payment( $payment_id ) {
    $args = [
        'payment_id'    => $payment_id,
        'succeed'       => false
    ];
    new SuccessfulBookingPayment( $args );
}

/**
 * Handles booking actions (delete/update).
 * 
 * @since 1.0.27
 */
function buddyc_handle_booking_payment_action() {
    $action = buddyc_get_param( 'action' );
    if ( ! $action ) return;

    // Get booking ID
    $payment_id = buddyc_get_param( 'booking_payment_id' );
    if ( ! is_numeric( $payment_id ) || $payment_id <= 0 ) return;
    $payment_id = intval( $payment_id );

    // Initialize redirect param
    $redirect_param = 'false';
    $redirect_key = '';
    $updated = false;

    // Update booking payment
    if ( $action === 'update_booking_payment' ) {
        $property = buddyc_get_param( 'property' );
        $value = buddyc_get_param( 'value' );

        if ( $property === 'status' && ( $value === 'paid' || $value === 'unpaid' ) ) {
            if ( $value === 'paid' ) {
                $updated = BookingPayment::succeed_manually( $payment_id );
            } else if ( $value === 'unpaid' ) {
                buddyc_unsucceed_booking_payment( $payment_id );
            }
        } else {        
            if ( empty( $property ) || empty( $value ) ) return;            
            $updated = buddyc_update_booking_payment( $payment_id, $property, $value );
        }
    }

    $redirect_param = $updated ? 'true' : 'false';
    $redirect_key = 'booking_payment_updated';

    // Redirect and exit
    if ( $redirect_key ) {
        wp_redirect( admin_url( sprintf( 'admin.php?page=buddyc-booking-payments&%s=%s', $redirect_key, $redirect_param ) ) );
        exit;
    }
}
add_action( 'admin_init', 'buddyc_handle_booking_payment_action' );