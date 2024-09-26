<?php
/**
 * Generates a help link describing the team selection process.
 * 
 * @since 0.2.10
 */
function bc_team_select_help() {
    $post_id = bc_get_reference_post_id( 'team_select' );
    return bc_help_link( $post_id );
}