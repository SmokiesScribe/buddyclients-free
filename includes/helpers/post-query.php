<?php
use BuddyClients\Includes\PostQuery as PostQuery;
/**
 * Performs a post query.
 * 
 * @since 0.4.0
 * 
 * @param   int     $post_type  The slug of the post type to query.
 * @param   array   $meta       Optional. An associative array of meta keys and values to match.
 */
function bc_post_query( $post_type, $meta = null ) {
    $posts = $meta && is_array( $meta ) ? new PostQuery( $post_type, $meta ) : new PostQuery( $post_type );
    return $posts ? $posts->posts : null;
}