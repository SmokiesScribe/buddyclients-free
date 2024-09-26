<?php
/**
 * Retrieves member types.
 * 
 * @since 0.1.0
 * 
 * @param   string  $type   The type of member types to return.
 *                          Accepts 'client', 'team', 'sales'.
 * @return  array   Associative array of member type names and labels.
 */
function bc_member_types( $type = null ) {
    
    switch ( $type ) {
        case 'client':
            $member_types = bc_get_setting('general', 'client_types');
            break;
        case 'team':
            $member_types = bc_get_setting('general', 'team_types');
            break;
        case 'sales':
            $member_types = bc_get_setting('sales', 'sales_types');
            break;
        default:
            $member_types = bp_get_member_types();
            break;
    }
    
    // Initialize array
    $options = [];
    
    // Loop through member types
    if ($member_types) {
        foreach ($member_types as $member_type) {
            $member_type_obj = bp_get_member_type_object($member_type);
            $member_type_name = $member_type_obj->labels['name'];
            
            $options[$member_type] = $member_type_name;
        }
    }
    return $options;
}