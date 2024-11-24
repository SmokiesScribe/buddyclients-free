<?php
namespace BuddyClients\Includes\Form;

/**
 * Form.
 * 
 * Generates the form structure.
 *
 * @since 0.1.0
 */
class Form {
    
    /**
     * The form key.
     * 
     * @var string
     */
    private $key;
    
    /**
     * An array of args to build the form.
     * 
     * @var array
     */
    private $args;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   array       $args {
     *     An array of arguments to create the form.
     * 
     *     @type    string      $key                    The form key.
     *     @type    callable    $fields_callback        The callback to generate the form fields.
     *     @type    string      $submission_class       The class that handles the form submission.
     *     @type    bool        $submit_button          Optional. Whether to include a submit button.
     *     @type    string      $submit_text            Optional. The text of the submit button.
     *                                                  Defaults to 'Submit'.
     *     @type    string      $submit_classes         Optional. Classes to apply to the submit button.
     *     @type    array       $values                 Optional. A keyed array of values to populate the form fields.
     *     @type    int         $avatar                 Optional. Creates a user avatar above the form.
     *     @type    string      $form_classes           Optional. Classes to apply to the form.
     *                          
     * }
     */
    public function __construct( $args ) { 
        $this->args = $args;
        $this->key = $args['key'];
    }
    
    /**
     * Echoes the form.
     * 
     * @since 1.0.16
     */
    public function echo() {
        $form = $this->build();
        $allowed_html = buddyc_allowed_html_form();
        echo wp_kses( $form, $allowed_html );
        return;
    }

    /**
     * Builds the form.
     * 
     * @since 0.1.0
     */
    public function build() {        
        
        // Initialize form container and form tag
        $form = '<div id="bc-' . $this->key . '-form-container" class="bc-form-container">';
        $form .= '<form id="bc-' . $this->key . '-form" method="post" enctype="multipart/form-data" class="bc-form ' . ( $this->args['form_classes'] ?? '' ) . '">';
        
        // User avatar
        $form .= $this->user_avatar();
        
        // Form title
        $form .= ( isset( $this->args['title'] ) && $this->args['title'] ) ? '<h3>' . $this->args['title'] . '</h3>' : '';
        
        // Call form fields method
        if ( is_callable( $this->args['fields_callback'] ) ) {
            
            // Retrieve args from callback
            $values = $this->args['values'] ?? '';
            if ( isset( $this->args['fields_callback'] ) && ! empty( $this->args['fields_callback'] ) ) {
                if ( is_callable( $this->args['fields_callback'] ) ) {
                    $args = call_user_func( $this->args['fields_callback'], $values ); // THIS LINE CAUSING ERROR
                }
            }
            
            // Create fields
            foreach ( $args as $single_args ) {
                $form .= $this->add_form_field( $single_args );
            }
        }
        
        // Verification field
        $form .= $this->verification_field();
        
        // Callback field
        $form .= $this->submission_class_field();
        
        // Honeypot field
        $form .= $this->honeypot_field();
        
        // Nonce
        $form .= $this->nonce_field();
        
        // Submit button
        $form .= $this->submit_field();
        
        // Close the form tag and form container
        $form .= '</form>';
        $form .= '</div>';
        
        return $form;
    }
    
    /**
     * Generates the avatar html.
     * 
     * @since 0.1.0
     */
    private function user_avatar() {
        
        // Check if avatar param is set
        if ( isset( $this->args['avatar'] ) ) {
            
            // Initialize
            $content = '';
            
            // Get avatar id from args
            $user_id = $this->args['avatar'];
            
            // Check if it's a valid ID
            if ( get_user_by( 'id', $user_id ) ) {
                $user_name = bp_core_get_user_displayname( $user_id );
            } else {
                $user_name = 'Guest';
            }
            
        $content .= '<div class="current-user form-group">';
        $content .= '<div class="bc-form-user-profile">';
        $content .= '<div class="bc-form-user-avatar">' . get_avatar( $user_id, 40 ) . '</div>';
        $content .= '<div class="bc-form-user-name">' . $user_name . '</div>';
        $content .= '</div>';
        $content .= '</div>';
        
        return $content;
            
        }
    }
    
    /**
     * Adds a FormField.
     * 
     * @since 0.1.0
     */
    private function add_form_field( $args ) {
        if ( ! empty( $args ) ) {
            $form_field = new FormField( $args );
            return $form_field->build();
        }
    }
    
    /**
     * Verification field.
     *
     * @since 0.1.0
     */
    private function verification_field() {
        $args = [
            'key'           => $this->key,
            'type'          => 'verify',
        ];
        
        // Add the field to the content
        return ( new FormField( $args ) )->build();
    }
    
    /**
     * Creates a field to pass the callback for handling submission.
     *
     * @since 0.1.0
     */
    private function submission_class_field() {
        $args = [
            'key'           => 'submission_class',
            'id'            => $this->key . '_submission_class',
            'type'          => 'hidden',
            'value'         => $this->args['submission_class']
        ];
        
        // Add the field to the content
        return ( new FormField( $args ) )->build();
    }
    
    /**
     * Creates a honeypot field to stop spammers.
     *
     * @since 0.1.0
     */
    private function honeypot_field() {
        $args = [
            'key'           => 'website',
            'id'            => $this->key . '-website',
            'type'          => 'text',
            'style'         => 'display: none;'
        ];
        
        // Add the field to the content
        return ( new FormField( $args ) )->build();
    }

    /**
     * Creates a nonce field.
     *
     * @since 1.0.4
     */
    private function nonce_field() {
        $args = [
            'key'           => $this->key,
            'type'          => 'nonce',
        ];
        
        // Add the field to the content
        return ( new FormField( $args ) )->build();
    }
    
    /**
     * Submit button.
     *
     * @since 0.1.0
     */
    private function submit_field() {
        
        $submit_button = $this->args['submit_button'] ?? true;
        $submit_text = $this->args['submit_text'] ?? 'Submit';
        $submit_classes = $this->args['submit_classes'] ?? '';
        
        if ( $submit_button ) {
            $args = [
                'key'           => $this->key . '-submit',
                'type'          => 'submit',
                'value'         => $submit_text,
                'field_classes' => $submit_classes,
            ];
            
            // Add the field to the content
            return ( new FormField( $args ) )->build();
        }
    }
}