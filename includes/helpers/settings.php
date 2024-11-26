<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\Settings;
/**
 * Retrieves the value of plugin settings.
 * 
 * @since 0.1.0
 * 
 * @param   string  $settings_group     The settings group to retrieve.
 * @param   string  $settings_key       Optional. The specific setting to retrieve.
 */
function buddyc_get_setting( $settings_group, $settings_key = null ) {
    return Settings::get_value( $settings_group, $settings_key );
}

/**
 * Retrieves the value of plugin settings.
 * 
 * @since 0.1.0
 * 
 * @param   string  $settings_group     The settings group.
 * @param   string  $settings_key       The specific setting field.
 * @param   mixed   $value              The value to set.
 */
function buddyc_update_setting( $settings_group, $settings_key, $value ) {
    return Settings::update_value( $settings_group, $settings_key, $value );
}

/**
 * Retrieves an array of help post type slugs.
 * 
 * @since 0.2.9
 */
function buddyc_help_post_types() {
    return buddyc_get_setting( 'help', 'help_post_types' );
}

/**
 * Retrieves the permalink to a page defined in the plugin settings.
 * 
 * @since 0.1.0
 * 
 * @param   string  $page_key       The key of the page to retrieve.
 * @param   bool    $return_link    Optional. Whether to return the link or the page ID.
 *                                  Defaults to true and returns permalink.
 * @return  int|string              The permalink or the page ID.
 */
function buddyc_get_page_link( $page_key, $return_link = true ) {
    $page_id = buddyc_get_setting( 'pages', $page_key );
    if ( $page_id ) {
        return $return_link ? get_permalink( $page_id ) : $page_id;
    } else {
        return $return_link ? '#' : null;
    }
}