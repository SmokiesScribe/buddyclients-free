<?php
/**
 * Builds array of all Xprofile filter field options.
 * 
 * @since 0.1.0
 */
function bc_xprofile_filter_options() {
    // Initialize array
    $options = array();
    
    // Get all exprofile fields
    $fields = bc_all_xprofile();
        
    foreach ($fields as $field_id => $field_data) {
        
        // Get team types
        $settings = get_option('bc_general_settings', array());
        $team_types = isset($settings['team_types']) ? $settings['team_types'] : array();
        
        // Make sure field is for team
        if (!array_intersect($field_data['member_types'], $team_types)) {
            continue;
        }
        
        // Get or create xprofile field id
        if (function_exists('bc_team_member_role_xprofile_field')) {
   //         $role_field_id = bc_team_member_role_xprofile_field();
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

/**
 * Builds an array of match type fields based on xprofile filter selections.
 * 
 * @since 0.1.0
 */
function bc_xprofile_match_type_fields() {
    $filter_fields = bc_get_setting( 'booking', 'xprofile_fields' );
    var_dump($filter_fields);
}
