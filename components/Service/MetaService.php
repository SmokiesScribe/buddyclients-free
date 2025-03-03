<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_service posts.
 *
 * @since 1.0.29
 */
class MetaServiceType {
    
   /**
     * Defines the post meta data.
     * 
     * @since 1.0.29
     */
    public static function meta() {
        return [
            'Type' => [
                'tables' => [
                    'Type' => [ 
                        'meta' => [
                            'service_type' => [
                                'label' => __( 'Service Type', 'buddyclients-free' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add service types */
                                    __( 'Select the category for this service. <a href="%s">Add service types.</a>', 'buddyclients-free' ),
                                    admin_url('/edit.php?post_type=buddyc_service_type')
                                ),
                                'type' => 'dropdown',
                                'options' => 'buddyc_service_type',
                            ],
                        ],
                    ],
                ],
            ],
            'Team' => [
                'freelancer' => 'disable',
                'tables' => [
                    'Team Member' => [
                        'meta' => [
                            'team_member_role' => [
                                'label' => __( 'Team Member Role', 'buddyclients-free' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add roles */
                                    __( 'Select which team member role applies to this service. <a href="%s">Add roles.</a>', 'buddyclients-free' ),
                                    admin_url('/edit.php?post_type=buddyc_role')
                                ),
                                'type' => 'dropdown',
                                'options' => 'buddyc_role',
                                'required' => true,
                            ],
                            'assigned_team_member' => [
                                'label' => __( 'Assigned Team Member', 'buddyclients-free' ),
                                'description' => __( 'Select a team member to ALWAYS be assigned this service. Selecting a team member here disables the Choose a Team Member option on the booking form and overrides all team member filtering.', 'buddyclients-free' ),
                                'required' => false,
                                'freelancer' => 'disable',
                                'type' => 'dropdown',
                                'options' => 'team',
                            ],
                        ],
                    ],
                ],
            ],
            'Rates' => [
                'tables' => [
                    'Client' => [
                        'meta' => [
                            'rate_value' => [
                                'label' => __( 'Rate Value', 'buddyclients-free' ),
                                'description' => __( 'Enter the fee for this service.', 'buddyclients-free' ),
                                'type' => 'number',
                                'required' => false,
                            ],
                            'rate_type' => [
                                'label' => __( 'Rate Type', 'buddyclients-free' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add rate types */
                                    __( 'Select the type of fee entered above. <a href="%s">Add rate types.</a>', 'buddyclients-free' ),
                                    admin_url('/edit.php?post_type=buddyc_rate_type')
                                ),
                                'type' => 'dropdown',
                                'options' => 'buddyc_rate_type',
                            ],
                        ],
                    ],
                    'Team' => [
                        'freelancer' => 'disable',
                        'meta' => [
                            'team_member_percentage' => [
                                'label' => __( 'Team Member Percentage', 'buddyclients-free' ),
                                'description' => __( 'What percentage of the client fee do team members receive for this service? Ex: 50', 'buddyclients-free' ),
                                'type' => 'number',
                                'required_component' => 'stripe',
                                'freelancer' => 'disable',
                            ],
                        ],
                    ],
                    'Adjustments' => [
                        'meta' => [
                            'adjustments' => [
                                'label' => __( 'Adjustments', 'buddyclients-free' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add adjustment fields */
                                    __( 'Select any rate adjustments that apply to this service. <a href="%s">Add adjustment fields.</a>', 'buddyclients-free' ),
                                    admin_url('/edit.php?post_type=buddyc_adjustment')
                                ),
                                'type' => 'checkbox',
                                'required' => false,
                                'options' => 'buddyc_adjustment',
                            ],
                        ],
                    ],
                ],
            ],
            'Service' => [
                'tables' => [
                    'Brief' => [
                        'meta' => [
                            'brief_type' => [
                                'label' => __( 'Brief Type', 'buddyclients-free' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add brief types */
                                    __( 'Select the brief type(s) to create for this service. <a href="%s">Add brief types.</a>', 'buddyclients-free' ),
                                    admin_url('/edit-tags.php?taxonomy=brief_type&&post_type=buddyc_brief')
                                ),
                                'required' => false,
                                'required_component' => 'briefs',
                                'type' => 'checkbox',
                                'options' => 'brief_type',
                            ],
                        ],
                    ],
                    'Dependencies' => [
                        'meta' => [
                            'dependency' => [
                                'label' => __( 'Required Services', 'buddyclients-free' ),
                                'description' => __( 'Which service(s) must be booked or selected before this one is available?', 'buddyclients-free' ),
                                'required' => false,
                                'type' => 'checkbox',
                                'options' => 'buddyc_service',
                            ],
                        ],
                    ],
                    'File Uploads' => [
                        'meta' => [
                            'file_uploads' => [
                                'label' => __( 'File Uploads', 'buddyclients-free' ),
                                'description' => __( 'Select any file upload types that apply to this service.', 'buddyclients-free' ),
                                'type' => 'checkbox',
                                'required' => false,
                                'options' => 'buddyc_file_upload',
                            ],
                        ],
                    ],
                ],
            ],
            'Display' => [
                'tables' => [
                    'Display' => [
                        'meta' => [
                            'order' => [
                                'label' => __( 'Order', 'buddyclients-free' ),
                                'description' => __( 'Higher numbers are shown first.', 'buddyclients-free' ),
                                'type' => 'number',
                                'required' => false,
                            ],
                            'hide' => [
                                'label' => __( 'Hide', 'buddyclients-free' ),
                                'description' => '',
                                'required' => false,
                                'type' => 'checkbox',
                                'options' => [
                                    true => __( 'Hide from booking form. (The service will still appear in shortcodes and archives.)', 'buddyclients-free' ),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}