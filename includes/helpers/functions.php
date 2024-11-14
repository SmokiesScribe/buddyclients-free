<?php
/**
 * Retrieves colors from settings.
 * 
 * @since 0.1.0
 * 
 * @param   string  $type   The color type to retrieve.
 *                          Accepts 'primary', 'accent', and 'tertiary'.
 */
function bc_color( $type ) {
    return bc_get_setting('style', $type . '_color');
}

/**
 * Echoes colors from settings.
 * 
 * @since 1.0.19
 * 
 * @param   string  $type   The color type to retrieve.
 *                          Accepts 'primary', 'accent', and 'tertiary'.
 */
function e_bc_color( $type ) {
    echo esc_attr( bc_color( $type ) );
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
        $request_uri = urldecode( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
        
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

/**
 * Checks whether a time has passed.
 * 
 * @since 1.0.17
 * 
 * @param   string|int  $target_time    The time to check.
 * 
 * @return  bool        True if the time has passed, false if not.
 */
function bc_time_has_passed( $target_time ) {
    // Cast to unix timestamp if necessary
    if ( is_string( $target_time ) ) {
        $target_time = strtotime( $target_time );
    }
        
    // Get current timestamp
    $current_timestamp = time();

    // Check if time has passed
    return $current_timestamp > $target_time;
}

/**
 * Outputs a javascript alert.
 * 
 * @since 1.0.20
 * 
 * @param   string  $message     The alert text.
 */
function buddyclients_js_alert( $message ) {
    if ( empty( $message ) ) {
        return;
    }

    // Sanitize the message for JavaScript
    $esc_message = esc_js( $message );
    $inline_script = "alert('" . $esc_message . "');";

    // Output the script
    buddyclients_inline_script( $inline_script );
}

/**
 * Adds inline styles.
 * 
 * @since 1.0.20
 * 
 * @param   string  $css    The CSS to add.
 */
function buddyclients_inline_style( $css ) {
    // Define handle
    $handle = 'buddyclients-inline-style';
    
    // Ensure the style is enqueued on wp_enqueue_scripts hook
    add_action( 'wp_enqueue_scripts', function() use ( $handle ) {
        // Register the style if it's not already registered
        if ( ! wp_style_is( $handle, 'registered' ) ) {
            wp_register_style( $handle, false );
        }
        
        // Enqueue the style if it's not already enqueued
        if ( ! wp_style_is( $handle, 'enqueued' ) ) {
            wp_enqueue_style( $handle );
        }
    }, 10, 0 );

    // Add inline styles on wp_enqueue_scripts hook
    add_action( 'wp_enqueue_scripts', function() use ( $handle, $css ) {
        wp_add_inline_style( $handle, $css );
    }, 20, 0 );
}

/**
 * Adds inline scripts.
 * 
 * Enqueues global script if necessary.
 * 
 * @since 1.0.20
 * 
 * @param   string  $script    The JavaScript to add.
 */
function buddyclients_inline_script( $script ) {
    // Define global js handle
    $handle = 'buddyclients-buddyclients-class-global';

    // Enqueue global script on wp_enqueue_scripts hook
    add_action( 'wp_enqueue_scripts', function() use ( $handle ) {
        // Register the script if it's not registered already
        if ( ! wp_script_is( $handle, 'registered' ) ) {
            wp_register_script( $handle, BC_PLUGIN_URL . 'assets/js/global.js', array(), null, true );
        }
        
        // Enqueue the script if it's not already enqueued
        if ( ! wp_script_is( $handle, 'enqueued' ) ) {
            wp_enqueue_script( $handle );
        }
    }, 10, 0 );

    // Add inline script on wp_enqueue_scripts hook
    add_action( 'wp_enqueue_scripts', function() use ( $handle, $script ) {
        wp_add_inline_script( $handle, $script );
    }, 20, 0 );
}