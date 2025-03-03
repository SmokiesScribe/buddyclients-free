<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_rate_type posts.
 *
 * @since 1.0.29
 */
class MetaRateType {
    
   /**
     * Defines the post meta data.
     * 
     * @since 1.0.29
     */
    public static function meta() {
        return [
            'Unit' => [
                'tables' => [
                    'Labels' => [
                        'meta' => [
                            'singular' => [
                                'label' => __( 'Singular', 'buddyclients-free' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Word, Hour, Page', 'buddyclients-free' ),
                            ],
                            'plural' => [
                                'label' => __( 'Plural', 'buddyclients-free' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Words, Hours, Pages', 'buddyclients-free' ),
                            ],
                        ],
                    ],
                    'Description' => [
                        'meta' => [
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients-free' ),
                                'description' => __( 'Instructions for users on booking form.', 'buddyclients-free' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Please add your full word count.', 'buddyclients-free' ),
                            ],
                        ],
                    ],
                ],
            ],
            'Settings' => [
                'tables' => [
                    'Calculations' => [
                        'meta' => [
                            'attach' => [
                                'label' => __( 'Attach Count To', 'buddyclients-free' ),
                                'description' => __( 'Each time a client books services, will this number be different for each service or the same for the entire project?', 'buddyclients-free' ),
                                'type' => 'dropdown',
                                'options' => [
                                    'project' => __( 'Project', 'buddyclients-free' ),
                                    'service' => __( 'Service', 'buddyclients-free' ),
                                ],
                                'default' => 'project'
                            ],
                            'minimum' => [
                                'label' => __( 'Minimum', 'buddyclients-free' ),
                                'description' => __( 'The minimum number of units allowed.', 'buddyclients-free' ),
                                'type' => 'number',
                                'placeholder' => 0
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}