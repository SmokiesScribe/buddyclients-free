<?php
use BuddyClients\Includes\Project;
/**
 * Handles AJAX calls to retrieve project data.
 * 
 * @since 0.1.3
 * 
 * @pararm  int $project_id The ID of the project to retrieve.
 */
function bc_get_project() {

    // Verify nonce
    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : null;
    if ( ! wp_verify_nonce( $nonce, 'bc_create_project_fields' ) ) {
        return;
    }
    
    // Get project id from javascript post
    $project_id = isset($_POST['project_id']) ? intval(wp_unslash($_POST['project_id'])) : null;
    
    // Get project object
    $project = new Project( $project_id );
    
    // Return the object
    echo wp_json_encode( $project );

    wp_die(); // Terminate
}
add_action('wp_ajax_bc_get_project', 'bc_get_project'); // For logged-in users
add_action('wp_ajax_nopriv_bc_get_project', 'bc_get_project'); // For logged-out users