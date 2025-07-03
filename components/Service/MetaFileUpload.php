<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_file_upload posts.
 *
 * @since 1.0.29
 */
class MetaFileUpload {
    
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
                                'label' => __('Singular', 'buddyclients-lite'),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __('e.g. File, Manuscript', 'buddyclients-lite'),
                            ],
                            'plural' => [
                                'label' => __('Plural', 'buddyclients-lite'),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __('e.g. Files, Manuscripts', 'buddyclients-lite'),
                            ],
                        ],
                    ],
                ],
            ],
            'Display' => [
                'tables' => [
                    'Description' => [
                        'meta' => [
                            'form_description' => [
                                'label' => __('Description', 'buddyclients-lite'),
                                'description' => __('Instructions for users on booking form.', 'buddyclients-lite'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Please upload your finalized manuscript.', 'buddyclients-lite'),
                            ],
                        ],
                    ],
                    'Help Doc' => [
                        'meta' => [
                            'help_post_id' => [
                                'label' => __('Help Post', 'buddyclients-lite'),
                                'description' => __('Help doc to show on booking form.', 'buddyclients-lite'),
                                'type' => 'dropdown',
                                'options' => 'help_docs',
                            ],
                        ],
                    ],
                ],
            ],
            'File' => [
                'tables' => [
                    'File' => [
                        'meta' => [
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
                            'multiple_files' => [
                                'label' => __('Multiple Files', 'buddyclients-lite'),
                                'description' => __('Should multiple files be allowed?', 'buddyclients-lite'),
                                'type' => 'dropdown',
                                'options' => [
                                    'false' => __('No', 'buddyclients-lite'),
                                    'true'  => __('Yes', 'buddyclients-lite'),
                                ],
                                'default' => false
                            ],
                            'required' => [
                                'label' => __('Required', 'buddyclients-lite'),
                                'description' => __('Should this file upload be required?', 'buddyclients-lite'),
                                'type' => 'dropdown',
                                'options' => [
                                    'false' => __('No', 'buddyclients-lite'),
                                    'true'  => __('Yes', 'buddyclients-lite'),
                                ],
                                'default' => false
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}