<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Final file deletion form submission.
 * 
 * Validates the form submission and deletes the file from the server.
 *
 * @since 0.1.0
 */
class FinalDeletionSubmission {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data ) {
        $this->delete_files( $post_data );
    }
    
    /**
     * Deletes files and generates alert.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     */
    private function delete_files( $post_data ) {
        $comma_sep_file_ids = isset( $post_data['file_ids'] ) ? sanitize_text_field( $post_data['file_ids'] ) : null;
        $file_ids = $comma_sep_file_ids ? array_map( 'sanitize_text_field', explode( ',', $comma_sep_file_ids ) ) : null;
        $verification = isset( $post_data['verify_delete'] ) ? $post_data['verify_delete'] : false;        
        
        // No files selected
        if ( empty( $file_ids ) ) {
            $this->alert( __( 'No files are selected.', 'buddyclients-free' ) );
            return;
        }
        
        // Check the verification
        if ( $verification !== 'DELETE' ) {
            $this->alert( __( 'Confirmation does not match. Files not deleted.', 'buddyclients-free' ) );
            return;
        }
        
        // Process the file deletions
        list( $successful_deletions, $failed_deletions ) = $this->process_deletions( $file_ids );
        
        // Generate and send the alert message
        $alert_message = $this->generate_alert_message( $successful_deletions, $failed_deletions );
        $this->alert( $alert_message );
    }
    
    /**
     * Processes file deletions.
     * 
     * @since 0.1.0
     * 
     * @param array $file_ids Array of file IDs to delete.
     * @return array An array containing two arrays: successful deletions and failed deletions.
     */
    private function process_deletions( $file_ids ) {
        $failed_deletions = [];
        $successful_deletions = [];
        
        foreach ( $file_ids as $file_id ) {
            $deleted = File::delete( $file_id );
            
            if ( $deleted ) {
                $successful_deletions[] = $file_id;
            } else {
                $failed_deletions[] = $file_id;
            }
        }
        
        return [ $successful_deletions, $failed_deletions ];
    }
    
    /**
     * Generates an alert message based on deletion results.
     * 
     * @since 0.1.0
     * 
     * @param array $successful_deletions Array of successfully deleted file IDs.
     * @param array $failed_deletions Array of failed deletion file IDs.
     * @return string The alert message.
     */
    private function generate_alert_message( $successful_deletions, $failed_deletions ) {
        $alert_message = '';
        
        if ( ! empty( $successful_deletions ) ) {
            $alert_message .= sprintf(
                /* translators: %d: number of successful deletions */
                _n( '%d file successfully deleted.', '%d files successfully deleted.', count( $successful_deletions ), 'buddyclients-free' ),
                count( $successful_deletions )
            );
        }
            
        if ( ! empty( $failed_deletions ) ) {
            $alert_message .= sprintf(
                /* translators: %d: number of failed deletions */
                _n( '%d file was not deleted. Please try again.', '%d files were not deleted. Please try again.', count( $failed_deletions ), 'buddyclients-free' ),
                count( $failed_deletions )
            );
        }
        
        return $alert_message;
    }
    
    /**
     * Generates an alert.
     * 
     * @since 1.0.0
     * @since 1.0.10 Use dedicated function.
     * 
     * @param string $message The alert message.
     */
    private function alert( $message ) {
        buddyc_js_alert( $message );
    }
}
    