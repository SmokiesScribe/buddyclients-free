<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Generates a help link describing the team selection process.
 * 
 * @since 0.2.10
 */
function buddyc_team_select_help() {
    $post_id = buddyc_get_reference_post_id( 'team_select' );
    return buddyc_help_link( $post_id );
}