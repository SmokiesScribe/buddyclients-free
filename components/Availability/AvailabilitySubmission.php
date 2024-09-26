<?php
namespace BuddyClients\Components\Availability;

/**
 * Handles availability form submission.
 * 
 * Updates the user's availability based on the form submission.
 * 
 * @since 0.1.0
 */
class AvailabilitySubmission {
    
    /**
     * Constructor method.
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     * 
     * @since 0.1.0
     */
    public function __construct(array $post_data, ?array $files_data) {
        session_start();
        
        $this->handle_form_submission( $post_data );
    }
    
    /**
     * Handles form submission.
     * 
     * @param array $post_data The POST data.
     * 
     * @since 0.1.0
     */
    private function handle_form_submission( array $post_data ): void {
        
        // Get var
        $user_id = $post_data['user_id'];
        $available_date = $post_data['available_date'] ?? null;
        $available_immediately = $post_data['available_immediately'] ?? null;
        
        // Define availability
        $availability = $available_immediately ?? ( $available_date ?? null );
        
        // Update user meta
        if ( $availability ) {
            Availability::update_availability( $user_id, $availability );
        }
        
        // Refresh the page
        $current_page_uri = $_SERVER['REQUEST_URI'];
        header("Location: $current_page_uri");
        exit;
    }
}