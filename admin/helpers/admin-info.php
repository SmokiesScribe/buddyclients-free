<?php
use BuddyClients\Admin\AdminInfo as AdminInfo;
/**
 * Initializes the AdminInfo class.
 * 
 * @since 0.3.0
 */
function buddyc_admin_info( $active_tab ) {
    if ( class_exists( AdminInfo::class ) ) {
        // Check setting
        if ( buddyc_get_setting( 'general', 'admin_info' ) === 'disable' ) {
            return;
        }
        new AdminInfo( $active_tab['label'] );
    }
}
add_action( 'buddyc_admin', 'buddyc_admin_info', 10, 1 );