<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Checks whether any valid services exist.
 * 
 * @since 0.4.0
 * 
 * @return  bool    True if services exist, false if not.
 */
function buddyc_services_exist() {
    // Define post query args
    $args = [
        'meta' => ['valid' => 'valid'], // valid services only
        'max' => 1 // just get the first service
    ];

    // Query the database
    $services = buddyc_post_query( 'buddyc_service', $args );

    // Return true if services exist
    return ! empty( $services );
}

/**
 * Retrieves the adjustment options for an adjustment post.
 * 
 * @since 0.1.0
 */
function buddyc_adjustment_options( $post_id ) {
    $adjustment = buddyc_get_service_cache( 'adjustment', $post_id );
    return $adjustment->get_options();
}

/**
 * Retrieves the number of adjustment options for an adjustment post.
 * 
 * @since 0.1.0
 */
function buddyc_adjustment_options_count( $post_id ) {
    $adjustment = buddyc_get_service_cache( 'adjustment', $post_id );
    return $adjustment->get_options_count();
}