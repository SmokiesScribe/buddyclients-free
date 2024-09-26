<?php
namespace BuddyClients\Components\Legal;

use BuddyClients\Includes\File as File;

/**
 * Legal agreement form submission.
 * 
 * Handles the submission of a user's legal agreement.
 * Processes the signature and updates the user data.
 *
 * @since 0.1.0
 * 
 * @see LegalForm
 */
class LegalSubmission {
    
    /**
     * Legal instance.
     * 
     * @var Legal
     */
    private $legal;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data ) {
        
        // Legal instance
        $this->legal = new Legal( $post_data['legal_type'], $post_data['user_id'] );
        
        // Process signature
        $this->signature_file_id = $this->process_signature( $post_data );
        
        // Update user data
        $this->update_user_data( $post_data, $this->signature_file_id );
        
        if ( $post_data['legal_type'] === 'affiliate' && ! $post_data['user_status'] ) {
        
            /**
             * Fires when user joins the affiliate program.
             * 
             * @since 0.1.0
             * 
             * @param   int $user_id    The ID of the new affiliate.
             */
            do_action( 'bc_new_affiliate', $post_data['user_id'] );
        }
    }
    
    /**
     * Handles signature data.
     * 
     * @since 0.1.0
     */
    private function process_signature( $post_data ) {
        
        if ( ! isset( $post_data['signature-data'] ) ) {
            return;
        }
        
        // Get signature data
        $signature_data = $post_data['signature-data'];
        
        // Get date for filename
        $date = date('YmdHis');
        
        // Convert name to string for filename
        $formatted_legal_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $post_data['legal_name']));
        
        // Remove the data URI prefix (e.g., 'data:image/png;base64,')
        $signature_data = preg_replace('#^data:image/\w+;base64,#i', '', $signature_data);
        
        // Decode the base64-encoded signature data
        $decoded_signature = base64_decode($signature_data);
    
        // Generate a unique file name for the signature image
        $file_name = $formatted_legal_name . '_' . $date . '.png';
        
        // Save the signature image on the server
        $file = new File;
        $signature_id = $file->upload_signature( $decoded_signature, $file_name, $post_data['user_id'] );
        $this->signature_file_path = $file->file_path;
        
        // Return image path
        return $signature_id;
    }
    
    /**
     * Updates the user's legal data.
     * 
     * @since 0.1.0
     * 
     * @param   array   $post_data              The array of global $_POST data.
     * @param   string  $signature_file_id      Optional. The ID of the signature file.
     */
    private function update_user_data( $post_data, $signature_file_id ) {
        
        // Define data
        $data = [
            'signature'         => $signature_file_id,
            'version'           => $post_data['version'],
            'name'              => $post_data['legal_name'] ?? null,
            'email'             => $post_data['legal_email'] ?? null,
            'payment_method'    => $post_data['payment_method'] ?? null,
            'date'              => time(),
            'pdf'               => $this->generate_pdf( $post_data )
        ];
        
        // Update the user's legal data
        $this->legal->update_user_data( $post_data['user_id'], $data );
    }
    
    /**
     * Generates a PDF from the form data.
     * 
     * @since 0.4.0
     */
    private function generate_pdf( $post_data ) {
        // Define args
        $args = [
            'content'       => $this->legal->content,
            'user_id'       => $this->legal->user_id ?? null,
            'type'          => $this->legal->type ?? null,
            'title'         => $this->legal->title,
            'image_path'    => $this->signature_file_path ?? null,
            'items'         => [$post_data['legal_name'], date('F d, Y')],
        ];
        
        // Create PDF
        return bc_create_pdf( $args );
    } 
}
    