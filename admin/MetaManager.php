<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
     * The array of meta fields data for the post type.
     * 
     * @var array
     */
    public $meta;

    /**
     * An associative array of meta names and types.
     * Used when saving meta values.
     * 
     * @var array
     */
    public $meta_types;

    /**
     * Stores instances of MetaManager per post type.
     * 
     * @var array
     */
    private static $instances = [];

    /**
     * Private constructor to enforce singleton.
     * 
     * @param   string  $post_type  The post type slug.
     */
    private function __construct( $post_type ) {
        $this->post_type = $post_type;
        $this->get_meta( $post_type );
    }

    /**
     * Retrieves the single instance of MetaManager for a given post type.
     * 
     * @param   string  $post_type  The post type slug.
     * @return  MetaManager  The singleton instance.
     */
    public static function get_instance( $post_type ) {
        if ( ! isset( self::$instances[ $post_type ] ) ) {
            self::$instances[ $post_type ] = new self( $post_type );
        }
        return self::$instances[ $post_type ];
    }

    /**
     * Prevents cloning.
     * 
     * @since 1.0.25
     */
    private function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning this class is not allowed.', 'buddyclients' ), BUDDYC_PLUGIN_VERSION );
    }

    /**
     * Prevents unserialization.
     * 
     * @since 1.0.25
     */
    private function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is not allowed.', 'buddyclients' ), BUDDYC_PLUGIN_VERSION );
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
         $callables = apply_filters( 'buddyc_meta_methods', $callables );
         
         // Return modified methods array
         return $callables;
    }

    /**
     * Builds the meta arrays.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The slug of the post type.
     */
    private function get_meta( $post_type ) {

        // Check cache
        $cache_key = $this->cache_key( $post_type );
        $cached_meta = get_option( $cache_key );

        // Check if cached data is valid
        if ( $this->validate_cache( $cached_meta ) ) {
            // Return cached
            $this->meta = $cached_meta['meta'];
            $this->meta_types = $cached_meta['meta_types'];
            return;
        }

        // Update cache if necessary
        $data = $this->update_cache( $post_type );
        $this->meta = $data['meta'] ?? [];
        $this->meta_types = $data['meta_types'] ?? [];
    }

    /**
     * Validates the cached data.
     * 
     * @since 1.0.25
     * 
     * @param   array   $cached_meta    The array of cached data to validate.
     * 
     * @return  bool    True if valid, false if not.
     */
    private function validate_cache( $cached_meta ) {
        // Empty value
        if ( empty( $cached_meta ) ) {
            return false;
        }

        // Meta or meta types not set
        if ( ! isset( $cached_meta['meta'] ) || ! isset( $cached_meta['meta_types'] ) ) {
            return false;
        }

        // Meta or meta types empty
        if ( empty( $cached_meta['meta'] ) || empty( $cached_meta['meta_types'] ) ) {
            return false;
        }

        // Meta or meta types not array
        if ( ! is_array( $cached_meta['meta'] ) || ! is_array( $cached_meta['meta_types'] ) ) {
            return false;
        }
        
        // Five by five
        return true;

    }

    /**
     * Updates the cached meta.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The slug of the post type.
     */
    private function update_cache( $post_type ) {
        // Get versions
        $version_cache = buddyc_version_cache();

        // Build data
        $meta = $this->post_type_meta( $post_type );
        $meta_types = self::build_meta_types( $post_type, $meta );

        // Build array
        $option_data = [
            'meta'          => $meta,
            'meta_types'    => $meta_types
        ];

        $cache_key = $this->cache_key( $post_type, $version_cache->curr_version );

        // Update cache
        update_option( $this->cache_key( $post_type, $version_cache->curr_version ), $option_data );

        // Delete previous cache
        delete_option( $this->cache_key( $post_type, $version_cache->prev_version ) );

        // Return data
        return $option_data;
    }

    /**
     * Builds the cache key for the option where the meta arrays are stored.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The post type slug.
     * @param   string  $version    Optional. The plugin version.
     *                              Defaults to the current version.
     */
    private function cache_key( $post_type, $version = null ) {
        $version = $version ?? BUDDYC_PLUGIN_VERSION;
        $formatted_version = str_replace( '.', '_', $version );
        return 'buddyc_meta_cache_' . $post_type . '_' . $formatted_version;
    }
    
    /**
     * Retrieves meta array by post type.
     * 
     * @since 0.1.0
     * @updated 0.3.4
     * 
     * @param   string  $post_type  The slug of the post type.
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
     * Generates an associative array of meta names and types.
     * Used when saving meta values.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The slug of the post type.
     * @param   array   $meta       The full array of meta data.
     */
    private static function build_meta_types( $post_type, $meta ) {
        // Initialize
        $meta_types = [];

        // Make sure meta exists
        if ( ! empty( $meta ) ) {
            // Loop through post type meta
            foreach ( $meta as $category => $category_data ) {
                // Loop through tables
                foreach ( $category_data['tables'] as $table => $table_data ) {
                    // Loop through meta items
                    foreach ( $table_data['meta'] as $meta_key => $field_data ) {
                        // Add meta key and type to array
                        $meta_types[$meta_key] = $field_data['type'] ?? '';
                    }
                }
            }
        }
        return $meta_types;
    }
    
    /**
     * Service meta.
     * 
     * @since 0.1.0
     */
    static private function buddyc_service() {
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
                                    admin_url('/edit.php?post_type=buddyc_service_type')
                                ),
                                'type' => 'dropdown',
                                'options' => 'buddyc_service_type',
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
                                    admin_url('/edit.php?post_type=buddyc_role')
                                ),
                                'type' => 'dropdown',
                                'options' => 'buddyc_role',
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
                                    admin_url('/edit.php?post_type=buddyc_rate_type')
                                ),
                                'type' => 'dropdown',
                                'options' => 'buddyc_rate_type',
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
                                'description' => sprintf(
                                    /* translators: %s: the url to add adjustment fields */
                                    __( 'Select any rate adjustments that apply to this service. <a href="%s">Add adjustment fields.</a>', 'buddyclients' ),
                                    admin_url('/edit.php?post_type=buddyc_adjustment')
                                ),
                                'type' => 'checkbox',
                                'required' => false,
                                'options' => 'buddyc_adjustment',
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
                                    admin_url('/edit-tags.php?taxonomy=brief_type&&post_type=buddyc_brief')
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
                                'options' => 'buddyc_service',
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
                                'options' => 'buddyc_file_upload',
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
    static private function buddyc_service_type() {
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
    static private function buddyc_filter() {
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
                                'options' => buddyc_options( 'xprofile', ['existing' => ['buddyc_filter' => 'xprofile_field']] ),
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
    static private function buddyc_role() {
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
    static private function buddyc_adjustment() {
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
                'classes' => 'buddyc-adjustment-options'
            ],
            'New Option' => [
                'description' => '<a id="buddyc_adjustment_create_option" class="button-secondary">' . __( 'Add Option', 'buddyclients' ) . '</a><div id="buddyc_adjustment_create_option_message"></div>',
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
        //$post_id = buddyc_get_param( 'post' );

        // Get the number of current options
        //$options_count = buddyc_adjustment_option_count( $post_id );

        $options_count = 10;
        
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
    static private function buddyc_rate_type() {
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
    static private function buddyc_file_upload() {
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
    static private function buddyc_brief() {
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
    static private function buddyc_brief_field() {
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
    static private function buddyc_quote() {
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
                                    esc_url(admin_url('/edit.php?post_type=buddyc_role'))
                                ),
                                'type' => 'dropdown',
                                'options' => 'buddyc_role',
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
                                    esc_url(admin_url('/edit.php?post_type=buddyc_rate_type'))
                                ),
                                'type' => 'dropdown',
                                'options' => 'buddyc_rate_type',
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
                                'options' => 'buddyc_adjustment'
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
                                    esc_url(admin_url('/edit-tags.php?taxonomy=brief_type&post_type=buddyc_brief'))
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
                                'options' => 'buddyc_file_upload'
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
    static private function buddyc_legal_mod() {
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
                                'options' => function_exists('buddyc_legal_types') ? buddyc_legal_types() : [],
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
    static private function buddyc_testimonial() {
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

    /**
     * Email meta.
     * 
     * @since 0.4.0
     */
    static private function buddyc_email() {
        return [
            'Email Info' => [
                'tables' => [
                    'Subject' => [
                        'meta' => [
                            '_buddyc_email_subject' => [
                                'label' => __('Email Subject', 'buddyclients'),
                                'description' => __('Enter the subject for the email.', 'buddyclients'),
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}