<?php
/**
 * Checks for admin table filter form submission.
 * 
 * @since 0.1.0
 */
function bc_admin_filter_submission() {
    // Check for filter form submission
    if ( isset( $_POST['bc_admin_filter_key'] ) ) {
        
        // Initialize params by resetting to page 1
        $url_params = ['paged' => 1];
        
        // Loop through post data
        foreach ( $_POST as $key => $value ) {
            // Skip submit button and verification field
            if ( strpos( $key, '_filter_submit' ) !== false || $key === 'bc_admin_filter_key' ) {
                continue;
            }
            // Add to params
            $url_params[$key] = $value;
        }
        
        // Get the current URL
        $url = $_SERVER['REQUEST_URI'];
        
        // Parse the URL to extract existing parameters
        $parts = parse_url($url);
        $query = isset($parts['query']) ? $parts['query'] : '';
        parse_str($query, $query_params);
        
        // Merge existing parameters with new parameters
        $merged_params = array_merge($query_params, $url_params);
        
        // Rebuild the query string
        $new_query = http_build_query($merged_params);
        
        // Rebuild the URL with the new query string
        $new_url = $parts['path'] . ($new_query ? '?' . $new_query : '');
    
        // Redirect to the new URL
        header('Location: ' . $new_url);
        exit;
    }
}
add_action('admin_init', 'bc_admin_filter_submission');