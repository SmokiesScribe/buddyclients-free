<?php
/**
 * Outputs an html-formatted group link.
 * 
 * @since 1.0.17
 * 
 * @param   int $group_id   The ID of the group.
 */
function bc_group_link( $group_id ) {
    if ( function_exists( 'bp_group_link' ) ) {
        ob_start();
        $group = groups_get_group( $group_id );
        bp_group_link( $group );
        return ob_get_clean();
    }
}

/**
 * Outputs the group name.
 * 
 * @since 1.0.17
 * 
 * @param   int $group_id   The ID of the group.
 */
function bc_group_name( $group_id ) {
    if ( function_exists( 'bp_get_group_name' ) ) {
        $group = groups_get_group( $group_id );
        return bp_get_group_name( $group );
    }
}