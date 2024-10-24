<?php
namespace BuddyClients\Includes;

use BuddyClients\Includes\Form\Form;
use BuddyClients\Includes\File;

/**
 * User files form.
 * 
 * Generates a form through which users can download and delete uploaded files.
 * Does not allow users to delete files attached to in-progress services.
 * Handles submission internally. Generates final deletion form.
 * 
 * @since 0.1.0
 */
class UserFilesForm {
    
    /**
     * The ID of the current user.
     * 
     * @var int
     */
    public $user_id;
    
    /**
     * An array of user Files.
     * 
     * @var array
     */
    public $user_files;
    
    /**
     * Array of File IDs to delete.
     * 
     * @var array
     */
    public $delete_file_ids;
    
    /**
     * The names of files to be deleted.
     * 
     * @var string
     */
    public $file_names;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        $this->user_id = get_current_user_id();
        $this->user_files = FileHandler::get_user_files( $this->user_id );
    }
    
    /**
     * Builds the form.
     * 
     * @since 0.1.0
     */
    public function build() {

        // Check if we're cancelling
        $canceled = $this->cancel_delete();

        if ( $canceled ) {
           // Output initial form
           $this->user_files_form();
            return;
        }
        
        // Check if the deletion form was submitted
        if ( isset( $_SESSION['bc_delete_file_ids'] ) ) {
            $file_ids = $this->get_session_array( 'bc_delete_file_ids' );
            $file_names = $this->get_session_array( 'bc_delete_file_names' );
            
            // Assign variables
            $this->delete_file_ids = $file_ids;
            $this->file_names = implode( '<br>', $file_names );
            
            // Output file names and final deltion form
            echo wp_kses( $this->file_names, array( 'br' => array() ) );
            $this->final_deletion_form( $file_ids );
        } else {
            // Output initial form
            $this->user_files_form();
        }
    }

    /**
     * Retrieves an array from session data.
     * 
     * @since 1.0.17
     * 
     * @param   string  $key    The session key.
     * 
     * @return  array   A sanitized array.
     */
    private function get_session_array( $key ) {
        return isset( $_SESSION[$key] ) ? array_map( 'sanitize_file_name', $_SESSION[$key] ) : [];
    }

    /**
     * Removes session data if we're cancelling deletion.
     * 
     * @since 1.0.17
     */
    private function cancel_delete() {
        // Retrieve cancel request ID
        $cancel_request = bc_get_param( 'bc-files-cancel-request' );

        // No cancel request
        if ( ! $cancel_request ) {
            return false;
        }

        // Check for completed cancel request
        if ( isset( $_SESSION['bc_cancel_request_complete'] ) ) {
            $cancel_request_complete = sanitize_text_field( wp_unslash( $_SESSION['bc_cancel_request_complete'] ) );
            if ( $cancel_request_complete === $cancel_request ) {
                return false;
            }
        }

        // Active cancel request - unset session data
        unset( $_SESSION['bc_delete_file_ids']);
        unset( $_SESSION['bc_delete_file_names']);
        
        // Add completed flag
        $_SESSION['bc_cancel_request_complete'] = $cancel_request;

        // Return true to revert to initial form
        return true;
    }
    
    /**
     * Final file deletion form.
     * 
     * @since 0.1.0
     */
    private function final_deletion_form() {
        
        // Make sure files are selected
        if ( $this->delete_file_ids ) {
            
            // Define form args
            $form_args = [
                'key'                   => 'final-deletion-files',
                'fields_callback'       => [$this, 'final_deletion_form_fields'],
                'submission_class'      => __NAMESPACE__ . '\FinalDeletionSubmission',
                'description'           => __( 'Type DELETE to confirm deletion of the files above.', 'buddyclients' ),
                'submit_text'           => __( 'Permanently Delete Files', 'buddyclients' ),
                'avatar'                => null
            ];
            
            $form = new Form( $form_args );
            $form->echo();
            echo wp_kses_post( $this->cancel_link() );
            return;
        }
    }

    /**
     * Generates a cancel button.
     * 
     * @since 1.0.17
     */
    private function cancel_link() {
        $link = bc_add_params( [ 'bc-files-cancel-request' => wp_rand() ] );
        $content = '<div class="bc-files-cancel-container">';
        $content .= '<a href="' . $link . '" class="bc-files-cancel">Cancel</a>';
        $content .= '</div>';
        return $content;
    }
    
    /**
     * Generates final deletion fields.
     * 
     * @since 0.1.0
     */
    public function final_deletion_form_fields() {
        
        $args = [];
        
        
        // Hidden file ids
        $args[] = [
            'key' => 'file_ids',
            'type' => 'hidden',
            'value' => implode( ',', $this->delete_file_ids )
        ];
        
        // DELETE confirmation
        $args[] = [
            'key' => 'verify_delete',
            'type' => 'input',
            'label' => __( 'Type DELETE to confirm deletion of the files above.', 'buddyclients' ),
            'description' => __( 'This action is permanent and cannot be undone.', 'buddyclients' ),
            'required'  => true
        ];
        
        return $args;
    }
    
    /**
     * Initial user files form.
     * 
     * @since 0.1.0
     */
    private function user_files_form() {
        
        // Make sure the user has files
        if ( empty( $this->user_files ) ) {
            esc_html_e( 'No files available.', 'buddyclients' );
            return;
        }
        
        // Define form args
        $form_args = [
            'key'                   => 'manage-user-files',
            'fields_callback'       => [$this, 'user_files_form_fields'],
            'submission_class'      => __NAMESPACE__ . '\UserFilesSubmission',
            'submit_text'           => __( 'Delete Files', 'buddyclients' ),
            'avatar'                => null
        ];
        
        $form = new Form( $form_args );
        return $form->echo();
    }
    
    /**
     * Generates user files fields.
     * 
     * @since 0.1.0
     */
    public function user_files_form_fields() {
        // Initialize options
        $options = [];
        
        if ( $this->user_files ) {
            // Add each group to the options array
            foreach ( $this->user_files as $file ) {
                
                // Make sure the file exists
                if ( ! File::exists( $file->ID ) ) {
                    continue;
                }
                
                // Check if the file is in use
                $in_use = File::in_use( $file->ID );

                $options[$file->ID] = [
                    'label' => bc_download_links( [$file->ID], true ),
                    'value' => $file->ID,
                    'disabled'   => $in_use
                ];
            }
        }
        
        // Build arguments for the project select field
        return [[
            'key' => 'user_files',
            'type' => 'checkbox',
            'label' => __( 'Your Files', 'buddyclients' ),
            'description' => __( 'Select files to delete. Files connected to in-progress services are not available for deletion.', 'buddyclients' ),
            'options' => $options, // Pass the generated options array
        ]];
    }
}