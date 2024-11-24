<?php
namespace BuddyClients\Admin;

/**
 * Post type.
 *
 * Creates a single custom post type.
 */
class Metaboxes {
    
    /**
     * Post type.
     * 
     * The post type to which the metaboxes belong.
     * 
     * @var string
     */
    public $post_type;
    
    /**
     * Meta.
     * 
     * The array of meta data for the post type.
     * 
     * @var array
     */
    protected $meta;
    
    /**
     * Constructor
     * 
     * @since 0.1.0
     */
    public function __construct( $post_type ) {
        $this->post_type = $post_type;
        
        // Get meta array
        $this->meta = ( new MetaManager( $post_type ) )->meta;
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Defines hooks and filters.
     *
     * @since 1.0.0
     */
    private function define_hooks() {
        add_action( 'add_meta_boxes', [$this, 'register_metaboxes'] );
        add_action( 'save_post_' . $this->post_type, [$this, 'save_meta'] );
    }
    
    /**
     * Saves meta values.
     * 
     * @since 0.1.0
     * 
     * @param int $post_id The ID of the post being saved.
     */
    public function save_meta( $post_id ) {
        
        // Exit if autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        // Verify nonce
        if ( ! isset( $_POST[ $this->post_type . '_meta_nonce' ] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $this->post_type . '_meta_nonce' ] ) ), 'save_' . $this->post_type . '_meta' ) ) {
            return;
        }
        
        // Check if user has permission
        if ( !current_user_can( 'edit_post', $post_id ) ) return;
        
        // Exit if no metaboxes
        if ( !$this->meta ) {
            return;
        }
        
        // Loop through post type meta
        foreach ( $this->meta as $category => $category_data ) {
            foreach ( $category_data['tables'] as $table => $table_data ) {
                foreach ( $table_data['meta'] as $meta_key => $field_data ) {
                    if ( $field_data['type'] === 'checkbox' ) {
                        // Check if the checkbox field is submitted
                        if ( isset( $_POST[ $meta_key ] ) && is_array( $_POST[ $meta_key ] ) ) {
                            // Sanitize each item in the array
                            $sanitized_values = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $meta_key ] ) );
                        
                            // Save the sanitized array of values
                            update_post_meta( $post_id, $meta_key, $sanitized_values );
                        } else {
                            // If the checkbox field is not submitted, delete the meta
                            delete_post_meta( $post_id, $meta_key );
                        }                        
                    } else {
                        // For other types of fields, sanitize and save the value
                        if ( isset( $_POST[$meta_key] ) ) {
                            $field_value = sanitize_text_field( wp_unslash( $_POST[$meta_key] ) );
                            update_post_meta( $post_id, $meta_key, $field_value );
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Creates metaboxes.
     * 
     * @since 0.1.0
     */
    public function register_metaboxes() {
        if ( is_array( $this->meta ) ) {
            foreach ( $this->meta as $category => $data ) {
                add_meta_box(
                    $this->post_type . '-' . $category . '_metabox', // Metabox ID
                    $category, // Title to display
                    array( $this, 'metabox_callback' ), // Function to call that contains the metabox content
                    $this->post_type, // Post type to display metabox on
                    'normal', // Where to put it (normal = main column, side = sidebar, etc.)
                    'default', // Priority relative to other metaboxes
                    array(
                        'post_type' => $this->post_type,
                        'data' => $data,
                        'category' => $category, // Pass data to callback
                    )
                );
            }
        }
    }
    
    /**
     * Generates metabox group.
     * 
     * @since 0.1.0
     * 
     * @param object $post The post being edited.
     * @param array $metabox {
     *     Arguments passed from add_meta_box.
     * 
     *     @type string $category The metabox group name.
     * 
     *     @type string $post_type The post type slug.
     * 
     *     @type array $data {
     *         Array containing metabox data for the post type.
     *
     *         @type array $tables {
     *             Information for each metabox table group.
     * 
     *             @type string $description Optional. Description for the entire metabox table.
     *             @type array $meta {
     *                 Fields for each metabox table. Keyed by field key.
     *
     *                 @type string $label The field label.
     *                 @type string $description Optional. The field description.
     *                 @type string $type The field type. Accepts 'dropdown', 'checkboxes', 'input'.
     *                 @type array $options Options for dropdown or checkbox fields.
     *             }
     *         }
     *     }
     * }
     */
    public function metabox_callback( $post, $metabox ) {
        // Initialize
        $content = '';

        // Get data passed to callback
        $data = $metabox['args']['data'];

        // Generate a nonce field for this meta box
        $content .= wp_nonce_field( 'save_' . $this->post_type . '_meta', $this->post_type . '_meta_nonce' );
        
        // Echo metabox category description
        $content .= isset( $data['description'] ) ? wp_kses_post( $data['description'] ) : '';
        
        // Check for Freelancer Mode
        $freelancer_check = self::freelancer_check( $data );
        if ( $freelancer_check ) {
            $content .= wp_kses_post( $freelancer_check . '</fieldset>' );
            return $content;
        }
        
        // Loop through metabox tables
        foreach ( $data['tables'] as $title => $table_data ) {
            
            // Generate table
            $content .= '<table class="widefat bp-postbox-table">';
            $content .= '<thead>';
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= esc_html( $title );
            $content .= '</th>';
            $content .= '</tr>';
            $content .= '</thead>';
            $content .= '<tbody>';
            
            // Check for Freelancer Mode
            $freelancer_check = self::freelancer_check( $table_data );
            if ( $freelancer_check ) {
                $content .= '<tr><td>';
                $content .= wp_kses_post( $freelancer_check );
                $content .= '</td></tr>';
            } else {
                // Generate meta fields
                $meta_group = $this->meta_group( $post, $table_data ) ?? '';
                //echo wp_kses_post( $meta_group );
                $content .= $meta_group;
            }
            
            // Close table
            $content .= '</tbody>';
            $content .= '</table>';
        }
        
        // Escape and output content
        $allowed_html = buddyc_allowed_html_form();
        echo wp_kses( $content, $allowed_html );
    }
    
    /**
     * Checks for Freelancer Mode.
     * 
     * @since 0.1.0
     * 
     * @param array $data The array of data to check.
     * @return string|bool Link to display, false if not enabled.
     */
    private static function freelancer_check( $data ) {
        if ( isset( $data['freelancer'] ) && $data['freelancer'] === 'disable' ) {
            if ( buddyc_freelancer_mode() ) {
                return buddyc_freelancer_mode_link();
            }
        }
        return false;
    }
    
    /**
     * Generates group of meta fields.
     * 
     * @since 0.1.0
     * 
     * @param object $post The current post being edited.
     * @param array $data Array of metabox data.
     *                     @see metabox_callback()
     */
    public function meta_group($post, $data) {
        // Initialize
        $content = '';
        
        // Open fieldset
        $content .= '<fieldset>';
        
        // Loop through fields
        foreach ( $data['meta'] as $field_id => $field_data ) {
            
            // Define options
            if ( isset( $field_data['options'] ) && is_array( $field_data['options'] ) ) {
                $options = $field_data['options'];
            } else if ( isset( $field_data['options'] ) && is_string( $field_data['options'] ) ) {
                $options = $this->options( $field_data['options'] );
            }
            
            // Build field args
            $field_args = [
                'field_id'      => $field_id,
                'freelancer'    => $field_data['freelancer'] ?? false,
                'field_type'    => $field_data['type'] ?? 'text',
                'field_value'   => get_post_meta( $post->ID, $field_id, true ),
                'description'   => $field_data['description'] ?? '',
                'placeholder'   => $field_data['placeholder'] ?? '',
                'options'       => $options ?? [],
                'post_id'       => $post->ID,
                'default'       => $field_data['default'] ?? null,
            ];
            
            // Open row
            $content .= '<tr>';
            $content .= '<th>';
            $content .= esc_html( $field_data['label'] ); // Translate label
            $content .= '</th>';
            $content .= '<td>';
            
            // Generate field by type
            switch ( $field_data['type'] ) {
                case 'text':
                case 'date':
                case 'number':
                    $field = $this->input_field( $field_args );
                    break;
                case 'dropdown':
                    $field = $this->dropdown_field( $field_args );
                    break;
                case 'checkbox':
                    $field = $this->checkbox_field( $field_args );
                    break;
                case 'display_date':
                    $field = $this->display_date( $field_args );
                    break;
                case 'button':
                    $field = $this->button( $field_id, $field_data );
                    break;
                default:
                $field = $this->input_field( $field_args );
                    break;
            }

            // Output field
            $content .= $field;
    
            // Close row
            $content .= '</td>';
            $content .= '</tr>';
        }
    
        // Close fieldset
        $content .= '</fieldset>';
        
        // Return content
        return $content;
    }

    /**
     * Defines reusable options arrays.
     * 
     * @since 0.1.0
     * 
     * @param string $option_key
     */
    private function options( $option_key ) {
        $options = [];
        
        // Check for post type
        if ( post_type_exists( $option_key ) ) {
            $options = buddyc_options( 'posts', ['post_type' => $option_key] );
            
        // Check for taxonomy
        } else if ( taxonomy_exists( $option_key ) ) {
            $options = buddyc_options( 'taxonomy', ['taxonomy' => $option_key] );
        }
        
        switch ( $option_key ) {
            
            // Users
            case 'team':
            case 'client':
            case 'affiliate':
            case 'users':
                $options = buddyc_options( 'users', ['user_type' => $option_key] );
                break;
                
            // Projects
            case 'projects':
                $options = buddyc_options( 'projects' );
                break;
            
            // Payment    
            case 'payment':
                $options = [
                    'pending' => __('Pending', 'buddyclients'),
                    'eligible' => __('Eligible', 'buddyclients'),
                    'paid' => __('Paid', 'buddyclients'),
                ];
                break;
            
            // Operator    
            case 'operator':
                $options = [
                    'x' => __('x (multiply)', 'buddyclients'),
                    '+' => __('+ (add)', 'buddyclients'),
                    '-' => __('- (subtract)', 'buddyclients'),
                ];
                break;
            
            // Help docs    
            case 'help_docs':
                $options = buddyc_options( 'posts', ['post_type' => buddyc_help_post_types()] );
                break;
        }
    
        return $options;
    }
    
    /**
     * Generates input meta field.
     * 
     * @since 0.1.0
     * 
     * @param array $args Field arguments.
     */
    private function input_field( $args ) {
        $default = $args['default'] ?? '';
        $field_value = $args['field_value'] ?? $default;
    
        $field = '';
        $field .= '<input type="' . esc_attr($args['field_type']) . '" class="bc-meta-field" name="' . esc_attr($args['field_id']) . '" placeholder="' . esc_attr($args['placeholder']) . '" value="' . esc_attr($field_value) . '" size="10">';
        $field .= '<div class="bc-meta-description">' . $args['description'] . '</div>';
        return $field;
    }
    
    /**
     * Generates dropdown meta field.
     * 
     * @since 0.1.0
     * 
     * @param array $args Field arguments.
     */
    private function dropdown_field( $args ) {
        $field = '';
        $field .= '<select class="bc-meta-input bc-meta-field" id="' . esc_attr($args['field_id']) . '" name="' . esc_attr($args['field_id']) . '">';
        $field .= '<option value="">' . esc_html($args['placeholder']) . '</option>'; // Empty option
        
        // Initialize
        $match_found = false;
        
        // Get default
        $default = $args['default'] ?? null;
        
        // Loop through options
        foreach ($args['options'] as $option_value => $option_label) {
            
            // Skip current post
            if ($args['post_id'] === $option_value) {
                continue;
            }
            
            // Initialize
            $selected = '';
            
            // Check if the current option value matches the field value
            if ($args['field_value']) {
                $selected = selected($option_value, $args['field_value'], false);
                if ($selected) {
                    $match_found = true;
                }
                
            // Else check if the current option matches the default
            } else if ( $default ) {
                $selected = $option_value == $default ? 'selected' : '';
            }
            
            // Build option
            $field .= '<option value="' . esc_attr($option_value) . '" ' . $selected . '>' . esc_html($option_label) . '</option>';
        }
        
        // Close dropdown
        $field .= '</select>';
        $field .= '<div class="bc-meta-description">' . $args['description'] . '</div>';
    
        // Display info if option not found for existing value
        if (!$match_found && $args['field_value'] !== '') {
            
            // Attempt to get title or display name
            $title = get_the_title($args['field_value']);
            $name = bp_core_get_user_displayname($args['field_value']);
            
            // Display title, name, or value
            $display = $title ? $title : ($name ?? $args['field_value']);
            $field .= sprintf(
                /* translators: %s: the field title, name, or value */
                __('Unavailable: %s', 'buddyclients'),
                esc_html( $display )
            );
        }
        return $field;
    }
    
    /**
     * Generates checkbox meta field.
     * 
     * @since 0.1.0
     * 
     * @param array $args Field arguments.
     */
    private function checkbox_field( $args ) {
        $default = $args['default'] ?? [];
        $field_value = !empty($args['field_value']) ? (array)$args['field_value'] : $default;
    
        $field = '';
        $field .= '<div class="bc-meta-description">' . $args['description'] . '</div>';
        $field .= '<div id="' . esc_attr($args['field_id']) . '">';
        foreach ($args['options'] as $option_value => $option_label) {
            if ($args['post_id'] === $option_value) {
                continue;
            }
            $field .= '<input type="checkbox" id="' . esc_attr($args['field_id'] . '-' . $option_value) . '" name="' . esc_attr($args['field_id'] . '[]') . '" value="' . esc_attr($option_value) . '" ' . checked(in_array($option_value, $field_value), true, false) . '>';
            $field .= '<label for="' . esc_attr($args['field_id'] . '-' . $option_value) . '">' . esc_html($option_label) . '</label><br>';
        }
        $field .= '</div>';
        return $field;
    }
    
    /**
     * Displays the value as a date.
     * 
     * @since 0.1.0
     * 
     * @param array $args Field arguments.
     */
    private function display_date( $args ) {
        return gmdate( 'F j, Y,  h:i A', strtotime($args['field_value']) );
    }
    
    /**
     * Displays a button.
     * 
     * @since 0.1.0
     * 
     * @param string $field_id The field ID.
     * @param array $field_data Field data from meta manager.
     */
    private function button( $field_id, $field_data ) {
        return '<a id="' . esc_attr($field_id) . '" class="button-secondary">' . esc_html($field_data['value']) . '</a>';
    }
}
