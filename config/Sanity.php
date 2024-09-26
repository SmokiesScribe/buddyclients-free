<?php
namespace BuddyClients\Config;

use BuddyClients\Includes\DatabaseManager;

/**
 * Handles sanity checks.
 * 
 * @since 0.4.3
 */
class Sanity {
    
    /**
     * Hash Database instance.
     * 
     * @var DatabaseManager|null
     */
    protected static $hash_database = null;
    
    /**
     * Initializes the hash Database.
     * 
     * @since 0.4.3
     */
    private static function init_hash_database() {
        if (self::$hash_database === null) {
            self::$hash_database = new DatabaseManager('hash');
        }
    }
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        if ( ! defined( 'BC_PLUGIN_VERSION' ) ) {
        	return;
        }
        
        // Initialize database
        self::init_hash_database();
        
        // Run hash updates
        $this->run_hash_updates();
        
        // Output hash
        //$this->output_hashes();
    }
    
    /**
     * Runs process to check and update hash database.
     * 
     * @since 0.4.3
     */
    private function run_hash_updates() {
        // Check if database has been updated for current version
        if ( get_option( 'bc_last_hash_update' ) < BC_PLUGIN_VERSION ) {
            
            // Update hash database
            $success = $this->update_hash_database();
            
            // Set the update flag
            if ( $success ) {
                update_option( 'bc_last_hash_update', BC_PLUGIN_VERSION );
            }
        }
        
        // Schedule the daily event if not already scheduled
        if ( ! wp_next_scheduled( 'bc_hash_database_update' ) ) {
            wp_schedule_event( time(), 'daily', 'bc_hash_database_update' );
        }
        
        // Hook into the scheduled event
        add_action( 'bc_hash_database_update', [ $this, 'update_hash_database' ] );
    }
    
    /**
     * Updates the hash database.
     * 
     * @since 0.1.0
     */
    public function update_hash_database() {
        
        // Get hash records
        $records = $this->get_hash_records();

        if ( empty( $records ) ) {
            return false;
        }

        // Update the database
        foreach ( $records as $record ) {
            // Cast object to array
            $array = (array) $record;
            
            // Unset ID to avoid conflicts
            unset( $array['ID'] );
            
            // Insert into database
            $record_id = self::$hash_database->insert_record( $array );
            
            // Check if successful
            if ( ! $record_id ) {
                // Return false on failure
                return false;
            }
        }
        
        // Return success
        return true;
    }
    
    /**
     * Retrieves hash records for the current plugin version.
     * 
     * @since 0.1.0
     */
    private function get_hash_records() {
        
        // Build the request url
        $key_url = $this->hash_key_url() . '&version=' . BC_PLUGIN_VERSION;
        
        // Get the response
        $response_json = wp_remote_get( $key_url );
        
        // Check for an error
        if ( is_wp_error( $response_json ) ) {
            return;
        }
        
        // Extract body
        $body = wp_remote_retrieve_body( $response_json );
        
        if ( empty( $body ) ) {
            return;
        }
        
        $records = json_decode( $body );
        
        if ( empty( $records ) ) {
            return;
        }
        
        return $records;
    }
    
    /**
     * Defines the server url.
     * 
     * @since 1.0
     */
    private function hash_key_url() {
        $key_url = $this->server_base() . 'endpoints/hash.php?api_key=' . $this->api_key();
        return $key_url;
    }
    
    /**
     * Defines the server base.
     * 
     * @since 1.0
     */
    private function server_base() {
        $base = 'https://buddyclients.com/wp-content/plugins/buddyclients-admin/';
        return $base;
    }
    
    /**
     * Defines the API key.
     * 
     * @since 1.0
     */
    private function api_key() {
        $api_key = 'vala';
        return $api_key;
    }
    
    /**
     * Retrieves hash by key and version.
     * 
     * @since 0.4.3
     * 
     * @param   string  $key        The hash key.
     * @param   string  $version    Optional. The plugin version.
     *                              Defaults to the current plugin version.
     */
    public static function get_hash( $key, $version = null ) {
        $version = $version ?? BC_PLUGIN_VERSION;
        
        // Initialize database
        self::init_hash_database();
        
        // Get records for version
        $records = self::$hash_database->get_all_records_by_column( 'version', $version );
        
        // If records exist
        if ( $records ) {
            
            // Loop through records
            foreach ( $records as $record ) {
                
                // Make sure the key and version match
                if ( $record->key === $key && $record->version === $version ) {
                    return $record->hash;
                }
            }
        }
    }
    
    /**
     * Defines file paths.
     * 
     * @since 0.4.3
     */
    private static function file_paths() {
        $files = [
            'autoload'          => plugin_dir_path( BC_PLUGIN_FILE ) . 'config/Autoloader.php',
            'buddyclients'      => plugin_dir_path( BC_PLUGIN_FILE ) . 'BuddyClients.php',
            'licensehandler'    => plugin_dir_path( BC_PLUGIN_FILE ) . 'config/LicenseHandler.php',
            'sanity-check'      => plugin_dir_path( BC_PLUGIN_FILE ) . 'config/helpers/sanity-check.php',
        ];
        return $files;
    }
    
    /**
     * Runs a sanity check.
     * 
     * @since 0.4.3
     */
    public function sanity_check() {
        // Check the last run time of the sanity check
        $last_run = get_option( 'bc_last_sanity_check' );

        // Check if the last run was less than 24 hours ago AND last run was clear
        if ( $last_run && ( time() - $last_run ) < DAY_IN_SECONDS
            && ! defined( 'BC_SANITY_ALERT' ) ) {
            return;
        }
    
        // Defines files to check
        $files = self::file_paths();
    
        // Flag to track if any tampered file is detected
        $tampered_files = [];
    
        // Loop through files
        foreach ( $files as $key => $path ) {
            if ( file_exists( $path ) ) {
                $current_hash = self::build_hash( $path );
                $expected_hash = self::get_hash( $key );
    
                // Compare hashes only if both are valid
                if ( $this->is_valid_hash( $current_hash ) && $this->is_valid_hash( $expected_hash ) ) {
                    if ( ! $this->compare_hashes( $current_hash, $expected_hash ) ) {
                        $tampered_files[] = basename( $path );
                    }
                } else {
                    // Log or handle cases where data is incomplete (optional)
                    error_log( __( "Sanity check skipped for {$key}: Invalid hash.", "buddyclients" ) );
                }
            }
        }
    
        // Define the alert only if a tampered file was detected and data was complete
        if ( ! empty( $tampered_files ) && ! defined( 'BC_SANITY_ALERT' ) ) {
            $message = sprintf( __( 'One or more plugin files have been tampered with: %s' ), implode( ', ', $tampered_files ) );
            define( 'BC_SANITY_ALERT', $message );
            bc_destroy();
        }
    
        // Update the last run time to the current time
        update_option( 'bc_last_sanity_check', time() );
    }
    
    /**
     * Checks if a given string is a valid hash.
     *
     * Validates based on length and character set for common hash algorithms:
     * - SHA-256 (64 characters)
     * - SHA-1 (40 characters)
     * - MD5 (32 characters)
     *
     * @param string $hash The hash string to validate.
     * @return bool True if the string is a valid hash, false otherwise.
     */
    public function is_valid_hash( $hash ) {
        // Check if the hash is a non-empty string
        if ( ! is_string( $hash ) || empty( $hash ) ) {
            return false;
        }
    
        // Check hash format: it should only contain hexadecimal characters (0-9, a-f, A-F)
        if ( ! preg_match( '/^[a-f0-9]+$/i', $hash ) ) {
            return false;
        }
    
        // Check the length of the hash for common algorithms
        $valid_lengths = [32, 40, 64]; // MD5, SHA-1, SHA-256 lengths
    
        if ( ! in_array( strlen( $hash ), $valid_lengths, true ) ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Compares two hashes to determine if they match, accounting for potential hidden differences.
     * 
     * @since 1.0.0
     *
     * @param   string  $hash1  The first hash string to compare.
     * @param   string  $hash2  The second hash string to compare.
     * @return  bool    True if the hashes match, false otherwise.
     */
    private function compare_hashes( $hash1, $hash2 ) {
        // Normalize both hashes by trimming whitespace
        $hash1 = trim( $hash1 );
        $hash2 = trim( $hash2 );
    
        // Check lengths
        if ( strlen( $hash1 ) !== strlen( $hash2 ) ) {
            return false;
        }
    
        // Compare hashes using hash_equals
        if ( ! hash_equals( $hash1, $hash2 ) ) {
            return false;
        }
    
        return true;
    }
    
    /**
     * Builds hash.
     * 
     * @since 0.4.3
     * 
     * @param   string  $path   The file path.
     */
    private static function build_hash( $path ) {
        return hash_file( 'sha256', $path );
    }
    
    /**
     * Outputs current hashes.
     * 
     * @since 0.4.3
     * 
     * @ignore
     */
    public function output_hashes() {
        $files = self::file_paths();
        if ( ! empty( $files ) ) {
            foreach ( $files as $key => $path ) {
                echo $key . ': ' . self::build_hash( $path ) . '<br>';
            }
        }
    }
}