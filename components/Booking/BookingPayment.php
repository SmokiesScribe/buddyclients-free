<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A single client payment for a BookingIntent.
 * 
 * Used to manage deposits and final payments.
 *
 * @since 1.0.27
 */
class BookingPayment {

    /**
     * The ID of the BookingPayment.
     * 
     * @var int
     */
    public $ID;

    /**
     * The ID of the BookingIntent.
     * 
     * @var int
     */
    public $booking_intent_id;

    /**
     * The ID of the client.
     * 
     * @var int
     */
    public $client_id;

    /**
     * The status of the BookingIntent.
     * 
     * @var string
     */
    public $booking_intent_status;

    /**
     * The ID of the Stripe PaymentIntent.
     * 
     * @var string
     */
    public $payment_intent_id;

    /**
     * The status of the payment ('unpaid' or 'paid').
     * 
     * @var string
     */
    public $status = 'unpaid';

    /**
     * The amount of the payment.
     * 
     * @var float
     */
    public $amount;

    /**
     * The amount received for the payment.
     * 
     * @var float
     */
    public $amount_received = 0.0;

    /**
     * The type of payment (deposit or final).
     * 
     * @var string
     */
    public $type;

    /**
     * The label for the type of payment.
     * 
     * @var string
     */
    public $type_label;

    /**
     * Timestamp the object was created.
     * 
     * @var string
     */
    public $created_at;

    /**
     * The payment method used.
     * Retrieved from the Stripe Charge.
     * 
     * @var string
     */
    public $payment_method;

    /**
     * The total client fee for the BookingIntent.
     * 
     * @var float
     */
    public $total_booking_fee;

    /**
     * The timestamp the payment was completed.
     * 
     * @var string
     */
    public $paid_at;

    /**
     * The url of the Stripe receipt.
     * Retrieved from the Stripe Charge.
     * 
     * @var string
     */
    public $receipt_url;

    /**
     * The timestamp the payment became due.
     * 
     * @var string
     */
    public $due_at;

    /**
     * Whether the payment is due.
     * 
     * @var bool
     */
    public $due;

    /**
     * ObjectHandler instance.
     *
     * @var ObjectHandler|null
     */
    private static $object_handler = null;

    /**
     * Initializes ObjectHandler.
     * 
     * @since 0.1.0
     */
    private static function init_object_handler() {
        if ( ! self::$object_handler ) {
            self::$object_handler = buddyc_object_handler( __CLASS__ );
        }
    }

    /**
     * Constructor method.
     * 
     * @since 1.0.27
     * 
     * @param   BookingIntent   $booking_intent The BookingIntent object.
     * @param   string          $type           The type of BookingPayment to create.
     *                                          Accepts 'deposit' and 'final'.
     */
    public function __construct( $booking_intent, $type ) {

        // Initialize object handler
        self::init_object_handler();

        // Build the new payment
        $this->build( $booking_intent, $type );
    }

    /**
     * Builds the new BookingPayment.
     * 
     * @since 1.0.27
     * 
     * @param   BookingIntent   $booking_intent The BookingIntent object.
     * @param   string          $type           The type of BookingPayment to create.
     *                                          Accepts 'deposit' and 'final'.  
     */
    private function build( $booking_intent, $type ) {
        $this->type = $type;
        $this->created_at = current_time( 'mysql' );
        $this->total_booking_fee = (float) $booking_intent->total_fee;
        $this->booking_intent_id = $booking_intent->ID;
        $this->booking_intent_status = $booking_intent->status;
        $this->client_id = $booking_intent->client_id;

        // Build type label
        $this->type_label = self::build_type_label( $this->type );

        // Make deposits due
        if ( $this->type === 'deposit' ) {
            $this->due = true;
            $this->due_at = current_time( 'mysql' );
        }

        // Calculate the payment amount
        $this->amount = $this->calculate_amount();

        // Add object to database
        $this->ID = self::$object_handler->new_object( $this );
    }

    /**
     * Builds the url where clients can pay the fee.
     * 
     * @since 1.0.27
     * 
     * @param   int $payment The ID of the the BookingPayment.
     */
    public static function build_pay_link( $payment_id, $booking_intent_id = null ) {
        if ( ! $payment_id ) return;

        // Get checkout page url
        $checkout_url = buddyc_get_page_link( 'checkout_page' );
        if ( ! $checkout_url || $checkout_url === '#' ) return;

        // Get BookingPayment
        $payment = self::get_payment( $payment_id );

        // Check if already paid
        if ( $payment->status === 'paid' ) {
            return;
        }

        // Build url
        // Note: buddyc_add_params nonce fails
        return sprintf(
            '%1$s?booking_id=%2$s&payment_id=%3$s',
            $checkout_url,
            $booking_intent_id ?? $payment->booking_intent_id,
            $payment_id
        );
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
    public static function get_unpaid_payment_data( $booking_intent_id ) {
        $data = [];

        // Get the BookingIntent
        $booking_intent = buddyc_get_booking_intent( $booking_intent_id );
        if ( ! is_object( $booking_intent ) ) return;

        // Get associated payments
        if ( ! empty( $booking_intent->payment_ids ) ) {
            foreach ( $booking_intent->payment_ids as $payment_id ) {
                $payment = self::get_payment( $payment_id );
                if ( $payment && $payment->status === 'unpaid' ) {
                    $data[$payment_id] = [
                        'pay_link'  => self::build_pay_link( $payment_id ),
                        'type'      => $payment->type,
                        'type_label'=> $payment->type_label,
                        'amount'    => $payment->amount,
                        'status'    => $payment->status
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Builds the label for the type of payment.
     * 
     * @since 1.0.27
     * 
     * @param   string  $type   The type of payment.
     *                          Accepts 'deposit' and 'final'.
     * @return  string  The formatted label.
     */
    private static function build_type_label( $type ) {
        return match ( $type ) {
            'deposit'   => __( 'Deposit', 'buddyclients-free' ),
            'final'     => __( 'Final Payment', 'buddyclients-free' )
        };
    }

    /**
     * Calculates the payment amount based on the payment type.
     * 
     * @since 1.0.27
     */
    private function calculate_amount() {
        return match ( $this->type ) {
            'deposit'   => $this->calculate_deposit_amount(),
            'final'     => $this->calculate_final_amount(),
        };
    }

    /**
     * Calculates the amount for a deposit payment.
     * 
     * @since 1.0.27
     */
    private function calculate_deposit_amount() {
        if ( ! buddyc_deposits_enabled() ) return 0;

        // Get settings
        $percentage_setting = buddyc_get_setting( 'booking', 'deposit_percentage' );
        $flat_setting = buddyc_get_setting( 'booking', 'deposit_flat' );
    
        // Initialize deposit
        $deposit = 0.0;
    
        // Calculate percentage if valid
        if ( $percentage_setting > 0 && $this->total_booking_fee > 0 ) {
            $percentage_setting = (float) $percentage_setting / 100;
            $deposit = $this->total_booking_fee * $percentage_setting;
        }
    
        // Add flat fee (ensure null handling)
        $deposit += (float) $flat_setting ?? 0.0;

        // Reduce to full fee if necessary
        if ( $deposit > $this->total_booking_fee ) {
            $deposit = $this->total_booking_fee;
        }
    
        // Round to 2 decimal places
        return round( $deposit, 2 );
    }    

    /**
     * Calculates the amount for a final payment.
     * 
     * @since 1.0.27
     */
    private function calculate_final_amount() {
        // Calculate deposit amount
        $deposit = (float) $this->calculate_deposit_amount();

        // Ensure total fee is valid
        $total_booking_fee = (float) $this->total_booking_fee;

        // Subtract deposit from final fee
        $final = $total_booking_fee - $deposit;

        // Prevent negative final amount
        if ( $final < 0 ) {
            $final = 0.0;
        }

        // Round to 2 decimal places
        return round( $final, 2 );
    }

    /**
     * Retrieves the BookedPayment by ID.
     * 
     * @since 1.0.27
     * 
     * @param   int     $ID The ID of the BookedPayment.
     * @return  object|bool BookedPayment on success, false on failure.
     */
    public static function get_payment( $ID ) {        
        // Initialize object handler
        self::init_object_handler();
            
        // Get object
        return self::$object_handler->get_object( $ID );
    }

    /**
     * Retrieves all BookingPayment objects.
     * 
     * @since 1.0.27
     * 
     * @return  array   Array of BookingPayment objects.
     */
    public static function get_all_payments() {
        
        // Initialize object handler
        self::init_object_handler();
            
        // Get all objects
        return self::$object_handler->get_all_objects();
    }

    /**
     * Retrieves all BookingPayment objects attached to succeeded BookingIntent objects.
     * 
     * @since 1.0.27
     * 
     * @return  array   Array of BookingPayment objects.
     */
    public static function get_all_active_payments() {        
        // Initialize object handler
        self::init_object_handler();
            
        // Get all objects with active BookingIntents
        return self::$object_handler->get_objects_by_property( 'booking_intent_status', 'succeeded' );
    }

    /**
     * Retrieves BookingPayment objects by booking intent ID.
     * 
     * @since 1.0.27
     * 
     * @var int $booking_intent_id  The ID of the BookingIntent.
     */
    public static function get_payments_by_booking_intent( $booking_intent_id ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Retrieve payment records
        return self::$object_handler->get_objects_by_property( 'booking_intent_id', $booking_intent_id );
    }

    /**
     * Deletes all BookingPayment objects associated with a BookingIntent.
     * 
     * @since 1.0.27
     * 
     * @param   int     $booking_intent_id      The ID of the BookingIntent.
     */
    public static function delete_intent_payments( $booking_intent_id ) {
        $payments = self::get_payments_by_booking_intent( $booking_intent_id );
        if ( $payments ) {
            foreach ( $payments as $payment ) {
                self::delete_payment( $payment->ID );
            }
        }
    }

    /**
     * Deletes a BookingPayment object.
     * 
     * @since 1.0.27
     * 
     * @var     int     $payment_id     The ID of the Payment to delete.
     */
    public static function delete_payment( $payment_id ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Delete object
        self::$object_handler->delete_object( $payment_id );
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
    public static function update_payment( $ID, $property, $value ) {
        // Initialize object handler
        self::init_object_handler();

        // Update properties
        return self::$object_handler->update_object_properties( $ID, [$property => $value] );
    }

    /**
     * Updates a single property of multiple BookingPayment objects.
     * 
     * @since 1.0.27
     * 
     * @param   array   $payment_ids    The BookingPayment IDs.
     * @param   string  $property       The property to update.
     * @param   mixed   $value          The new value for the property.
     * @return  array   An array of updated objects on success, false on failure of any object.
     */
    public static function update_payments( $payment_ids, $property, $value ) {
        // Initialize object handler
        self::init_object_handler();

        // Update and return objects or false
        $updated_objects = self::$object_handler->update_objects_properties( $payment_ids, [$property => $value] );
        return $updated_objects;
    }

    /**
     * Updates a BookingPayment object.
     * 
     * @since 1.0.27
     * 
     * @param   int     $ID         The BookingPayment ID.
     * @param   string  $object     The new BookingPayment object.
     */
    public static function update_payment_object( $ID, $object ) {
        // Initialize object handler
        self::init_object_handler();        

        // Update object
        return self::$object_handler->update_object( $ID, $object );
    }

    /**
     * Updates properties of a BookingPayment.
     * 
     * @since 1.0.27
     * 
     * @param   int     $ID         The BookingPayment ID.
     * @param   array  $properties  An associative array of property-value pairs.
     */
    public static function update_properties( $ID, $properties ) {
        // Initialize object handler
        self::init_object_handler();

        // Update properties
        $updated_intent = self::$object_handler->update_object_properties( $ID, $properties );

        // Return updated object
        return $updated_intent;
    }
    
    /**
     * Updates the status of a BookingPayment.
     * 
     * @since 1.0.27
     * 
     * @param   int     $ID         The ID of the BookingPayment.
     * @param   string  $new_status The new status.
     * @return  bool    True on success, false on failure.
     */
    public static function update_status( $ID, $new_status ) {
        return self::update_payment( $ID, 'status', $new_status );
    }

    /**
     * Updates the payment intent id of a BookingPayment.
     * 
     * @since 1.0.27
     * 
     * @param   int     $ID                 The ID of the BookingPayment.
     * @param   string  $payment_intent_id  The ID of the Stripe PaymentIntent.
     * @return  bool    True on success, false on failure.
     */
    public static function update_payment_intent_id( $ID, $payment_intent_id ) {
        return self::update_payment( $ID, 'payment_intent_id', $payment_intent_id );
    }

    /**
     * Retrieves the status of the BookingIntent attached to a BookingPayment.
     * 
     * @since 1.0.27
     * 
     * @param   int $payment_id The ID of the BookingPayment.
     */
    public static function get_booking_status( $payment_id ) {
        $payment = self::get_payment( $payment_id );
        $booking_intent_id = $payment->booking_intent_id;
        return buddyc_get_booking_intent_status( $booking_intent_id );
    }

    /**
     * Checks whether the BookingPayment is due.
     * 
     * @since 1.0.27
     * 
     * @param   int     $ID         The ID of the BookingPayment.
     * @return  bool    True if the payment is due, false if not.
     */
    public static function is_due( $ID ) {
        $payment = self::get_payment( $ID );
        return $payment->due;
    }

    /**
     * Updates the BookingPayment to due.
     * 
     * @since 1.0.27
     * 
     * @param   int     $ID         The ID of the BookingPayment.
     */
    public static function make_due( $ID ) {
        // Get the BookingPayment
        $payment = self::get_payment( $ID );
        
        // Exit if already due
        if ( $payment->due ) return;

        // Update properties
        $payment->due = true;
        $payment->due_at = current_time( 'mysql' );

        // Update object
        self::update_payment_object( $ID, $payment );
    }

    /**
     * Updates the BookingPayment to not due.
     * 
     * @since 1.0.27
     * 
     * @param   int     $ID         The ID of the BookingPayment.
     */
    public static function make_not_due( $ID ) {
        // Get the BookingPayment
        $payment = self::get_payment( $ID );
        
        // Exit if already not due
        if ( ! $payment->due ) return;

        // Update properties
        $payment->due = false;
        $payment->due_at = null;

        // Update object
        self::update_payment_object( $ID, $payment );
    }
}