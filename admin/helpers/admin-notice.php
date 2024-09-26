<?php
/**
 * Builds an admin notice.
 * 
 * @since 0.1.0
 * 
 * @param   array   $args {
 *     An array of arguments for building the admin notice.
 * 
 *     @type    string  $repair_link    The link to the repair page.
 *     @type    string  $message        The message to display in the notice.
 *     @type    bool    $dismissable    Optional. Whether the notice should be dismissable.
 *                                      Defaults to false.
 *     @type    string  $color          Optional. The color of the notice.
 *                                      Defaults to blue.
 * }
 */
function bc_admin_notice( $args ) {
    new BuddyClients\Admin\AdminNotice( $args );
}