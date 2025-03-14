<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\UpgradePage;
use BuddyClients\Admin\UpgradeLink;

/**
 * Initializes the upgrade page.
 * 
 * @since 1.0.27
 */
function buddyc_init_upgrade() {
    if ( class_exists( UpgradePage::class ) ) {
        UpgradePage::get_instance();
    }
}
add_action( 'init', 'buddyc_init_upgrade' );

/**
 * Generates a url to the BuddyClients pricing page.
 * 
 * @since 0.1.0
 * @since 1.0.27 Use constant.
 * 
 * @ignore
 */
function buddyc_upgrade_url() {
    return trailingslashit( BUDDYC_URL ) . 'pricing';
}

/**
 * Generates a url to the BuddyClients website.
 * 
 * @since 0.1.0
 * 
 * @ignore
 * 
 * @param   string  $path   Optional. Path to append to the url.
 */
function buddyc_site_url( $path = null ) {
    $url = 'https://buddyclients.com';
    
    return $path ? $url . $path : $url;
}

/**
 * Generates a URL to the BuddyClients account pagbuddyc_account_linke.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function buddyc_account_url() {
    return buddyc_site_url('/license');
}

/**
 * Generates a url to the enable components admin page.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function buddyc_enable_component_url() {
    return admin_url( '/admin.php?page=buddyc-components-settings' );
}

/**
 * Generates a URL to the Freelancer Mode setting.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function buddyc_freelancer_mode_url() {
    return admin_url('/admin.php?page=buddyc-booking-settings');
}

/**
 * Generates an upgrade link.
 * 
 * @since 0.1.0
 * 
 * @ignore
 * 
 * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
 *                               Defaults to null.
 */
function buddyc_upgrade_link( $boxed = null ) {
    return UpgradeLink::build( $boxed );
}

/**
 * Echoes an upgrade link.
 * 
 * @since 1.0.21
 * 
 * @ignore
 * 
 * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
 *                               Defaults to null.
 */
function buddyc_echo_upgrade_link( $boxed = null ) {
    return UpgradeLink::echo( $boxed );
}

/**
 * Generates a BuddyClients account link.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function buddyc_account_link() {
    return UpgradeLink::account_link();
}

/**
 * Generates a link to enable a component.
 * 
 * @since 0.1.0
 * 
 * @ignore
 * 
 * @param   string   $component  The component to be enabled.
 * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
 *                               Defaults to null.
 */
function buddyc_enable_component_link( $component, $boxed = null ) {
    return UpgradeLink::enable_component_link( $component, $boxed = null );
}

/**
 * Echoes a link to enable a component.
 * 
 * @since 1.0.21
 * 
 * @ignore
 * 
 * @param   string   $component  The component to be enabled.
 * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
 *                               Defaults to null.
 */
function buddyc_echo_enable_component_link( $component, $boxed = null ) {
    return UpgradeLink::echo_enable_component_link( $component, $boxed = null );
}

/**
 * Generates a link to disable Freelancer Mode.
 * 
 * @since 0.1.0
 * 
 * @ignore
 * 
 * @param   ?bool    $enable     Optional. Whether to include language about enabling Freelancer Mode.
 *                               Defaults to language about disabling Freelancer Mode.
 * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
 *                               Defaults to null.
 */
function buddyc_freelancer_mode_link( $enable = null, $boxed = null ) {
    return UpgradeLink::freelancer_mode_link( $enable = null, $boxed = null );
}