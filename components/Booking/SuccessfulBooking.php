<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\Project;
use BuddyClients\Includes\FileHandler;

use BuddyClients\Components\Booking\BookedService\BookedService;
use BuddyClients\Components\Booking\BookedService\PaymentGroup;

use BuddyClients\Components\Brief\Brief;

/**
 * Successful booking.
 * 
 * Handles a succeeded BookingIntent.
 *
 * @since 0.1.0
 */
class SuccessfulBooking {
    
    /**
     * The Booking Intent object.
     * 
     * @var BookingIntent
     */
    public $booking_intent;
    
    /**
     * Array of LineItem objects.
     *
     * @var array
     */
    public $line_items; 

    /**
     * The ID of the client.
     *
     * @var int
     */
    public $client_id; 
    
    /**
     * The ID of the project group.

     * @var int
     */
    public $project_id;

    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param   int     $booking_intent_id  The ID of the successful BookingIntent.
     * @param   string  $status             Optional. The new status for the BookingIntent.
     *                                      Defaults to 'succeeded'.
     */
    public function __construct( $booking_intent_id, $status = 'succeeded' ) {
        
        // Get Booking Intent
        $this->booking_intent = buddyc_get_booking_intent( $booking_intent_id );

        if ( ! $this->booking_intent ) {
            return;
        }

        if ( $this->booking_intent->status === 'succeeded' ) {
            return;
        }
        
        // Update BookingIntent to succeeded
        $this->booking_intent->status = $status;
        
        // Unserialize line items
        $this->line_items = unserialize( $this->booking_intent->line_items );
        
        // Create project
        $this->booking_intent->project_id = $this->create_project();
        
        // Upgrade files
        $this->upgrade_files();
        
        // Create BookedService objects
        $booked_services = $this->create_booked_services( $this->booking_intent->project_id );
        
        // Create PaymentGroup
        new PaymentGroup( $this->booking_intent->ID, $booked_services );
        
        // Create Brief objects
        $this->create_briefs( $this->booking_intent->project_id, $this->line_items );

        // Update BookingIntent object
        BookingIntent::update_booking_intent_object( $this->booking_intent->ID, $this->booking_intent );
        
        /**
         * Fires on a successful booking.
         * 
         * @since 0.1.0
         * 
         * @param object $successful_booking    The SuccessfulBooking object.
         */
        do_action( 'buddyc_successful_booking', $this );
    }
    
    /**
     * Creates Brief posts.
     * 
     * @since 0.1.0
     * 
     * @param   int     $project_id     The ID of the project group.
     * @param   array   $line_items     An array of LineItem objects.
     */
    private function create_briefs( $project_id, $line_items ) {

        if ( class_exists( Brief::class ) ) {
            
            // Loop through line items
            foreach ( $line_items as $line_item ) {
                
                // Create Brief instance
                $brief = new Brief;
                
                // Create briefs for each service
                $brief->create( $project_id, $line_item->service_id );
            }
        }
    }
    
    /**
     * Upgrade Files to permanent.
     * 
     * @since 0.1.0
     */
    private function upgrade_files() {
        // Get File IDs from BookingIntent
        $file_ids = $this->booking_intent->file_ids;
        
        if ( $file_ids ) {
            // Upgrade to permanent
            FileHandler::upgrade_files( $file_ids );
        }
    }
    
    /**
     * Creates or updates project.
     * 
     * @since 0.1.0
     */
    private function create_project() {
            
        // Check if init has fired
        if (did_action('init')) {
        
            // Create new project
            $project = new Project;
            $project_id = $project->create_project( $this->booking_intent->ID, $this->booking_intent->client_id );
            
        // Otherwise hook to init
        } else {
            add_action('init', function() {
                // Inside this closure, create the project with the parameters
                $project = new Project;
                $project_id = $project->create_project( $this->booking_intent->ID, $this->booking_intent->client_id );
            });
        }
    
        // Return project id
        return $project_id;
    }

    
    /**
     * Creates BookedService objects.
     * 
     * @since 0.1.0
     * 
     * @return  array   An array of newly created BookedService objects.
     */
    private function create_booked_services( $project_id ) {
        
        // Check if booked services exist for booking intent
        $existing_services = BookedService::get_services_by_booking_intent( $this->booking_intent->ID );

        // Init array
        $created_booked_services = [];
        
        // Loop through line items
        foreach ( $this->line_items as $line_item ) {
            // Create new BookedService
            $booked_service = new BookedService;
            $booked_service->create( $this->booking_intent->ID, $line_item, $project_id );

            // Add to array
            $created_booked_services[] = $booked_service;
        }

        // Return array of objects
        return $created_booked_services;
    }
}