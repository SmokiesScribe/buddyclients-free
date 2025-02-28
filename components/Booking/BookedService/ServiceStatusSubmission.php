<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles submission of the update service status form.
 * 
 * Updates the status of the booked service.
 *
 * @since 0.1.0
 * 
 * @see ServiceStatusForm
 */
class ServiceStatusSubmission {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data ) {
        $this->update_status( $post_data );
    }
    
    /**
     * Updates the BookedService status.
     * 
     * @since 0.1.0
     */
    private function update_status( $post_data ) {        
        // Get variables
        $new_status = $post_data['update_status'];
        $booked_service_id = $post_data['booked_service_id'];
        
        // Update status
        BookedService::update_status( $booked_service_id, $new_status );

        // Redirect to the same page
        $curr_url = buddyc_curr_url();
        wp_redirect( $curr_url );
        exit;
    }
}
    