<?php
namespace BuddyClients\Config;

/**
 * Manages assets.
 * 
 * Enqueues all styles and scripts in given directory.
 * 
 * @since 0.1.0
 */
class AssetManager {
	
	/**
	 * The directory path of scripts or styles.
	 *
	 * @var string
	 */
	protected $dir_path;
	
	/**
	 * The directory url of scripts or styles.
	 *
	 * @var string
	 */
	protected $dir_url;
	
	/**
	 * Optional. File to require.
	 *
	 * @var string
	 */
	protected $file;
	
	/**
	 * Formatted source name.
	 *
	 * @var string
	 */
	protected $source;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 * 
	 * @param   string  $source_file    The class file name with extension.
	 * @param   string  $dir            The directory partial path.
	 * @param   string  $file           Optional. The specific file to load.
	 */
	public function __construct( $source_file, $dir, $file = null ) {
	    
        // Define variables
		$this->dir_path = plugin_dir_path( $source_file ) . $dir;
		$this->dir_url = plugin_dir_url( $source_file ) . $dir;
		$this->file = $file ?? null;
		
		// Get source file name for handle
        $this->source = pathinfo( basename( $source_file ), PATHINFO_FILENAME );
	}
	
	/**
	 * Rerieves files to load.
	 * 
	 * @since 0.1.0
	 */
	public function run() {
	    
        // Check if the directory exists
        if ( is_dir( $this->dir_path ) ) {
            
            // Use specific file if defined
            if ( $this->file ) {
                
                // Build file path
                $file_path = $this->dir_path . '/' . $this->file;
                
                // Make sure file exists
                if ( file_exists( $file_path ) ) {
                    $this->handle_file( $this->file );
                }
                
            // Else get all files in directory
            } else {
                
                // Get all files in the directory
                $files = scandir( $this->dir_path );
            
                // Skip . and ..
                $files = array_diff( $files, array( '.', '..' ) );
            
                // Handle each file
                foreach ( $files as $file ) {
                    $this->handle_file( $file );
                }                
            }
        }    
	}
	
	/**
	 * Handles file based on type.
	 * 
	 * @since 0.1.0
	 * 
	 * @param   array   $file   The file.
	 */
	private function handle_file( $file ) {
	    
	    // Extract file info
        $extension = pathinfo( $file, PATHINFO_EXTENSION );
        $file_name = pathinfo( $file, PATHINFO_FILENAME );
        
        // Handle files by type
        switch ($extension) {
            case 'css':
            case 'js':
                $this->enqueue_script( $file, $file_name, $extension );
                break;
            case 'php':
                $this->require_file( $file, $file_name, $extension );
                break;
            }
	}
	
	/**
	 * Enqueues CSS and JS files.
	 * 
	 * @since 0.1.0
	 * 
	 * @param   array   $file       File to enqueue.
	 * @param   string  $file_name  The file name without extension.
	 * @param   string  $extension  The file extension.
	 */
	 private function enqueue_script( $file, $file_name, $extension ) {
	    
	    // Build script handle
        $handle = $this->build_handle( $file_name );
        
        // Build full url
        $file_url = $this->dir_url . '/' . $file;
        
        if ($extension === 'js') {
            // Enqueue script
            wp_enqueue_script($handle, $file_url, array(), BC_PLUGIN_VERSION, 'all');
        } else if ($extension === 'css') {
            // Enqueue style
            wp_enqueue_style($handle, $file_url, array(), BC_PLUGIN_VERSION, 'all');
        }
	}
	
	/**
	 * Requires PHP files.
	 * 
	 * @since 0.1.0
	 * 
	 * @param   array   $file       File to enqueue.
	 * @param   string  $file_name  The file name without extension.
	 * @param   string  $extension  The file extension.
	 */
	 private function require_file( $file, $file_name, $extension ) {
	    $file_path = $this->dir_path . '/' . $file;
	    // Require php file
	    require_once($file_path);
	}
	
	/**
	 * Builds handle.
	 * 
	 * @since 0.1.0
	 * 
	 * @param   string  $file_name  The file name without extension.
	 */
	private function build_handle( $file_name ) {
	    return 'bc-' . strtolower( $this->source ) . '-' . $file_name;
	}

}
