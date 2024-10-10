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
    // Initialize empty string
    $current_url = '';
    // Get current URI
    if ( isset( $_SERVER['REQUEST_URI'] ) ) {
        // Unsplash and sanitize the request URI
        $request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
        
        // Build the current URL
        $current_url = site_url( $request_uri );
    }
    return $current_url;    
}

/**
 * Clips content by word count.
 * 
 * @since 0.1.0
 *  
 * @param string $content Content to truncate.
 * @param int $word_count Number of words.
 * 
 * @return string $content Truncated content.
 * 
 */
function bc_truncate_content($content, $word_count) {
    $content = wp_strip_all_tags($content); // Remove HTML tags
    $content = preg_replace('/\s+/', ' ', $content); // Remove extra whitespace
    $words = explode(' ', $content);
    if (count($words) > $word_count) {
        $words = array_slice($words, 0, $word_count);
        $content = implode(' ', $words);
        $content .= '...'; // Add ellipsis
    }
    return $content;
}

/**
 * Clips content by character count.
 * 
 * @since 0.1.0
 *  
 * @param string $content Content to truncate.
 * @param int $char_count Number of characters.
 * 
 * @return string $content Truncated content.
 * 
 */
function bc_truncate_content_by_char($content, $char_count) {
    $content = wp_strip_all_tags($content); // Remove HTML tags
    $content = preg_replace('/\s+/', ' ', $content); // Remove extra whitespace
    if (strlen($content) > $char_count) {
        $content = substr($content, 0, $char_count); // Truncate by character count
        $content = rtrim($content); // Remove trailing spaces
        $content .= '...'; // Add ellipsis
    }
    return $content;
}

/**
 * Formats status value for display.
 * 
 * @since 0.1.0
 * 
 * @param   string  $value  The value to format.
 */
function bc_format_status( $value, $add_class = null ) {
    
    // Replace underscores and hyphens
    $formatted_value = str_replace( '_', ' ', $value );
    $formatted_value = str_replace( '-', ' ', $formatted_value );
    
    // Capitalize words
    $formatted_value = ucwords( $formatted_value );
    
    // Add original value as class
    if ( $add_class ) {
        $formatted_value = '<span class="bc-status ' . esc_attr( $value ) . '">' . esc_html( $formatted_value ) . '</span>';
    }
    
    return $formatted_value;
}