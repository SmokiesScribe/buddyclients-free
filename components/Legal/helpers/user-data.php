<?php
use BuddyClients\Components\Legal\Legal as Legal;
/**
 * Retrieves a user's legal data.
 * 
 * @since 0.2.6
 * 
 * @param   string  $type       The type of legal data to retrieve.
 *                              Accepts 'team', 'affiliate', 'client'.
 * @param   int     $user_id    The ID of the user.
 *                              Defaults to current user.
 */
function bc_legal_user_data( $type, $user_id = null ) {
    // New legal instance
    $legal = new Legal( $type, $user_id );
    
    // Get user data
    return $legal->get_user_data( $user_id );
}