<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Icon;
use BuddyClients\Includes\Archive;
use BuddyClients\Includes\TemplateManager;

/**
 * Check for BuddyBoss theme.
 * 
 * @since 0.1.0
 * 
 * @return bool
 */
function buddyc_buddyboss_theme() {
    if (function_exists('buddyboss_theme_register_required_plugins')) {
        return true;
    } else {
        return false;
    }
}

/**
 * Outputs icon html.
 * 
 * @since 1.0.20
 * 
 * @param   string  $key    The identifying key of the icon.
 * @param   string  $color  Optional. The color of the icon.
 *                          Accepts 'blue', 'black', 'green', 'red', or 'gray'.
 * 
 * @return  string  The icon html.
 */
function buddyc_icon( $key, $color = null ) {
    $icon = new Icon( $key, $color );
    return $icon->html;
}

/**
 * Outputs a string of icon classes
 * 
 * @since 1.0.25
 * 
 * @param   string  $key    The identifying key of the icon.
 * @param   string  $color  Optional. The color of the icon.
 *                          Accepts 'blue', 'black', 'green', 'red', or 'gray'.
 * 
 * @return  string  The string of icon classes.
 */
function buddyc_icon_class( $key, $color = null ) {
    $icon = new Icon( $key, $color );
    return $icon->class;
}

/**
 * Checks whether the active theme is a Wordpress default theme.
 * 
 * @since 1.0.21
 * 
 * @return  bool    True if the active theme is a WP theme.
 */
function buddyc_is_wp_theme() {
    return TemplateManager::is_wp_theme();
}

/**
 * Initializes TemplateManager.
 * 
 * @since 1.0.21
 */
function buddyc_init_template_manager() {
    if ( class_exists( TemplateManager::class ) ) {
        new TemplateManager;
    }
}
buddyc_init_template_manager();