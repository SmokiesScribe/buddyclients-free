<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingPayment;

/**
 * Handles a successful payment event.
 *
 * @since 1.0.27
 */
class SuccessfulBookingPayment {
    
    /**
     * The ID of the BookingPayment.
     * 
     * @var int
     */
    private $payment_id;

    /**
     * The Stripe PaymentIntent object.
     * 
     * @var PaymentIntent
     */
    private $payment_intent;

    /**
     * The BookingPayment object.
     * 
     * @var BookingPayment
     */
    private $booking_payment;

    /**
     * Whether we are succeeding the BookingPayment.
     * Defaults to true.
     * 
     * @var bool
     */
    private $succeed;

    /**
     * Constructor method.
     * 
     * @since 1.0.27
     * 
     * @param   array   $args {
     *     An array of args to succeed or unsucceed a BookingPayment.
     *     Either a PaymentIntent or BookingPayment ID must be passed in the args.
     * 
     *     @type    PaymentIntent   $payment_intent Optional. The Stripe PaymentIntent object.
     *     @type    int             $payment_id     Optional. The ID of the BookingPayment to succeed or unsucceed.
     *     @type    bool            $succeed        Optional. Succeed the payment if true, unsucceed if false.
     *                                              Defaults to true.
     * }
     */
    public function __construct( $args ) {
        $this->extract_args( $args );
        $this->update_payment( $this->booking_payment, $this->succeed );
    }

    /**
     * Extracts properties from the args.
     * 
     * @since 1.0.27
     * 
     * @param   array   $args {
     *     An array of args to succeed or unsucceed a BookingPayment.
     *     Either a PaymentIntent or BookingPayment ID must be passed in the args.
     * 
     *     @type    PaymentIntent   $payment_intent Optional. The Stripe PaymentIntent object.
     *     @type    int             $payment_id     Optional. The ID of the BookingPayment to succeed or unsucceed.
     *     @type    bool            $succeed        Optional. Succeed the payment if true, unsucceed if false.
     *                                              Defaults to true.
     * }
     */
    private function extract_args( $args ) {
        $this->payment_intent = $args['payment_intent'] ?? null;
        $this->succeed = $args['succeed'] ?? true;
        $this->payment_id = self::get_payment_id( $args );
        $this->booking_payment = $this->get_booking_payment();
    }

    /**
     * Extracts the payment ID from the args.
     * 
     * @since 1.0.27
     */
    private static function get_payment_id( $args ) {
        // Check if passed directly
        $payment_id = $args['payment_id'] ?? null;
        if ( $payment_id ) return $payment_id;

        // Otherwise extract from payment intent
        if ( $this->payment_intent ) {
            $metadata = isset( $this->payment_intent->metadata ) ? $this->payment_intent->metadata : [];
            return $metadata['BookingPaymentId'] ?? null;
        }
    }

    /**
     * Retrieves the BookingPayment object from the ID.
     * 
     * @since 1.0.27
     */
    private function get_booking_payment() {
        if ( ! $this->payment_id ) return;
        return BookingPayment::get_payment( $this->payment_id );
    }

    /**
      * Updates the BookingPayment on a successful Stripe payment.
      * 
      * @since 1.0.27
      */
      private function succeed_payment() {
        // Update BookingPayment
        $this->update_payment( $this->booking_payment, $success = true );
    }

    /**
      * Reverts a payment to unpaid.
      * 
      * @since 1.0.27
      */
      public static function unsucceed_payment() {
        // Update BookingPayment
        $this->update_payment( $this->booking_payment, $success = false );
     }

    /**
     * Updates the properties of the BookingPayment.
     * 
     * @since 1.0.27
     * 
     * @param   BookingPayment   $booking_payment   The BookingPayment object to update.
     * @param   bool             $success           Optional. Whether the object is being succeeded.
     *                                              Defaults to true.
     */
    private function update_payment( $booking_payment, $success = true ) {
        if ( ! $booking_payment ) return;

        // Check if the payment is already the correct status
        if ( ( $success && $booking_payment->status === 'paid' ) || ( ! $success && $booking_payment->status === 'unpaid' ) ) {
            return;
        }

        // Update general properties
        $booking_payment = $this->update_properties( $booking_payment, $success );

        // Update properties from Stripe PaymentIntent
        $booking_payment = $this->update_stripe_properties( $booking_payment );

        // Update the BookingPayment
        return BookingPayment::update_payment_object( $booking_payment->ID, $booking_payment );
    }

    /**
     * Updates the general success properties for the BookingPayment.
     * 
     * @since 1.0.27
     * 
     * @param   BookingPayment  $booking_payment    The BookingPayment object to update.
     * @param   bool            $success            Whether the object is being succeeded.
     * @return  BookingPayment  The updated BookingPayment object.
     */
    private function update_properties( $booking_payment, $success ) {
        // Succeeded if successful payment, otherwise unchanged
        $booking_payment->booking_intent_status = $success ? 'succeeded' : $booking_payment->booking_intent_status;

        // Update status and paid date based on success param
        $booking_payment->status = $success ? 'paid' : 'unpaid';
        $booking_payment->paid_at = $success ? current_time( 'mysql' ) : '';

        // Return object
        return $booking_payment;
    }

    /**
     * Updates the BookingPayment properties associated with Stripe PaymentIntent properties.
     * 
     * @since 1.0.27
     * 
     * @param   BookingPayment  $booking_payment    The BookingPayment object to update.
     * @return  BookingPayment  The updated BookingPayment object.
     */
    private function update_stripe_properties( $booking_payment ) {
        // Check if we have a PaymentIntent
        if ( $this->payment_intent ) {

            // Make sure the Stripe PaymentIntent id matches
            if ( ! $this->payment_intent_id_matches( $booking_payment ) ) {
                // Return unmodified object
                return $booking_payment;
            }

            // Update properties from Stripe PaymentIntent data
            $booking_payment->amount_received = $success ? ( $this->payment_intent['amount_received'] / 100 ) : 0.0;
            $booking_payment->payment_method = $success ? $this->payment_intent['payment_method'] : '';

            // Update Charge properties
            $charge = $this->payment_intent->latest_charge;
            // Make sure it's not just the id
            if ( is_object( $charge ) ) {
                $booking_payment->receipt_url = $charge->receipt_url;
                $booking_payment->payment_method = $charge->payment_method_details->type;
            }

        // No PaymentIntent means manually recorded
        } else {
            $booking_payment->payment_method = $success ? 'manually_recorded' : '';
        }
        return $booking_payment;
    }

    /**
     * Checks whether the PaymentIntent ID stored in the BookingPayment
     * matches the PaymentIntent ID of the Stripe PaymentIntent object.
     * 
     * @since 1.0.27
     * 
     * @param   BookingPayment  $booking_payment    The BookingPayment object to check.
     * @return  bool    True if the IDs match, false if not.
     */
    private function payment_intent_id_matches( $booking_payment ) {
        if ( ! $this->payment_intent || ! isset( $this->payment_intent['id'] ) ) {
            return false;
        }
        // Compare the two
        return $booking_payment->payment_intent_id === $this->payment_intent['id'];
    }
}