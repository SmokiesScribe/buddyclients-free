<?php
namespace BuddyClients\Components\Contact;

use BuddyClients\Components\Email\Email as Email;
use BuddyClients\Includes\Popup as Popup;

/**
 * Contact form submission.
 * 
 * Handles submission of the contact form.
 * Notifies the user and the admin.
 *
 * @since 0.1.0
 * 
 * @see ContactForm
 */
class ContactSubmission {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data = null ) {
        
        // Email user
        $user_emailed = $this->email_user( $post_data );
        
        // Email admin
        $admin_emailed = $this->email_admin( $post_data );
        
		// Confirmation popup
		$this->confirmation_popup( $admin_emailed );
    }
    
    /**
     * Generates the confirmation popup.
     * 
     * @since 0.1.0
     * 
     * @param   bool    $success    Whether the admin email was sent successfully.
     */
    private function confirmation_popup( $success = false ) {
        
        // Generate container to align center
        $content = '<div style="text-align: center">';
        
        // Define success content
        $success_content = '<h2>' . __( 'Success!', 'buddyclients' ) . '</h2>';
        $success_content .= '<p>' . sprintf(
            __( 'Thank you for contacting %s. We will return your message as soon as possible.', 'buddyclients' ),
            get_option('blogname')
        ) . '</p>';
        
        // Define failure content
        $failure_content = '<h2>' . __( 'Uh oh, something went wrong.', 'buddyclients' ) . '</h2>';
        $failure_content .= '<p>' . sprintf(
            __( 'Please try again or email us at %s.', 'buddyclients' ),
            bc_get_setting('email', 'notification_email')
        ) . '</p>';
        
        // Define content
        $content .= $success ? $success_content : $failure_content;
        $content .= '</div>';
        
        // Get popup instance
        $popup = Popup::get_instance();
        
        // Modify content and set visibility
        $popup->update_content( $content );
    }
    
    /**
     * Emails the user.
     * 
     * @since 0.1.0
     */
    private function email_user( $post_data ) {
        
        // Build email args
        $args = [
            'to_email'      => sanitize_email( $post_data['contact_email'] ),
            'to_user_id'    => absint( $post_data['user_id'] ),
            'client_name'   => sanitize_text_field( $post_data['contact_name'] ),
            'user_email'    => sanitize_email( $post_data['contact_email'] ),
            'message'       => sanitize_textarea_field( $post_data['contact_message'] ),
        ];
        
        // Send and log email
        $email = new Email( 'contact_form_confirmation', $args );
        
        return $email->sent;
    }
    
    /**
     * Emails the admin.
     * 
     * @since 0.1.0
     */
    private function email_admin( $post_data ) {
        
        // Build email args
        $args = [
            'to_email'      => 'admin', // Static value, no sanitization needed
            'client_name'   => sanitize_text_field( $post_data['contact_name'] ),
            'reply_to'      => sanitize_email( $post_data['contact_email'] ),
            'message'       => sanitize_textarea_field( $post_data['contact_message'] ),
        ];
        
        // Send and log email
        $email = new Email( 'contact_form_admin', $args );
        
        return $email->sent;
    }
}
    