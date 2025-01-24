<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Form submission.
 * 
 * Handles all form submissions.
 *
 * @since 0.1.0
 * 
 * @todo Review warning: Processing form data without nonce verification.
 */
class FormSubmission {

    /**
     * The nonce prefix.
     * 
     * @var string
     */
    private $nonce_prefix = 'buddyclients_';

    /**
     * The nonce suffix.
     * 
     * @var string
     */
    private $nonce_suffix = '_nonce';
    
    /**
     * Form key.
     * 
     * @var string
     */
    public $form_key;

    /**
     * Nonce field name.
     * 
     * @var string
     */
    private $nonce_field_name;

    /**
     * Nonce action name.
     * 
     * @var string
     */
    private $nonce_action_name;

    /**
     * The sanitized nonce value.
     * 
     * @string
     */
    private $nonce_value;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        if ( ! isset( $_POST ) || empty( $_POST ) ) {
            return;
        }

        // Retrieve nonce info
        $this->handle_nonce();

        // Exit if nonce not verified
        if ( ! wp_verify_nonce( $this->nonce_value, $this->form_key ) ) {
            return;
        }

        // Make sure it's a plugin submission
        if ( ! isset( $_POST['buddyc_submission'] ) ) {
            return;
        }
        
        // Check for spam
        if ( $this->is_spam() ) {
            $this->failure_message();
            return;
        }
        
        // Handle submission
        $this->handle_submission();
    }

    /**
     * Verifies form submission and nonce.
     * 
     * @since 1.0.17
     * 
     * @return  string|bool  The form key on success, false on failure
     */
    private function handle_nonce() {
        // Check for nonce field
        $this->nonce_field_name = $this->get_nonce_field();
        
        if ( $this->nonce_field_name ) {
            $this->form_key = $this->extract_form_key( $this->nonce_field_name );
            $this->nonce_value = $this->get_nonce_value( $this->nonce_field_name ); 
        }
    }

    /**
     * Retrieves and sanitizes the nonce value.
     * 
     * @since 1.0.17
     */
    private function get_nonce_value( $nonce_field_name ) {
        if ( isset( $_POST[$nonce_field_name] ) ) {
            return sanitize_text_field( wp_unslash( $_POST[$nonce_field_name] ) ); 
        }
    }

    /**
     * Retrieves the nonce field name from post data.
     * 
     * @since 1.0.17
     */
    private function get_nonce_field() {        
        foreach ( array_keys( $_POST ) as $key ) {
            $key = sanitize_text_field( $key );
            if ( strpos( $key, $this->nonce_prefix ) === 0 && strpos( $key, $this->nonce_suffix ) === strlen( $key ) - strlen( $this->nonce_suffix ) ) {
                return $key;
            }
        }
    }

    /**
     * Extracts the form key from the nonce field name
     * 
     * @since 1.0.17
     * 
     * @param   string  $nonce_field_name   The name of the nonce field.
     */
    private function extract_form_key( $nonce_field_name ) {
        if ( ! empty( $nonce_field_name ) ) {
            return str_replace( [ $this->nonce_prefix, $this->nonce_suffix ], '', $nonce_field_name );
        }
    }

    /**
     * Verifies the nonce.
     * 
     * @since 1.0.17
     * 
     * @param   string  $nonce_field_name   The name of the nonce field.
     * @param   string  $nonce_action       The name of the nonce action.
     */
    private function verify_nonce( $nonce_field_name, $nonce_action ) {
        if ( isset( $_POST[$nonce_field_name] ) ) {
            // Unslash the nonce value
            $nonce_value = sanitize_text_field( wp_unslash( $_POST[$nonce_field_name] ) );

            // Sanitize the nonce value
            $sanitized_nonce = sanitize_text_field( wp_unslash( $_POST[$nonce_field_name] ) );

            // Verify the nonce
            if ( wp_verify_nonce( $sanitized_nonce, $nonce_action ) ) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Builds the nonce field name.
     * 
     * @since 1.0.17
     * 
     * @param   string  $form_key   The form key.
     */
    private function build_nonce_name( $form_key ) {
        return $this->nonce_prefix . $form_key . $this->nonce_suffix;
    }
    
    /**
     * Checks for honeypot to filter spam.
     * 
     * @since 0.1.0
     */
    private function is_spam() {
        if ( wp_verify_nonce( $this->nonce_value, $this->form_key ) ) {
            // Check for honeypot submission
            if ( isset( $_POST['website'] ) && ! empty( $_POST['website'] ) ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Generates a generic failure message.
     * 
     * @since 0.1.0
     */
    private function failure_message() {
        // Generate container to align center
        $content = '<div style="text-align: center">';
        
        // Define failure content
        $content .= '<h2>' . __( 'Uh oh, something went wrong.', 'buddyclients-free' ) . '</h2>';
        $content .= '<p>' . __( 'Please try again later.', 'buddyclients-free' ) . '</p>';
        
        $content .= '</div>';
        
        // Update popup
        buddyc_update_popup( $content );
    }
    
    /**
     * Handles submissions.
     * 
     * Creates a new instance of the class passed in the Form field.
     * 
     * @since 0.1.0
     */
    private function handle_submission() {
        
        // Check if nonce is set and verified
        if ( wp_verify_nonce( $this->nonce_value, $this->form_key ) ) {

            // Retrieve callback class
            if ( isset( $_POST['submission_class'] ) ) {
                $submission_class = sanitize_text_field( wp_unslash( $_POST['submission_class'] ) );
                
                // Make sure the class exists
                if ( class_exists( $submission_class ) ) {
                    // New instance
                    new $submission_class( $_POST, $_FILES );
                }
            }
        }
    }
}