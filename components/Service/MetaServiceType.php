<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_service_type posts.
 *
 * @since 1.0.29
 */
class ServiceTypeMeta {
    
   /**
     * Defines the post meta data.
     * 
     * @since 1.0.29
     */
    public static function meta() {
        return [
            'Service Type' => [
                'tables' => [
                    'Display' => [
                        'meta' => [
                            'form_field_type' => [
                                'label' => __( 'Form Field Type', 'buddyclients-lite' ),
                                'description' => __( 'Type of field to display these services on booking form. Choose checkboxes to allow users to select multiple options.', 'buddyclients-lite' ),
                                'type' => 'dropdown',
                                'default' => 'dropdown',
                                'options' => [
                                    'dropdown' => __( 'Dropdown', 'buddyclients-lite' ),
                                    'checkbox' => __( 'Checkboxes', 'buddyclients-lite' )
                                ],
                            ],
                            'order' => [
                                'label' => __( 'Order', 'buddyclients-lite' ),
                                'description' => __( 'Higher numbers are shown first.', 'buddyclients-lite' ),
                                'type' => 'number',
                                'placeholder' => '0',
                            ],
                            'hide' => [
                                'label' => __( 'Hide', 'buddyclients-lite' ),
                                'description' => '',
                                'type' => 'checkbox',
                                'options' => [
                                    true => __( 'Hide all services of this category from the booking form. (They will still appear in shortcodes and archives.)', 'buddyclients-lite' ),
                                ],
                            ],
                        ],
                    ],
                    'Help Doc' => [
                        'meta' => [
                            'help_post_id' => [
                                'label' => __( 'Help Post', 'buddyclients-lite' ),
                                'description' => __( 'Help doc to show on booking form.', 'buddyclients-lite' ),
                                'type' => 'dropdown',
                                'options' => 'help_docs',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}