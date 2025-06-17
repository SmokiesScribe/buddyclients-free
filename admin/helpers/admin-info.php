<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminInfo;
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

/**
 * Dismisses admin tips.
 * 
 * @since 1.0.27
 */
function buddyc_dismiss_admin_tips() {

    // Verify nonce
    $valid = buddyc_verify_ajax_nonce( 'dismiss_admin' );
    if ( ! $valid ) return;

    // Update option
    AdminInfo::dismiss();
}
add_action('wp_ajax_buddyc_dismiss_admin_tips', 'buddyc_dismiss_admin_tips'); // For logged-in users