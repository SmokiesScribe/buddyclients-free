<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for budyc_role posts.
 *
 * @since 1.0.29
 */
class MetaRole {
    
   /**
     * Defines the post meta data.
     * 
     * @since 1.0.29
     */
    public static function meta() {
        return [
            'Display' => [
                'tables' => [
                    'Labels' => [
                        'meta' => [
                            'singular' => [
                                'label' => __( 'Singular', 'buddyclients-free' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Editor', 'buddyclients-free' ),
                            ],
                            'plural' => [
                                'label' => __( 'Plural', 'buddyclients-free' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Editors', 'buddyclients-free' ),
                            ],
                        ],
                    ],
                    'Description' => [
                        'meta' => [
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients-free' ),
                                'description' => __( 'Optional.', 'buddyclients-free' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Does awesome things for clients.', 'buddyclients-free' ),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}