<?php
namespace BuddyClients\Components\Booking\BookedService;

use BuddyClients\Includes\{
    Form\Form as Form
};

/**
 * Cancellation request form.
 * 
 * Generates a form to allow clients to request to cancel a service.
 * Only displays if the service is within the cancellation window.
 *
 * @since 0.1.0
 */
class CancelRequestForm {
    
    /**
     * The ID of the BookedService.
     * 
     * @var int
     */
    public $booked_service_id;
    
    /**
     * The status of the BookedService.
     * 
     * @var string
     */
    public $status;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   int     $booked_service_id      The ID of the BookedService object.
     */
    public function __construct( $booked_service_id ) {
        $this->booked_service_id = $booked_service_id;
        $this->status = BookedService::get_status( $booked_service_id );
    }
    
    /**
     * Builds the form.
     * 
     * @since 0.1.0
     */
    public function build() {
        
        // Check if cancellation has already been requested
        if ( $this->status === 'cancellation_requested' ) {
            return __( 'Cancellation requested.', 'buddyclients-free' );
        }
        
        // Check if cancellation is allowed
        if ( ! $this->cancellation_allowed() ) {
            return '-';
        }
        
        // Otherwise build form
        $args = [
            'key'               => 'cancel_service',
            'fields_callback'   => [$this, 'form_fields'],
            'submit_text'       => __( 'Request Cancellation', 'buddyclients-free' ),
            'submission_class'  => __NAMESPACE__ . '\CancelRequestSubmission',
            'form_classes'      => 'buddyc-table-form'
        ];

        $form = new Form( $args );
        $content = $form->build();
        return $content;
    }
    
    /**
     * Checks if cancellation is allowed.
     * 
     * @since 0.1.0
     */
    private function cancellation_allowed() {
        // Get cancellation window setting
        $cancellation_window = buddyc_get_setting( 'booking', 'cancellation_window' );
        
        // Get booked time
        $created_at = BookedService::get_created_at( $this->booked_service_id );
        
        if ( ! $created_at ) {
            return '';
        }
        
        $booked_time = strtotime( $created_at );
        
        // Get current timestamp
        $current_timestamp = time();
        
        // Calculate the difference in seconds
        $time_difference = $current_timestamp - $booked_time;
        
        // Check if the difference is greater than the number of days
        return $time_difference < $cancellation_window * 24 * 60 * 60;
    }
    
    /**
     * Creates the form field args.
     * 
     * @since 0.1.0
     */
    public function form_fields() {
         return [
             'cancellation_reason' => [
                'key'           => 'cancellation_reason',
                'type'          => 'text',
                'placeholder'   => __( 'Cancellation reason...', 'buddyclients-free' ),
                'required'      => true
            ],
            'booked_service_id' => [
                'key'           => 'booked_service_id',
                'type'          => 'hidden',
                'value'         => $this->booked_service_id,
                'required'      => true
            ]
        ];
    }
}