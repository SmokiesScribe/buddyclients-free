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