<?php
use BuddyClients\Admin\PluginPage;

/**
 * Handle create page button clicks.
 * 
 * @since 0.1.0
 */
function bc_create_plugin_page() {
    
    // Create page and return object
    $plugin_page = ( new PluginPage( $_POST['args']['page_key'] ) )->create_page( $_POST['args'] );
    
    // Check if page was created
    if ( $plugin_page->post_id ) {
        echo json_encode( [
            'success' => true, 
            'edit_post_url' => $plugin_page->edit_post_url, 
            'permalink' => $plugin_page->permalink, 
            'new_page_id' => $plugin_page->post_id
        ]);
        wp_die();
        
    } else {
        // If page creation failed, return an error message
        $error_message = __( 'Failed to create a new page.', 'buddyclients' );
        echo json_encode([ 
            'success' => false, 
            'error_message' => $error_message
        ]);
    }

    // Exit to avoid further execution
    wp_die();
}
add_action('wp_ajax_bc_admin_create_new_page', 'bc_create_plugin_page');