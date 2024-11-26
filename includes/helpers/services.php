<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Service\Adjustment;
/**
 * Checks whether any valid services exist.
 * 
 * @since 0.4.0
 * 
 * @return  bool    True if services exist, false if not.
 */
function buddyc_services_exist() {
    $services = buddyc_post_query( 'buddyc_service', ['valid' => 'valid']);
    if ( $services && ! empty( $services ) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Retrieves the adjustment options for an adjustment post.
 * 
 * @since 0.1.0
 */
function buddyc_adjustment_options( $post_id ) {
    $adjustment = new Adjustment( $post_id );
    return $adjustment->get_options();
}

/**
 * Retrieves the number of adjustment options for an adjustment post.
 * 
 * @since 0.1.0
 */
function buddyc_adjustment_options_count( $post_id ) {
    $adjustment = new Adjustment( $post_id );
    return $adjustment->get_options_count();
}