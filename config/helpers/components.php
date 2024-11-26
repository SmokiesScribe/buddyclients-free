<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Config\ComponentsHandler as ComponentsHandler;
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
        'Booking'           => __( 'Booking', 'buddyclients-free' ),
        'Checkout'          => __( 'Checkout', 'buddyclients-free' ),
        'Service'           => __( 'Service', 'buddyclients-free' ),
        'Email'             => __( 'Email', 'buddyclients-free' ),
        'Brief'             => __( 'Brief', 'buddyclients-free' ),
        'Stripe'            => __( 'Stripe', 'buddyclients-free' ),
        'Affiliate'         => __( 'Affiliate', 'buddyclients-free' ),
        'Availability'      => __( 'Availability', 'buddyclients-free' ),
        'Contact'           => __( 'Contact', 'buddyclients-free' ),
        'Legal'             => __( 'Legal', 'buddyclients-free' ),
        'Quote'             => __( 'Quote', 'buddyclients-free' ),
        'Sales'             => __( 'Sales', 'buddyclients-free' ),
        'Testimonial'       => __( 'Testimonial', 'buddyclients-free' ),
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