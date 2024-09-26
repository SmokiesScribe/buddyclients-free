<?php
use BuddyClients\Config\ReferencePosts as ReferencePosts;
/**
 * Retrieves the ID of a reference post by key.
 * 
 * @since 0.2.10
 * 
 * @param   string  $key    The key of the post ID to retrieve.
 */
function bc_get_reference_post_id( $key ) {
    return ReferencePosts::get_reference_post_id( $key );
}

/**
 * Adds a new reference post.
 * 
 * @since 0.4.0
 * 
 * @param   string  $key    The unique key for the reference post.
 * @param   array   $args {
 *     An array of arguments for creating the new reference post.
 * 
 *     @string  $content    The content of the post.
 *     @string  $title      The title of the post.
 * }
 */
function bc_add_reference_post( $key, $args ) {
    return ReferencePosts::add_post( $key, $args );
}