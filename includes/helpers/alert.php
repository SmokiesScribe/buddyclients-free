<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Alert;

/**
 * Outputs a front end alert.
 * 
 * @since 1.0.25
 * 
 * @param   string  $content    The content to display.
 * @param   ?int    $priority   Optional. The priority of the alert.
 */
function buddyc_alert( $content, $priority = null ) {
    new Alert( $content, $priority );
}