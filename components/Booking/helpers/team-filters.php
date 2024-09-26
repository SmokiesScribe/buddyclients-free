<?php
use BuddyClients\Includes\Project as Project;
/**
 * Handles AJAX calls to check team filter matches.
 * 
 * @since 0.1.3
 * 
 * @return  bool    Whether the filters match.
 */
function bc_team_filter_match() {
    
    // Initialize
    $match = true;
    
    // Get data from javascript post
    $team_id = $_POST['team_id'];
    $filter_values = $_POST['filter_values'];
    
    // Make sure all params available
    if ( $team_id && $filter_values ) {
        // Loop thorugh filter values
        foreach ( $filter_values as $filter_id => $filter_value ) {
            
            // Get xprofile field id
            $xprofile_id = get_post_meta( $filter_id, 'xprofile_field', true );
            
            // Get match type
            $match_type = get_post_meta( $filter_id, 'match_type', true );
            
            // Get user selection
            $team_selection = xprofile_get_field_data( $xprofile_id, $team_id );
            
            // Normalize single values to arrays
            if ( ! is_array( $team_selection ) ) {
                $team_selection = [$team_selection];
            }
            if ( ! is_array( $filter_value ) ) {
                $filter_value = [$filter_value];
            }
            
            // Check for intersection
            $intersection = array_intersect( $team_selection, $filter_value );
            
            // Check if user selection matches
            switch ( $match_type ) {
                case 'exact':
                    // Check if they match exactly
                    $match = ( $team_selection == $filter_values );
                    break;
                case 'include_any':
                    // Check if there is any intersection
                    $match = ( ! empty( $intersection ) );
                    break;
                case 'include_all':
                    // Check if all selected values are in the team selection
                    $match = ( count( $intersection ) === count( $filter_values ) );
                    break;
                case 'exclude':
                    // Check if there is any intersection
                    $match = ( empty( $intersection ) );
                    break;
            }
        }
    }
    
    // Return the object
    echo json_encode( $match );

    wp_die(); // Terminate
}
add_action('wp_ajax_bc_team_filter_match', 'bc_team_filter_match'); // For logged-in users
add_action('wp_ajax_nopriv_bc_team_filter_match', 'bc_team_filter_match'); // For logged-out users