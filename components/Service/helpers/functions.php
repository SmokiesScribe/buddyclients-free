<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Retrieves the current number of AdjustmentOptions
 * attached to an Adjustment.
 * 
 * @since 1.0.25
 * 
 * @param   int $ID    The ID of the Adjustment.
 */
function buddyc_adjustment_option_count( $ID ) {
    // Default to 10 while saving (no post id)
    $option_count = 10;

    if ( $ID ) {
        // Get Adjustment options count
        $adjustment = buddyc_get_service_cache( 'adjustment', $ID );

        // Get options count - default to 2
        $option_count = $adjustment->options_count === 0 ? 2 : $adjustment->options_count;
    }

    return $option_count;
}