<?php
namespace BuddyClients\Components\Booking;

use BuddyClients\Components\Booking\BookingIntent   as BookingIntent;
use BuddyClients\Includes\DatabaseManager           as DatabaseManager;


/**
 * A single booked service.
 * 
 * Handles an individual service for a succeeded BookingIntent.
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
     * The ID of the BookingIntent.
     * 
     * @var int
     */
    public $booking_intent_id;
    
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
     * File IDs.
     * 
     * @var int
     */
    public $file_ids;
    
    /**
     * DatabaseManager instance.
     * 
     * @var DatabaseManager
     */
    protected $database;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   ?int $ID The ID of the BookedService.
     */
    public function __construct( $ID = null ) {
        $this->ID = $ID ?? null;
        $this->database = new DatabaseManager( 'services' );
    }
    
    /**
     * Builds BookedService from line item.
     * 
     * @since 0.1.0
     * 
     * @param   int         $booking_intent_id  The ID of the BookingIntent.
     * @param   LineItem    $line_item          Single LineItem object.
     */
    public function build( $booking_intent_id, $line_item ) {
        // Insert blank record to get ID
        if ( ! $this->ID ) {
            $this->ID = $this->database->insert_record( ['service' => ''] );
        }
        
        // Get BookingIntent data
        $booking_intent = BookingIntent::get_booking_intent( $booking_intent_id );
        
        // Retrieve line item variables
        $this->booking_intent_id    = $booking_intent_id;
        $this->service_id           = $line_item->service_id;
        $this->name                 = $line_item->service->title;
        $this->adjustment_label     = $line_item->adjustment_label;
        $this->team_id              = $line_item->team_id;
        $this->client_fee           = number_format((float) $line_item->service_fee, 2, '.', '' );
        $this->team_fee             = number_format((float)( $line_item->service->team_member_percentage / 100) * $this->client_fee, 2, '.', '' );
        $this->client_id            = $booking_intent->client_id;
        // $this->file_ids          = @todo
        
        // Update database
        $this->update_database();
    }
    
    /**
     * Updates database.
     * 
     * @since 0.1.0
     */
    private function update_database() {
        
        $data = [
            'service'           => serialize($this),
            'booking_intent_id' => $this->booking_intent_id,
            'service_id'        => $this->service_id,
            'team_id'           => $this->team_id,
            'client_id'         => $this->client_id,
        ];
        
        $this->database->update_record( $this->ID, $data );
    }
    
    /**
     * Retrieves BookedService object.
     * 
     * @since 0.1.0
     * 
     * @param   int $ID The ID of the BookedService object.
     */
    public static function get_booked_service( $ID ) {
        
        $database = new DatabaseManager( 'services' );
        
        // Get record by ID
        $record = $database->get_record_by_id( $ID );

        if ( $record ) {
            $serialized_object = $record->service;
            return unserialize( $serialized_object );
        } else {
            return false;
        }
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
        
        $database = new DatabaseManager( 'services' );
        
        // Intialize array
        $services = [];
        
        if ( $booking_intent_id ) {
        
            // Get all matching records
            $records = $database->get_all_records_by_column( 'booking_intent_id', $booking_intent_id );
            
        } else {
            
            // Get all records
            $records = $database->get_all_records();
        }
        
        // Loop through records
        foreach ( $records as $record ) {
            $services[] = unserialize( $record->service );
        }
        
        return $services;
    }
    
}