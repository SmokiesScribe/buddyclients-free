<?php
/**
 * Initializes Cleanup manager.
 * 
 * @since 0.1.0
 */
function bc_init_cleanup() {
    BuddyClients\Config\Cleanup::instance();
}
add_action('init', 'bc_init_cleanup');