<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_filter posts.
 *
 * @since 1.0.29
 */
class MetaFilter {
    
   /**
     * Defines the post meta data.
     * 
     * @since 1.0.29
     */
    public static function meta() {
        return [
            'Xprofile Field' => [
                'tables' => [
                    'Field' => [
                        'meta' => [
                            'xprofile_field' => [
                                'label' => __( 'Xprofile Field', 'buddyclients-free' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add xprofile fields */
                                    __( 'Choose from checkbox and dropdown Xprofile fields. <a href="%s">Create a new field.</a>', 'buddyclients-free' ),
                                    admin_url('/admin.php?page=bp-profile-setup')
                                ),
                                'type' => 'dropdown',
                                'options' => buddyc_options( 'xprofile', ['existing' => ['buddyc_filter' => 'xprofile_field']] ),
                                'placeholder' => __( 'Select a Field', 'buddyclients-free' ),
                            ],
                        ],
                    ],
                ],
            ],
            'Display' => [
                'tables' => [
                    'Labels' => [
                        'meta' => [
                            'form_label' => [
                                'label' => __( 'Form Label', 'buddyclients-free' ),
                                'description' => __( 'The label to display on the booking form.', 'buddyclients-free' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Preferences', 'buddyclients-free' ),
                            ],
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients-free' ),
                                'description' => __( 'The description to display on the booking form.', 'buddyclients-free' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Select applicable preferences.', 'buddyclients-free' ),
                            ],
                        ],
                    ],
                ],
            ],
            'Field' => [
                'tables' => [
                    'Field' => [
                        'meta' => [
                            'match_type' => [
                                'label' => __( 'Match Type', 'buddyclients-free' ),
                                'description' => __( '<strong>Exact:</strong> Only team members whose response matches exactly will be available.
                                <br><strong>Exclude:</strong> Only team members whose profile response does not include the client\'s response will be available.
                                <br><strong>Include Any:</strong> Only team members whose response includes any of the selected options will be available.
                                <br><strong>Include All:</strong> Only team members whose response includes all of the selected options will be available.', 'buddyclients-free' ),
                                'type' => 'dropdown',
                                'default' => 'exact',
                                'placeholder' => __( 'Select a Match Type', 'buddyclients-free' ),
                                'options' => [
                                    'exact' => __( 'Exact Match', 'buddyclients-free' ),
                                    'include_any' => __( 'Include Any', 'buddyclients-free' ),
                                    'include_all' => __( 'Include All', 'buddyclients-free' ),
                                    'exclude' => __( 'Exclude', 'buddyclients-free' )
                                ],
                            ],
                            'multiple_options' => [
                                'label' => __( 'Multiple Options', 'buddyclients-free' ),
                                'description' => __( 'Can clients select multiple options for this field?', 'buddyclients-free' ),
                                'type' => 'dropdown',
                                'placeholder' => __( 'Select One', 'buddyclients-free' ),
                                'default' => 'no',
                                'options' => [
                                    'no' => __( 'No (dropdown field)', 'buddyclients-free' ),
                                    'yes' => __( 'Yes (checkbox field)', 'buddyclients-free' ),
                                ],
                            ],
                        ],
                    ],
                    'Help Doc' => [
                        'meta' => [
                            'help_post_id' => [
                                'label' => __( 'Help Post', 'buddyclients-free' ),
                                'description' => __( 'Help doc to show on booking form.', 'buddyclients-free' ),
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