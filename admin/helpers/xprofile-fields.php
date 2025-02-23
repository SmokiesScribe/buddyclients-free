<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Builds array of all Xprofile filter field options.
 * 
 * @since 0.1.0
 */
function buddyc_xprofile_filter_options() {
    // Initialize array
    $options = array();
    
    // Get all exprofile fields
    $fields = buddyc_all_xprofile();
        
    foreach ($fields as $field_id => $field_data) {
        
        // Get team types
        $settings = get_option('buddyc_general_settings', array());
        $team_types = isset($settings['team_types']) ? $settings['team_types'] : array();
        
        // Make sure field is for team
        if (!array_intersect($field_data['member_types'], $team_types)) {
            continue;
        }
        
        // Get or create xprofile field id
        if (function_exists('buddyc_team_member_role_xprofile_field')) {
   //         $role_field_id = buddyc_team_member_role_xprofile_field();
        } else {
            $role_field_id = '';
        }

        if ($field_data['type'] !== 'checkbox' && $field_data['type'] !== 'selectbox' && $field_id !== $role_field_id) {
            continue;
        } else {
            // Add to array
            $options[$field_id] = $field_data['name'];
        }
    }
    return $options;
}