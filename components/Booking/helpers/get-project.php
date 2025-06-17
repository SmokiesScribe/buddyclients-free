<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Project;
/**
 * Handles AJAX calls to retrieve project data.
 * 
 * @since 0.1.3
 * 
 * @pararm  int $project_id The ID of the project to retrieve.
 */
function buddyc_get_project() {
    
    // Verify nonce
    $valid = buddyc_verify_ajax_nonce( 'booking_form' );
    if ( ! $valid ) return;
    
    // Get project id from javascript post
    $project_id = isset($_POST['project_id']) ? intval(wp_unslash($_POST['project_id'])) : null;
    
    // Get project object
    $project = new Project( $project_id );
    
    // Return the object
    echo wp_json_encode( $project );

    wp_die(); // Terminate
}
add_action('wp_ajax_buddyc_get_project', 'buddyc_get_project'); // For logged-in users
add_action('wp_ajax_nopriv_buddyc_get_project', 'buddyc_get_project'); // For logged-out users