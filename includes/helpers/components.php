<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Config\ComponentsHandler;
/**
 * Checks whether a component is enabled.
 * 
 * @since 0.1.0
 * 
 * @ignore
 *
 * @param   string  $component  The component to check.
 */
function buddyc_component_enabled( $component ) {
    return ComponentsHandler::in_components( $component );
}

/**
 * Checks whether a component exists.
 * 
 * @since 0.1.0
 * 
 * @ignore
 *
 * @param   string  $component  The component to check.
 */
function buddyc_component_exists( $component ) {
    return ComponentsHandler::component_exists( $component );
}

/**
 * Defines component names.
 * 
 * @since 1.0.0
 */
function buddyc_components_map() {
    return [
        'Booking'           => __( 'Booking', 'buddyclients-lite' ),
        'Checkout'          => __( 'Checkout', 'buddyclients-lite' ),
        'Service'           => __( 'Service', 'buddyclients-lite' ),
        'Email'             => __( 'Email', 'buddyclients-lite' ),
        'Brief'             => __( 'Brief', 'buddyclients-lite' ),
        'Stripe'            => __( 'Stripe', 'buddyclients-lite' ),
        'Affiliate'         => __( 'Affiliate', 'buddyclients-lite' ),
        'Availability'      => __( 'Availability', 'buddyclients-lite' ),
        'Contact'           => __( 'Contact', 'buddyclients-lite' ),
        'Legal'             => __( 'Legal', 'buddyclients-lite' ),
        'Quote'             => __( 'Quote', 'buddyclients-lite' ),
        'Sales'             => __( 'Sales', 'buddyclients-lite' ),
        'Testimonial'       => __( 'Testimonial', 'buddyclients-lite' ),
    ];
}

/**
 * Retrieves translatable component name.
 * 
 * @since 1.0.0
 * 
 * @param   string  $component  The component.
 */
function buddyc_component_name( $component ) {
    $map = buddyc_components_map();
    return $map[$component] ?? $component;
}