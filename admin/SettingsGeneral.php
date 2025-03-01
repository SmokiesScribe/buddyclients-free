<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the general settings.
 *
 * @since 1.0.25
 */
class SettingsGeneral {

    /**
     * Defines default General settings.
     * 
     * @since 1.0.25
     */
    public static function defaults() {
        return [
            'client_types'          => [],
            'team_types'            => [],
            'self_select_roles'     => 'no',
            'register_button_url'   => buddyc_get_page_link( 'booking_page' ),
            'register_button_text'  => __( 'Get Started', 'buddyclients-free' ),
            'admin_info'            => 'enable',
            'enable_cta'            => 'enable',
            'primary_color'         => '#037AAD',
            'accent_color'          => '#067F06',
            'tertiary_color'        => '#0e5929',
        ];
    }
    
   /**
     * Defines the general settings.
     * 
     * @since 1.0.25
     */
    public static function settings() {
        return [
            'style' => [
                'title' => __('Style Settings', 'buddyclients-free'),
                'description' => __('Adjust global buddyclients styles to match your brand.', 'buddyclients-free'),
                'fields' => [
                    'primary_color' => [
                        'label' => __('Primary Color', 'buddyclients-free'),
                        'type' => 'color',
                        'class' => 'color-field',
                        'description' => '',
                    ],
                    'accent_color' => [
                        'label' => __('Accent Color', 'buddyclients-free'),
                        'type' => 'color',
                        'description' => '',
                    ],
                    'tertiary_color' => [
                        'label' => __('Tertiary Color', 'buddyclients-free'),
                        'type' => 'color',
                        'description' => '',
                    ],
                ]
            ],
            'user_types' => [
                'title' => __('User Types and Permissions', 'buddyclients-free'),
                'description' => __('Select the member types for clients, team members, and site admins.', 'buddyclients-free'),
                'fields' => [
                    'client_types' => [
                        'label' => __('Client Types', 'buddyclients-free'),
                        'type' => 'checkboxes',
                        'options' => buddyc_member_types(),
                        'description' => __('Select the types for clients.', 'buddyclients-free'),
                    ],
                    'default_client_type' => [
                        'label' => __('Default Client Type', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => buddyc_member_types(),
                        'description' => __('Select the default member type for new clients.', 'buddyclients-free'),
                    ],
                    'team_types' => [
                        'label' => __('Team Types', 'buddyclients-free'),
                        'type' => 'checkboxes',
                        'options' => buddyc_member_types(),
                        'description' => __('Select the types for team members.', 'buddyclients-free'),
                    ],
                ],
            ],
            'self_select_roles' => [
                'title' => __('Allow Team to Self-Select Roles', 'buddyclients-free'),
                'description' => __('Should team members be allowed to select their own roles?', 'buddyclients-free'),
                'fields' => [
                    'self_select_role' => [
                        'label' => __('Allow Self-Selection', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => [
                            'no' => __('No', 'buddyclients-free'),
                            'yes' => __('Yes', 'buddyclients-free'),
                        ],
                        'description' => __('Allow team members to select their own roles.', 'buddyclients-free'),
                    ],
                ],
            ],
            'payment_methods' => [
                'title' => __( 'Team Payment Methods', 'buddyclients-free' ),
                'description' => __( 'Select the payment methods available to team members.', 'buddyclients-free' ),
                'fields' => [
                    'team_payment_methods' => [
                        'label' => __( 'Team Payment Methods', 'buddyclients-free' ),
                        'type' => 'checkboxes',
                        'options' => buddyc_options( 'payment_methods' ),
                        'description' => __( 'Team members can choose their preferred payment method.', 'buddyclients-free' ),
                    ],
                ]
            ],
            'registration' => [
                'title' => __('Call to Action', 'buddyclients-free'),
                'description' => __('Change the text and link for the CTA button at the top right of the page. This setting currently only works with the BuddyBoss Theme..', 'buddyclients-free'),
                'fields' => [
                    'enable_cta' => [
                        'label' => __('Enable CTA Button', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => [
                            'enable' => __('Enable', 'buddyclients-free'),
                            'disable' => __('Disable', 'buddyclients-free'),
                        ],
                        'description' => __('Display plugin info messages in the admin area.', 'buddyclients-free'),
                    ],
                    'register_button_text' => [
                        'label' => __('CTA Button Text', 'buddyclients-free'),
                        'type' => 'text',
                        'description' => __('The text will appear on the button linking to the booking form when user registration is disabled.', 'buddyclients-free'),
                    ],
                    'register_button_url' => [
                        'label' => __('CTA Button Link', 'buddyclients-free'),
                        'type' => 'url',
                        'description' => __('The button at the top right of the screen will point to this link. Defaults to the Booking Page.', 'buddyclients-free'),
                    ],
                ],
            ],
            'admin' => [
                'title' => __('Admin', 'buddyclients-free'),
                'description' => '',
                'fields' => [
                    'admin_info' => [
                        'label' => __('Info Messages', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => [
                            'disable' => __('Disable', 'buddyclients-free'),
                            'enable' => __('Enable', 'buddyclients-free'),
                        ],
                        'description' => __('Display plugin info messages in the admin area.', 'buddyclients-free'),
                    ],
                ],
            ],
        ];
    }
}