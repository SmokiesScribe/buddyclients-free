<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Service\ServiceCache;

/**
 * Retrieves the cached value or a new instance of a service component class.
 * 
 * @since 1.0.25
 */
function buddyc_get_service_cache( $class_key, $post_id ) {
    return ServiceCache::get_instance( $class_key, $post_id );
}

/**
 * Clears the cache on post updates.
 * 
 * @since 1.0.25
 */
function buddyc_clear_service_cache_on_update( $post_id, $post ) {
    // Prevent clearing cache during autosaves or revisions
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }

    // Get the post type
    $post_type = get_post_type( $post_id );

    // Clear cache
    ServiceCache::clear_cache_on_update( $post_type, $post_id );
}
add_action( 'save_post', 'buddyc_clear_service_cache_on_update', 10, 2 );
