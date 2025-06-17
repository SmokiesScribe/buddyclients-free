<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminNotice;
/**
 * Builds an admin notice.
 * 
 * @since 0.1.0
 * 
 * @param   array   $args {
 *     An array of arguments for building the admin notice.
 * 
 *     @type    string  $repair_link        The link to the repair page.
 *     @type    string  $repair_link_text   Optional. The link text.
 *                                          Defaults to 'Repair'.
 *     @type    string  $message            The message to display in the notice.
 *     @type    bool    $dismissable        Optional. Whether the notice should be dismissable.
 *                                          Defaults to false.
 *     @type    string  $color              Optional. The color of the notice.
 *                                          Accepts 'green', 'blue', 'orange', 'red'.
 *                                          Defaults to blue.
 * }
 */
function buddyc_admin_notice( $args ) {
    new AdminNotice( $args );
}

/**
 * Builds the ID for the admin notice.
 * 
 * @since 1.0.25
 * 
 * @param   string  $key    The unique key for the admin notice.
 */
function buddyc_admin_notice_id( $key ) {
    return AdminNotice::build_id( $key );
}

/**
 * Checks whether an admin notice is dimissed.
 * 
 * @since 1.0.27
 * 
 * @param   string  $key  The key of the notice.
 */
function buddyc_admin_notice_dismissed( $key ) {
    return AdminNotice::dismissed( $key );
}

/**
 * Dismisses an admin notice.
 * 
 * @since 1.0.27
 */
function buddyc_dismiss_admin_notice() {

    // Verify nonce
    $valid = buddyc_verify_ajax_nonce( 'dismiss_admin' );
    if ( ! $valid ) return;

    // Get the admin notice id
    $notice_id = isset( $_POST['noticeId'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['noticeId'] ) ) ) : null;

    // Update option
    AdminNotice::dismiss( $notice_id );
}
add_action('wp_ajax_buddyc_dismiss_admin_notice', 'buddyc_dismiss_admin_notice'); // For logged-in users