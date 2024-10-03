<?php
/**
 * Handles switch between BuddyClients and BuddyClients Free.
 *
 * @since 1.0.14
 */
public function bc_handle_version_switch() {
    // Initialize flag
    $new_version = false;

    // Check the current version
    $current_version = get_option( 'bc_plugin_name', 'BuddyClients Free' ); // Default to free if not set

    // Perform actions based on the version switch
    if ( BC_PLUGIN_NAME === 'BuddyClients' && $current_version !== 'BuddyClients' ) {        
        update_option( 'bc_plugin_name', 'BuddyClients' );
        $new_version = 'BuddyClients';

    } else if ( BC_PLUGIN_NAME === 'BuddyClients Free' && $current_version !== 'BuddyClients Free' ) {
        update_option( 'bc_plugin_name', 'BuddyClients Free' );
        $new_version = 'BuddyClients Free';
    }

    if ( $new_version ) {
        /**
         * Fires on a switch between BuddyClients and BuddyClients Free.
         *
         * @since 1.0.14
         *
         * @param   string  $new_version    The new plugin name.
         *                                  Accepts 'BuddyClients' and 'BuddyClients Free'.
         */
        do_action( 'bc_version_switch', $new_version );
    }
}
add_action( 'plugins_loaded', 'bc_handle_version_switch' );
