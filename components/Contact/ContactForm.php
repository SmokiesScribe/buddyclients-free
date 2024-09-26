<?php
namespace BuddyClients\Components\Contact;

use BuddyClients\Includes\Form\Form as Form;

/**
 * Contact form content.
 * 
 * Generates the contact form.
 *
 * @since 0.1.0
 */
class ContactForm {
    
    /**
     * Builds the form.
     * 
     * @since 0.1.0
     * 
     * @param   array   $values     Optional. An array of values to populate the field.
     */
    public function build( $values = null ) {
        if ( ! bc_component_enabled( 'Contact' ) ) {
            return bc_contact_message( true );
        }
        
        $args = [
            'key'               => 'contact',
            'fields_callback'   => [$this, 'form_fields'],
            'submission_class'  => __NAMESPACE__ . '\ContactSubmission',
            'title' => sprintf(__('Contact %s', 'buddyclients'), get_option('blogname'))
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
        $user_id = get_current_user_id();
        
         return [
            'contact_name' => [
                'key'           => 'contact_name',
                'type'          => 'text',
                'label'         => __( 'Your Name', 'buddyclients' ),
                'description'   => '',
                'value'         => bp_core_get_user_displayname( $user_id ),
                'required'      => true
            ],
            'contact_email' => [
                'key'           => 'contact_email',
                'type'          => 'email',
                'label'         => __( 'Your Email', 'buddyclients' ),
                'description'   => '',
                'value'         => $user_id ? bp_core_get_user_email( $user_id ) : null,
                'required'      => true
            ],
             'contact_message' => [
                'key'           => 'contact_message',
                'type'          => 'textarea',
                'label'         => __( 'Message', 'buddyclients' ),
                'description'   => '',
                'placeholder'   => '',
                'rows'          => 4,
                'required'      => true
            ],
            'user_id' => [
                'key'           => 'user_id',
                'type'          => 'hidden',
                'value'         => get_current_user_id(),
            ]
        ];
    }
}