<?php
namespace BuddyClients\Includes;

/**
 * Handles submission of the initial user files deletion form.
 * 
 * Passes the file IDs as URL params to be processed by the final deltion form.
 *
 * @since 1.0.17
 */
class UserFilesSubmission {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data ) {
        $this->process_data( $post_data );
    }
    
    /**
     * Processes the submission.
     * 
     * @since 1.0.17
     * 
     * @param array $post_data The POST data.
     */
    private function process_data( $post_data ) {
        // Initialize
        $file_ids = [];
        $file_names = [];

        // Retrieve the file IDs selected for deletion
        if ( isset( $post_data['user_files'] ) && is_array( $post_data['user_files'] ) ) {
            $post_data = array_map( 'sanitize_text_field', wp_unslash( $post_data['user_files'] ) );
            
            // Loop through post data
            foreach ( $post_data as $key => $value ) {
                
                // Integers are file ids
                if ( is_int( $key ) ) {
                    $file_ids[] = $value;
                    $file_names[] = File::get_file_name( $value );
                }
            }
        }

        // No files selected
        if ( empty( $file_ids ) ) {
            $this->alert( __( 'No files are selected.', 'buddyclients-free' ) );
            return;
        }

        // Add to session data
        $_SESSION['buddyc_delete_file_ids'] = $file_ids;
        $_SESSION['buddyc_delete_file_names'] = $file_names;
    }
    
    /**
     * Generates an alert.
     * 
     * @since 1.0.0
     * 
     * @param string $message The alert message.
     */
    private function alert( $message ) {
        buddyclients_js_alert( $message );
    }
}
    