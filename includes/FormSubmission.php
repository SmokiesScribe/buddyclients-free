<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Form submission.
 * 
 * Handles all form submissions.
 *
 * @since 0.1.0
 */
class FormSubmission {
    
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

        // Make sure it's a plugin submission
        if ( ! isset( $_POST['buddyc_submission'] ) ) {
            return;
        }

        // Get form key
        $this->form_key = $this->get_form_key();
        if ( ! $this->form_key ) return;

        // Verify nonce
        $verified = $this->verify_nonce();
        if ( ! $verified ) return;
        
        // Check for spam
        if ( $this->is_spam() ) {
            $this->failure_message();
            return;
        }
        
        // Handle submission
        $this->handle_submission();
    }

    /**
     * Retrieves the form key from the post data. 
     * 
     * @since 1.0.32
     */
    private function get_form_key(){
        $field_name = 'buddyc_form_key';
        if ( isset( $_POST[$field_name] ) ) {
            return sanitize_text_field( wp_unslash( $_POST[$field_name] ) );
        }
    }

    /**
     * Verifies form submission and nonce.
     * 
     * @since 1.0.17
     * 
     * @return  bool  True on success, false on failure
     */
    private function verify_nonce() {

        // Build nonce field name
        $nonce_name = $this->build_nonce_name();
        
        // Get nonce value
        $nonce_value = $this->get_nonce_value( $nonce_name );

        // Verify nonce and return result
        return wp_verify_nonce( $nonce_value, $this->form_key );
    }

    /**
     * Retrieves and sanitizes the nonce value.
     * 
     * @since 1.0.17
     * 
     * @param   string  nonce_name  The name of the nonce field.
     */
    private function get_nonce_value( $nonce_name ) {
        if ( isset( $_POST[$nonce_name] ) ) {
            return sanitize_text_field( wp_unslash( $_POST[$nonce_name] ) ); 
        }
    }

    /**
     * Builds the nonce field name.
     * 
     * @since 1.0.17
     * @since 1.0.32 Use form key property.
     */
    private function build_nonce_name() {
        return sprintf(
            'buddyclients_%s_nonce',
            $this->form_key
        );
    }

    /**
     * Checks for spam submissions.
     * 
     * Checks the honeypot field and the reCAPTCHA result.
     * 
     * @since 0.1.0
     * 
     * @return  bool    True if spam, false if not.
     */
    private function is_spam() {
        // No spam in admin area
        if ( is_admin() ) return false;
        
        // Check for honeypot submission
        if ( isset( $_POST['website'] ) && ! empty( $_POST['website'] ) ) {
            return true;
        }

        // Check reCAPTCHA
        if ( ! $this->verify_recaptcha() ) {
            return true;
        }
        
        // Five by five
        return false;
    }

    /**
     * Verifies the reCAPTCHA.
     * 
     * @since 1.0.25
     */
    function verify_recaptcha() {

        // Return true if not enabled
        $enabled = buddyc_recaptcha_enabled();
        if ( ! $enabled ) {
            return true;
        }

        // reCAPTCHA settings
        $secret_key = buddyc_recaptcha_secret_key();
        $threshold = buddyc_recaptcha_threshold();

        // reCAPTCHA response

        if ( ! $recaptcha_response ) {
            $recaptcha_response = isset( $_POST['recaptcha_response'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['recaptcha_response'] ) ) ) : null;
            return false; // No response, so reject
        }
        
        // reCAPTCHA verification URL
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';

        // Make the POST request to verify the reCAPTCHA response
        $response = wp_remote_post( $verify_url, [
            'body' => [
                'secret'   => $secret_key,
                'response' => $recaptcha_response,
            ]
        ]);

        // Check for errors in the response
        if ( is_wp_error( $response ) ) {
            return false; // Return false if request failed
        }

        // Get the response body
        $response_body = wp_remote_retrieve_body( $response );

        // Decode the response
        $result = json_decode( $response_body );

        // Check if the score is above the threshold
        if ( isset( $result->score ) && $result->score > $threshold ) {
            return true; // reCAPTCHA passed
        } else {
            return false; // reCAPTCHA failed
        }
    }
    
    /**
     * Generates a generic failure message.
     * 
     * @since 0.1.0
     */
    private function failure_message() {
        // Generate container to align center
        $content = '<div class="buddyc-text-center">';
        
        // Define failure content
        $content .= '<h2>' . __( 'Uh oh, something went wrong.', 'buddyclients-lite' ) . '</h2>';
        $content .= '<p>' . __( 'Please try again later.', 'buddyclients-lite' ) . '</p>';
        
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