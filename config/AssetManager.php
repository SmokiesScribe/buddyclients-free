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

		// Load CSS variables if necessary
		add_action( 'wp_enqueue_scripts', [$this, 'load_variables'] );
	}

	/**
	 * Loads CSS variables.
	 * 
	 * @since 1.0.20
	 */
	public function load_variables() {
		if ( ! wp_style_is( 'buddyclients-css-variables', 'enqueued' ) ) {

			// Enqueue variables file
			wp_enqueue_style( 'buddyclients-css-variables', BC_PLUGIN_URL . 'assets/css/variables.css' );

			// Initialize variables with colors
			$css_variables = [
				'primary-color'		=> bc_color( 'primary' ),
				'accent-color'		=> bc_color( 'accent' ),
				'tertiary-color'	=> bc_color( 'tertiary' ),
			];

			/**
			 * Filters custom CSS variables.
			 *
			 * @since 1.0.20
			 *
			 * @param array  $css_variables The associative array of css names and variables.
			 */
			$css_variables = apply_filters( 'buddyclients_css_variables', $css_variables );

			// Build custom css
			$custom_css = ":root {";

			// Make sure variables exist
			if ( ! empty( $css_variables ) ) {
				foreach ( $css_variables as $name => $value ) {
					$custom_css .= "--buddyclients-{$name}: {$value};";
				}

				// Close
				$custom_css .= "}";
			}		

			// Add variables as inline style
			wp_add_inline_style( 'buddyclients-css-variables', $custom_css );
		}
	}
	
	/**
	 * Retrieves files to load.
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

		// Verify file
		if ( ! $this->verify_file( $file, $file_name, $extension ) ) {
			return;
		}
	    
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
	 * Verifies that the file should be enqueued.
	 * 
	 * @since 1.0.20
	 * 
	 * @param   array   $file       File to enqueue.
	 * @param   string  $file_name  The file name without extension.
	 * @param   string  $extension  The file extension.
	 */
	private function verify_file( $file, $file_name, $extension ) {

		// Check whether to load BuddyPress-specific styles
		if ( $file_name === 'bp-global' ) {
			if ( bc_buddyboss_theme() || is_admin() ) {
				return false;
			}
		}

		// All checks passed
		return true;
	}

	/**
	 * Defines localization data.
	 * 
	 * @since 1.0.15
	 */
	private static function localization_info() {
		$localization_info = [
			'email-entered' 		=> [],
			'help-popup'			=> [],
			'create-account'		=> [],
			'line-items-table'		=> [],
			'service-fields'		=> [],
			'create-project-fields'	=> [],
			'search'				=> [],
			'create-page'			=> [],
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

		// Localize the script
		$this->localize_script( $file_name, $handle );
	}

	/**
	 * Localizes a javascript file.
	 * 
	 * @since 1.0.16
	 */
	public function localize_script( $file_name, $handle ) {
		// Fetch localization info
		$localization_info = self::localization_info();

		// Check if localization info exists for the file
		if ( isset( $localization_info[$file_name] ) ) {
			// Initialize array
			$file_localization_info = $localization_info[$file_name] ?? [];

			// Build nonce
			$file_localization_info['nonce'] = wp_create_nonce( $this->build_nonce_action( $file_name ) );
			$file_localization_info['nonceAction'] = $this->build_nonce_action( $file_name );
			$file_localization_info['fileName'] = $file_name;

			// Localize and pass data
			$data_name = $this->build_data_name( $file_name );
	        wp_localize_script( $handle, $data_name, $file_localization_info );
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

	/**
	 * Builds a nonce action name.
	 * 
	 * @since 1.0.16
	 * 
	 * @param   string  $file_name  The file name without extension.
	 */
	private function build_nonce_action( $file_name ) {
		$action = strtolower( $file_name );
		$action = str_replace( '-', '_', $file_name );
		$action = 'bc_' . $action;
		return $action;
	}

	/**
	 * Converts snake case to camel case.
	 * 
	 * @since 1.0.16
	 */
	private function build_data_name( $string ) {
		// Split the string by underscores
		$parts = explode( '-', $string );
		
		// Capitalize the first letter of each part except the first one
		$parts = array_map( 'ucfirst', $parts );
		
		// Make the first letter lowercase to follow camelCase
		$parts[0] = strtolower( $parts[0] );
		
		// Join the parts back together
		$data_name = implode( '', $parts );

		// Add suffix
		return $data_name . 'Data';
	}
}