<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles a single payment for a BookedService.
 *
 * @since 0.1.0
 */
class Payment {
    
    /**
     * The ID.
     * 
     * @var int
     */
    public $ID;
    
    /**
     * The time created.
     * 
     * @var string
     */
    public $created_at;
    
    /**
     * The ID of the BookedService.
     * 
     * @var int
     */
    public $booked_service_id;
    
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
     * The ID of the project.
     * 
     * @var int
     */
    public $project_id;
    
    /**
     * The memo with payment details.
     * 
     * @var string
     */
    public $memo;
    
    /**
     * Type.
     * Accepts 'team', 'affiliate', 'sales'.
     * 
     * @var string
     */
    public $type;
    
    /**
     * Service name.
     * 
     * @var string
     */
    public $service_name;
    
    /**
     * All service names.
     * 
     * @var string
     */
    public $service_names;
    
    /**
     * Status.
     * Accepts 'pending', 'eligible', 'paid'.
     * 
     * @var string
     */
    public $status;
    
    /**
     * Payee ID.
     * 
     * @var int
     */
    public $payee_id;
    
    /**
     * Amount.
     * 
     * @var float
     */
    public $amount;
    
    /**
     * Paid date.
     * 
     * @var string
     */
    public $paid_date;

    /**
     * The timestamp the payment will be eligible.
     * 
     * @var string
     */
    public $time_eligible;
    
    /**
     * ObjectHandler instance.
     * 
     * @var ObjectHandler
     */
    protected static $object_handler = null;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   ?int $ID The ID of the Payment.
     */
    public function __construct( $ID = null ) {
        $this->ID = $ID ?? null;
        $this->status = $this->status ?? 'pending';
        $this->created_at = $this->created_at ?? time();
        
        // Initialize object handler
        self::init_object_handler();
    }
    
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
     * Creates a new Payment.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args {
     *     Array of arguments for creating the Payment object.
     * 
     *     @type    int     $booked_service_id      The ID of the BookedService.
     *     @type    int     $booking_intent_id      The ID of the BookingIntent.
     *     @type    string  $type                   The type of Payment.
     *     @type    string  $status                 Optional. The Payment status. Defaults to 'pending'.
     *     @type    int     $payee_id               The ID of the payee.
     *     @type    float   $amount                 The amount to be paid.
     *     @type    string  $paid_date              Optional. The date payment was completed.
     *     @type    string  $service_name           Optional. The name of the service.
     *     @type    string  $service_names          Optional. The names of all services for the BookingIntent.
     * }
     */
    public function create( $args ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Extract args
        foreach ( $args as $key => $value ) {
            $this->{$key} = $value;
        }
        
        // Format amount
        $this->amount = number_format( (float) $this->amount, 2, '.', '' );

        // Build memo
        $this->memo = $this->build_memo();

        // Schedule update to eligible
        $this->schedule_eligible();
        
        // Create new object in database
        $this->ID = self::$object_handler->new_object( $this );
        
        // Return the Payment object
        return $this;
    }

    /**
     * Builds the memo.
     * 
     * @since 1.0.17
     */
    public function build_memo() {
        $items = [
            $this->service_names, // service names
            bp_core_get_user_displayname( $this->client_id ), // client id
            buddyc_group_name( $this->project_id ) // project name
        ];

        // Remove empty values
        $filtered_items = array_filter( $items, function( $item ) {
            return ! empty( trim( $item ) );
        });
        
        // Implode to string
        return implode( ' | ', $filtered_items );
    }
    
    /**
     * Schedules status update to 'eligible'.
     * 
     * @since 0.1.0
     */
    private function schedule_eligible() {
        // Get cancellation window
        $cancellation_window = buddyc_get_setting('booking', 'cancellation_window' );
        
        // Eligible if no cancellation window set
        if ( $cancellation_window == 0 ) {
            $this->status = 'eligible';
            $this->time_eligible = current_time('timestamp');
            return;
        }
        
        // Calculate the eligible time
        $scheduled_date = strtotime('+' . $cancellation_window . ' days', current_time('timestamp') );

        // Set the scheduled time property
        $this->time_eligible = $scheduled_date;
        
        // Store payment ID in a variable
        $payment_id = $this->ID;
        
        // Schedule the update_status function
        wp_schedule_single_event( $scheduled_date, 'buddyc_payment_eligible', [$this->ID, $cancellation_window, current_time('timestamp')] );
    }
    
    /**
     * Retrieves Payment records by type.
     * 
     * @since 0.1.0
     * 
     * @param   string     $type        Optional. The type to retrieve.
     *                                  Defaults to all payments.
     */
    public static function get_payments_by_type( $type = null ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Retrieve payment records
        if ( $type ) {
            return self::$object_handler->get_objects_by_property( 'type', $type );
        } else {
            return self::get_all_payments();
        }
    }
    
    /**
     * Retrieves a Payment by ID.
     * 
     * @since 0.1.0
     * 
     * @param   int     $payment_id        The ID of the payment.
     */
    public static function get_payment( $payment_id ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Retrieve payment
        return self::$object_handler->get_object( $payment_id );
    }

    /**
     * Retrieves a Payment by the ID of the BookedService.
     * 
     * @since 0.1.0
     * 
     * @param   int     $booked_service_id  The ID of the BookedService.
     * @param   int     $team_id            The ID of the team member.
     */
    public static function get_booked_service_payment( $booked_service_id, $team_id ) {
        
        // Initialize object handler
        self::init_object_handler();

        // Get payments
        $payments = self::$object_handler->get_objects_by_property( 'booked_service_id', $booked_service_id );

        // Make sure team id matches
        foreach ( $payments as $payment ) {
            if ( $payment->payee_id === $team_id ) {
                return $payment;
            }
        }
    }
    
    /**
     * Retrieves all Payment records.
     * 
     * @since 0.1.0
     */
    public static function get_all_payments() {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Retrieve payment records
        return self::$object_handler->get_all_objects();
    }
    
    /**
     * Updates status.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The Payment ID.
     * @param   string  $new_status The status to update to.
     */
    public static function update_status( $ID, $new_status ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Update status
        $updated_payment = self::$object_handler->update_object_properties( $ID, ['status' => $new_status] );
        
        // Check if we transitioned to a new status
        if ( $updated_payment->status === $new_status ) {

            // Check if the new status is succeeded
            if ( $new_status === 'paid' ) {
                
                // Update paid date
                $curr_date_time = current_datetime()->format('Y-m-d H:i:s');
                self::$object_handler->update_object_properties( $ID, ['paid_date' => $curr_date_time, 'status' => $new_status] );
                
                /**
                 * Fires on change of Payment status to 'paid'.
                 * 
                 * @since 0.1.0
                 * 
                 * @param int $ID    The Payment ID.
                 */
                do_action('buddyc_payment_paid', $ID);
                
            } else {
                // Clear paid date
                self::$object_handler->update_object_properties( $ID, ['paid_date' => '', 'status' => $new_status] );
            }
        }
    }
    
    /**
     * Retrieves payments by booking intent ID.
     * 
     * @since 0.2.5
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
     * Deletes a Payment object.
     * 
     * @since 0.2.5
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
     * Updates a property.
     * 
     * @since 0.3.3
     * 
     * @param   int     $ID         The ID of the Payment to update.
     * @param   string  $property   The property to update.
     * @param   mixed   $value      The new value for the property.
     */
    public static function update_property( $ID, $property, $value ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Update property
        self::$object_handler->update_object_properties( $ID, [$property => $value] );
    }

    /**
     * Deletes all Payments associated with a BookingIntent.
     * 
     * @since 1.0.21
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
}