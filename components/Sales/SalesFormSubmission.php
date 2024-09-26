<?php
namespace BuddyClients\Components\Sales;

/**
 * Sales form submission.
 * 
 * Handles a submission of the assisted booking form.
 * Processes form submission and redirects to the booking form
 * to allow the user to complete the manual/assisted booking.
 *
 * @since 0.1.0
 * 
 * @see SalesForm
 */
class SalesFormSubmission {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data ) {
        // Redirect to checkout
        $this->redirect( $post_data );
    }
    
    /**
     * Adds url params and redirects.
     * 
     * @since 0.1.0
     */
    private function redirect( $post_data ) {

        // Get the current URL
        $current_url = $_SERVER['REQUEST_URI'];
        
        // Add URL parameters
        $sales_id = $post_data['sales_id'] ?? null;
        $prev_paid = $post_data['previously_paid'][0] ?? null;
        $sales_client_id = $post_data['sales_client_id'] ?? null;
        $sales_client_email = $post_data['sales_client_email'] ?? null;
        
        // Prepare the URL parameters string
        $params = http_build_query([
            'sales_id' => $sales_id,
            'prev_paid' => $prev_paid,
            'sales_client_id' => $sales_client_id,
            'sales_client_email' => $sales_client_email
        ]);
        
        // Append the parameters to the current URL
        $redirect_url = $current_url . '?' . $params;
        
        // Redirect
        header('Location: ' . $redirect_url);
        exit;
    }
}
    