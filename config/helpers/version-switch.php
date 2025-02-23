<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Handles switch between BuddyClients and BuddyClients Free.
 *
 * @since 1.0.14
 */
function buddyc_handle_version_switch() {
    // Check the current version
    $prev_version = get_option( 'buddyc_plugin_name', null );

    if ( $prev_version && $prev_version !== BUDDYC_PLUGIN_NAME ) {
        $new_version = BUDDYC_PLUGIN_NAME;
        /**
         * Fires on a switch between BuddyClients and BuddyClients Free.
         *
         * @since 1.0.14
         *
         * @param   string  $new_version    The new plugin name.
         *                                  Accepts 'buddyclients-free' and 'BuddyClients Free'.
         */
        do_action( 'buddyc_version_switch', $new_version );
    }

    // Update option
    update_option( 'buddyc_plugin_name', BUDDYC_PLUGIN_NAME );
}
add_action( 'init', 'buddyc_handle_version_switch' );

/**
 * Handles an update to a new BuddyClients version.
 *
 * @since 1.0.26
 */
function buddyc_handle_version_update() {
    // Check the current version
    $prev_version = get_option( '_buddyc_plugin_version', null );   

    if ( $prev_version && $prev_version !== BUDDYC_PLUGIN_VERSION ) {
        $new_version = BUDDYC_PLUGIN_VERSION;
        /**
         * Fires on a switch between BuddyClients and BuddyClients Free.
         *
         * @since 1.0.14
         *
         * @param   string  $new_version    The new plugin version.
         */
        do_action( 'buddyc_version_update', $new_version );
    }

    // Update option
    update_option( '_buddyc_plugin_version', BUDDYC_PLUGIN_VERSION );
}
add_action( 'init', 'buddyc_handle_version_update' );