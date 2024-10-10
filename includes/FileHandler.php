<?php
namespace BuddyClients\Includes;

use BuddyClients\Includes\{
    Directory,
    File
};

/**
 * Handles multiple files.
 * 
 * Handles file uploads, retrieval, and location changes.
 * Generates file download links.
 *
 * @since 0.1.0
 * 
 * @see File
 */
class FileHandler {
    
    /**
     * ObjectHandler instance.
     *
     * @var ObjectHandler|null
     */
    private static $object_handler = null;
    
    /**
     * An array of File IDs.
     * 
     * @var array
     */
    public $file_ids;
    
    /**
     * Initializes ObjectHandler.
     * 
     * @since 0.1.0
     */
    private static function init_object_handler() {
        if ( ! self::$object_handler ) {
            self::$object_handler = new ObjectHandler( __NAMESPACE__ . '/File' );
        }
    }

    /**
     * Constructor.
     * 
     * @since 0.1.0
     * 
     * @param   array   $file   Superglobal $_FILES data.
     * @param   array   $args {
     *     Array of arguments for File creation.
     *     
     *     @type    bool    $temporary      Whether the files are temporary.
     *     @type    ?int    $user_id        File owner ID.
     *     @type    ?int    $project_id     Associated project ID.
     * }
     */
    public function __construct( $file, $args ) {
        
        // Initialize file ids
        $this->file_ids = [];

        // Create files
        $this->file_ids = $this->create_files( $file, $args );
    }
    
    /**
     * Creates Files.
     * 
     * @since 0.1.0
     */
    private function create_files( $file, $args ) {
        // Initialize
        $file_ids = [];
        
        // Get directory path
        $args['dir_path'] = (new Directory( $args['user_id'] ))->full_path();
        
        // Extract files from superglobal
        $files = self::extract_files( $file );
        
        // Make sure files exist
        if ( $files ) {
            // Create new File for each
            foreach ( $files as $field_id => $file_info ) {
                $args['field_id'] = $field_id;
                $file = new File;
                $file->upload_file( $file_info, $args );
                $file_ids[] = $file->ID;
            }
        }
        
        return $file_ids;
    }
    
    /**
     * Extracts file arrays from superglobal.
     * 
     * @since 0.1.0
     * 
     * @param   array   $file   Superglobal $_FILES data.
     */
    private static function extract_files( $file ) {
        
        // Initialize array to store file data
        $files = array();
        
        // Loop through file inputs
        foreach ( $file as $field_id => $file_data ) {
            
            // Make sure it's not empty
            if ( ! isset( $file_data['tmp_name'] ) ) {
                continue;
            }
        
            // Check if $file is an array
            $array = is_array( $file_data['name'] );
            
            // Get number of files
            $count = $array ? count( $file_data['name'] ) : 1;
            
            // Add array of file data to new array
            if ( $array ) {
                
                for ( $i = 0; $i < $count; $i++ ) {
                    
                    // Define file data
                    $single_file_data = [
                        'name' => $file_data['name'][$i],
                        'tmp_name' => $file_data['tmp_name'][$i],
                        'error' => $file_data['error'][$i],
                    ];
                    
                    // Add to array
                    $files[$field_id . '-' . $i] = $single_file_data;
                }
            } else {
                // Use original for single file
                $files[$field_id] = $file_data;
            }
        }
        
        return $files;
    }
    
    /**
     * Retrieves all File objects.
     * 
     * @since 0.3.0
     */
    public static function get_all_files() {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Get all files
        return self::$object_handler->get_all_objects();
    }
    
    /**
     * Updates file urls with site url.
     * 
     * @since 0.3.0
     * 
     * @param   string  $old_url    The old site url.
     */
    public static function update_file_urls( $old_url ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Get new url
        $new_url = site_url();
        
        // Get all files
        $files = self::get_all_files();
        
        // Loop through files
        foreach ( $files as $file ) {
            
            // Replace url
            $file_args = [
                'dir_path'  => str_replace( basename( $old_url ), basename( $new_url ), $file->dir_path ),
                'file_path' => str_replace( basename( $old_url ), basename( $new_url ), $file->file_path )
            ];
            
            // Update file object
            $updated = self::$object_handler->update_object_properties( $file->ID, $file_args );
        }
    }
     
    /**
     * Retrieves all File objects for a user.
     * 
     * @since 0.1.0
     * 
     * @param   int $user_id    The ID of the user.
     */
    public static function get_user_files( $user_id ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Get all user files
        $files = self::$object_handler->get_objects_by_property( 'user_id', $user_id );
        
        // Sort by project
        return self::$object_handler->sort_objects( $files, 'project_id', $order = 'asc' );
    }
    
    /**
     * Retrieves all File objects for a project.
     * 
     * @since 0.1.0
     * 
     * @param   int $project_id    The ID of the project.
     */
    public static function get_project_files( $project_id ) {
        
        // Initialize object handler
        self::init_object_handler();
        
        // Get all project files
        return self::$object_handler->get_objects_by_property( 'project_id', $project_id );
    }
    
     /**
      * Upgrades Files to permanent.
      * 
      * @since 0.1.0
      * 
      * @param  array    $file_ids    An array of File IDs to upgrade.
      */
     public static function upgrade_files( $file_ids ) {
         
        // Initialize object handler
        self::init_object_handler();
        
        // Loop through File IDs
        foreach ( $file_ids as $file_id ) {
            
            // Update object
            $updated = self::$object_handler->update_object_properties( $file_id, ['temporary' => false] );
        }
     }
     
     /**
      * Clears temporary Files.
      * 
      * @since 0.1.0
      */
     public static function clear_temporary_files() {
         
        // Initialize object handler
        self::init_object_handler();
        
        // Retrieves temporary files
        $temp_files = self::$object_handler->get_objects_by_property( 'temporary', true );
        
        // Get expired files
        $days = 1; // remove temp files more than 1 day old
        $expired_temp_files = self::$object_handler->get_expired_objects( $days, $temp_files );
        
        // Delete files from server
        $deleted_files = self::delete_files_from_server( $expired_temp_files );
        
        // Delete expired files
        self::$object_handler->delete_objects( $deleted_files );
     }
     
    /**
     * Deletes files from the server.
     * 
     * @since 0.4.3
     * 
     * @param array $files Array of File objects to delete.
     * @return array An array of deleted File objects.
     */
    private static function delete_files_from_server( $files ) {
        $deleted_files = [];
        
        foreach ( $files as $file ) {
            // Ensure the file path is set
            if ( ! empty( $file->file_path ) && file_exists( $file->file_path ) ) {
                // Attempt to delete the file
                if ( wp_delete_file( $file->file_path ) ) {
                    $deleted_files[] = $file;
                }
            }
        }
        
        return $deleted_files;
    }
     
     /**
      * Retrieves a File object by ID.
      * 
      * @since 0.1.0
      * 
      * @param  int    $file_id    The ID of the file to retrieve.
      */
     public static function get_file_by_id( $file_id ) {
         
        // Initialize object handler
        self::init_object_handler();
        
        // Get the file
        return self::$object_handler->get_object( $file_id );
     }
     
    /**
      * Generates an image from a file ID.
      * 
      * @since 0.1.0
      * 
      * @param  int     $file_id    The ID of the file to retrieve.
      * @param  array   $args {
      *     Optional. An array of arguments to use in generating the image html.
      * 
      *     @type   string  $classes    Classes to add to the image.
      *     @type   string  $link       URL to link the image to.
      *     @type   string  $alt        Alt text. Defaults to file name.
      * }
      */
     public static function generate_image( $file_id, $args = [] ) {
         
         // Initialize
         $image = '';
         
        // Get the File
        $file = self::get_file_by_id( $file_id );
        
        if ( ! $file ) {
            return;
        }
        
        // Convert the file path to a url
        $url = self::path_to_url( $file->file_path );
        
        // Extract html args
        $classes    = $args['classes'] ?? '';
        $link       = $args['link'] ?? null;
        $alt        = $args['alt'] ?? basename( $file->file_path );
        
        // Build link
        $image .= $link ? '<a href="' . $link . '">' : '';
        
        // Build the html image
        $image .= '<img src="' . $url . '" class="' . $classes . '" alt="' . $alt . '">';
        
        // Close link
        $image .= $link ? '</a>' : '';
        
        return $image;
    }
     
     /**
      * Convert a full path to a url.
      * 
      * @since 0.1.0
      * 
      * @param  string  $file_path  The full file path.
      */
     public static function path_to_url( $file_path ) {
         $subpath = str_replace( ABSPATH, '', $file_path );
         return site_url( $subpath );
     }
     
    /**
     * Generates download links from an array of files.
     * 
     * @since 0.1.0
     * 
     * @param   array   $file_ids           An array of file IDs.
     * @param   bool    $show_file_name     Optional. Whether to display the file name.
     *                                      Defaults to false;
     */
    public static function download_links( $file_ids, $show_file_name = false ) {

        // Initialize
        $links = '';
        
        // Exit if not an array
        if ( ! is_array( $file_ids ) ) {
            return;
        }
        
        // Build each link
        foreach ( $file_ids as $file_id ) {
            $links .= self::download_link( $file_id, $show_file_name );
        }
        
        return $links;
    }
     
    /**
      * Generates a download link from a file ID.
      * 
      * @since 0.1.0
      * 
      * @param   int     $file_id            The ID of the file to retrieve.
      * @param   bool    $show_file_name     Optional. Whether to display the file name.
      *                                      Defaults to false.
      * }
      */
     public static function download_link( $file_id, $show_file_name = false ) {
         
         // Initialize
         $download = '';
         
        // Get the File
        $file = self::get_file_by_id( $file_id );
        
        // Make sure file is found
        if ( $file && $file->file_path ) {
            
            // Convert the file path to a url
            $url = self::path_to_url( $file->file_path );
            
            // Check if the file exists on the server
            if ( ! file_exists( $file->file_path ) ) {
                $download = '<div class="no-ms-message">' . __( 'File not found: ', 'buddyclients-free' ) . $file->file_name . '</div>';
                
            } else {
                
                // Define icon
                $icon = bc_icon( 'download' );
                
                // Build download link
                $text = $show_file_name ? __( 'Download File: ', 'buddyclients-free' ) . $file->file_name : __( 'Download File', 'buddyclients-free' );
                $download = '<a class="ms-download-button" href="' . $url . '" download>' . $icon . ' ' . $text . '</a><br><br>';
            }
        }
        
        return $download;
    }
}
?>
