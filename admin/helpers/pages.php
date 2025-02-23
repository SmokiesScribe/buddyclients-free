<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\PluginPage;

/**
 * Handle create page button clicks.
 * 
 * @since 0.1.0
 */
function buddyc_create_plugin_page() {

    // Get the nonce from the AJAX request
    $nonce = isset( $_POST['nonce'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ) : null;
    $nonce_action = isset( $_POST['nonceAction'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonceAction'] ) ) ) : null;

    // Verify nonce
    if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
        return;
    }

    // Check for args
    if ( isset( $_POST['args']['page_key'], $_POST['args'] ) ) {
        
        // Sanitize each element in the 'args' array
        $args_sanitized = array_map( 'sanitize_text_field', wp_unslash( $_POST['args'] ) );

        // Unslash and sanitize 'page_key'
        $page_key = sanitize_text_field( wp_unslash( $_POST['args']['page_key'] ) );
        
        // Proceed with PluginPage creation using sanitized inputs
        $plugin_page = ( new PluginPage( $page_key ) )->create_page( $args_sanitized );
    } else {
        // Handle the case where 'args' or 'page_key' is not set
        $error_message = __( 'Invalid input for creating new page.', 'buddyclients' );
        echo wp_json_encode([ 
            'success' => false, 
            'error_message' => $error_message
        ]);
    }  
    
    // Check if page was created
    if ( $plugin_page->post_id ) {
        echo wp_json_encode( [
            'success' => true, 
            'edit_post_url' => $plugin_page->edit_post_url, 
            'permalink' => $plugin_page->permalink, 
            'new_page_id' => $plugin_page->post_id
        ]);
        wp_die();
        
    } else {
        // If page creation failed, return an error message
        $error_message = __( 'Failed to create a new page.', 'buddyclients' );
        echo wp_json_encode([ 
            'success' => false, 
            'error_message' => $error_message
        ]);
    }

    // Exit to avoid further execution
    wp_die();
}
add_action('wp_ajax_buddyc_admin_create_new_page', 'buddyc_create_plugin_page');