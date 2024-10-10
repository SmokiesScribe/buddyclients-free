<?php
use BuddyClients\Includes\Directory;
use BuddyClients\Config\Cleanup;
/**
 * Ensures the htaccess domain is up to date.
 * 
 * @since 0.3.0
 */
function bc_check_htaccess() {
    new Directory('');
}
add_action('init', 'bc_check_htaccess');

/**
 * Initializes Cleanup manager.
 * 
 * @since 0.1.0
 */
function bc_init_cleanup() {
    Cleanup::instance();
}
add_action('init', 'bc_init_cleanup');