<?php
use BuddyClients\Includes\ParamManager;
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

        // Initialize param manager with current url
        $param_manager = new ParamManager;

        // Add params to the url
        $new_url = $param_manager->add_params( $url_params );
    
        // Redirect to the new URL
        header('Location: ' . $new_url);
        exit;
    }
}
add_action('admin_init', 'bc_admin_filter_submission');