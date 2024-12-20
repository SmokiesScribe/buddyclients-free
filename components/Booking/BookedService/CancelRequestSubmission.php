<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Cancellation request submission.
 * 
 * Handles submission of the service cancellation request form.
 * Updates the service status. Notifies the admin of the request.
 *
 * @since 0.1.0
 * 
 * @see CancelRequestForm
 */
class CancelRequestSubmission {
    
    /**
     * The ID of the BookedService.
     * 
     * @var int
     */
    public $booked_service_id;
    
    /**
     * The cancellation reason.
     * 
     * @var string
     */
    public $cancellation_reason;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data ) {
        $this->request_cancel( $post_data );
    }
    
    /**
     * Submits a CancelRequest.
     * 
     * @since 0.1.0
     */
    private function request_cancel( $post_data ) {
        
        // Get variables
        $this->booked_service_id = $post_data['booked_service_id'];
        $this->cancellation_reason = $post_data['cancellation_reason'];
        
        // Update status to cancellation requested
        BookedService::update_status( $this->booked_service_id, 'cancellation_requested' );
        
        // Update cancellation reason
        BookedService::update_cancellation_reason( $this->booked_service_id, $this->cancellation_reason );
        
        /**
         * Fires on new cancellation request.
         * 
         * @since 0.1.0
         * 
         * @param   CancelRequest   The CancelRequest object.
         */
        do_action( 'buddyc_cancel_request', $this );
    }
}
    