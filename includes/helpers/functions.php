<?php
/**
 * Retrieves colors from settings.
 * 
 * @since 0.1.0
 * 
 * @param   string  $type   The color type to retrieve.
 *                          Accepts 'primary', 'accent', and 'tertiary'.
 */
function buddyc_color( $type ) {
    return buddyc_get_setting('style', $type . '_color');
}

/**
 * Echoes colors from settings.
 * 
 * @since 1.0.19
 * 
 * @param   string  $type   The color type to retrieve.
 *                          Accepts 'primary', 'accent', and 'tertiary'.
 */
function e_buddyc_color( $type ) {
    echo esc_attr( buddyc_color( $type ) );
}

/**
 * Retrieves the current url.
 * 
 * @since 1.0.4
 * 
 * @return  string  The current url, or an empty string on failure.
 */
function buddyc_curr_url() {
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
function buddyc_truncate_content($content, $word_count) {
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
function buddyc_truncate_content_by_char($content, $char_count) {
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
function buddyc_format_status( $value, $add_class = null ) {
    
    // Replace underscores and hyphens
    $formatted_value = str_replace( '_', ' ', $value );
    $formatted_value = str_replace( '-', ' ', $formatted_value );
    
    // Capitalize words
    $formatted_value = ucwords( $formatted_value );
    
    // Add original value as class
    if ( $add_class ) {
        $formatted_value = '<span class="buddyc-status ' . esc_attr( $value ) . '">' . esc_html( $formatted_value ) . '</span>';
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
function buddyc_time_has_passed( $target_time ) {
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
* @param   bool    $admin     Optional. Whether to also apply the script to the admin area.
 *                             Defaults to false (i.e., front end).
 */
function buddyclients_js_alert( $message, $admin = false ) {
    if ( empty( $message ) ) {
        return;
    }

    // Sanitize the message for JavaScript
    $esc_message = esc_js( $message );
    $inline_script = "alert('" . $esc_message . "');";

    // Output the script
    buddyclients_inline_script( $inline_script, $admin );
}

/**
 * Adds inline styles to the front end.
 * 
 * @since 1.0.20
 * 
 * @param   string  $css    The CSS to add.
 * @param   bool    $admin  Optional. Whether to also apply the styles to the admin area.
 *                          Defaults to false.
 * 
 * Note: This function should be called on a hook that runs before `wp_enqueue_scripts`, 
 * such as `init`, to ensure the styles are properly enqueued and applied.
 */
function buddyclients_inline_style( $css, $admin = false ) {
    // Define handle
    $handle = 'buddyclients-inline-style';

    // Use the appropriate hook based on the $admin flag
    $hook = $admin ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
    
    // Delay the style registration until the appropriate action is fired
    add_action( $hook, function() use ( $handle, $css ) {
        // Register the style if it's not already registered
        if ( ! wp_style_is( $handle, 'registered' ) ) {            
            wp_register_style( $handle, false );
        }
        
        // Enqueue the style if it's not already enqueued
        if ( ! wp_style_is( $handle, 'enqueued' ) ) {
            wp_enqueue_style( $handle );
        }
        
        // Add the inline styles
        wp_add_inline_style( $handle, $css );
    }, 10, 0 );
}

/**
 * Adds inline scripts to the front end or admin area.
 * 
 * @since 1.0.20
 * 
 * @param   string  $script    The JavaScript to add.
 * @param   bool    $admin     Optional. Whether to also apply the script to the admin area.
 *                             Defaults to false (i.e., front end).
 * @param   bool    $direct    Optional. Whether to call the inline script function immediately,
 *                             as opposed to using a hook. Defaults to false.
 */
function buddyclients_inline_script( $script, $admin = false, $direct = false ) {
    // Define global js handle
    $handle = 'buddyclients-buddyclients-class-global';

    // Determine the correct hook based on the $admin flag
    $hook = $admin ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';

    // Enqueue global script on the appropriate hook
    add_action( $hook, function() use ( $handle ) {
        // Register the script if it's not registered already
        if ( ! wp_script_is( $handle, 'registered' ) ) {
            wp_register_script( $handle, BUDDYC_PLUGIN_URL . 'assets/js/global.js', array(), null, true );
        }

        // Enqueue the script if it's not already enqueued
        if ( ! wp_script_is( $handle, 'enqueued' ) ) {
            wp_enqueue_script( $handle );
        }
    }, 10, 0 );

    if ( $direct ) {
        // Call directly
        wp_add_inline_script( $handle, $script );
    } else {
        // Add inline script on the appropriate hook
        add_action( $hook, function() use ( $handle, $script ) {
            wp_add_inline_script( $handle, $script );
        }, 20, 0 );
    }
}