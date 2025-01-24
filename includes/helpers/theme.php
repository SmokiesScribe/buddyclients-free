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
 * Outputs an icon or icon class.
 * 
 * @since 1.0.20
 * 
 * @param   string  $key    The identifying key of the icon.
 * @param   bool    $html   Optional. Outputs the full html if true,
 *                          outputs the class only if false. Defaults to true.
 * 
 * @return  string  The full icon html or the icon classes.
 */
function buddyc_icon( $key, $html = true ) {
    $icon = new Icon( $key );
    return $html ? $icon->html : $icon->class;
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
function init_template_manager() {
    if ( class_exists( TemplateManager::class ) ) {
        new TemplateManager;
    }
}
init_template_manager();