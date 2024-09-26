<?php
use BuddyClients\Includes\Directory as Directory;
/**
 * Ensures the htaccess domain is up to date.
 * 
 * @since 0.3.0
 */
function bc_check_htaccess() {
    new Directory('');
}
add_action('init', 'bc_check_htaccess');