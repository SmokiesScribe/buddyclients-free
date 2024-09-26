<?php
use BuddyClients\Components\Service\Adjustment as Adjustment;
/**
 * Retrieves the adjustment options for an adjustment post.
 * 
 * @since 0.1.0
 */
function bc_adjustment_options( $post_id ) {
    $adjustment = new Adjustment( $post_id );
    return $adjustment->get_options();
}

/**
 * Retrieves the number of adjustment options for an adjustment post.
 * 
 * @since 0.1.0
 */
function bc_adjustment_options_count( $post_id ) {
    $adjustment = new Adjustment( $post_id );
    return $adjustment->get_options_count();
}