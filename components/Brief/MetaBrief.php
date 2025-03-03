<?php
namespace BuddyClients\Components\Brief;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_brief posts.
 *
 * @since 1.0.29
 */
class MetaBrief {
    
   /**
     * Defines the post meta data.
     * 
     * @since 1.0.29
     */
    public static function meta() {
        return [
            'Project' => [
                'tables' => [
                    'Project' => [
                        'meta' => [
                            'project_id' => [
                                'label' => __('Project', 'buddyclients-free'),
                                'type' => 'dropdown',
                                'options' => 'projects',
                                'placeholder' => __('Select Project', 'buddyclients-free'),
                            ],
                        ],
                    ],
                ],
            ],
            'Brief' => [
                'tables' => [
                    'Updated' => [
                        'meta' => [
                            'updated_date' => [
                                'label' => __('Last Updated', 'buddyclients-free'),
                                'type' => 'display_date',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}