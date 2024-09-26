<?php
/**
 * Flush permalinks
 * 
 * @since 0.1.0
 */
function bc_flush_permalinks() {
    $timestamp = get_transient('bc_flush_permalinks');
    $flushed = get_transient('bc_flush_permalinks_complete');
    if ($timestamp !== $flushed) {
        flush_rewrite_rules();
        set_transient('bc_flush_permalinks_complete', $timestamp);
    }
}
add_action('init', 'bc_flush_permalinks');