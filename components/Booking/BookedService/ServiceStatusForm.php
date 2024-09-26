<?php
namespace BuddyClients\Components\Booking\BookedService;

use BuddyClients\Includes\{
    Form\Form          as Form
};

/**
 * Update service status form.
 * 
 * Generates a form to update a the status of a booked service.
 *
 * @since 0.1.0
 */
class ServiceStatusForm {
    
    /**
     * Button class.
     * 
     * @var string
     */
    public $button_class;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        $this->button_class = is_admin() ? ' button-secondary' : '';
    }
    
    /**
     * Builds the form.
     * 
     * @since 0.1.0
     * 
     * @param   array   $values     Optional. An array of values to populate the field.
     */
    public function build( $values = null ) {
        $args = [
            'key'               => 'update_service_status',
            'fields_callback'   => [$this, 'form_fields'],
            'submission_class'  => __NAMESPACE__ . '\ServiceStatusSubmission',
            'submit_text'       => __( 'Update Status', 'buddyclients' ),
            'submit_classes'    => 'button action button-secondary',
            'form_classes'      => 'bc-table-form',
            'values'            => $values
        ];
        
        return ( new Form( $args ) )->build();
    }
    
    
    /**
     * Creates the form field args.
     * 
     * @since 0.1.0
     * 
     * @param   array   $values     Optional. An array of values to populate the field.
     */
    public function form_fields( $values = null ) {
        
        // Status dropdown options
        $options = [
            'pending' => [
                'label' => __( 'Pending', 'buddyclients' ),
                'value' => 'pending',
            ],
            'in_progress' => [
                'label' => __( 'In Progress', 'buddyclients' ),
                'value' => 'in_progress',
            ],
            'complete' => [
                'label' => __( 'Complete', 'buddyclients' ),
                'value' => 'complete',
            ],
            'cancellation_requested' => [
                'label' => __( 'Cancellation Requested', 'buddyclients' ),
                'value' => 'cancellation_requested',
            ],
            'canceled' => [
                'label' => __( 'Canceled', 'buddyclients' ),
                'value' => 'canceled',
            ],
        ];
        
        // Status dropdown
         $args = [
             'update_status' => [
                'key'           => 'update_status',
                'type'          => 'dropdown',
                'options'       => $options,
                'value'         => $values['update_status'] ?? '',
                'required'      => true
            ],
            'booked_service_id' => [
                'key'           => 'booked_service_id',
                'type'          => 'hidden',
                'value'         => $values['booked_service_id'] ?? '',
                'required'      => true
            ]
        ];
        
        return $args;
    }
}