<?php
namespace BuddyClients\Includes\Form;

use BuddyClients\Includes\Popup;

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
     * Form key.
     * 
     * @var string
     */
    public $form_key;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        
        // Check for submission
        if ( isset( $_POST['bc_submission'] ) ) {
            $this->form_key = sanitize_text_field( wp_unslash( $_POST['bc_submission'] ) );

            // Verify nonce
            if ( $this->check_nonce() ) {
            
                // Check for spam
                if ( $this->is_spam() ) {
                    $this->failure_message();
                    return;
                }
                
                // Handle submission
                $this->handle_submission();
            }
        }
    }
    
    /**
     * Checks nonce.
     * 
     * @since 0.1.0
     */
    private function check_nonce() {
        $nonce = new Nonce( $this->form_key );
        return $nonce->check();
    }
    
    /**
     * Checks for honeypot to filter spam.
     * 
     * @since 0.1.0
     */
    private function is_spam() {
        if ( isset( $_POST['website'] ) && ! empty( $_POST['website'] ) ) {
            return true;
        } else {
            return false;
        }
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
        $content .= '<h2>' . __( 'Uh oh, something went wrong.', 'buddyclients' ) . '</h2>';
        $content .= '<p>' . __( 'Please try again later.', 'buddyclients' ) . '</p>';
        
        $content .= '</div>';
        
        // Get popup instance
        $popup = Popup::get_instance();
        
        // Modify content and set visibility
		$popup->update_content( $content );
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
        if ( isset($_POST['submission_class'] ) ) {
            $this->form_key = sanitize_text_field( wp_unslash( $_POST['submission_class'] ) );
            
            // Make sure the class exists
            if ( class_exists( $submission_class ) ) {
                // New instance
                new $submission_class( $_POST, $_FILES );
            }
        }
    }
}