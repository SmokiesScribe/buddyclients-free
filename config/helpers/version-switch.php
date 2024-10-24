<?php
/**
 * Handles switch between BuddyClients and BuddyClients Free.
 *
 * @since 1.0.14
 */
function bc_handle_version_switch() {
    // Check the current version
    $prev_version = get_option( 'bc_plugin_name', null );

    if ( $prev_version && $prev_version !== BC_PLUGIN_NAME ) {
        $new_version = BC_PLUGIN_NAME;
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

    // Update option
    update_option( 'bc_plugin_name', BC_PLUGIN_NAME );
}
add_action( 'init', 'bc_handle_version_switch' );