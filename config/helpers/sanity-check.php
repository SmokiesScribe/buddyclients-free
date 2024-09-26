<?php
use BuddyClients\Config\Sanity;
/**
 * Makes sure everything is five by five.
 * 
 * @since 0.4.3
 */
function bc_sanity_check() {
    $sanity = new Sanity;
    $sanity->sanity_check();
}

/**
 * Stops BuddyClients plugin processing.
 * 
 * @since 0.2.1
 * 
 * @ignore
 */
function bc_destroy() {
    add_filter( 'bc_sanity_check', '__return_false' );
}