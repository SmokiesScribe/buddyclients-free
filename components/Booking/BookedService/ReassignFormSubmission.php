<?php
namespace BuddyClients\Components\Booking\BookedService;

/**
 * Update service status submission.
 * 
 * Handles form submission to update a BookedService status.
 *
 * @since 0.1.0
 */
class ReassignFormSubmission {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data ) {
        $this->reassign( $post_data );
    }
    
    /**
     * Updates the BookedService team member.
     * 
     * @since 0.1.0
     */
    private function reassign( $post_data ) {
        
        // Get variables
        $new_team_id = $post_data['team_id'];
        $booked_service_id = $post_data['booked_service_id'];
        
        // Update status
        BookedService::update_team_id( $booked_service_id, $new_team_id );
    }
}
    