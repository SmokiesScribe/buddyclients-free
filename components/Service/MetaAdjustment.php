<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_adjustment posts.
 *
 * @since 1.0.29
 */
class MetaAdjustment {
    
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
                            'form_field_type' => [
                                'label' => __( 'Form Field Type', 'buddyclients-lite' ),
                                'description' => __( 'Type of field on booking form.', 'buddyclients-lite' ),
                                'type' => 'dropdown',
                                'options' => [
                                    'dropdown' => __( 'Dropdown', 'buddyclients-lite' ),
                                    'checkbox' => __( 'Checkboxes', 'buddyclients-lite' ),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Display' => [
                'tables' => [
                    'Labels' => [
                        'meta' => [
                            'label' => [
                                'label' => __( 'Label', 'buddyclients-lite' ),
                                'description' => __( 'Field label to display on booking form.', 'buddyclients-lite' ),
                                'type' => 'text',
                            ],
                            'field_description' => [
                                'label' => __( 'Description', 'buddyclients-lite' ),
                                'description' => __( 'Field description to display on booking form.', 'buddyclients-lite' ),
                                'type' => 'text',
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
            'Options' => [
                'description' => __( 'Each option will adjust the service fee based on the user response.', 'buddyclients-lite' ),
                'tables' => self::adjustment_options(),
                'classes' => 'buddyc-adjustment-options'
            ],
            'New Option' => [
                'description' => '<a id="buddyc_adjustment_create_option" class="button-secondary">' . __( 'Add Option', 'buddyclients-lite' ) . '</a><div id="buddyc_adjustment_create_option_message"></div>',
                'tables' => [],
            ],
        ];
    }

   /**
     * Defines the adjustment options.
     * 
     * @since 1.0.29
     */
    private static function adjustment_options() {
        
        // Initialize array and count
        $meta_fields = [];
        $options_count = 10;
        
        // Loop with number of options
        for ($i = 1; $i <= $options_count; $i++) {
            $meta_fields['Option ' . $i] = [
                'meta' => [
                    'option_' . $i . '_label' => [
                        'label' => __( 'Label', 'buddyclients-lite' ),
                        'description' => '',
                        'type' => 'text',
                    ],
                    'option_' . $i . '_operator' => [
                        'label' => __( 'Operator', 'buddyclients-lite' ),
                        'description' => '',
                        'type' => 'dropdown',
                        'options' => 'operator',
                    ],
                    'option_' . $i . '_value' => [
                        'label' => __( 'Value', 'buddyclients-lite' ),
                        'description' => '',
                        'type' => 'text',
                    ],
                ],
            ];
        }
        return $meta_fields;
    }
}