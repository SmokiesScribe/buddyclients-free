<?php
/**
 * Get colors.
 * 
 * @since 0.1.0
 */
function bc_color( $type ) {
    return bc_get_setting('style', $type . '_color');
}

/**
 * Check for BuddyBoss theme.
 * 
 * @since 0.1.0
 * 
 * @return bool
 */
function bc_buddyboss_theme() {
    if (function_exists('buddyboss_theme_register_required_plugins')) {
        return true;
    } else {
        return false;
    }
}