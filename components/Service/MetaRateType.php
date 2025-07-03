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
                                'label' => __( 'Singular', 'buddyclients-lite' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Word, Hour, Page', 'buddyclients-lite' ),
                            ],
                            'plural' => [
                                'label' => __( 'Plural', 'buddyclients-lite' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Words, Hours, Pages', 'buddyclients-lite' ),
                            ],
                        ],
                    ],
                    'Description' => [
                        'meta' => [
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients-lite' ),
                                'description' => __( 'Instructions for users on booking form.', 'buddyclients-lite' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Please add your full word count.', 'buddyclients-lite' ),
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
                                'label' => __( 'Attach Count To', 'buddyclients-lite' ),
                                'description' => __( 'Each time a client books services, will this number be different for each service or the same for the entire project?', 'buddyclients-lite' ),
                                'type' => 'dropdown',
                                'options' => [
                                    'project' => __( 'Project', 'buddyclients-lite' ),
                                    'service' => __( 'Service', 'buddyclients-lite' ),
                                ],
                                'default' => 'project'
                            ],
                            'minimum' => [
                                'label' => __( 'Minimum', 'buddyclients-lite' ),
                                'description' => __( 'The minimum number of units allowed.', 'buddyclients-lite' ),
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