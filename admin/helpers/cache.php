<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\VersionCache;

/**
 * Initializes VersionCache.
 * 
 * @since 1.0.25
 */
function buddyc_version_cache() {
    return VersionCache::get_instance();
}
add_action( 'init', 'buddyc_version_cache' );