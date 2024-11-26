<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Flush permalinks
 * 
 * @since 0.1.0
 */
function buddyc_flush_permalinks() {
    $timestamp = get_transient('buddyc_flush_permalinks');
    $flushed = get_transient('buddyc_flush_permalinks_complete');
    if ($timestamp !== $flushed) {
        flush_rewrite_rules();
        set_transient('buddyc_flush_permalinks_complete', $timestamp);
    }
}
add_action('init', 'buddyc_flush_permalinks');