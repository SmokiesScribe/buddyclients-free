<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
            'submit_text'           => __( 'Update', 'buddyclients-lite' ),
            'submit_classes'        => 'button action',
            'form_classes'          => 'buddyc-table-form',
            'values'                => $values
        ];
        
        return buddyc_build_form( $args );
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
                        'label' => __( 'Pending', 'buddyclients-lite' ),
                        'value' => 'pending',
                    ],
                    'eligible' => [
                            'label' => __( 'Eligible', 'buddyclients-lite' ),
                            'value' => 'eligible',
                        ],
                    'paid' => [
                            'label' => __( 'Paid', 'buddyclients-lite' ),
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