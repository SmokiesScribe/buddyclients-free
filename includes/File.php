<?php
namespace BuddyClients\Includes;

use BuddyClients\Components\Booking\BookedService\BookedService;

/**
 * Single uploaded file.
 * 
 * Processes and uploads a single file.
 * Updates the database with the file data.
 *
 * @since 0.1.0
 */
class File {
    
    /**
     * ObjectHandler instance.
     *
     * @var ObjectHandler|null
     */
    private static $object_handler = null;
    
    /**
     * File ID.
     *
     * @var int
     */
    public $ID;
    
    /**
     * The date created.
     *
     * @var string
     */
    public $created_at;
    
    /**
     * File owner ID.
     *
     * @var int|null
     */
    public $user_id;
    
    /**
     * Whether the file is temporary.
     *
     * @var bool
     */
    public $temporary;
    
    /**
     * Associated project ID.
     *
     * @var int|null
     */
    public $project_id;
    
    /**
     * Full directory path.
     *
     * @var string
     */
    public $dir_path;
    
    /**
     * Full file path.
     *
     * @var string
     */
    public $file_path;
    
    /**
     * File name.
     *
     * @var string
     */
    public $file_name;
    
    /**
     * The post ID of the upload type.
     *
     * @var int
     */
    public $upload_type;
    
    /**
     * The ID of the upload field.
     *
     * @var string
     */
    public $upload_id;
    
    /**
     * Initializes ObjectHandler.
     * 
     * @since 0.1.0
     */
    private static function init_object_handler() {
        if ( ! self::$object_handler ) {
            self::$object_handler = new ObjectHandler( __CLASS__ );
        }
    }
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        // Initialize object handler
        self::init_object_handler();
    }
    
    /**
     * Uploads a file to the server.
     * 
     * @since 0.1.0
     * 
     * @param   array   $file_info {
     *     An array of info from the $_FILE superglobal.
     * 
     *     @type    string  $tmp_name
     *     @type    string  $name
     * }
     * 
     * @param   array  $args {
     *     Optional. An array of args from the FileHandler.
     * 
     *     @type ?bool      $temporary      Optional. Whether the file is temporary.
     *                                      Defaults to false.
     *     @type ?int       $user_id        Optional. The ID of the file owner.
     *     @type ?int       $project_id     Optional. The ID of the associated project.
     *     @type ?string    $field_id       Optional. The ID of the upload field.
     * }
     */
    public function upload_file( $file_info, $args = [] ) {
        
        // Make sure it's not empty
        if ( ! $file_info['tmp_name'] ) {
            return false;
        }
        
        // Check for upload errors
        if ($file_info['error'] !== UPLOAD_ERR_OK) {
            error_log('File upload error: ' . $file_info['name']);
            return false;
        }
        
        // Extract args
        $this->user_id      = $args['user_id'] ?? null;
        $this->project_id   = $args['project_id'] ?? null;
        $this->upload_id    = $this->get_upload_id( $args['field_id'] );
        $this->temporary    = $args['temporary'] ?? false;
        
        // Build new file path
        $new_dir = (new Directory( $args['user_id'] ?? '' ))->full_path();
        $target_file = $this->build_file_path( $new_dir, $file_info['name'] );

        // Move file to target
        if ( move_uploaded_file($file_info['tmp_name'], $target_file )) {
            
            // Return file path
            $this->file_path = $target_file;
            
            // Add object to database
            $this->ID = $this->add_to_database();
        }
        
        // Return File ID
        return $this->ID;
    }
    
    /**
     * Retrieves the upload ID from the field ID.
     * 
     * @since 0.1.0
     * 
     * @param   string  $field_id   The upload field ID.
     */
    private function get_upload_id( $field_id ) {
        // Replace "booking-upload-" with an empty string
        $stripped_string = str_replace("booking-upload-", "", $field_id);
        
        // Split the string by "-" and get the first item
        $parts = explode("-", $stripped_string);
        
        // Return the first item
        return $parts[0];
    }
    
    /**
     * Uploads a signature image to the server.
     * 
     * @since 0.1.0
     * 
     * @param   array   $decoded_signature  Signature image data.
     * @param   string  $file_name          The file name.
     * @param   int     $user_id            The ID of the user to whom the signature belongs.
     */
    public function upload_signature( $decoded_signature, $file_name, $user_id ) {
        
        // Extract args
        $this->user_id      = $args['user_id'] ?? null;
        
        // Build new file path
        $new_dir = (new Directory( 'signatures' ))->full_path();
        $target_file = $this->build_file_path( $new_dir, $file_name );

        // Move file to target
        if ( file_put_contents( $target_file, $decoded_signature ) ) {
            
            // Return file path
            $this->file_path = $target_file;
            
            // Add object to database
            $this->ID = $this->add_to_database();
        }
        
        // Return File ID
        return $this->ID;
    }
    
    /**
     * Adds object to database.
     * 
     * @since 0.1.0
     */
    private function add_to_database() {
        if ( $this->file_path ) {
            return self::$object_handler->new_object( $this );
        }
    }
    
    /**
     * Builds file path.
     * 
     * @since 0.1.0
     * 
     * @param   string  $dir_path   The full directory path.
     * @param   string  $file_name  The file name.
     */
    private function build_file_path( $dir_path, $file_name ) {

        // Sanitize and format file name
        $this->file_name = self::format( $file_name );
        
        // Define target file path
        $target_file = trailingslashit( $dir_path ) . $this->file_name;
        
        // Check if file with same name exists
        $target_file = self::no_conflicts( $target_file );
        
        // Return file path
        return $target_file;
    }
    
    /**
     * Formats the file name.
     * 
     * @since 0.1.0
     * 
     * @param   string  $file_name  The file name to format.
     */
    private static function format( $file_name ) {
        // Replace unwanted characters with underscores
        $file_name = preg_replace('/[^\w.-]/', '_', $file_name);
        
        // Find the position of the last dot (.)
        $last_dot_position = strrpos($file_name, '.');
        
        // Replace all dots except the last one with underscores
        if ($last_dot_position !== false) {
            $file_name = str_replace('.', '_', substr($file_name, 0, $last_dot_position)) . substr($file_name, $last_dot_position);
        }
        
        // Remove any consecutive underscores
        $file_name = preg_replace('/_+/', '_', $file_name);
        
        // Trim leading and trailing underscores
        $file_name = trim($file_name, '_');
        
        // Truncate
        $file_name = self::truncate( $file_name );
        
        return $file_name;
    }
    
    /**
     * Truncates the file name.
     * 
     * @since 0.1.0
     * 
     * @param   string  $file_name  The file name to truncate.
     */
    private static function truncate( $file_name ) {
       $char = 50;
       
       // Check if char count exceeded
        if (strlen($file_name) > $char) {
            // Find the position of the last dot (.) in the file name
            $dot_position = strrpos($file_name, '.');
            
            // If a dot is found
            if ($dot_position !== false) {
                // Extract the file extension
                $file_extension = substr($file_name, $dot_position);
                
                // Remove extension
                $file_name = substr($file_name, 0, $dot_position);
                
                // Truncate the file name before the last dot
                $formatted_file_name = substr($file_name, 0, $char);
                
                // Append the file extension back to the truncated file name
                $formatted_file_name .= $file_extension;
            } else {
                // Truncate the file name
                $formatted_file_name = substr($file_name, 0, $char);
            }
        } else {
            return $file_name;
        }
        return $formatted_file_name;
    }
    
    /**
     * Avoids file name conflicts.
     * 
     * Checks for existing file. Applies unique file name if necessary.
     * 
     * @since 0.1.0
     * 
     * @param   string  $target_file    The file path to check for conflicts.
     */
    private static function no_conflicts( $target_file ) {
        // Check if file with same name exists
        if (file_exists($target_file)) {
            // Append unique id to avoid conflicts
            $file_name = basename($target_file);
            $new_file_name = uniqid() . '_' . $file_name;
            $target_file = str_replace($file_name, $new_file_name, $target_file);
        }
        return $target_file;
    }
    
    /**
     * Gets file path by file id.
     * 
     * @since 0.1.0
     * 
     * @param int       $file_id    The ID of the File.
     */
     public static function get_file_path( $file_id ) {
         
        // Initialize object handler
        self::init_object_handler();
         
        // Get file object
        $file = self::$object_handler->get_object( $file_id );
        
        if ( $file ) {
            // Get file path
            return $file->file_path;
        }
     }
     
    /**
     * Gets file upload ID.
     * 
     * @since 0.1.0
     * 
     * @param int       $file_id    The ID of the File.
     */
     public static function get_file_upload_id( $file_id ) {
         
        // Initialize object handler
        self::init_object_handler();
         
        // Get file object
        $file = self::$object_handler->get_object( $file_id );
        
        // Make sure the file exists
        if ( $file ) {
            // Get upload field id
            return $file->upload_id;
        }
     }
     
    /**
     * Gets file name.
     * 
     * @since 0.1.0
     * 
     * @param int       $file_id    The ID of the File.
     */
     public static function get_file_name( $file_id ) {
         
        // Initialize object handler
        self::init_object_handler();
         
        // Get file object
        $file = self::$object_handler->get_object( $file_id );
        
        // Get file path
        if ( $file ) {
            return $file->file_name;
        }
     }
    
    /**
     * Deletes a file from the server.
     * 
     * @since 0.1.0
     * 
     * @param int $file_id The ID of the file.
     * 
     * @return bool True if the file was successfully deleted, false otherwise.
     */
    public static function delete( $file_id ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Get File object
        $file = self::$object_handler->get_object( $file_id );
        
        // Check if file exists before attempting to delete
        if ( file_exists( $file->file_path ) ) {
            
            // Attempt to delete the file
            $deleted = unlink( $file->file_path );
            
            if ( $deleted ) {
                // Update database only if file deletion was successful
                $deleted = self::$object_handler->delete_object( $file_id );
                return true;
            } else {
                // File deletion failed
                return false;
            }
        }
        
        // File does not exist
        return false;
    }

    
    /**
     * Moves File to new location.
     * 
     * @since 0.1.0
     * 
     * @param int       $file_id    The ID of the File.
     * @param string    $new_path   New location for the file.
     */
     public function move_file( $file_id, $new_path ) {
         
        // Initialize object handler
        self::init_object_handler();
        
        // Get File object
        $file = self::$object_handler->get_object( $file_id );

        // Get original file path and name
        $old_file = $file->file_path;
        $file_name = $file->file_name;
        
        // Build new file path
        $new_dir = (new Directory( $new_path ))->full_path();
        $target_file = $this->build_file_path( $new_dir, $file_name );
        
        // Check if file exists and the new location is different from the old one
        if ( file_exists( $old_file ) && $old_file !==  $target_file ) {
        
            // Attempt to move the file to the new location
            $moved = rename( $old_file, $target_file );
            
            // Check if successful
            if ( $moved ) {
                 // Update object
                 $updated = self::$object_handler->update_object_properties( $file_id, ['file_path' => $target_file] );
            }
        }
     }
     
     /**
      * Retrieves a file's status.
      * 
      * @since 0.1.0
      * 
      * @param  int     $file_id    The ID of the File.
      * @return bool    Whether the file is in use.
      */
    public static function in_use( $file_id ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Get File object
        $file = self::$object_handler->get_object( $file_id );
        
        // Look for services that are using this file
        $user_services = BookedService::get_services_by( 'client_id', $file->user_id );
        
        foreach ( $user_services as $service ) {
            
            // Skip if the service has no files
            if ( ! is_array( $service->file_ids ) ) {
                continue;
            }
            
            // Skip if service is complete or cancelled
            if ( $service->status === 'complete' || $service->status === 'canceled' ) {
                continue;
            }

            // Check if the file is in the services files
            if ( in_array( $file_id, $service->file_ids ) ) {
                return true;
            }
        }
        
        // Not in use
        return false;
    }
    
     /**
      * Checks if the file exists on the server.
      * 
      * @since 0.1.0
      * 
      * @param  int     $file_id    The ID of the File.
      * @return bool    Whether the file exists.
      */
    public static function exists( $file_id ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Get File object
        $file = self::$object_handler->get_object( $file_id );
        
        if ( ! $file ) {
            return false;
        }
        
        return file_exists( $file->file_path );
        
    }
    
}