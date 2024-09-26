<?php
namespace BuddyClients\Components\Testimonial;

use BuddyClients\Includes\{
    Form\Form          as Form
};

/**
 * Testimonial form content.
 * 
 * Generates a form for users to submit a testimonial.
 *
 * @since 0.1.0
 */
class TestimonialForm {
    
    /**
     * Builds the form.
     * 
     * @since 0.1.0
     * 
     * @param   array   $values     Optional. An array of values to populate the field.
     */
    public function build( $values = null ) {
        
        if ( isset( $_POST['testimonial_content'] ) && isset( $_POST['accept_terms'] ) ) {
            echo '<div class="bc-thank-you-message"><p>Thank you for submitting your testimonial!</p></div>';
            return;
        } else if ( isset( $_POST['testimonial_content'] ) && ! isset( $_POST['accept_terms'] ) ) {
            echo '<script>alert("Please accept the terms.");</script>';
        }
        
        if ( ! is_user_logged_in() ) {
            return 'Please log in to leave a testimonial.';
        }
        
        $args = [
            'key'               => 'testimonial',
            'fields_callback'   => [$this, 'form_fields'],
            'submit_text'       => __ ( 'Submit Testimonial', 'buddyclients' ),
            'submission_class'  => __NAMESPACE__ . '\TestimonialSubmission',
            'title'             => __( 'Submit a Testimonial', 'buddyclients' ),
            'avatar'            => get_current_user_id()
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
            'testimonial' => [
                'key'           => 'testimonial_content',
                'type'          => 'editor',
                'description'   => sprintf(
                    __('How was your experience working with %s? Consider what you would say to someone who is considering working with us.', 'buddyclients'),
                    get_bloginfo('blogname')
                ),
                'placeholder'   => '',
                'rows'          => 6,
                'required'      => true
            ],
            'name' => [
                'key'           => 'testimonial_name',
                'type'          => 'text',
                'label'         => __('Your Name', 'buddyclients'),
                'description'   => __('What name should we include with your testimonial?', 'buddyclients'),
                'value'         => bp_core_get_user_displayname( get_current_user_id() )
            ],
            'accept' => [
                'key'           => 'terms',
                'type'          => 'checkbox',
                'required'      => true,
                'label'         => '',
                'options'       => [
                    'accept_terms' => [
                        'label'     => sprintf(
                            __('I agree to allow %s to publish my testimonial on %s. %s may edit for grammatical correctness.', 'buddyclients'),
                            get_bloginfo('blogname'),
                            site_url(),
                            get_bloginfo('blogname')
                        ),
                        'value'     => true,
                        'classes'   => 'bc_description_checkbox'
                    ]
                ]
            ],
            'client_id' => [
                'key'           => 'client_id',
                'type'          => 'hidden',
                'value'         => get_current_user_id(),
            ]
        ];
    }
}