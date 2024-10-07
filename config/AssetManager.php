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
	 * The array of script localization data.
	 * 
	 * @var array
	 */
	protected $localization_info;
	
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

		// Get script localization data
		$this->localization_info = $this->localization_info();
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
			$this->enqueue_js( $handle, $file_url, $file_name );
            wp_enqueue_script($handle, $file_url, array(), BC_PLUGIN_VERSION, 'all');

        } else if ($extension === 'css') {
            // Enqueue style
            wp_enqueue_style($handle, $file_url, array(), BC_PLUGIN_VERSION, 'all');
        }
	}

	/**
	 * Defines localization data.
	 * 
	 * @since 1.0.15
	 */
	private function localization_info() {
		$localization_info = [
			'email-entered' => [
				'nonce' => wp_create_nonce( $this->build_handle( 'email-entered') ),
			],
			'help-popup'	=> [
				'nonce' => wp_create_nonce( $this->build_handle( 'help-popup') ),
			],
			'create-account'	=> [
				'nonce' => wp_create_nonce( $this->build_handle( 'create-account') ),
			],
			'line-items-table'	=> [
				'nonce' => wp_create_nonce( $this->build_handle( 'line-items-table') ),
			],
			'service-fields'	=> [
				'nonce' => wp_create_nonce( $this->build_handle( 'service-fields') ),
			],
		];

	 	/**
		 * Filters the script localization info.
		 * 
		 * @since 1.0.15
		 */
		$localization_info = apply_filters( 'bc_script_localization', $localization_info );		

		return $localization_info;
	}

	/**
	 * Enqueues and localizes Javascript file.
	 * 
	 * @since 1.0.15
	 * 
	 * @param   string  $handle     The script handle.
	 * @param   string  $file_url   The full file url.
	 * @param	string	$file_name	The file name without extension.
	 */
	private function enqueue_js( $handle, $file_url, $file_name ) {
		// Enqueue script
		wp_enqueue_script( $handle, $file_url, array(), BC_PLUGIN_VERSION, 'all' );

		// Check if localization info exists for the file
		$file_localization_info = $this->localization_info[$file_name] ?? null;
		if ( is_array( $file_localization_info ) ) {
			// Localize and pass data
	        wp_localize_script( $handle, 'bcData', $file_localization_info );
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
