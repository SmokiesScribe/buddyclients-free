<?php
namespace BuddyClients\Components\Brief;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\FileHandler;

/**
 * Brief form submission.
 * 
 * Handles submission of a single brief form.
 * Saves the submitted data and handles file uploads.
 *
 * @since 0.1.0
 * 
 * @see SingleBrief
 */
class BriefSubmission extends SingleBrief {
    
    /**
     * Whether the brief fields were updated.
     * 
     * @var bool
     */
    public $updated;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   array   $post   Global $_POST data from the form submission.
     * @param   array   $files  Global $_FILES data from the form submission.

     */
    public function __construct( $post, $files = null ) {

        // Construct SingleBrief
        parent::__construct( $post['brief_id'] );
        
        // Initialize
        $this->updated = false;
        
        // Update meta values
        $this->update_meta( $post );
        
        // Handle files
        $this->handle_files( $files );
        
        // Add last updated date
        update_post_meta( $this->brief_id, 'updated_date', current_time( 'Y-m-d H:i:s' ) );
        
        // Check whether values were updated
        if ( $this->updated ) {
        
            /**
             * Fires when a brief is updated.
             * 
             * @param   object  The BriefSubmission object.
             */
            do_action( 'buddyc_brief_updated', $this );
        }
        
        // Redirect to completed view
        $this->redirect();
    }
    
    /**
     * Redirects to completed view.
     * 
     * @since 0.1.0
     */
    private function redirect() {
        
        // Completed param
        $target_url = buddyc_add_params( ['brief-view' => 'completed'] );
        
        // Redirect
        wp_redirect( $target_url );
        exit;
    }
    
    /**
     * Updates meta values.
     * 
     * @since 0.1.0
     * 
     * @param   array   $post   Global post data from the form submission.
     */
    private function update_meta( $post ) {
        
        // Loop through fields
        foreach ( $this->fields as $field_id => $field_data ) {
            
            // Check post for field data
            $value = $post[$field_id] ?? null;
            
            // If the field has a value
            if ( $value ) {
                
                // Update flag
                $this->updated = true;
                
                // Update brief meta
                update_post_meta( $this->brief_id, $field_id, $value );
            } else {
                // No value, delete meta
                delete_post_meta( $this->brief_id, $field_id );
            }
        }
    }
    
    /**
     * Handles File meta values.
     * 
     * @since 0.1.0
     * 
     * @param   array   $files   Global files data from the form submission.
     */
    private function handle_files( $files ) {
        
        // Loop through fields
        foreach ( $this->fields as $field_id => $field_data ) {
            
            // Check files for field data
            $file = $files[$field_id] ?? null;
            
            // If files exist and is not empty
            if ( $file && $file['tmp_name'][0] !== '' ) {
                
                $args = [
                    'user_id'           => get_current_user_id(),
                    'project_id'        => $this->project_id,
                    'temporary'         => false,
                ];
                
                // Upload files and get ids
                $file_handler = new FileHandler( [$field_id => $file], $args );
                $file_ids = $file_handler->file_ids;
                
                // Update flag
                $this->updated = true;
                
                // Update meta with file ids
                update_post_meta( $this->brief_id, $field_id, $file_ids );
            }
        }
    }
}