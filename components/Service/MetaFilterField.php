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
                                'label' => __( 'Xprofile Field', 'buddyclients-lite' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add xprofile fields */
                                    __( 'Choose from checkbox and dropdown Xprofile fields. <a href="%s">Create a new field.</a>', 'buddyclients-lite' ),
                                    admin_url('/admin.php?page=bp-profile-setup')
                                ),
                                'type' => 'dropdown',
                                'options' => buddyc_options( 'xprofile', ['existing' => ['buddyc_filter' => 'xprofile_field']] ),
                                'placeholder' => __( 'Select a Field', 'buddyclients-lite' ),
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
                                'label' => __( 'Form Label', 'buddyclients-lite' ),
                                'description' => __( 'The label to display on the booking form.', 'buddyclients-lite' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Preferences', 'buddyclients-lite' ),
                            ],
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients-lite' ),
                                'description' => __( 'The description to display on the booking form.', 'buddyclients-lite' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Select applicable preferences.', 'buddyclients-lite' ),
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
                                'label' => __( 'Match Type', 'buddyclients-lite' ),
                                'description' => __( '<strong>Exact:</strong> Only team members whose response matches exactly will be available.
                                <br><strong>Exclude:</strong> Only team members whose profile response does not include the client\'s response will be available.
                                <br><strong>Include Any:</strong> Only team members whose response includes any of the selected options will be available.
                                <br><strong>Include All:</strong> Only team members whose response includes all of the selected options will be available.', 'buddyclients-lite' ),
                                'type' => 'dropdown',
                                'default' => 'exact',
                                'placeholder' => __( 'Select a Match Type', 'buddyclients-lite' ),
                                'options' => [
                                    'exact' => __( 'Exact Match', 'buddyclients-lite' ),
                                    'include_any' => __( 'Include Any', 'buddyclients-lite' ),
                                    'include_all' => __( 'Include All', 'buddyclients-lite' ),
                                    'exclude' => __( 'Exclude', 'buddyclients-lite' )
                                ],
                            ],
                            'multiple_options' => [
                                'label' => __( 'Multiple Options', 'buddyclients-lite' ),
                                'description' => __( 'Can clients select multiple options for this field?', 'buddyclients-lite' ),
                                'type' => 'dropdown',
                                'placeholder' => __( 'Select One', 'buddyclients-lite' ),
                                'default' => 'no',
                                'options' => [
                                    'no' => __( 'No (dropdown field)', 'buddyclients-lite' ),
                                    'yes' => __( 'Yes (checkbox field)', 'buddyclients-lite' ),
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