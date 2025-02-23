<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Shortcodes;

/**
 * Retrieves the array of shortcodes data.
 * 
 * @since 1.0.26
 * 
 * @return  array   An associative array of shortcode names and data.
 */
function buddyc_shortcodes_data() {
    return Shortcodes::shortcodes_data();
}
