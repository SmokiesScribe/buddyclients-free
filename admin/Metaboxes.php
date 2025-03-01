<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
     * The array of meta data for the post type.
     * 
     * @var array
     */
    protected $meta;

    /**
     * The array of meta types keyed by meta key.
     * 
     * @var array
     */
    protected $meta_types;
    
    /**
     * Constructor
     * 
     * @since 0.1.0
     */
    public function __construct( $post_type ) {
        $this->post_type = $post_type;
        
        // Get MetaManager instance
        $meta_manager = MetaManager::get_instance( $post_type );

        // Get meta arrays
        $this->meta = $meta_manager->meta;
        $this->meta_types = $meta_manager->meta_types;
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Defines hooks.
     *
     * @since 1.0.0
     */
    private function define_hooks() {
        add_action( 'add_meta_boxes', [$this, 'register_metaboxes'] );
        add_action( 'save_post_' . $this->post_type, [$this, 'save_meta'] );
    }

    /**
     * Builds the nonce name.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The post type slug.
     * @param   string  $category   The metabox category name.
     * @return  string  The name for the nonce field
     */
    private static function build_nonce_name( $post_type, $category ) {
        $formatted_category = strtolower( str_replace( ' ', '_', $category ) );
        return sprintf(
            'buddyc_%1$s_%2$s_meta_nonce',
            $post_type,
            $formatted_category
        );
    }
    
    /**
     * Saves meta values.
     * 
     * @since 0.1.0
     * 
     * @param int $post_id The ID of the post being saved.
     */
    public function save_meta( $post_id ) {

        // Exit if submission not valid
        if ( ! $this->validate_meta_save( $post_id ) ) {
            return;
        }
        
        // Exit if no metaboxes
        if ( empty( $this->meta_types ) || ! is_array( $this->meta_types ) ) {
            return;
        }
        
        // Loop through post type meta
        foreach ( $this->meta_types as $meta_key => $type ) {
            // Save single meta field
            $this->save_meta_field( $meta_key, $type, $post_id );
        }
    }

    /**
     * Saves a single meta field.
     * 
     * @since 1.0.25
     * 
     * @param   string  $meta_key   The key of the meta field.
     * @param   string  $type       The type of field (e.g. 'checkbox').
     * @param   int     $post_id    The ID of the post being saved.
     */
    private function save_meta_field( $meta_key, $type, $post_id ) {
        switch ( $type ) {
            case 'checkbox':
                $this->save_checkbox_meta_field( $meta_key, $post_id );
                break;
            default:
                $this->save_single_meta_field( $meta_key, $post_id );
                break;
        }
    }

    /**
     * Saves a checkbox meta field.
     * 
     * @since 1.0.25
     * 
     * @param   string  $meta_key   The key of the meta field.
     * @param   int     $post_id    The ID of the post being saved.
     */
    private function save_checkbox_meta_field( $meta_key, $post_id ) {
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
    }

    /**
     * Saves a non-checkbox meta field.
     * 
     * @since 1.0.25
     * 
     * @param   string  $meta_key   The key of the meta field.
     * @param   int     $post_id    The ID of the post being saved.
     */
    private function save_single_meta_field( $meta_key, $post_id ) {
        // For other types of fields, sanitize and save the value
        if ( isset( $_POST[$meta_key] ) ) {
            $field_value = sanitize_text_field( wp_unslash( $_POST[$meta_key] ) );
            update_post_meta( $post_id, $meta_key, $field_value );
        }
    }

    /**
     * Validates the meta submission before saving
     * 
     * @since 1.0.25
     * 
     * @param   int     $post_id    The ID of the post whose meta is being saved.
     * @return  bool    True if valid submission, false if not.
     */
    private function validate_meta_save( $post_id ) {
        // Exit if autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return false;
        }

        // Check if user has permission
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        // Verify nonce
        $category = isset( $_POST['buddyc_meta_category'] ) ? sanitize_text_field( wp_unslash( $_POST['buddyc_meta_category'] ) ) : '';
        $nonce_name = self::build_nonce_name( $this->post_type, $category );

        // Nonce not set
        if ( ! isset( $_POST[ $nonce_name ] ) ) {
            return false;
        }

        // Get nonce value from post                
        $nonce_value = sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) );
        $nonce_action = 'save_' . $this->post_type . '_meta';

        // Nonce not valid        
        if ( ! wp_verify_nonce( $nonce_value, $nonce_action ) ) {
            return false;
        }

        // Five by five
        return true;
    }
    
    /**
     * Registers the metaboxes.
     * 
     * @since 0.1.0
     */
    public function register_metaboxes() {
        // Make sure there is meta
        if ( ! empty( $this->meta ) && is_array( $this->meta ) ) {
            // Loop through meta categories
            foreach ( $this->meta as $category => $data ) {

                // Build data for the metabox
                $data = $this->build_metabox_data( $category, $data );

                // Add meta box for each category
                add_meta_box(
                    $data['metabox_id'],
                    $data['title'],
                    $data['callback'],
                    $data['screen'],
                    $data['context'],
                    $data['priority'],
                    $data['args']
                );
            }
        }
    }

    /**
     * Builds the configuration data required to register a single metabox.
     *
     * @since 1.0.25
     *
     * @param string $category The metabox category name.
     * @param array  $data     An associative array containing the metabox fields and settings.
     * 
     * @return array {
     *     An associative array containing the necessary arguments for registering the metabox.
     *
     *     @type string   $metabox_id  The unique ID of the metabox.
     *     @type string   $title       The title of the metabox.
     *     @type callable $callback    The callback function for rendering the metabox content.
     *     @type string   $screen      The post type where the metabox should appear.
     *     @type string   $context     The display context ('normal', 'side', etc.).
     *     @type string   $priority    The priority of the metabox.
     *     @type array    $args {
     *         Additional arguments passed to the callback.
     *
     *         @type string $post_type The post type the metabox belongs to.
     *         @type array  $data      The data used to build the metabox fields.
     *         @type string $category  The metabox category.
     *     }
     * }
     */
    private function build_metabox_data( $category, $data ) {
        return [
            'metabox_id'        => $this->post_type . '-' . $category . '_metabox',
            'title'             => $category,
            'callback'          => [$this, 'metabox_callback' ],
            'screen'            => $this->post_type, // Post type to display metabox on
            'context'           => 'normal', // Where to put it (normal = main column, side = sidebar, etc.)
            'priority'          => 'default', // Priority relative to other metaboxes
            'args'              => [ // Pass args to the callback
                'post_type' => $this->post_type,
                'data'      => $data,
                'category'  => $category,
            ]
        ];
    }
    
    /**
     * Generates a metabox group for the specified post.
     *
     * @since 0.1.0
     *
     * @param WP_Post $post The post object being edited.
     * @param array   $metabox {
     *     Arguments passed from add_meta_box().
     *
     *     @type string $category  The name of the metabox group.
     *     @type string $post_type The post type slug.
     *     @type array  $data {
     *         Contains metabox data for the post type.
     *
     *         @type array $tables {
     *             Information for each metabox table group.
     *
     *             @type string $description Optional. A description for the entire metabox table.
     *             @type array  $meta {
     *                 An associative array of fields for each metabox table, keyed by field key.
     *
     *                 @type string $label       The label for the field.
     *                 @type string $description Optional. A description of the field.
     *                 @type string $type        The field type. Accepts 'dropdown', 'checkboxes', 'input'.
     *                 @type array  $options     Optional. The available options for 'dropdown' or 'checkboxes' fields.
     *             }
     *         }
     *     }
     * }
     */
    public function metabox_callback( $post, $metabox ) {

        // Initialize
        $content = '';

        // Get data passed to callback
        $data = $metabox['args']['data'] ?? null;
        if ( ! $data ) return;

        // Get metabox group
        $category = $metabox['args']['category'];

        // Generate a nonce field for this meta box
        $nonce_name = self::build_nonce_name( $this->post_type, $category );
        $content .= wp_nonce_field( 'save_' . $this->post_type . '_meta', $nonce_name, $referer = true, $display = false );

        // Add hidden category field
        $content .= '<input type="hidden" name="buddyc_meta_category" value="' . esc_attr( $category ) . '">';
        
        // Get the metabox category description
        $content .= $data['description'] ?? '';
        
        // Check for Freelancer Mode
        $freelancer_check = self::freelancer_check( $data );

        // Return freelancer mode link if necessary
        if ( $freelancer_check ) {
            $content .= $freelancer_check;
            return $content;
        }

        // Build metabox tables
        $content .= $this->build_metabox_tables( $post, $data );
        
        // Escape and output content
        $allowed_html = $this->allowed_html();
        echo wp_kses( $content, $allowed_html );
    }

    /**
     * Builds all metabox tables for a post
     * 
     * @since 1.0.25
     * 
     * @param WP_Post $post     The post object being edited.
     * @param array   $metabox  Arguments passed from add_meta_box().
     */
    private function build_metabox_tables( $post, $data ) {
        // Initialize
        $content = '';

        // Make sure metabox tables exist
        if ( ! empty( $data['tables'] ) && is_array( $data['tables'] ) ) {

            // Get extra classes
            $classes = isset( $data['classes'] ) ? ' ' . $data['classes'] . ' ' : '';

            //  Loop through metabox tables
            foreach ( $data['tables'] as $title => $table_data ) {

                // Build single table
                $content .= $this->build_single_metabox_table( $post, $title, $classes, $table_data );
            }
        }
        return $content;
    }

    /**
     * Builds a single metabox table. 
     * 
     * @since 1.0.25
     * 
     * @param   WP_Post     $post       The post object being edited.
     * @param   string      $title      The title fo the metabox table. 
     * @param   string      $classes    Additional classes for the table.
     * @param   array       $table_data An array of data to build the table.
     * 
     * @return  string      The table html.
     */
    private function build_single_metabox_table( $post, $title, $classes, $table_data ) {
        // Open table
        $content = '<table class="widefat buddyc-postbox-table bp-postbox-table' . $classes . '">';

        // Table head
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
        
        // Output freelancer mode message
        if ( $freelancer_check ) {
            $content .= '<tr><td>';
            $content .= $freelancer_check;
            $content .= '</td></tr>';

        // Otherwise build meta fields
        } else {
            // Generate meta fields
            $meta_group = $this->meta_group( $post, $table_data ) ?? '';
            $content .= $meta_group;
        }
        
        // Close table
        $content .= '</tbody>';
        $content .= '</table>';

        // Return html
        return $content;
    }

    /**
     * Defines the allowed html for the metaboxes.
     * 
     * @since 1.0.21
     */
    private function allowed_html() {
        $allowed_html = buddyc_allowed_html_form();
        $allowed_html['a']['id'] = [];
        return $allowed_html;
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
    public function meta_group( $post, $data ) {
        
        // Initialize and open fieldset
        $content = '<fieldset>';

        $meta = $data['meta'] ?? [];
        
        // Loop through fields
        foreach ( $meta as $field_id => $field_data ) {
            // Add field
            $content .= $this->single_meta_field( $field_id, $field_data, $post->ID );
        }
    
        // Close fieldset
        $content .= '</fieldset>';
        
        // Return content
        return $content;
    }

    /**
     * Builds a single meta field.
     * 
     * @since 1.0.25
     * 
     * @param   string  $field_id       The ID of the field
     * @param   array   $field_data     An array of field data.
     * @param   string  $post_id        The ID of the post being edited.
     */
    private function single_meta_field( $field_id, $field_data, $post_id ) {

          // Initialize and open row
          $content = '<tr>';

          // Header
          $content .= '<th>';
          $content .= esc_html( $field_data['label'] ?? '' );
          $content .= '</th>';

          // Open cell
          $content .= '<td>';

          // Build the field by type
          $content .= $this->build_field_by_type( $field_id, $field_data, $post_id );
  
          // Close cell
          $content .= '</td>';

          // Close row
          $content .= '</tr>';

          return $content;
    }

    /**
     * Generates a field by type.
     *
     * @since 1.0.25
     *
     * @param   string  $field_id       The ID of the field
     * @param   array   $field_data     An array of field data.
     * @param   string  $post_id        The ID of the post being edited.
     */
    private function build_field_by_type( $field_id, $field_data, $post_id ) {
        $type = $field_data['type'] ?? 'text';
        $field_args = $this->build_field_args( $field_id, $field_data, $post_id );

        return match ( $type ) {
            'dropdown'     => $this->dropdown_field( $field_args ),
            'checkbox'     => $this->checkbox_field( $field_args ),
            'display_date' => $this->display_date( $field_args ),
            'button'       => $this->button( $field_id, $field_data ),
            default        => $this->input_field( $field_args ),
        };
    }

    /**
     * Outputs an array of arguments to build a single field.
     *
     * @since 1.0.25
     */
    private function build_field_args( $field_id, $field_data, $post_id ) {
        return [
            'field_id'    => $field_id,
            'field_type'  => $field_data['type'] ?? 'text',
            'field_value' => get_post_meta( $post_id, $field_id, true ),
            'description' => $field_data['description'] ?? '',
            'placeholder' => $field_data['placeholder'] ?? '',
            'options'     => $this->define_field_options( $field_data['options'] ?? null ),
            'post_id'     => $post_id,
            'default'     => $field_data['default'] ?? null,
            'freelancer'  => $field_data['freelancer'] ?? false,
        ];
    }

    /**
     * Builds the options for a single meta field.
     *
     * @since 1.0.25
     */
    private function define_field_options( $field_options ) {
        if ( is_array( $field_options ) ) {
            return $field_options;
        }
        return is_string( $field_options ) ? $this->options( $field_options ) : [];
    }

    /**
     * Defines reusable options arrays.
     *
     * @since 0.1.0
     *
     * @param string $option_key The key for the options array.
     * @return array Options based on the given key.
     */
    private function options( $option_key ) {
        // Check for post type
        if ( post_type_exists( $option_key ) ) {
            return buddyc_options( 'posts', ['post_type' => $option_key] );
        }

        // Check for taxonomy
        if ( taxonomy_exists( $option_key ) ) {
            return buddyc_options( 'taxonomy', ['taxonomy' => $option_key] );
        }

        return match ( $option_key ) {
            'team', 'client', 'affiliate', 'users' => buddyc_options( 'users', ['user_type' => $option_key] ),
            'projects' => buddyc_options( 'projects' ),
            'payment' => [
                'pending'  => __( 'Pending', 'buddyclients-free' ),
                'eligible' => __( 'Eligible', 'buddyclients-free' ),
                'paid'     => __( 'Paid', 'buddyclients-free' ),
            ],
            'operator' => [
                'x' => sprintf(
                    'x (%s)',
                    __( 'multiply', 'buddyclients-free' )
                ),
                '+' => sprintf(
                    '+ (%s)',
                    __( 'add', 'buddyclients-free' )
                ),
                '-' => sprintf(
                    '- (%s)',
                    __( 'subtract', 'buddyclients-free' )
                )
            ],
            'help_docs' => buddyc_options( 'posts', ['post_type' => buddyc_help_post_types()] ),
            default => [],
        };
    }
    
    /**
     * Generates an input meta field.
     *
     * @since 0.1.0
     */
    private function input_field( $args ) {
        $field_value = $args['field_value'] ?: ($args['default'] ?? '');

        return sprintf(
            '<input type="%s" class="buddyc-meta-field" name="%s" id="%s" placeholder="%s" value="%s" size="10">
            <div class="buddyc-meta-description">%s</div>',
            esc_attr( $args['field_type'] ),
            esc_attr( $args['field_id'] ),
            esc_attr( $args['field_id'] ),
            esc_attr( $args['placeholder'] ),
            esc_attr( $field_value ),
            $args['description']
        );
    }
    
    /**
     * Generates dropdown meta field.
     * 
     * @since 0.1.0
     * 
     * @param array $args Field arguments.
     * @return string HTML for the dropdown field.
     */
    private function dropdown_field( $args ) {
        $field_id     = esc_attr( $args['field_id'] );
        $placeholder  = esc_html( $args['placeholder'] );
        $description  = $args['description'] ?? '';
        $field_value  = $args['field_value'] ?? '';
        $default      = $args['default'] ?? null;
        $match_found  = false;

        // Start select element
        $field  = sprintf(
            '<select class="buddyc-meta-input buddyc-meta-field" id="%1$s" name="%1$s">', 
            $field_id
        );
        $field .= sprintf('<option value="">%s</option>', $placeholder); // Empty option
        
        // Loop through options
        foreach ( $args['options'] as $option_value => $option_label ) {
            // Skip current post
            if ( $args['post_id'] === $option_value ) {
                continue;
            }

            // Determine selection
            $selected = $field_value ? selected( $option_value, $field_value, false ) : ( $option_value == $default ? 'selected' : '' );
            if ( $selected ) {
                $match_found = true;
            }

            // Append option
            $field .= sprintf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $option_value ),
                $selected,
                esc_html( $option_label )
            );
        }

        // Close select and add description
        $field .= '</select>';
        $field .= sprintf('<div class="buddyc-meta-description">%s</div>', $description);

        // Display info if selected option is unavailable
        if ( ! $match_found && $field_value !== '' ) {
            $title   = get_the_title( $field_value ) ?: '';
            $name    = bp_core_get_user_displayname( $field_value ) ?: '';
            $display = $title ?: $name ?: $field_value;

            $field .= sprintf(
                '<p class="buddyc-meta-unavailable">%s</p>',
                sprintf(
                    /* translators: %s: the name of the unavailable field */
                    __('Unavailable: %s', 'buddyclients-free'),
                    esc_html( $display )
                )
            );
        }

        return $field;
    }
    
    /**
     * Generates a checkbox meta field.
     * 
     * @since 0.1.0
     * 
     * @param array $args Field arguments.
     * @return string HTML for the checkbox field.
     */
    private function checkbox_field( $args ) {
        $field_id    = esc_attr( $args['field_id'] );
        $description = $args['description'] ?? '';
        $default     = $args['default'] ?? [];
        $field_value = ! empty( $args['field_value'] ) ? (array) $args['field_value'] : $default;

        $field  = sprintf( '<div class="buddyc-meta-description">%s</div>', $description );
        $field .= sprintf( '<div id="%s" class="buddyc-meta-checkbox-group">', $field_id );

        foreach ( $args['options'] as $option_value => $option_label ) {
            if ( $args['post_id'] === $option_value ) {
                continue;
            }

            $input_id   = sprintf( '%s-%s', $field_id, esc_attr( $option_value ) );
            $checked    = checked( in_array( $option_value, $field_value ) );
            $field     .= sprintf(
                '<div class="buddyc-meta-checkbox">
                    <input type="checkbox" id="%1$s" name="%2$s[]" value="%3$s" %4$s>
                    <label for="%1$s">%5$s</label>
                </div>',
                $input_id,  // Checkbox ID
                $field_id,  // Field name
                esc_attr( $option_value ),  // Option value
                $checked,  // Checked attribute
                esc_html( $option_label )  // Option label
            );
        }

        $field .= '</div>';
        return $field;
    }
    
    /**
     * Displays the value as a formatted date.
     * 
     * @since 0.1.0
     * 
     * @param array $args Field arguments.
     * @return string Formatted date or placeholder.
     */
    private function display_date( $args ) {
        // Ensure field_value is set and not empty
        if ( empty( $args['field_value'] ) ) {
            return '';
        }

        // Convert to timestamp
        $timestamp = strtotime( $args['field_value'] );

        // Validate timestamp
        if ( $timestamp === false ) {
            return '';
        }

        // Return formatted date
        return gmdate( 'F j, Y, h:i A', $timestamp );
    }
    
    /**
     * Displays a button.
     * 
     * @since 0.1.0
     * 
     * @param string $field_id The field ID.
     * @param array $field_data Field data from meta manager.
     * @return string HTML button element.
     */
    private function button( $field_id, $field_data ) {
        // Set default values
        $label = ! empty( $field_data['value'] ) ? esc_html( $field_data['value'] ) : __( 'Click Here', 'buddyclients-free' );
        $href = ! empty( $field_data['href'] ) ? esc_url( $field_data['href'] ) : '#';
        $class = ! empty( $field_data['class'] ) ? esc_attr( $field_data['class'] ) : 'button-secondary';

        return sprintf(
            '<a id="%s" class="%s" href="%s">%s</a>',
            esc_attr($field_id),
            esc_attr($class),
            $href,
            $label
        );
    }
}
