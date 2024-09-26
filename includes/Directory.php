<?php
namespace BuddyClients\Includes;

/**
 * Handles a single directory.
 * 
 * Creates and updates the directory,
 * including index and htaccess files.
 *
 * @since 0.1.0
 */
class Directory {

    /**
     * Provided directory path.
     *
     * @var string
     */
    private $path;
    
    /**
     * Full directory path.
     *
     * @var string
     */
    private $full_path;
    
    /**
     * True if site URL has changed.
     *
     * @var bool
     */
    private $new_site_url;

    /**
     * Constructs a Directory object.
     * 
     * @since 0.1.0
     * @param string $path The path of the directory to be created.
     */
    public function __construct( string $path ) {
        
        // Define full directory path
        $this->full_path = self::primary_dir() . $path;
        
        // Create directory and associated files
        $this->create_dir();
        
        // Create .htaccess file in primary directory if necessary
        $this->check_site_url();
        $this->htaccess();
    }

    /**
     * Defines the primary directory for the plugin files.
     * 
     * @since 0.1.0
     * @return string The path of the primary directory.
     */
    public static function primary_dir() {
         
        // Get WordPress upload base directory
        $upload_dir = wp_upload_dir()['basedir'];
        
        // Define plugin directory within upload directory
        $upload_dir = trailingslashit($upload_dir) . 'bc_files/' ;
        
        return $upload_dir;
    }
    
    /**
     * Creates a directory and places an index file inside it.
     * 
     * @since 0.1.0
     * @return $this The current Directory object instance.
     */
    private function create_dir() {
        
        // Make sure directory does not exist already
        if ( ! file_exists( $this->full_path ) ) {
            
            // Create the directory recursively with full permissions
            mkdir( $this->full_path, 0755, true );
            
            // Create an index file inside the directory
            file_put_contents( $this->full_path . '/index.php', '<?php // Silence is golden.' );
        }
        return $this;
    }
    
    /**
     * Checks if the site URL has changed since the last access.
     * 
     * @since 0.1.0
     * @return $this The current Directory object instance.
     */
    private function check_site_url() {
        
        // Get the previous site URL
        $prev_url = get_option('bc_htaccess_url', '');
        
        // Get the current site URL
        $site_url = site_url();
        
        // Check if the URLs match
        $this->new_site_url = $prev_url === $site_url ? false : true;
        
        if ( $this->new_site_url ) {
            // Update file urls if necessary
            $this->update_file_urls( $prev_url );
        }
    }
    
    /**
     * Updates file urls with new site url.
     * 
     * @since 0.3.0
     * 
     * @param   string  $prev_url   The old site url.
     */
    private function update_file_urls( $prev_url ) {
        // Make sure site url has changed
        if ( $this->new_site_url ) {
            FileHandler::update_file_urls( $prev_url );
        }
    }
        
    /**
     * Creates or updates the .htaccess file for access control.
     * 
     * @since 0.1.0
     * @return $this The current Directory object instance.
     */
    private function htaccess() {
        
        // Define the path to the .htaccess file
        $htaccess_file = self::primary_dir() . '/.htaccess';
    
        // Check if the .htaccess file exists or needs updating
        if ( ! file_exists( $htaccess_file ) || $this->new_site_url ) {
            
            // Define the contents of the .htaccess file
            $htaccess_content = "# Allow access to files through download links\n";
            $htaccess_content .= "SetEnvIfNoCase Referer \"^" . site_url() . "/\" allow_access\n\n";
            $htaccess_content .= "# Limit access to all files\n";
            $htaccess_content .= "<FilesMatch \"\\.\">\n";
            $htaccess_content .= "    Order deny,allow\n";
            $htaccess_content .= "    Deny from all\n";
            $htaccess_content .= "    Allow from env=allow_access\n";
            $htaccess_content .= "</FilesMatch>\n\n";
            $htaccess_content .= "# Custom error page for 403 (Access Denied) error - redirect to homepage\n";
            $htaccess_content .= "ErrorDocument 403 " . site_url() . "/\n";
    
            // Create or update the .htaccess file with the specified content
            file_put_contents($htaccess_file, $htaccess_content);
            
            // Set the current site URL as the option value for future checks
            update_option('bc_htaccess_url', site_url());
        }
        return $this;
    }
    
    /**
     * Gets full directory path.
     * 
     * @since 0.1.0
     */
     public function full_path() {
         return $this->full_path;
     }
}