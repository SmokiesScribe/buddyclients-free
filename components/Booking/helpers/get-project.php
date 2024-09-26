<?php
use BuddyClients\Includes\Project as Project;
/**
 * Handles AJAX calls to retrieve project data.
 * 
 * @since 0.1.3
 * 
 * @pararm  int $project_id The ID of the project to retrieve.
 */
function bc_get_project() {
    
    // Get project id from javascript post
    $project_id = $_POST['project_id'];
    
    // Get project object
    $project = new Project( $project_id );
    
    // Return the object
    echo json_encode( $project );

    wp_die(); // Terminate
}
add_action('wp_ajax_bc_get_project', 'bc_get_project'); // For logged-in users
add_action('wp_ajax_nopriv_bc_get_project', 'bc_get_project'); // For logged-out users