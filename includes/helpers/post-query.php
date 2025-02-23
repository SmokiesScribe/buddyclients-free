<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\PostQuery;
/**
 * Performs a post query.
 * 
 * @since 0.4.0
 * 
 * @param   string  $post_type  The slug of the post type.
 * @param   array   $args {}
 *     An optional array of args for the post query.
 * 
 *     @array   array   $meta           An associative array of meta keys and values.
 *     @string  string  $compare        The compare operator for the meta queries.
 *                                      Defaults to '='.
 *     @array   array   $tax            An associative arary of tax names and tags.
 *     @int     int     $max            The maximum number of posts to retrieve.
 * }
 */
function buddyc_post_query( $post_type, $args = [] ) {
    $post_query = new PostQuery( $post_type, $args );
    return $post_query ? $post_query->posts : null;
}