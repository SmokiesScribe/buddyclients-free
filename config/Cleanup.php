<?php
namespace BuddyClients\Config;

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
        // Schedule daily run
        $this->daily_cleanup();
        
        // Define hooks
        $this->define_hooks();
    }

    /**
     * Defines hooks.
     * 
     * @since 0.1.0
     */
    public function define_hooks() {
        // Hook cleanup method to scheduled event
        add_action('cleanup_event', [$this, 'run_daily_cleanup']);
    }
    
    /**
     * Schedule cleanup event.
     *
     * @since 0.1.0
     */
    public function daily_cleanup() {
        // FILE 63 - Temporary for testing
        if ( ! wp_next_scheduled('cleanup_event') ) {
            wp_schedule_event(time(), 'daily', 'cleanup_event');
        }
    }

    /**
     * Performs all cleanup tasks.
     *
     * @since 0.1.0
     */
    public function run_daily_cleanup() {
        $this->cleanup_email_log();
        $this->cleanup_temp_files();
    }
    
    /**
     * Cleans up email database.
     *
     * @since 0.1.0
     */
    public function cleanup_email_log() {
        Email::cleanup_database();
    }
    
    /**
     * Cleanup temporary files.
     *
     * @since 0.1.0
     */
    public function cleanup_temp_files() {
        FileHandler::clear_temporary_files();
    }
}
