<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Config\ComponentsHandler;

/**
 * Defines the Stripe settings.
 *
 * @since 1.0.25
 */
class SettingsComponents {

    /**
     * Defines default Components settings.
     * 
     * @since 1.0.25
     */
    public static function defaults() {
        return [
            'components' => self::components_options(), // enable all by default
        ];
    }
    
    /**
     * Defines the Components settings.
     * 
     * @since 1.0.25
     */
    public static function settings() {
        return [
            'components' => [
                'title' => __('Components', 'buddyclients-lite'),
                'description' => __('Enable BuddyClients components.', 'buddyclients-lite'),
                'fields' => [
                    'components' => [
                        'label' => __('Components', 'buddyclients-lite'),
                        'type' => 'checkbox_table',
                        'options' => self::components_options(),
                        'descriptions' => self::components_descriptions(),
                        'required_options' => ComponentsHandler::required_components(),
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Retrieves components options.
     * 
     * @since 0.1.0
     */
    private static function components_options() {
        
        // Initialize array
        $options = [];
        
        // Get components
        $components = ComponentsHandler::get_components();
        
        // Loop through post types
        foreach ( $components as $component ) {
            // Add to array
            if ( function_exists( 'buddyc_component_name' ) ) {
                $options[$component] = buddyc_component_name( $component );
            }
        }
        return $options;
    }
    
    /**
     * Defines components descriptions.
     * 
     * @since 0.1.0
     */
    private static function components_descriptions() {
        return [
            // Required
            'Booking'       => __('Allow clients to book services.', 'buddyclients-lite'),
            'Checkout'      => __('Allow clients to check out on your website.', 'buddyclients-lite'),
            'Service'       => __('Create services.', 'buddyclients-lite'),
            // Core
            'Email'         => __('Send email notifications to clients, team members, and admins.', 'buddyclients-lite'),
            'Brief'         => __('Request information from clients after booking.', 'buddyclients-lite'),
            'Stripe'        => __('Accept payments at checkout.', 'buddyclients-lite'),
            // Premium
            'Affiliate'     => __('Allow users to earn commission for referring clients.', 'buddyclients-lite'),
            'Availability'  => __('Display the next date each team member is available.', 'buddyclients-lite'),
            'Contact'       => __('Accept messages through a contact page and a floating contact button.', 'buddyclients-lite'),
            'Legal'         => __('Manage legal agreements for clients, team members, and affiliates.', 'buddyclients-lite'),
            'Quote'         => __('Create custom quotes for one-off projects.', 'buddyclients-lite'),
            'Sales'         => __('Allow team members to create bookings on behalf of clients and earn commission.', 'buddyclients-lite'),
            'Testimonial'   => __('Accept and display testimonials from clients.', 'buddyclients-lite'),
        ];
    }
}