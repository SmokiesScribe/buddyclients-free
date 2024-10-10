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
        
        // Check if the deletion form was submitted
        if ( isset( $_SESSION['bc_user_files'] ) && is_array( $_SESSION['bc_user_files'] ) ) {
            $post_data = array_map( 'sanitize_text_field', $_SESSION['bc_user_files'] );

            // Initialize
            $file_ids = [];
            $file_names = [];
            
            // Loop through post data
            foreach ( $post_data as $key => $value ) {
                
                // Integers are file ids
                if ( is_int( $key ) ) {
                    $file_ids[] = $key;
                    $file_names[] = File::get_file_name( $key );
                }
            }
            
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
                'description'           => __( 'Type DELETE to confirm deletion of the files above.', 'buddyclients-free' ),
                'submit_text'           => __( 'Permanently Delete Files', 'buddyclients-free' ),
                'avatar'                => null
            ];
            
            return ( new Form( $form_args ) )->echo();
        }
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
            'value' => serialize( $this->delete_file_ids )
        ];
        
        // DELETE confirmation
        $args[] = [
            'key' => 'verify_delete',
            'type' => 'input',
            'label' => __( 'Type DELETE to confirm deletion of the files above.', 'buddyclients-free' ),
            'description' => __( 'This action is permanent and cannot be undone.', 'buddyclients-free' ),
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
        
        // Define form args
        $form_args = [
            'key'                   => 'manage-user-files',
            'fields_callback'       => [$this, 'user_files_form_fields'],
            'submission_class'      => null,
            'submit_text'           => __( 'Delete Files', 'buddyclients-free' ),
            'avatar'                => null
        ];
        
        return ( new Form( $form_args ) )->echo();
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
            'label' => __( 'Your Files', 'buddyclients-free' ),
            'description' => __( 'Select files to delete. Files connected to in-progress services are not available for deletion.', 'buddyclients-free' ),
            'options' => $options, // Pass the generated options array
        ]];
    }
}