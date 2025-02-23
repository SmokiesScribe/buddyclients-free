<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\PluginLinks;

/**
 * Initializes the links added to the plugin page.
 * 
 * @since 0.2.1
 * @since 1.0.25 Initializes the class only.
 */
function buddyc_plugin_page_links() {
    new PluginLinks;
}
add_action('init', 'buddyc_plugin_page_links');
