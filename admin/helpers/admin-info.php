<?php
use BuddyClients\Admin\AdminInfo;
/**
 * Initializes the AdminInfo class.
 * 
 * @since 0.3.0
 */
function bc_admin_info( $active_tab ) {
    if ( class_exists( AdminInfo::class ) ) {
        // Check setting
        if ( bc_get_setting( 'general', 'admin_info' ) === 'disable' ) {
            return;
        }
        new AdminInfo( $active_tab['label'] );
    }
}
add_action( 'bc_admin', 'bc_admin_info', 10, 1 );