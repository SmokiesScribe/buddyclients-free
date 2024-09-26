<?php
/**
 * Helper functions for the Sales component.
 * 
 * @since 0.1.0
 */
    /**
     * Checks whether Sales Mode is enabled.
     * 
     * @since 0.1.0
     * 
     * @return bool     True if Sales Mode is enabled, false if not.
     */
    function bc_sales_enabled() {
        $enabled = bc_get_setting( 'sales', 'sales_team_mode');
        return $enabled === 'yes';
    }
    
    /**
     * Checks whether a user can create sales bookings.
     * 
     * @since 0.1.0
     * 
     * @param   int $user_id    Optional. The ID of the user to check.
     *                          Defaults to the current user.
     * @return  bool
     */
    function bc_is_sales( $user_id = null ) {
        // Default to current user
        $user_id = $user_id ?? get_current_user_id();
        
        // False for logged out users
        if ( ! $user_id ) {
            return false;
        }
        
        // Get setting
        $sales_types = bc_get_setting( 'sales', 'sales_types' );
    
        // Get user type
        $user_member_type = bp_get_member_type( $user_id, true );
        
        // Check if user type is in settings array
        if ( in_array( $user_member_type, $sales_types ) ) {
            return $user_member_type;
        } else {
            return false;
        }
    }