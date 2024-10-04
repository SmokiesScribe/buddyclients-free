<?php
/**
 * Pretty prints an array.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_print( $value ) {
    echo '<pre>';
    print_r( $value );
    echo '</pre>';
}

/**
 * Retrieves the current url.
 * 
 * @since 1.0.4
 * 
 * @return  string  The current url, or an empty string on failure.
 */
function bc_curr_url() {
    $current_url = '';
    if ( isset( $_SERVER['REQUEST_URI'] ) ) {
        // Unsplash and sanitize the request URI
        $request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
        
        // Sanitize the request URI (you can use wp_sanitize_url() or any appropriate sanitization function)
        $current_url = trailingslashit( site_url( $request_uri ) );
    }
    return $current_url;
}