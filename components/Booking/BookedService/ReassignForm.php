<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Form to reassign services to team members.
 * 
 * Generates a form to reassign a booked service to a new team member.
 *
 * @since 0.1.0
 */
class ReassignForm {
    
    /**
     * Builds the form.
     * 
     * @since 0.1.0
     * 
     * @param   array   $values     Optional. An array of values to populate the field.
     */
    public function build( $values = null ) {
        $args = [
            'key'               => 'reassign',
            'fields_callback'   => [$this, 'form_fields'],
            'submit_text'       => __( 'Reassign', 'buddyclients-lite' ),
            'submit_classes'    => 'button action',
            'form_classes'      => 'buddyc-table-form',
            'values'            => $values
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
        
        $options_args = [
            'format' => 'detail',
            'user_type' => 'team'
        ];
        
        $options = buddyc_options( 'users', $options_args );
        
        // Team dropdown
         $args = [
             'team_id' => [
                'key'           => 'team_id',
                'type'          => 'dropdown',
                'options'       => $options,
                'value'         => $values['team_id'] ?? '',
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