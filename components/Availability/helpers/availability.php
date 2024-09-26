<?php
/**
 * Retrieves a team member's availability.
 * 
 * @since 0.2.2
 * 
 * @param   int     $user_id    The ID of the user.
 */
function bc_get_availability( $user_id ) {
    if ( ! bc_component_enabled( 'Availability' ) ) {
        return;
    }
    
    // Get availability
    $availability = new BuddyClients\Components\Availability\Availability( $user_id );
    
    // Make sure availability has not expired
    if ( ! BuddyClients\Components\Availability\Availability::expired( $availability->availability ) ) {
        return $availability->human_readable;
    }
}