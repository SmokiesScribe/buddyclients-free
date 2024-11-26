<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\ProfileExtension;

/**
 * Generates a link to a user's profile.
 * 
 * @since 0.4.0
 * 
 * @param   array   $args {
 *     An array of arguments to build the link.
 * 
 *     @int     $user_id    Optional. The ID of the user.
 *     @string  $slug       Optional. The slug to append to the profile link.
 * }
 */
function buddyc_profile_link( $args = null ) {
    $user_id = isset( $args['user_id'] ) ? $args['user_id'] : get_current_user_id();
    $profile_link = bp_core_get_userlink( $user_id, false, true );
    return isset( $args['slug'] ) ? trailingslashit( $profile_link ) . $args['slug'] : $profile_link;
}

/**
 * Retrieves profile extension link.
 * 
 * @since 0.1.0
 * 
 * @param   string  $key   The profile extension key.
 */
function buddyc_profile_ext_link( $key ) {
    return ProfileExtension::link( $key );
}