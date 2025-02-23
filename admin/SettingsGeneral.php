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
            'register_button_text'  => __( 'Get Started', 'buddyclients' ),
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
                'title' => __('Style Settings', 'buddyclients'),
                'description' => __('Adjust global buddyclients styles to match your brand.', 'buddyclients'),
                'fields' => [
                    'primary_color' => [
                        'label' => __('Primary Color', 'buddyclients'),
                        'type' => 'color',
                        'class' => 'color-field',
                        'description' => '',
                    ],
                    'accent_color' => [
                        'label' => __('Accent Color', 'buddyclients'),
                        'type' => 'color',
                        'description' => '',
                    ],
                    'tertiary_color' => [
                        'label' => __('Tertiary Color', 'buddyclients'),
                        'type' => 'color',
                        'description' => '',
                    ],
                ]
            ],
            'user_types' => [
                'title' => __('User Types and Permissions', 'buddyclients'),
                'description' => __('Select the member types for clients, team members, and site admins.', 'buddyclients'),
                'fields' => [
                    'client_types' => [
                        'label' => __('Client Types', 'buddyclients'),
                        'type' => 'checkboxes',
                        'options' => buddyc_member_types(),
                        'description' => __('Select the types for clients.', 'buddyclients'),
                    ],
                    'default_client_type' => [
                        'label' => __('Default Client Type', 'buddyclients'),
                        'type' => 'dropdown',
                        'options' => buddyc_member_types(),
                        'description' => __('Select the default member type for new clients.', 'buddyclients'),
                    ],
                    'team_types' => [
                        'label' => __('Team Types', 'buddyclients'),
                        'type' => 'checkboxes',
                        'options' => buddyc_member_types(),
                        'description' => __('Select the types for team members.', 'buddyclients'),
                    ],
                ],
            ],
            'self_select_roles' => [
                'title' => __('Allow Team to Self-Select Roles', 'buddyclients'),
                'description' => __('Should team members be allowed to select their own roles?', 'buddyclients'),
                'fields' => [
                    'self_select_role' => [
                        'label' => __('Allow Self-Selection', 'buddyclients'),
                        'type' => 'dropdown',
                        'options' => [
                            'no' => __('No', 'buddyclients'),
                            'yes' => __('Yes', 'buddyclients'),
                        ],
                        'description' => __('Allow team members to select their own roles.', 'buddyclients'),
                    ],
                ],
            ],
            'payment_methods' => [
                'title' => __( 'Team Payment Methods', 'buddyclients' ),
                'description' => __( 'Select the payment methods available to team members.', 'buddyclients' ),
                'fields' => [
                    'team_payment_methods' => [
                        'label' => __( 'Team Payment Methods', 'buddyclients' ),
                        'type' => 'checkboxes',
                        'options' => buddyc_options( 'payment_methods' ),
                        'description' => __( 'Team members can choose their preferred payment method.' ),
                    ],
                ]
            ],
            'registration' => [
                'title' => __('Call to Action', 'buddyclients'),
                'description' => __('Change the text and link for the CTA button at the top right of the page. This setting currently only works with the BuddyBoss Theme..', 'buddyclients'),
                'fields' => [
                    'enable_cta' => [
                        'label' => __('Enable CTA Button', 'buddyclients'),
                        'type' => 'dropdown',
                        'options' => [
                            'enable' => __('Enable', 'buddyclients'),
                            'disable' => __('Disable', 'buddyclients'),
                        ],
                        'description' => __('Display plugin info messages in the admin area.', 'buddyclients'),
                    ],
                    'register_button_text' => [
                        'label' => __('CTA Button Text', 'buddyclients'),
                        'type' => 'text',
                        'description' => __('The text will appear on the button linking to the booking form when user registration is disabled.', 'buddyclients'),
                    ],
                    'register_button_url' => [
                        'label' => __('CTA Button Link', 'buddyclients'),
                        'type' => 'url',
                        'description' => __('The button at the top right of the screen will point to this link. Defaults to the Booking Page.', 'buddyclients'),
                    ],
                ],
            ],
            'admin' => [
                'title' => __('Admin', 'buddyclients'),
                'description' => '',
                'fields' => [
                    'admin_info' => [
                        'label' => __('Info Messages', 'buddyclients'),
                        'type' => 'dropdown',
                        'options' => [
                            'disable' => __('Disable', 'buddyclients'),
                            'enable' => __('Enable', 'buddyclients'),
                        ],
                        'description' => __('Display plugin info messages in the admin area.', 'buddyclients'),
                    ],
                ],
            ],
        ];
    }
}