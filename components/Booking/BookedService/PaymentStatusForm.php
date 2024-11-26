<?php
namespace BuddyClients\Components\Booking\BookedService;

use BuddyClients\Includes\Form\Form as Form;

/**
 * Payment status form.
 * 
 * Generates a form to allow the admin to update the status of a payment.
 *
 * @since 0.1.0
 */
class PaymentStatusForm {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        
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
            'key'                   => 'update_payment_status',
            'fields_callback'       => [$this, 'form_fields'],
            'submission_class'      => __NAMESPACE__ . '\PaymentStatusSubmission',
            'submit_text'           => __( 'Update', 'buddyclients-free' ),
            'submit_classes'        => 'button action',
            'form_classes'          => 'buddyc-table-form',
            'values'                => $values
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
         return [
             'payment_status' => [
                'key'           => 'payment_status',
                'type'          => 'dropdown',
                'options'       => [
                    'pending' => [
                        'label' => __( 'Pending', 'buddyclients-free' ),
                        'value' => 'pending',
                    ],
                    'eligible' => [
                            'label' => __( 'Eligible', 'buddyclients-free' ),
                            'value' => 'eligible',
                        ],
                    'paid' => [
                            'label' => __( 'Paid', 'buddyclients-free' ),
                            'value' => 'paid',
                        ],
                    ],
                'value'         => $values['payment_status'],
                'required'      => true
            ],
             'payment_id' => [
                'key'           => 'payment_id',
                'type'          => 'hidden',
                'value'         => $values['payment_id']
            ],
        ];
    }
}