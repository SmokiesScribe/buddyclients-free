<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles submission of the update payment status form.
 * 
 * Updates the status of the payment.
 *
 * @since 0.1.0
 * 
 * @see PaymentStatusForm
 */
class PaymentStatusSubmission {
    
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
        $new_status = $post_data['payment_status'];
        $payment_id = $post_data['payment_id'];
        
        // Update status
        Payment::update_status( $payment_id, $new_status );

        // Redirect to the same page
        $curr_url = buddyc_curr_url();
        wp_redirect( $curr_url );
        exit;
    }
}
    