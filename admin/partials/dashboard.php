<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminDashboard;

/**
 * Builds the bookings dashboard content.
 * 
 * @since 0.1.0
 */
function buddyc_bookings_dashboard() {    
    new AdminDashboard();
};