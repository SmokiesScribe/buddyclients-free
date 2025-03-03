<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminDashboard;

/**
 * Builds the bookings overview content.
 * 
 * @since 0.1.0
 */
function buddyc_bookings_dashboard() {    
    new AdminDashboard();
};

/**
 * The callback for the top-level menu.
 * 
 * @since 1.0.28
 */
function buddyc_dashboard_content() {
    return buddyc_admin_table( 'booking_intents' );
}