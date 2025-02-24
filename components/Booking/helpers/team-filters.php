<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Projectg;
/**
 * Handles AJAX calls to check team filter matches.
 * 
 * @since 0.1.3
 * 
 * @return  bool    Whether the filters match.
 */
function buddyc_team_filter_match() {
    
    // Initialize
    $match = true;

    // Get the nonce from the AJAX request
    $nonce = isset( $_POST['nonce'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ) : null;
    $nonce_action = isset( $_POST['nonceAction'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonceAction'] ) ) ) : null;

    // Verify nonce
    if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
        return;
    }
    
    // Get data from JavaScript post
    $team_id = isset($_POST['team_id']) ? intval(wp_unslash($_POST['team_id'])) : null;
    $filter_values = isset($_POST['filter_values']) ? array_map('sanitize_text_field', wp_unslash($_POST['filter_values'])) : null;
    
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
    echo wp_json_encode( $match );

    wp_die(); // Terminate
}
add_action('wp_ajax_buddyc_team_filter_match', 'buddyc_team_filter_match'); // For logged-in users
add_action('wp_ajax_nopriv_buddyc_team_filter_match', 'buddyc_team_filter_match'); // For logged-out users