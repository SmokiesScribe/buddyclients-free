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
                                'label' => __('Brief Types', 'buddyclients-free'),
                                'description' => __('Select the brief types that should display the field.', 'buddyclients-free'),
                                'type' => 'checkbox',
                                'options' => 'brief_type',
                            ],
                        ],
                    ],
                    'Display' => [
                        'meta' => [
                            'field_type' => [
                                'label' => __('Field Type', 'buddyclients-free'),
                                'type' => 'dropdown',
                                'placeholder' => __('Select one', 'buddyclients-free'),
                                'options' => [
                                    'disabled'      => __('Disabled', 'buddyclients-free'),
                                    'text_area'     => __('Text Area', 'buddyclients-free'),
                                    'input'         => __('Input', 'buddyclients-free'),
                                    'checkbox'      => __('Checkbox', 'buddyclients-free'),
                                    'dropdown'      => __('Dropdown', 'buddyclients-free'),
                                    'upload'        => __('Upload', 'buddyclients-free'),
                                ],
                            ],
                            'field_description' => [
                                'label' => __('Field Description', 'buddyclients-free'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Select an option.', 'buddyclients-free'),
                            ],
                            'help_post_id' => [
                                'label' => __('Help Post', 'buddyclients-free'),
                                'description' => __('Select a help doc to show on the brief form.', 'buddyclients-free'),
                                'type' => 'dropdown',
                                'options' => 'help_docs',
                                'placeholder' => __('Select one', 'buddyclients-free')
                            ],
                        ],
                    ],
                ],
            ],
            'Upload Fields' => [
                'description' => __('These options only apply to upload fields.', 'buddyclients-free'),
                'tables' => [
                    'Field Options' => [
                        'meta' => [
                            'multiple_files' => [
                                'label' => __('Multiple Files', 'buddyclients-free'),
                                'description' => __('Should the upload field accept multiple files?', 'buddyclients-free'),
                                'type' => 'dropdown',
                                'options' => [
                                    false => __('No', 'buddyclients-free'),
                                    true  => __('Yes', 'buddyclients-free'),
                                ]
                            ],
                            'file_types' => [
                                'label' => __('Accepted File Types', 'buddyclients-free'),
                                'description' => __('Select all file types to accept.', 'buddyclients-free'),
                                'type' => 'checkbox',
                                'options' => [
                                    '.pdf'           => __('PDF', 'buddyclients-free'),
                                    '.jpg, .jpeg'    => __('JPG Image', 'buddyclients-free'),
                                    '.png'           => __('PNG Image', 'buddyclients-free'),
                                    '.doc, .docx'    => __('Microsoft Word', 'buddyclients-free'),
                                    '.gif'           => __('GIF', 'buddyclients-free'),
                                    '.xlsx, .xls'    => __('Microsoft Excel', 'buddyclients-free'),
                                    '.pptx, .ppt'    => __('Microsoft PowerPoint', 'buddyclients-free'),
                                    '.mp3'           => __('MP3 Audio', 'buddyclients-free'),
                                    '.mp4, .mov'     => __('Video', 'buddyclients-free'),
                                    '.zip'           => __('ZIP', 'buddyclients-free'),
                                    '.txt'           => __('Text', 'buddyclients-free'),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Dropdown and Checkbox Fields' => [
                'description' => __('These options only apply to dropdown and checkbox fields.', 'buddyclients-free'),
                'tables' => [
                    'Field Options' => [
                        'meta' => [
                            'field_options' => [
                                'label' => __('Field Options', 'buddyclients-free'),
                                'description' => __('Enter all options the client can select from.', 'buddyclients-free'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Option 1, Option 2', 'buddyclients-free')
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}