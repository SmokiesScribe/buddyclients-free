<?php
namespace BuddyClients\Config;

use stdClass;

/**
 * Handles plugin updates.
 * 
 * @since 1.0.5
 */
class UpdateManager {
    
    /**
     * The current plugin version.
     * 
     * @var string
     */
    public $current_version;
    
    /**
     * The remote update path.
     * 
     * @var string
     */
    public $update_path;
    
    /**
     * The plugin slug. (plugin_directory/plugin_file.php)
     * 
     * @var string
     */
    public $plugin_slug;
    
    /**
     * The plugin name. (plugin_file)
     * 
     * @var string
     */
    public $slug;
    
    /**
     * Constructor method.
     * 
     * @since 1.0.5
     */
    public function __construct() {
        $this->define_var();
        $this->define_hooks();
    }
    
    /**
     * Defines the hooks and filters.
     * 
     * @since 1.0.5
     */
    private function define_hooks() {
        // Update plugin info
        add_filter( 'plugins_api', [&$this, 'check_info'], 10, 3 );
        
        // Check for updates
        add_filter( 'pre_set_site_transient_update_plugins', [ &$this, 'check_update' ] );
        
        // Track updates
        add_action( 'upgrader_process_complete', [$this, 'track_update'], 10, 2 );

        // Modify upgrade package options for GitHub versioning
        add_filter('upgrader_package_options', [&$this, 'modify_package_options']);
    }
    
    /**
     * Defines the variables for the class.
     * 
     * @since 1.0.5
     */
    private function define_var() {
        $this->current_version = BC_PLUGIN_VERSION;
        $this->update_path = 'https://buddyclients.com/wp-content/plugins/buddyclients-admin/endpoints/info.php';
        $this->plugin_slug = 'buddyclients/buddyclients.php';
        $this->slug = $this->build_slug( $this->plugin_slug );
    }
    
    /**
     * Builds the plugin name.
     * 
     * @since 1.0.5
     * 
     * @param   string      $plugin_slug    The plugin slug.
     */
    private function build_slug( $plugin_slug ) {
        list ( $t1, $t2 ) = explode( '/', $plugin_slug );
        return str_replace( '.php', '', $t2 );
    }

    /**
     * Modifies the package options to remove version number from the directory name.
     * 
     * @since 1.0.9
     *
     * @param array $options The options for the upgrader.
     * @return array The modified options.
     */
    public function modify_package_options( $options ) {
        
        $package = $options['package'] ?? '';
        $destination = $options['destination'] ?? '';

        // Get the plugin slug from the options
        $plugin_slug = isset( $options['hook_extra']['plugin'] ) 
                    ? $options['hook_extra']['plugin'] 
                    : '';

        // Check if the package is from GitHub and matches your plugin slug
        if ( strpos( $package, 'buddyclients.com' ) !== false && 
            $destination === WP_PLUGIN_DIR && 
            $plugin_slug === $this->plugin_slug ) {
                
                // Modify the destination path to avoid the version number
                $options['destination'] = path_join( WP_PLUGIN_DIR, dirname( $plugin_slug ) );
        }

        return $options;
    }
    
    /**
     * Checks for plugin updates.
     *
     * Integrates with the WordPress update system, checking if a new version of the 
     * plugin is available. If a newer version exists, it modifies the transient to include 
     * the update details.
     *
     * @param object $transient The update transient that holds information about all plugins and themes.
     * 
     * @return object $transient The modified transient object, possibly with an added update for this plugin.
     */
    public function check_update( $transient ) {
        // If there are no checked plugins, return the transient as is.
        if ( empty( $transient->checked ) ) {
            return $transient;
        }
    
        // Fetch the version of the plugin from the remote server.
        $remote_version = $this->get_remote( 'version' );
        $package_url = $this->get_remote( 'package' );
    
        // Compare the current version with the remote version.
        // If the remote version is newer, add the update details to the transient.
        if ( version_compare( $this->current_version, $remote_version, '<' ) ) {
            $obj = new stdClass();
            $obj->slug = $this->slug; // Plugin slug identifier.
            $obj->new_version = $remote_version; // The new version available.
            $obj->url = $this->update_path; // URL to more info about the update.
            $obj->package = $package_url; // URL to download the update package.
            
            // Add the update information to the transient's response property.
            $transient->response[$this->plugin_slug] = $obj;
        }
    
        // Return the modified transient object.
        return $transient;
    }
    
    /**
     * Provides plugin information for the 'View details' link in the plugin list.
     *
     * Hooks into the plugin information request process. Returns detailed 
     * information about the plugin when the user clicks on the 'View details' link.
     *
     * @param bool    $false   Default false, passed to the filter. If no information is found, return false.
     * @param array   $action  An array containing the action type (e.g., 'plugin_information').
     * @param object  $arg     An object containing details of the plugin being queried, including the slug.
     * 
     * @return bool|object     Returns plugin information object if found, or false if not applicable.
     */
    public function check_info( $false, $action, $arg ) {
        // Check if the requested plugin's slug matches this plugin's slug.
        if ( property_exists( $arg, 'slug' ) && $arg->slug === $this->slug ) {
            // Retrieve plugin information from external server
            $information = $this->get_remote( 'info' );

            // Return the plugin information object to provide details for the 'View details' link.
            return $information;
        }
    
        // Return false if the plugin slug does not match, indicating no relevant info.
        return false;
    }
    
    /**
     * Retrieves detailed information about the plugin from a remote server.
     *
     * Sends a POST request to the remote server to retrieve either the plugin version,
     * detailed information about the plugin, or the total update count.
     * 
     * @param   string   $action      The remote info to retrieve.
     *                                Accepts 'version', 'info', and 'count'.
     *
     * @return object|false The plugin information object from the remote server, or false if the request fails.
     */
    public function get_remote( $action ) {
        // Send a POST request to the remote server to retrieve plugin information.
        $request = wp_remote_post( $this->update_path, array( 'body' => array( 'action' => $action ) ) );
    
        // If the request is successful and the response code is 200, unserialize the response and return it.
        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
            return unserialize( $request['body'] );
        }
    
        // Return false if the request fails or if an error is encountered.
        return false;
    }
    
    /**
     * Tracks the successful update of the plugin.
     *
     * This method is triggered after a plugin has been successfully updated.
     *
     * @param object $upgrader_object The upgrader object instance.
     * @param array $hook_extra Extra information about the upgrade.
     * 
     * @return void
     */
    public function track_update( $upgrader_object, $hook_extra ) {
        // Check if the action was for updating a plugin
        if ( $hook_extra['type'] === 'plugin' ) {
            // Get the updated plugin slug
            $plugin = $hook_extra['plugins'][0];
    
            // Optionally check if the updated plugin matches this plugin
            if ( $plugin === $this->plugin_slug ) {
                // Check if the update was successful
                if ( isset( $upgrader_object->result ) && is_wp_error( $upgrader_object->result ) ) {
                    error_log( 'Plugin update failed: ' . $upgrader_object->result->get_error_message() );
                    return; // Exit if the update was not successful
                }
    
                // Fetch the total count from the remote server if the update was successful
                $remote_count = $this->get_remote( 'count' );
    
                // Check if fetching remote count was successful
                if ( ! $remote_count ) {
                    error_log( 'Failed to retrieve remote update count.' );
                }
            }
        }
    }
}