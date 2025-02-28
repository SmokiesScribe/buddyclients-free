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

/**
 * Retrieves the shortcode by key.
 * 
 * @since 1.0.27
 * 
 * @param   string  $key    The shortcode key.
 */
function buddyc_get_shortcode( $key ) {
    return Shortcodes::get_shortcode( $key );
}

/**
 * Checks whether a shortcode is present in the page content.
 * 
 * @since 1.0.27
 * 
 * @param   string  $shortcode_key  The shortcode key.
 * @return  bool    True if the shortcode exists on the page, false if not.
 */
function buddyc_shortcode_exists( $shortcode_key ) {
    return Shortcodes::shortcode_exists( $shortcode_key );
}

/**
 * Checks whether any plugin shortcode is present in the page content.
 * 
 * @since 1.0.27
 * 
 * @return  bool    True if any shortcode exists on the page, false if not.
 */
function buddyc_any_shortcode_exists() {
    return Shortcodes::any_shortcode_exists();
}