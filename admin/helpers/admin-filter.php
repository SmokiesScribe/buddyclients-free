<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminTableFilter;

/**
 * Checks for admin table filter form submission.
 * 
 * @since 0.1.0
 */
function buddyc_admin_filter_submission() {
    AdminTableFilter::submission();
}
add_action('admin_init', 'buddyc_admin_filter_submission');