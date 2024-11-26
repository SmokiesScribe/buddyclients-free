<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\{
    Project     as Project,
    FileHandler as FileHandler
};

use BuddyClients\Components\Booking\BookedService\{
    BookedService   as BookedService,
    PaymentGroup    as PaymentGroup
};

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
     * The client ID.
     *
     * @var int
     */
    public $client_id; 
    
    /**
     * The project group ID.

     * @var int
     */
    public $project_id;

    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param   int     $booking_intent_id  The ID of the successful BookingIntent.
     */
    public function __construct( $booking_intent_id ) {
        
        // Get Booking Intent
        $this->booking_intent = BookingIntent::get_booking_intent( $booking_intent_id );
        
        // Update BookingIntent to succeeded
        $this->succeed_booking_intent( $booking_intent_id );
        
        // Unserialize line items
        $this->line_items = unserialize( $this->booking_intent->line_items );
        
        // Create project
        $this->create_project();
        
        // Upgrade files
        $this->upgrade_files();
        
        // Create BookedService objects
        $this->create_booked_services();
        
        // Create PaymentGroup
        new PaymentGroup( $this->booking_intent );
        
        // Create Brief objects
        $this->create_briefs( $this->booking_intent->project_id, $this->line_items );
        
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
    
        // Update project id
        $this->project_id = $project_id;
        
        // Update booking intent with project id
        BookingIntent::update_project_id( $this->booking_intent->ID, $project_id );
        
        // Retrieve updated booking intent
        $this->booking_intent = BookingIntent::get_booking_intent( $this->booking_intent->ID );
    }

    
    /**
     * Creates BookedService objects.
     * 
     * @since 0.1.0
     */
    private function create_booked_services() {
        
        // Check if booked services exist for booking intent
        $existing_services = BookedService::get_all_services( $this->booking_intent->ID );
        
        if ( $existing_services ) {
            return;
        }
        
        // Loop through line items
        foreach ( $this->line_items as $line_item ) {
            (new BookedService())->create( $this->booking_intent->ID, $line_item );
        }
        return $this;
    }
    
    /**
     * Updates Booking Intent.
     * 
     * @since 0.1.0
     * 
     * @param   int $booking_intent_id  The ID of the successful BookingIntent.
     */
    private function succeed_booking_intent( $booking_intent_id ) {
        // Update status
        BookingIntent::update_status( $booking_intent_id, 'succeeded' );
    }
}