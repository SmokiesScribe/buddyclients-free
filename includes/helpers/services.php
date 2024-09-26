<?php
/**
 * Checks whether any valid services exist.
 * 
 * @since 0.4.0
 * 
 * @return  bool    True if services exist, false if not.
 */
function bc_services_exist() {
    $services = bc_post_query( 'bc_service', ['valid' => 'valid']);
    if ( $services && ! empty( $services ) ) {
        return true;
    } else {
        return false;
    }
}