<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Scheduler;

/**
 * Initializes the Scheduler.
 * 
 * @since 1.0.27
 */
function buddyc_init_scheduler() {
    Scheduler::init();
}
add_action( 'init', 'buddyc_init_scheduler' );

/**
 * Schedules a new event.
 * 
 * @since 1.0.27
 * 
 * @param   array   $args {
 *     An array of args to construct the scheduled event.
 * 
 *     @param   string      $event_key  The event key.
 *     @param   string      $timeout    The timeout timestamp.
 *     @param   array       $args       Optional. An array of args to pass to the callback.
 *                                      Defaults to empty array.
 *     @param   string|int  $identifier Optional. An identifier to prevent duplicate events.
 * }
 */
function buddyc_schedule( $args ) {
    new Scheduler( $args );
}