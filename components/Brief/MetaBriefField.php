<?php
namespace BuddyClients\Components\Brief;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_brief_field posts.
 *
 * @since 1.0.29
 */
class MetaBriefField {
    
   /**
     * Defines the post meta data.
     * 
     * @since 1.0.29
     */
    public static function meta() {
        return [
            'Details' => [
                'tables' => [
                    'Brief Types' => [
                        'meta' => [
                            'brief_types' => [
                                'label' => __('Brief Types', 'buddyclients-lite'),
                                'description' => __('Select the brief types that should display the field.', 'buddyclients-lite'),
                                'type' => 'checkbox',
                                'options' => 'brief_type',
                            ],
                        ],
                    ],
                    'Display' => [
                        'meta' => [
                            'field_type' => [
                                'label' => __('Field Type', 'buddyclients-lite'),
                                'type' => 'dropdown',
                                'placeholder' => __('Select one', 'buddyclients-lite'),
                                'options' => [
                                    'disabled'      => __('Disabled', 'buddyclients-lite'),
                                    'text_area'     => __('Text Area', 'buddyclients-lite'),
                                    'input'         => __('Input', 'buddyclients-lite'),
                                    'checkbox'      => __('Checkbox', 'buddyclients-lite'),
                                    'dropdown'      => __('Dropdown', 'buddyclients-lite'),
                                    'upload'        => __('Upload', 'buddyclients-lite'),
                                ],
                            ],
                            'field_description' => [
                                'label' => __('Field Description', 'buddyclients-lite'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Select an option.', 'buddyclients-lite'),
                            ],
                            'help_post_id' => [
                                'label' => __('Help Post', 'buddyclients-lite'),
                                'description' => __('Select a help doc to show on the brief form.', 'buddyclients-lite'),
                                'type' => 'dropdown',
                                'options' => 'help_docs',
                                'placeholder' => __('Select one', 'buddyclients-lite')
                            ],
                        ],
                    ],
                ],
            ],
            'Upload Fields' => [
                'description' => __('These options only apply to upload fields.', 'buddyclients-lite'),
                'tables' => [
                    'Field Options' => [
                        'meta' => [
                            'multiple_files' => [
                                'label' => __('Multiple Files', 'buddyclients-lite'),
                                'description' => __('Should the upload field accept multiple files?', 'buddyclients-lite'),
                                'type' => 'dropdown',
                                'options' => [
                                    false => __('No', 'buddyclients-lite'),
                                    true  => __('Yes', 'buddyclients-lite'),
                                ]
                            ],
                            'file_types' => [
                                'label' => __('Accepted File Types', 'buddyclients-lite'),
                                'description' => __('Select all file types to accept.', 'buddyclients-lite'),
                                'type' => 'checkbox',
                                'options' => [
                                    '.pdf'           => __('PDF', 'buddyclients-lite'),
                                    '.jpg, .jpeg'    => __('JPG Image', 'buddyclients-lite'),
                                    '.png'           => __('PNG Image', 'buddyclients-lite'),
                                    '.doc, .docx'    => __('Microsoft Word', 'buddyclients-lite'),
                                    '.gif'           => __('GIF', 'buddyclients-lite'),
                                    '.xlsx, .xls'    => __('Microsoft Excel', 'buddyclients-lite'),
                                    '.pptx, .ppt'    => __('Microsoft PowerPoint', 'buddyclients-lite'),
                                    '.mp3'           => __('MP3 Audio', 'buddyclients-lite'),
                                    '.mp4, .mov'     => __('Video', 'buddyclients-lite'),
                                    '.zip'           => __('ZIP', 'buddyclients-lite'),
                                    '.txt'           => __('Text', 'buddyclients-lite'),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Dropdown and Checkbox Fields' => [
                'description' => __('These options only apply to dropdown and checkbox fields.', 'buddyclients-lite'),
                'tables' => [
                    'Field Options' => [
                        'meta' => [
                            'field_options' => [
                                'label' => __('Field Options', 'buddyclients-lite'),
                                'description' => __('Enter all options the client can select from.', 'buddyclients-lite'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Option 1, Option 2', 'buddyclients-lite')
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}