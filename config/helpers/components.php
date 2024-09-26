<?php
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
function bc_component_enabled( $component ) {
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
function bc_component_exists( $component ) {
    return ComponentsHandler::component_exists( $component );
}

/**
 * Defines component names.
 * 
 * @since 1.0.0
 */
function bc_components_map() {
    return [
        'Booking'           => __( 'Booking', 'buddyclients' ),
        'Checkout'          => __( 'Checkout', 'buddyclients' ),
        'Service'           => __( 'Service', 'buddyclients' ),
        'Email'             => __( 'Email', 'buddyclients' ),
        'Brief'             => __( 'Brief', 'buddyclients' ),
        'Stripe'            => __( 'Stripe', 'buddyclients' ),
        'Affiliate'         => __( 'Affiliate', 'buddyclients' ),
        'Availability'      => __( 'Availability', 'buddyclients' ),
        'Contact'           => __( 'Contact', 'buddyclients' ),
        'Legal'             => __( 'Legal', 'buddyclients' ),
        'Quote'             => __( 'Quote', 'buddyclients' ),
        'Sales'             => __( 'Sales', 'buddyclients' ),
        'Testimonial'       => __( 'Testimonial', 'buddyclients' ),
    ];
}

/**
 * Retrieves translatable component name.
 * 
 * @since 1.0.0
 * 
 * @param   string  $component  The component.
 */
function bc_component_name( $component ) {
    $map = bc_components_map();
    return $map[$component] ?? $component;
}