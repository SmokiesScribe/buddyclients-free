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
                                'label' => __( 'Singular', 'buddyclients-lite' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Editor', 'buddyclients-lite' ),
                            ],
                            'plural' => [
                                'label' => __( 'Plural', 'buddyclients-lite' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Editors', 'buddyclients-lite' ),
                            ],
                        ],
                    ],
                    'Description' => [
                        'meta' => [
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients-lite' ),
                                'description' => __( 'Optional.', 'buddyclients-lite' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Does awesome things for clients.', 'buddyclients-lite' ),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}