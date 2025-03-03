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
                                'label' => __('Singular', 'buddyclients-free'),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __('e.g. File, Manuscript', 'buddyclients-free'),
                            ],
                            'plural' => [
                                'label' => __('Plural', 'buddyclients-free'),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __('e.g. Files, Manuscripts', 'buddyclients-free'),
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
                                'label' => __('Description', 'buddyclients-free'),
                                'description' => __('Instructions for users on booking form.', 'buddyclients-free'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Please upload your finalized manuscript.', 'buddyclients-free'),
                            ],
                        ],
                    ],
                    'Help Doc' => [
                        'meta' => [
                            'help_post_id' => [
                                'label' => __('Help Post', 'buddyclients-free'),
                                'description' => __('Help doc to show on booking form.', 'buddyclients-free'),
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
                            'multiple_files' => [
                                'label' => __('Multiple Files', 'buddyclients-free'),
                                'description' => __('Should multiple files be allowed?', 'buddyclients-free'),
                                'type' => 'dropdown',
                                'options' => [
                                    'false' => __('No', 'buddyclients-free'),
                                    'true'  => __('Yes', 'buddyclients-free'),
                                ],
                                'default' => false
                            ],
                            'required' => [
                                'label' => __('Required', 'buddyclients-free'),
                                'description' => __('Should this file upload be required?', 'buddyclients-free'),
                                'type' => 'dropdown',
                                'options' => [
                                    'false' => __('No', 'buddyclients-free'),
                                    'true'  => __('Yes', 'buddyclients-free'),
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