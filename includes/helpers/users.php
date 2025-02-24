<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Check if user is a team member.
 * 
 * @since 0.1.0
 * 
 * @param $user_id (optional. defaults to current user.)
 * @return str | bool $member_type or false
 * 
 */
function buddyc_is_team( $user_id = null ) {
    
    // Exit if function does not exist
    if ( ! function_exists( 'bp_get_member_type') ) {
        return;
    }
    
    // Default to current user
    if ( ! $user_id ) {
        $user_id = get_current_user_id();
    }
    
    // False for logged out users
    if ( ! $user_id ) {
        return false;
    }
    
    // Get setting
    $team_types = buddyc_get_setting('general', 'team_types');
    
    if ( ! $team_types || empty( $team_types ) ) {
        return false;
    }

    // Get user type
    $user_member_type = bp_get_member_type( $user_id, true );
    
    if ( ! $user_member_type ) {
        return;
    }
    
    // Check if user type is in settings array
    if ( in_array( $user_member_type, $team_types ) ) {
        return $user_member_type;
    } else {
        return false;
    }
}

/**
 * Check if user is a client.
 * 
 * @since 0.1.0
 * 
 * @param $user_id (optional. defaults to current user.)
 * @return str | bool $member_type or false
 * 
 */
function buddyc_is_client($user_id = null) {
    if ( ! function_exists( 'bp_get_member_type') ) {
        return;
    }
    
    // Default to current user
    if ( ! $user_id ) {
        $user_id = get_current_user_id();
    }
    
    // False for logged out users
    if ( ! $user_id ) {
        return false;
    }
    
    // Get setting
    $client_types = buddyc_get_setting('general', 'client_types');
    
    if ( ! $client_types || empty( $client_types ) ) {
        return false;
    }

    // Get user type
    $user_member_type = bp_get_member_type($user_id, true);
    
    // Check if user type is in settings array
    if (in_array($user_member_type, $client_types) || $user_member_type === 'client') {
        return $user_member_type;
    } else {
        return false;
    }
}

/**
 * Check if user is a site admin.
 * 
 * @since 0.1.0
 * 
 * @param $user_id (optional. defaults to current user.)
 * @return bool
 * 
 */
function buddyc_is_admin($user_id = null) {
    
    // Default to current user
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    // False for logged out users
    if (!$user_id) {
        return false;
    }
    
    // Get user object
    $user = get_user_by('id',$user_id);
    
    // Check if admin
    $admin = isset($user->caps['administrator']) ? $user->caps['administrator'] : false;
    
    // Check if user is admin
    if ($admin) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get site admin id.
 * 
 * @since 0.1.0
 */
function buddyc_admin_id() {
    
    // Get all users
    $users = get_users();
    
    // Check each users permissions
    foreach ($users as $user) {
        $admin = isset($user->caps['administrator']) ? $user->caps['administrator'] : false;
        if ($admin) {
            // Admin found. Assign and break
            $admin_id = $user->ID;
            break;
        }
    }
    return $admin_id;
}

/**
 * Get all team members.
 * 
 * @since 0.1.0
 */
function buddyc_all_team() {
    // Get team member types
    $team_types = buddyc_get_setting( 'general', 'team_types' );
    
    // Get all users
    $all_team_members = bp_core_get_users(array(
        'member_type' => $team_types,
        'per_page' => false,
    ));
    
    return $all_team_members;
}

/**
 * Get all clients.
 * 
 * @since 0.1.0
 */
function buddyc_all_clients() {
    // Get client types
    $client_types = buddyc_get_setting( 'general', 'client_types' );
    
    // Get all users
    $all_clients = bp_core_get_users(array(
        'member_type' => $client_types,
        'per_page' => false,
    ));
    return $all_clients;
}

/**
 * Retrieves member types.
 * 
 * @since 0.1.0
 * 
 * @param   string  $type   The type of member types to return.
 *                          Accepts 'client', 'team', 'sales'.
 * @return  array   Associative array of member type names and labels.
 */
function buddyc_member_types( $type = null ) {
    
    switch ( $type ) {
        case 'client':
            $member_types = buddyc_get_setting('general', 'client_types');
            break;
        case 'team':
            $member_types = buddyc_get_setting('general', 'team_types');
            break;
        case 'sales':
            $member_types = buddyc_get_setting('sales', 'sales_types');
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