<?php
namespace BuddyClients\Config;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Email\Email;
use BuddyClients\Includes\FileHandler;

/**
 * Cleans up temporary files and email log.
 * 
 * @since 0.1.0
 */
class Cleanup {
    
    /**
     * Instance of the class.
     *
     * @var Cleanup
     * @since 0.1.0
     */
    protected static $instance = null;
    
    /**
     * BuddyClients Cleanup Instance.
     *
     * @since 0.1.0
     * @static
     * @return Cleanup instance
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        $this->run_cleanup();
    }

    /**
     * Runs the cleanup processes.
     * 
     * @since 1.0.25
     */
    private function run_cleanup() {
        // Check transient
        $cleaned = get_transient( 'buddyc_cleanup_complete' );
        if ( $cleaned ) return;

        $this->cleanup_email_log();
        $this->cleanup_temp_files();

        // Set transient
        set_transient( 'buddyc_cleanup_complete', true, DAY_IN_SECONDS );
    }
    
    /**
     * Cleans up email database.
     *
     * @since 0.1.0
     */
    public function cleanup_email_log() {
        if ( class_exists( Email::class ) ) {
            Email::cleanup_database();
        }
    }
    
    /**
     * Cleanup temporary files.
     *
     * @since 0.1.0
     */
    public function cleanup_temp_files() {
        if ( class_exists( FileHandler::class ) ) {
            FileHandler::clear_temporary_files();
        }
    }
}
