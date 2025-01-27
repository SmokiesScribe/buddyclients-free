<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Booking\BookedService\Payment;
use BuddyClients\Components\Booking\BookingIntent;

/**
 * Booked service.
 * 
 * Handles individual services for a succeeded BookingIntent.
 *
 * @since 0.1.0
 */
class BookedService {
    
    /**
     * The ID.
     * 
     * @var int
     */
    public $ID;
    
    /**
     * The status.
     * 
     * Accepts 'pending', 'in_progress', 'complete', 'cancellation_requested', 'canceled'.
     * 
     * @var string
     */
    public $status;
    
    /**
     * The ID of the BookingIntent.
     * 
     * @var int
     */
    public $booking_intent_id;
    
    /**
     * Datetime created.
     * 
     * @var string
     */
    public $created_at;

    /**
     * The timestamp when the service was completed.
     * 
     * @var string
     */
    public $complete_date;
    
    /**
     * Service ID.
     * 
     * @var int
     */
    public $service_id;
    
    /**
     * Service name.
     * 
     * @var string
     */
    public $name;
    
    /**
     * Adjustment label.
     * 
     * @var string
     */
    public $adjustment_label;
    
    /**
     * The ID of the client.
     * 
     * @var int
     */
    public $client_id;
    
    /**
     * Team member ID.
     * 
     * @var int
     */
    public $team_id;
    
    /**
     * Team member fee.
     * 
     * @var int
     */
    public $team_fee;
    
    /**
     * Client fee.
     * 
     * @var int
     */
    public $client_fee;
    
    /**
     * The ID of the project.
     * 
     * @var int
     */
    public $project_id;
    
    /**
     * File IDs.
     * 
     * @var int
     */
    public $file_ids;
    
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
     * @param   ?int $ID The ID of the BookedService.
     */
    public function __construct( $ID = null ) {
        $this->ID = $ID ?? null;
        
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
     * Builds BookedService from line item.
     * 
     * @since 0.1.0
     * 
     * @param   int         $booking_intent_id  The ID of the BookingIntent.
     * @param   LineItem    $line_item          Single LineItem object.
     * @param   int         $project_id         The ID of the project group.
     */
    public function create( $booking_intent_id, $line_item, $project_id ) {
        
        // Get BookingIntent data
        $booking_intent = BookingIntent::get_booking_intent( $booking_intent_id );
        
        // Retrieve variables
        $this->booking_intent_id    = $booking_intent_id;
        $this->status               = 'pending';
        $this->created_at           = $this->created_at ?? gmdate('Y-m-d H:i:s');
        
        // Set properties from booking intent
        $this->booking_intent_var( $booking_intent );
        
        // Set properties from line item
        $this->line_items_var( $line_item );

        // Get file IDs        
        $this->file_ids = $this->filter_file_ids( $this->file_ids );

        // Set project id
        if ( ! empty( $project_id ) ) {
            $this->project_id = $project_id;
        }
        
        // Create new object in database
        self::$object_handler->new_object( $this );
        
        /**
         * Fires on creation of a new BookedService object.
         * 
         * @since 0.1.0
         * 
         * @param object $booked_service    The BookedService object.
         */
        do_action('buddyc_new_booked_service', $this);
    }
    
    /**
     * Retrieves variables from BookingIntent.
     * 
     * @since 0.1.0
     * 
     * @param object    $booking_intent BookingIntent object.
     */
     private function booking_intent_var( $booking_intent ) {
        $this->client_id            = $booking_intent->client_id;
        $this->project_id           = $booking_intent->project_id;
        $this->file_ids             = $booking_intent->file_ids;
     }
     
     /**
      * Filters file ids for the service.
      * 
      * @since 0.1.0
      * 
      * @param  array   $file_ids   The File IDs to filter.
      */
     private function filter_file_ids( $file_ids ) {
         // Initialize
         $filtered_ids = [];
         
         // Get service upload ids
         $service_upload_ids = get_post_meta( $this->service_id, 'file_uploads', true);
         
         // Exit if it's not an array
         if ( ! is_array( $service_upload_ids ) ) {
             return $filtered_ids;
         }
         
         $service_upload_ids = array_map('trim', $service_upload_ids);
         
         // Loop through the file ids
         foreach ( $file_ids as $file_id ) {
             $upload_id = buddyc_get_file_upload_id( $file_id );
             
             // Check if the file id is in the service uploads
             if ( in_array( $upload_id, $service_upload_ids ) ) {
                 $filtered_ids[] = $file_id;
             }
         }
         
         return $filtered_ids;
     }
     
    /**
     * Retrieves variables from LineItems.
     * 
     * @since 0.1.0
     * 
     * @param   object  $line_item  LineItem object.
     */
     private function line_items_var( $line_item ) {
        $this->service_id           = $line_item->service_id;
        $this->name                 = $line_item->service_name;
        $this->adjustment_label     = $line_item->adjustment_label;
        $this->team_id              = $line_item->team_id;
        $this->client_fee           = self::format_currency( $line_item->service_fee );
        $this->team_fee             = self::format_currency( ( floatval( $line_item->service->team_member_percentage ) / 100) * self::format_currency( $line_item->service_fee ) );
     }
     
     /**
      * Formats number for currency.
      * 
      * @since 0.1.0
      */
     private static function format_currency( $value ) {
         $value = str_replace( ',', '', $value );
         return number_format((float) $value, 2, '.', '' );
     }
    
    /**
     * Retrieves BookedService object.
     * 
     * @since 0.1.0
     * 
     * @param   int $ID The ID of the BookedService object.
     */
    public static function get_booked_service( $ID ) {
        
        // Initialize database
        self::init_object_handler();
        
        // Get object by ID
        return self::$object_handler->get_object( $ID );
    }
    
    /**
     * Retrieves all BookedServices.
     * 
     * If a BookingIntent ID is passed, retrieves all BookedService objects for that BookingIntent.
     * 
     * @since 0.1.0
     * 
     * @param   int $booking_intent_id  Optional. The ID of the BookingIntent.
     */
    public static function get_all_services( $booking_intent_id = null ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Check if booking intent id is defined
        if ( $booking_intent_id ) {
        
            // Retrieve all BookedService objects by booking intent id
            return self::get_services_by_booking_intent( $booking_intent_id );
            
        } else {            
            // Get all objects
            return self::$object_handler->get_all_objects();
        }
    }
    
    /**
     * Retrieves all BookedServices for a specific property.
     * 
     * @since 0.1.0
     * 
     * @param   string  $property   The property to search by.
     * @param   mixed   $value      The value to filter by.
     */
    public static function get_services_by( $property, $value ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Retrieve all BookedService objects by booking intent id
        return self::$object_handler->get_objects_by_property( $property, $value );
    }
    
    /**
     * Retrieves the status
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The BookedService ID.
     */
    public static function get_status( $ID ) {
        $booked_service = self::get_booked_service( $ID );
        return $booked_service->status;
    }
    
    /**
     * Retrieves the time created.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The BookedService ID.
     */
    public static function get_created_at( $ID ) {
        $booked_service = self::get_booked_service( $ID );
        return $booked_service->created_at;
    }

    /**
     * Retrieves the associated team Payment object.
     * 
     * @since 1.0.21
     * 
     * @param   int     $ID         The ID of the BookedService.
     */
    public static function team_payment_object( $ID ) {
        $booked_service = self::get_booked_service( $ID );
        $payment = Payment::get_booked_service_payment( $ID, $booked_service->team_id );
        if ( $payment ) {
            return $payment;
        }
    }

    /**
     * Retrieves the status of the associated team payment.
     * 
     * @since 1.0.21
     * 
     * @param   int     $ID         The ID of the BookedService.
     */
    public static function team_payment_status( $ID ) {
        $payment = self::team_payment_object( $ID );
        if ( $payment && isset( $payment->status ) ) {
            return $payment->status;
        }
    }
    
    /**
     * Updates status.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The BookedService ID.
     * @param   string  $new_status The status to update to.
     */
    public static function update_status( $ID, $new_status ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Get initial status
        $old_status = self::get_status( $ID );
        
        // Update object
        $updated = self::$object_handler->update_object_properties( $ID, ['status' => $new_status] );
        
        // Check if we transitioned to a new status
        if ( $updated->status !== $old_status ) {
            
            $booked_service = self::get_booked_service( $ID );
            $booked_service->status = $updated->status;

            // Check if we transitioned to 'complete' status
            if ( $updated->status === 'complete' ) {
                $curr_time = current_time( 'timestamp' );
                $booked_service->complete_date = $curr_time;
                $updated = self::$object_handler->update_object_properties( $ID, ['status' => $new_status, 'complete_date' => $curr_time] );
            }
            
            /**
             * Fires on transition to new BookedService status.
             * 
             * @since 0.1.0
             * 
             * @param object $booked_service    The BookedService object.
             * @param string $old_status        The old status.
             * @param string $new_status        The new status.
             */
            do_action( 'buddyc_service_status_updated', $updated );
        }
    }
    
    /**
     * Updates cancellation reason.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID                     The BookedService ID.
     * @param   string  $cancellation_reason    The submitted cancellation reason.
     */
    public static function update_cancellation_reason( $ID, $cancellation_reason ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Update object
        $updated = self::$object_handler->update_object_properties( $ID, ['cancellation_reason' => $cancellation_reason, 'status' => 'cancellation_requested'] );
    }
    
    /**
     * Updates team member.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID                     The BookedService ID.
     * @param   string  $team_id                The ID of the new team member.
     */
    public static function update_team_id( $ID, $team_id ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Update object
        $updated = self::$object_handler->update_object_properties( $ID, ['team_id' => $team_id] );
    }
    
    /**
     * Retrieves Booked Services by booking intent ID.
     * 
     * @since 0.2.5
     * 
     * @var int $booking_intent_id  The ID of the BookingIntent.
     */
    public static function get_services_by_booking_intent( $booking_intent_id ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Retrieve Booked Services
        return self::$object_handler->get_objects_by_property( 'booking_intent_id', $booking_intent_id );
    }
    
    /**
     * Deletes a Booked Service object.
     * 
     * @since 0.2.5
     * 
     * @var     int     $booked_service_id     The ID of the Booked Service to delete.
     */
    public static function delete_booked_service( $booked_service_id ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Delete object
        self::$object_handler->delete_object( $booked_service_id );
    }

    /**
     * Deletes all BookedService objects associated with a BookingIntent.
     * 
     * @since 1.0.21
     * 
     * @param   int     $booking_intent_id      The ID of the BookingIntent.
     */
    public static function delete_intent_booked_services( $booking_intent_id ) {
        $services = self::get_services_by_booking_intent( $booking_intent_id );
        if ( $services ) {
            foreach ( $services as $service ) {
                self::delete_booked_service( $service->ID );
            }
        }
    }
}