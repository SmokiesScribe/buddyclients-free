<?php
namespace BuddyClients\Admin;

use BuddyClients\Components\Service\Adjustment as Adjustment;

/**
 * Manages meta fields for all post types.
 *
 * @since 0.1.0
 */
class MetaManager {
    
    /**
     * Post type.
     * 
     * The slug of the post type.
     * 
     * @var string
     */
    public $post_type;
    
    /**
     * Meta info.
     * 
     * The meta fields to apply to the post type.
     * 
     * @var array
     */
    public $meta;
    
    /**
     * Constructor method.
     * 
     * @since 0.3.4
     * 
     * @param   string  $post_type  The post type slug.
     */
    public function __construct( $post_type ) {
        $this->post_type = $post_type;
        $this->meta = $this->post_type_meta( $post_type );
    }
    
    /**
     * Generates an array of callables that define meta info.
     * 
     * @since 0.3.4
     */
    private static function meta_callbacks() {
        
        // Get class methods
        $methods = get_class_methods( static::class );
        
        // Convert method names to callables
        $callables = array_map( function( $method ) {
            return [static::class, $method];
        }, $methods );
        
        /**
         * Filters the callbacks defining meta fields.
         *
         * @since 0.3.4
         *
         * @param array  $callables   An array of callables defining meta fields.
         */
         $callables = apply_filters( 'bc_meta_methods', $callables );
         
         // Return modified methods array
         return $callables;
    }
    
    /**
     * Retrieves meta array by post type.
     * 
     * @since 0.1.0
     * @updated 0.3.4
     */
    public function post_type_meta( $post_type ) {
        // Get meta callbacks
        $callables = static::meta_callbacks();
        
        // Iterate through callables to find the matching post type method
        foreach ( $callables as $method ) {
            if ( $method[1] === $post_type && is_callable( $method ) ) {
                return call_user_func( $method );
            }
        }
        
        // No matching callable found
        return [];
    }
    
    /**
     * Service meta.
     * 
     * @since 0.1.0
     */
    static private function bc_service() {
        return [
            'Type' => [
                'tables' => [
                    'Type' => [ 
                        'meta' => [
                            'service_type' => [
                                'label' => __( 'Service Type', 'buddyclients' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add service types */
                                    __( 'Select the category for this service. <a href="%s">Add service types.</a>', 'buddyclients' ),
                                    admin_url('/edit.php?post_type=bc_service_type')
                                ),
                                'type' => 'dropdown',
                                'options' => 'bc_service_type',
                            ],
                        ],
                    ],
                ],
            ],
            'Team' => [
                'freelancer' => 'disable',
                'tables' => [
                    'Team Member' => [
                        'meta' => [
                            'team_member_role' => [
                                'label' => __( 'Team Member Role', 'buddyclients' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add roles */
                                    __( 'Select which team member role applies to this service. <a href="%s">Add roles.</a>', 'buddyclients' ),
                                    admin_url('/edit.php?post_type=bc_role')
                                ),
                                'type' => 'dropdown',
                                'options' => 'bc_role',
                                'required' => true,
                            ],
                            'assigned_team_member' => [
                                'label' => __( 'Assigned Team Member', 'buddyclients' ),
                                'description' => __( 'Select a team member to ALWAYS be assigned this service. Selecting a team member here disables the Choose a Team Member option on the booking form and overrides all team member filtering.', 'buddyclients' ),
                                'required' => false,
                                'freelancer' => 'disable',
                                'type' => 'dropdown',
                                'options' => 'team',
                            ],
                        ],
                    ],
                ],
            ],
            'Rates' => [
                'tables' => [
                    'Client' => [
                        'meta' => [
                            'rate_value' => [
                                'label' => __( 'Rate Value', 'buddyclients' ),
                                'description' => __( 'Enter the fee for this service.', 'buddyclients' ),
                                'type' => 'number',
                                'required' => false,
                            ],
                            'rate_type' => [
                                'label' => __( 'Rate Type', 'buddyclients' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add rate types */
                                    __( 'Select the type of fee entered above. <a href="%s">Add rate types.</a>', 'buddyclients' ),
                                    admin_url('/edit.php?post_type=bc_rate_type')
                                ),
                                'type' => 'dropdown',
                                'options' => 'bc_rate_type',
                            ],
                        ],
                    ],
                    'Team' => [
                        'freelancer' => 'disable',
                        'meta' => [
                            'team_member_percentage' => [
                                'label' => __( 'Team Member Percentage', 'buddyclients' ),
                                'description' => __( 'What percentage of the client fee do team members receive for this service? Ex: 50', 'buddyclients' ),
                                'type' => 'number',
                                'required_component' => 'stripe',
                                'freelancer' => 'disable',
                            ],
                        ],
                    ],
                    'Adjustments' => [
                        'meta' => [
                            'adjustments' => [
                                'label' => __( 'Adjustments', 'buddyclients' ),
                                'description' => __( 'Select any rate adjustments that apply to this service.', 'buddyclients' ),
                                'type' => 'checkbox',
                                'required' => false,
                                'options' => 'bc_adjustment',
                            ],
                        ],
                    ],
                ],
            ],
            'Service' => [
                'tables' => [
                    'Brief' => [
                        'meta' => [
                            'brief_type' => [
                                'label' => __( 'Brief Type', 'buddyclients' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add brief types */
                                    __( 'Select the brief type(s) to create for this service. <a href="%s">Add brief types.</a>', 'buddyclients' ),
                                    admin_url('/edit-tags.php?taxonomy=brief_type&&post_type=bc_brief')
                                ),
                                'required' => false,
                                'required_component' => 'briefs',
                                'type' => 'checkbox',
                                'options' => 'brief_type',
                            ],
                        ],
                    ],
                    'Dependencies' => [
                        'meta' => [
                            'dependency' => [
                                'label' => __( 'Required Services', 'buddyclients' ),
                                'description' => __( 'Which service(s) must be booked or selected before this one is available?', 'buddyclients' ),
                                'required' => false,
                                'type' => 'checkbox',
                                'options' => 'bc_service',
                            ],
                        ],
                    ],
                    'File Uploads' => [
                        'meta' => [
                            'file_uploads' => [
                                'label' => __( 'File Uploads', 'buddyclients' ),
                                'description' => __( 'Select any file upload types that apply to this service.', 'buddyclients' ),
                                'type' => 'checkbox',
                                'required' => false,
                                'options' => 'bc_file_upload',
                            ],
                        ],
                    ],
                ],
            ],
            'Display' => [
                'tables' => [
                    'Display' => [
                        'meta' => [
                            'order' => [
                                'label' => __( 'Order', 'buddyclients' ),
                                'description' => __( 'Higher numbers are shown first.', 'buddyclients' ),
                                'type' => 'number',
                                'required' => false,
                            ],
                            'hide' => [
                                'label' => __( 'Hide', 'buddyclients' ),
                                'description' => '',
                                'required' => false,
                                'type' => 'checkbox',
                                'options' => [
                                    true => __( 'Hide from booking form. (The service will still appear in shortcodes and archives.)', 'buddyclients' ),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    /**
     * Service type meta.
     * 
     * @since 0.1.0
     */
    static private function bc_service_type() {
        return [
            'Service Type' => [
                'tables' => [
                    'Display' => [
                        'meta' => [
                            'form_field_type' => [
                                'label' => __( 'Form Field Type', 'buddyclients' ),
                                'description' => __( 'Type of field to display these services on booking form. Choose checkboxes to allow users to select multiple options.', 'buddyclients' ),
                                'type' => 'dropdown',
                                'default' => 'dropdown',
                                'options' => [
                                    'dropdown' => __( 'Dropdown', 'buddyclients' ),
                                    'checkbox' => __( 'Checkboxes', 'buddyclients' )
                                ],
                            ],
                            'order' => [
                                'label' => __( 'Order', 'buddyclients' ),
                                'description' => __( 'Higher numbers are shown first.', 'buddyclients' ),
                                'type' => 'number',
                                'placeholder' => '0',
                            ],
                            'hide' => [
                                'label' => __( 'Hide', 'buddyclients' ),
                                'description' => '',
                                'type' => 'checkbox',
                                'options' => [
                                    true => __( 'Hide all services of this category from the booking form. (They will still appear in shortcodes and archives.)', 'buddyclients' ),
                                ],
                            ],
                        ],
                    ],
                    'Help Doc' => [
                        'meta' => [
                            'help_post_id' => [
                                'label' => __( 'Help Post', 'buddyclients' ),
                                'description' => __( 'Help doc to show on booking form.', 'buddyclients' ),
                                'type' => 'dropdown',
                                'options' => 'help_docs',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Filter field meta.
     * 
     * @since 0.1.0
     */
    static private function bc_filter() {
        return [
            'Xprofile Field' => [
                'tables' => [
                    'Field' => [
                        'meta' => [
                            'xprofile_field' => [
                                'label' => __( 'Xprofile Field', 'buddyclients' ),
                                'description' => sprintf(
                                    /* translators: %s: the url to add xprofile fields */
                                    __( 'Choose from checkbox and dropdown Xprofile fields. <a href="%s">Create a new field.</a>', 'buddyclients' ),
                                    admin_url('/admin.php?page=bp-profile-setup')
                                ),
                                'type' => 'dropdown',
                                'options' => bc_options( 'xprofile', ['existing' => ['bc_filter' => 'xprofile_field']] ),
                                'placeholder' => __( 'Select a Field', 'buddyclients' ),
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
                                'label' => __( 'Form Label', 'buddyclients' ),
                                'description' => __( 'The label to display on the booking form.', 'buddyclients' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Preferences', 'buddyclients' ),
                            ],
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients' ),
                                'description' => __( 'The description to display on the booking form.', 'buddyclients' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Select applicable preferences.', 'buddyclients' ),
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
                                'label' => __( 'Match Type', 'buddyclients' ),
                                'description' => __( '<strong>Exact:</strong> Only team members whose response matches exactly will be available.
                                <br><strong>Exclude:</strong> Only team members whose profile response does not include the client\'s response will be available.
                                <br><strong>Include Any:</strong> Only team members whose response includes any of the selected options will be available.
                                <br><strong>Include All:</strong> Only team members whose response includes all of the selected options will be available.', 'buddyclients' ),
                                'type' => 'dropdown',
                                'default' => 'exact',
                                'placeholder' => __( 'Select a Match Type', 'buddyclients' ),
                                'options' => [
                                    'exact' => __( 'Exact Match', 'buddyclients' ),
                                    'include_any' => __( 'Include Any', 'buddyclients' ),
                                    'include_all' => __( 'Include All', 'buddyclients' ),
                                    'exclude' => __( 'Exclude', 'buddyclients' )
                                ],
                            ],
                            'multiple_options' => [
                                'label' => __( 'Multiple Options', 'buddyclients' ),
                                'description' => __( 'Can clients select multiple options for this field?', 'buddyclients' ),
                                'type' => 'dropdown',
                                'placeholder' => __( 'Select One', 'buddyclients' ),
                                'default' => 'no',
                                'options' => [
                                    'no' => __( 'No (dropdown field)', 'buddyclients' ),
                                    'yes' => __( 'Yes (checkbox field)', 'buddyclients' ),
                                ],
                            ],
                        ],
                    ],
                    'Help Doc' => [
                        'meta' => [
                            'help_post_id' => [
                                'label' => __( 'Help Post', 'buddyclients' ),
                                'description' => __( 'Help doc to show on booking form.', 'buddyclients' ),
                                'type' => 'dropdown',
                                'options' => 'help_docs',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
        
    /**
     * Team member role meta.
     * 
     * @since 0.1.0
     */
    static private function bc_role() {
        return [
            'Display' => [
                'tables' => [
                    'Labels' => [
                        'meta' => [
                            'singular' => [
                                'label' => __( 'Singular', 'buddyclients' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Editor', 'buddyclients' ),
                            ],
                            'plural' => [
                                'label' => __( 'Plural', 'buddyclients' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Editors', 'buddyclients' ),
                            ],
                        ],
                    ],
                    'Description' => [
                        'meta' => [
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients' ),
                                'description' => __( 'Optional.', 'buddyclients' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Does awesome things for clients.', 'buddyclients' ),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Rate adjustment meta.
     * 
     * @since 0.1.0
     */
    static private function bc_adjustment() {
        return [
            'Type' => [
                'tables' => [
                    'Type' => [
                        'meta' => [
                            'form_field_type' => [
                                'label' => __( 'Form Field Type', 'buddyclients' ),
                                'description' => __( 'Type of field on booking form.', 'buddyclients' ),
                                'type' => 'dropdown',
                                'options' => [
                                    'dropdown' => __( 'Dropdown', 'buddyclients' ),
                                    'checkbox' => __( 'Checkboxes', 'buddyclients' ),
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
                                'label' => __( 'Label', 'buddyclients' ),
                                'description' => __( 'Field label to display on booking form.', 'buddyclients' ),
                                'type' => 'text',
                            ],
                            'field_description' => [
                                'label' => __( 'Description', 'buddyclients' ),
                                'description' => __( 'Field description to display on booking form.', 'buddyclients' ),
                                'type' => 'text',
                            ],
                        ],
                    ],
                    'Help Doc' => [
                        'meta' => [
                            'help_post_id' => [
                                'label' => __( 'Help Post', 'buddyclients' ),
                                'description' => __( 'Help doc to show on booking form.', 'buddyclients' ),
                                'type' => 'dropdown',
                                'options' => 'help_docs',
                            ],
                        ],
                    ],
                ],
            ],
            'Options' => [
                'description' => __( 'Each option will adjust the service fee based on the user response.', 'buddyclients' ),
                'tables' => self::adjustment_options(),
            ],
            'New Option' => [
                'description' => __( '<a id="bc_adjustment_create_option" class="button-secondary">Add Option</a>', 'buddyclients' ),
                'tables' => [],
            ],
        ];
    }
    
    /**
     * Generates array for adjustment options.
     * 
     * @since 0.1.0
     */
    static private function adjustment_options() {
        
        // Initialize array
        $meta_fields = [];

        // Handle params
        $param_manager = bc_param_manager();
        $post_id = $param_manager->get( 'post' );
        
        // Default to 10 while saving
        if ( ! $post_id ) {
            $options_count = 10;
        } else {
            
            // Get options count
            $adjustment = new Adjustment( $post_id );
            
            $options_count = $adjustment->options_count;
            
            // Default to 2
            $options_count = $options_count === 0 ? 2 : $options_count;
        }
        
        // Loop with number of options
        for ($i = 1; $i <= $options_count; $i++) {
            $meta_fields['Option ' . $i] = [
                'meta' => [
                    'option_' . $i . '_label' => [
                        'label' => __( 'Label', 'buddyclients' ),
                        'description' => '',
                        'type' => 'text',
                    ],
                    'option_' . $i . '_operator' => [
                        'label' => __( 'Operator', 'buddyclients' ),
                        'description' => '',
                        'type' => 'dropdown',
                        'options' => 'operator',
                    ],
                    'option_' . $i . '_value' => [
                        'label' => __( 'Value', 'buddyclients' ),
                        'description' => '',
                        'type' => 'text',
                    ],
                ],
            ];
        }
        return $meta_fields;
    }
    
    /**
     * Rate type meta.
     * 
     * @since 0.1.0
     */
    static private function bc_rate_type() {
        return [
            'Unit' => [
                'tables' => [
                    'Labels' => [
                        'meta' => [
                            'singular' => [
                                'label' => __( 'Singular', 'buddyclients' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Word, Hour, Page', 'buddyclients' ),
                            ],
                            'plural' => [
                                'label' => __( 'Plural', 'buddyclients' ),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Words, Hours, Pages', 'buddyclients' ),
                            ],
                        ],
                    ],
                    'Description' => [
                        'meta' => [
                            'form_description' => [
                                'label' => __( 'Description', 'buddyclients' ),
                                'description' => __( 'Instructions for users on booking form.', 'buddyclients' ),
                                'type' => 'text',
                                'placeholder' => __( 'e.g. Please add your full word count.', 'buddyclients' ),
                            ],
                        ],
                    ],
                ],
            ],
            'Settings' => [
                'tables' => [
                    'Calculations' => [
                        'meta' => [
                            'attach' => [
                                'label' => __( 'Attach Count To', 'buddyclients' ),
                                'description' => __( 'Each time a client books services, will this number be different for each service or the same for the entire project?', 'buddyclients' ),
                                'type' => 'dropdown',
                                'options' => [
                                    'project' => __( 'Project', 'buddyclients' ),
                                    'service' => __( 'Service', 'buddyclients' ),
                                ],
                                'default' => 'project'
                            ],
                            'minimum' => [
                                'label' => __( 'Minimum', 'buddyclients' ),
                                'description' => __( 'The minimum number of units allowed.', 'buddyclients' ),
                                'type' => 'number',
                                'placeholder' => 0
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
        
    /**
     * File upload type meta.
     * 
     * @since 0.1.0
     */
    static private function bc_file_upload() {
        return [
            'Unit' => [
                'tables' => [
                    'Labels' => [
                        'meta' => [
                            'singular' => [
                                'label' => __('Singular', 'buddyclients'),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __('e.g. File, Manuscript', 'buddyclients'),
                            ],
                            'plural' => [
                                'label' => __('Plural', 'buddyclients'),
                                'description' => '',
                                'type' => 'text',
                                'placeholder' => __('e.g. Files, Manuscripts', 'buddyclients'),
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
                                'label' => __('Description', 'buddyclients'),
                                'description' => __('Instructions for users on booking form.', 'buddyclients'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Please upload your finalized manuscript.', 'buddyclients'),
                            ],
                        ],
                    ],
                    'Help Doc' => [
                        'meta' => [
                            'help_post_id' => [
                                'label' => __('Help Post', 'buddyclients'),
                                'description' => __('Help doc to show on booking form.', 'buddyclients'),
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
                                'label' => __('Accepted File Types', 'buddyclients'),
                                'description' => __('Select all file types to accept.', 'buddyclients'),
                                'type' => 'checkbox',
                                'options' => [
                                    '.pdf'           => __('PDF', 'buddyclients'),
                                    '.jpg, .jpeg'    => __('JPG Image', 'buddyclients'),
                                    '.png'           => __('PNG Image', 'buddyclients'),
                                    '.doc, .docx'    => __('Microsoft Word', 'buddyclients'),
                                    '.gif'           => __('GIF', 'buddyclients'),
                                    '.xlsx, .xls'    => __('Microsoft Excel', 'buddyclients'),
                                    '.pptx, .ppt'    => __('Microsoft PowerPoint', 'buddyclients'),
                                    '.mp3'           => __('MP3 Audio', 'buddyclients'),
                                    '.mp4, .mov'     => __('Video', 'buddyclients'),
                                    '.zip'           => __('ZIP', 'buddyclients'),
                                    '.txt'           => __('Text', 'buddyclients'),
                                ],
                            ],
                            'multiple_files' => [
                                'label' => __('Multiple Files', 'buddyclients'),
                                'description' => __('Should multiple files be allowed?', 'buddyclients'),
                                'type' => 'dropdown',
                                'options' => [
                                    'false' => __('No', 'buddyclients'),
                                    'true'  => __('Yes', 'buddyclients'),
                                ],
                                'default' => false
                            ],
                            'required' => [
                                'label' => __('Required', 'buddyclients'),
                                'description' => __('Should this file upload be required?', 'buddyclients'),
                                'type' => 'dropdown',
                                'options' => [
                                    'false' => __('No', 'buddyclients'),
                                    'true'  => __('Yes', 'buddyclients'),
                                ],
                                'default' => false
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Brief meta.
     * 
     * @since 0.1.0
     */
    static private function bc_brief() {
        return [
            'Project' => [
                'tables' => [
                    'Project' => [
                        'meta' => [
                            'project_id' => [
                                'label' => __('Project', 'buddyclients'),
                                'type' => 'dropdown',
                                'options' => 'projects',
                                'placeholder' => __('Select Project', 'buddyclients'),
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
                                'label' => __('Last Updated', 'buddyclients'),
                                'type' => 'display_date',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Brief field meta.
     * 
     * @since 0.1.0
     */
    static private function bc_brief_field() {
        return [
            'Details' => [
                'tables' => [
                    'Brief Types' => [
                        'meta' => [
                            'brief_types' => [
                                'label' => __('Brief Types', 'buddyclients'),
                                'description' => __('Select the brief types that should display the field.', 'buddyclients'),
                                'type' => 'checkbox',
                                'options' => 'brief_type',
                            ],
                        ],
                    ],
                    'Display' => [
                        'meta' => [
                            'field_type' => [
                                'label' => __('Field Type', 'buddyclients'),
                                'type' => 'dropdown',
                                'placeholder' => __('Select one', 'buddyclients'),
                                'options' => [
                                    'disabled'      => __('Disabled', 'buddyclients'),
                                    'text_area'     => __('Text Area', 'buddyclients'),
                                    'input'         => __('Input', 'buddyclients'),
                                    'checkbox'      => __('Checkbox', 'buddyclients'),
                                    'dropdown'      => __('Dropdown', 'buddyclients'),
                                    'upload'        => __('Upload', 'buddyclients'),
                                ],
                            ],
                            'field_description' => [
                                'label' => __('Field Description', 'buddyclients'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Select an option.', 'buddyclients'),
                            ],
                            'help_post_id' => [
                                'label' => __('Help Post', 'buddyclients'),
                                'description' => __('Select a help doc to show on the brief form.', 'buddyclients'),
                                'type' => 'dropdown',
                                'options' => 'help_docs',
                                'placeholder' => __('Select one', 'buddyclients')
                            ],
                        ],
                    ],
                ],
            ],
            'Upload Fields' => [
                'description' => __('These options only apply to upload fields.', 'buddyclients'),
                'tables' => [
                    'Field Options' => [
                        'meta' => [
                            'multiple_files' => [
                                'label' => __('Multiple Files', 'buddyclients'),
                                'description' => __('Should the upload field accept multiple files?', 'buddyclients'),
                                'type' => 'dropdown',
                                'options' => [
                                    false => __('No', 'buddyclients'),
                                    true  => __('Yes', 'buddyclients'),
                                ]
                            ],
                            'file_types' => [
                                'label' => __('Accepted File Types', 'buddyclients'),
                                'description' => __('Select all file types to accept.', 'buddyclients'),
                                'type' => 'checkbox',
                                'options' => [
                                    '.pdf'           => __('PDF', 'buddyclients'),
                                    '.jpg, .jpeg'    => __('JPG Image', 'buddyclients'),
                                    '.png'           => __('PNG Image', 'buddyclients'),
                                    '.doc, .docx'    => __('Microsoft Word', 'buddyclients'),
                                    '.gif'           => __('GIF', 'buddyclients'),
                                    '.xlsx, .xls'    => __('Microsoft Excel', 'buddyclients'),
                                    '.pptx, .ppt'    => __('Microsoft PowerPoint', 'buddyclients'),
                                    '.mp3'           => __('MP3 Audio', 'buddyclients'),
                                    '.mp4, .mov'     => __('Video', 'buddyclients'),
                                    '.zip'           => __('ZIP', 'buddyclients'),
                                    '.txt'           => __('Text', 'buddyclients'),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Dropdown and Checkbox Fields' => [
                'description' => __('These options only apply to dropdown and checkbox fields.', 'buddyclients'),
                'tables' => [
                    'Field Options' => [
                        'meta' => [
                            'field_options' => [
                                'label' => __('Field Options', 'buddyclients'),
                                'description' => __('Enter all options the client can select from.', 'buddyclients'),
                                'type' => 'text',
                                'placeholder' => __('e.g. Option 1, Option 2', 'buddyclients')
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
        
    /**
     * Custom quote meta.
     * 
     * @since 0.1.0
     */
    static private function bc_quote() {
        return [
            'Project' => [
                'tables' => [
                    'Project' => [
                        'meta' => [
                            'client_id' => [
                                'label' => __('Client', 'buddyclients'),
                                'description' => __('Select the client who can access this custom quote.', 'buddyclients'),
                                'type' => 'dropdown',
                                'options' => 'client',
                            ],
                            'project_id' => [
                                'label' => __('Project', 'buddyclients'),
                                'description' => __('Optionally, select the project this quote applies to.', 'buddyclients'),
                                'type' => 'dropdown',
                                'options' => 'projects',
                            ],
                        ],
                    ],
                ],
            ],
            'Team' => [
                'freelancer' => 'disable',
                'tables' => [
                    'Team Member' => [
                        'meta' => [
                            'team_member_role' => [
                                'label' => __('Team Member Role', 'buddyclients'),
                                'description' => sprintf(
                                    /* translators: %s: URL to add roles */
                                    __('Select which team member role applies to this quote. <a href="%s">Add roles.</a>', 'buddyclients'),
                                    esc_url(admin_url('/edit.php?post_type=bc_role'))
                                ),
                                'type' => 'dropdown',
                                'options' => 'bc_role',
                                'required' => true,
                            ],
                            'assigned_team_member' => [
                                'label' => __('Assigned Team Member', 'buddyclients'),
                                'description' => __('Select a specific team member to be assigned this quote. Selecting a team member here disables the Choose a Team Member option on the booking form and overrides all team member filtering.', 'buddyclients'),
                                'required' => false,
                                'freelancer' => 'disable',
                                'type' => 'dropdown',
                                'options' => 'team',
                            ],
                        ],
                    ],
                ],
            ],
            'Rates' => [
                'tables' => [
                    'Client' => [
                        'meta' => [
                            'rate_value' => [
                                'label' => __('Rate Value', 'buddyclients'),
                                'description' => __('Enter the fee for this service.', 'buddyclients'),
                                'type' => 'number',
                                'required' => false,
                            ],
                            'rate_type' => [
                                'label' => __('Rate Type', 'buddyclients'),
                                'description' => sprintf(
                                    /* translators: %s: URL to add rate types */
                                    __('Select the type of fee entered above. <a href="%s">Add rate types.</a>', 'buddyclients'),
                                    esc_url(admin_url('/edit.php?post_type=bc_rate_type'))
                                ),
                                'type' => 'dropdown',
                                'options' => 'bc_rate_type',
                            ],
                        ],
                    ],
                    'Team' => [
                        'freelancer' => 'disable',
                        'meta' => [
                            'team_member_percentage' => [
                                'label' => __('Team Member Percentage', 'buddyclients'),
                                'description' => __('What percentage of the client fee do team members receive for this service? Ex: 50', 'buddyclients'),
                                'type' => 'number',
                                'required_component' => 'stripe',
                                'freelancer' => 'disable',
                            ],
                        ],
                    ],
                    'Adjustments' => [
                        'meta' => [
                            'adjustments' => [
                                'label' => __('Adjustments', 'buddyclients'),
                                'description' => __('Select any rate adjustments that apply to this service.', 'buddyclients'),
                                'type' => 'checkbox',
                                'required' => false,
                                'options' => 'bc_adjustment'
                            ],
                        ],
                    ],
                ],
            ],
            'Service' => [
                'tables' => [
                    'Brief' => [
                        'meta' => [
                            'brief_type' => [
                                'label' => __('Brief Type', 'buddyclients'),
                                'description' => sprintf(
                                    /* translators: %s: URL to add brief types */
                                    __('Select the brief type(s) to create for this service. <a href="%s">Add brief types.</a>', 'buddyclients'),
                                    esc_url(admin_url('/edit-tags.php?taxonomy=brief_type&post_type=bc_brief'))
                                ),
                                'required' => false,
                                'required_component' => 'briefs',
                                'type' => 'checkbox',
                                'options' => 'brief_type',
                            ],
                        ],
                    ],
                    'File Uploads' => [
                        'meta' => [
                            'file_uploads' => [
                                'label' => __('File Uploads', 'buddyclients'),
                                'description' => __('Select any file upload types that apply to this service.', 'buddyclients'),
                                'type' => 'checkbox',
                                'required' => false,
                                'options' => 'bc_file_upload'
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Custom user legal modification.
     * 
     * @since 0.4.0
     */
    static private function bc_legal_mod() {
        return [
            'Info' => [
                'tables' => [
                    'Info' => [
                        'meta' => [
                            'user_id' => [
                                'label' => __('User', 'buddyclients'),
                                'description' => __('Select the users to apply this to.', 'buddyclients'),
                                'type' => 'checkbox',
                                'options' => 'users',
                            ],
                            'legal_type' => [
                                'label' => __('Legal Type', 'buddyclients'),
                                'description' => __('Select the type of legal agreement this content is for.', 'buddyclients'),
                                'type' => 'dropdown',
                                'options' => function_exists('bc_legal_types') ? bc_legal_types() : [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Testimonial.
     * 
     * @since 0.4.0
     */
    static private function bc_testimonial() {
        return [
            'Author' => [
                'tables' => [
                    'Author' => [
                        'meta' => [
                            'testimonial_author' => [
                                'label' => __('Testimonial Author Name', 'buddyclients'),
                                'description' => __('Enter a name here to override the author info.', 'buddyclients'),
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}