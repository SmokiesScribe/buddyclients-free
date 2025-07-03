<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\Settings;

/**
 * Creates a single admin settings page.
 * 
 * @since 0.1.0
 */
class SettingsPage {
    
    /**
     * Setting data.
     * 
     * @var array Associative array of settings data.
     */
     private $data;
     
    /**
     * Key used to build slug.
     * 
     * @var string
     */
     private $key;
     
    /**
     * Name of settings group.
     * 
     * @var string
     */
     private $name;
    
    /**
     * Slug.
     * 
     * @var string.
     */
     private $slug;
     
    /**
     * Parent menu slug.
     * 
     * @var string.
     */
     private $parent_menu;
     
    /**
     * Page title.
     * 
     * @var string.
     */
     private $title;
     
    /**
     * Menu order.
     * 
     * @var int|null
     */
     private $menu_order;
     
    /**
     * Capability.
     * 
     * @var string Optional. Default 'manage_options'.
     */
     private $cap;

    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $args ) {

        // Extract data
        $this->key = $args['key'] ?? '';
        $this->title = $args['title'] ?? 'Settings';
        $this->name = $this->build_settings_name();
        
        // Get settings data
        $settings = new Settings( $args['key'] );
        $this->data = $settings->get_data();
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Builds settings name.
     * 
     * @since 0.1.0
     */
    private function build_settings_name() {
        $key = str_replace('-', '_', $this->key );
        return 'buddyc_' . $key . '_settings';
    }
    
    /**
     * Registers hooks.
     * 
     * @since 0.1.0
     */
    private function define_hooks() {
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    /**
     * Registers settings.
     * 
     * @since 0.1.0
     * @since 1.0.17 Use sanitization callback.
     */
    public function register_settings() {
        register_setting( $this->name . '_group', $this->name, [
            'sanitize_callback' => [ $this, 'sanitize_settings' ]
        ]);
    
        add_settings_section( $this->name . '_section', '', [ $this, 'section_callback' ], $this->name );
    } 

    /**
     * Sanitizes settings.
     *
     * Ensures that empty checkboxes are saved as an empty array.
     * 
     * @since 1.0.17
     *
     * @param array $input The raw settings input.
     * @return array Sanitized settings.
     */
    public function sanitize_settings( $input ) {

        // Loop through settings sections
        foreach ( $this->data as $section_key => $section_data ) {

            // Loop through fields
            foreach ( $section_data['fields'] as $field_id => $field_data ) {

                // Set checkbox fields to empty array if no value submitted
                if ( $field_data['type'] === 'checkboxes' ) {
                    if ( ! isset( $input[ $field_id ] ) ) {
                        $input[ $field_id ] = [];
                    }
                }
            }
        }
        return $input;
    }

    /**
     * Renders the settings page.
     * 
     * @since 0.1.0
     */
    public function render_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( $this->title ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( $this->name . '_group' ); ?>
                <?php do_settings_sections( $this->name ); ?>
                <?php submit_button( __('Save Settings', 'buddyclients-lite') ); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Renders the settings content.
     * 
     * @since 0.1.0
     * 
     * @return  void    Echoes the content.
     */
     public function section_callback() {

        /**
         * Fires at the top of every BuddyClients settings page.
         *
         * @since 0.1.0
         *
         * @param string $settings_key  The key of the settings group.
         */
        do_action('buddyc_before_settings', $this->key);
        
        // Make sure we have an array of settings data
        if ( is_array( $this->data )) {

            // Loop through settings data
            foreach ( $this->data as $section_key => $section_data ) {
                // Output the group
                $section_group = $this->section_group( $section_key, $section_data ) ?? '';
                $allowed_html = self::allowed_html();
                echo wp_kses( $section_group, $allowed_html );
            }
        // No settings data available
        } else {
            echo esc_html__( 'Not available.', 'buddyclients-lite' );
        }
    }
    
    /**
     * Displays section group.
     * 
     * @since 0.1.0
     * 
     * @param   string  $section_key    The key for the settings group section. 
     * @param   array   $section_data   The array of data for the settings group section.
     * @return  string  The section group content.
     */
     public function section_group( string $section_key, $section_data ) {
        return sprintf(
            '<div class="buddyclients-settings-section">
                <div class="buddyclients-settings-section-title-wrap">
                    <h2 class="buddyclients-settings-section-title">%1$s</h2>
                    <p class="description">%2$s</p>
                    <hr class="buddyclients-settings-section-title-divider">
                </div>  
                %3$s
            </div>',
            esc_html( $section_data['title'] ?? '' ),
            $section_data['description'] ?? '',
            $this->section_group_field( $section_key, $section_data )
        );
    }

    /**
     * Displays individual field.
     * 
     * @since 0.1.0
     * 
     * @param   string  $section_key    The key for the settings group field. 
     * @param   array   $section_data   The array of data for the settings group field.
     * @return  string  The section field content.
     */
    public function section_group_field( $section_key, $section_data ) {

        // Initialize
        $content = '';

        if ( isset( $section_data['fields'] ) || is_array( $section_data['fields'] ) ) {

            // Loop through section fields
            foreach ( $section_data['fields'] as $field_id => $field_data ) {
                
                // Define field info
                $type = $field_data['type'] ?? 'text';
                
                // Get current field value
                $value = buddyc_get_setting( $this->key, $field_id );

                // Build field name from settings group name and field id
                $field_name = sprintf( '%1$s[%2$s]', $this->name, $field_id );
                
                // Define output by field type
                $content .= match ( $type ) {
                    'display'       => $this->display( $type, $field_id, $field_name, $field_data, $value ),
                    'checkboxes'    => $this->checkbox_field( $type, $field_id, $field_name, $field_data, $value ),
                    'checkbox_table'=> $this->checkbox_table( $type, $field_id, $field_name, $field_data, $value ),
                    'dropdown', 'stripe_dropdown'      => $this->select_field( $type, $field_id, $field_name, $field_data, $value ),
                    'text', 'number', 'date', 'email', 'stripe_input'   => $this->input_field( $type, $field_id, $field_name, $field_data, $value ),
                    'hidden'        => $this->hidden_field( $type, $field_id, $field_name, $field_data, $value ),
                    'color'         => $this->color_field( $field_id, $field_name, $field_data, $value ),
                    'page'          => $this->select_field( $type, $field_id, $field_name, $field_data, $value ),
                    'legal'         => $this->legal_field( $type, $field_id, $field_name, $field_data, $value ),
                    'copy'          => $this->copy_field( $type, $field_id, $field_name, $field_data ),
                    default         => $this->input_field( 'text', $field_id, $field_name, $field_data, $value )
                };
            }
        }
        return $content;
    }
    
    /**
     * Displays content directly.
     *
     * @since 0.1.0
     * 
     * @param   string  $type       The type of field to output. 
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The field html.
     */
    public function display( $type, $field_id, $field_name, $field_data, $value ) {
        return sprintf(
            '<div class="buddyclients-admin-field">
                <label for="%1$s">
                    %2$s
                </label>
                <div class="buddyclients-admin-field-input-wrap">
                    %3$s
                    <p class="description">%4$s</p>
                </div>
            </div>',
            esc_attr( $field_name ),
            esc_html( $field_data['label'] ),
            $field_data['content'] ?? '',
            $field_data['description'] ?? ''
        );
    }
    
    /**
     * Renders a checkbox field.
     *
     * @since 0.1.0
     * 
     * @param   string  $type       The type of field to output. 
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The field html.
     */
    public function checkbox_field( $type, $field_id, $field_name, $field_data, $value ) {
        return sprintf(
            '<div class="buddyclients-admin-field">
                <label for="%1$s">
                    %2$s
                </label>
                <div class="buddyclients-admin-field-input-wrap">
                    %3$s
                    <p class="description">%4$s</p>
                </div>
            </div>',
            esc_attr( $field_name ),
            esc_html( $field_data['label'] ),
            $this->checkbox_options( $field_name, $field_data, $value ),
            $field_data['description'] ?? ''
        );
    }

    /**
     * Renders all options for a checkbox field.
     *
     * @since 1.0.27
     * 
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The option html.
     */
    private function checkbox_options( $field_name, $field_data, $value ) {
        if ( empty( $field_data['options'] ) || ! is_array( $field_data['options'] ) ) return;

        // Append array indicator to field name
        $field_name = $field_name . '[]';

        // Initialize
        $content = '';

        // Loop through options
        foreach ( $field_data['options'] as $option_key => $option_label ) {

            // Check if current value
            $checked = is_array( $value ) && in_array( $option_key, $value ) ? 'checked' : '';

            // Add option html to content
            $content .= sprintf(
                '<label>
                    <input type="checkbox" name="%1$s" 
                        value="%2$s" %3$s>
                        %4$s
                </label><br>',
                esc_attr( $field_name ),
                esc_attr( $option_key ),
                esc_attr( $checked ),
                wp_kses_post( $option_label )
            );
        }
        return $content;
    }

    /**
     * Renders a checkbox field as a table.
     *
     * @since 0.1.0
     * 
     * @param   string  $type       The type of field to output. 
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The field html.
     */
    public function checkbox_table( $type, $field_id, $field_name, $field_data, $value ) {
        // Append array indicator to field name
        $field_name = $field_name . '[]';
    
        return sprintf(
            '<div class="buddyclients-admin-field">
                <button type="button" class="buddyc-select-all" data-target="%1$s">Select All</button>
                <table class="buddyc-checkbox-table">
                    <tbody>
                        %2$s
                    </tbody>
                </table>
            </div>',
            esc_attr( $field_name ), // Target field for select all
            $this->checkbox_table_rows( $field_id, $field_name, $field_data, $value )
        );
    }    

    /**
     * Generates the table rows for a checkbox table field.
     *
     * @since 1.0.27
     * 
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The rows html.
     */
    private function checkbox_table_rows( $field_id, $field_name, $field_data, $value ) {
        if ( empty( $field_data['options'] ) || ! is_array( $field_data['options'] ) ) return '';

        $content = '';

        foreach ( $field_data['options'] as $option_key => $option_label ) {
            $required = in_array( $option_key, ( $field_data['required_options'] ?? [] ) );
            $checked  = is_array( $value ) && in_array( $option_key, $value ) || $required ? 'checked' : '';
            $checkbox_id = $field_id . '_' . $option_key;

            $content .= sprintf(
                '<tr class="%1$s">
                    <td class="buddyc-checkbox-column">
                        <input type="checkbox" id="%2$s" name="%3$s" value="%4$s" %5$s>
                    </td>
                    <td>
                        <label for="%2$s">%6$s</label>
                    </td>
                    <td>
                        <p class="description">
                            %7$s
                            %8$s
                        </p>
                    </td>
                </tr>',
                esc_attr( trim( ($checked ? 'checked ' : '') . ($required ? 'required' : '') ) ),
                esc_attr( $checkbox_id ),
                esc_attr( $field_name ),
                esc_attr( $option_key ),
                esc_attr( $checked ),
                esc_html( $option_label ) ?? '',
                $required ? esc_html__( 'Required. ', 'buddyclients-lite' ) : '',
                isset( $field_data['descriptions'][$option_key] ) ? wp_kses_post( $field_data['descriptions'][$option_key] ) : ''
            );
        }

        return $content;
    }
    
    /**
     * Renders a dropdown field.
     *
     * @since 0.1.0
     *
     * @param   string  $type       The type of field to output. 
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The field html.
     */
    public function select_field( $type, $field_id, $field_name, $field_data, $value ) {
        $append = match ( $type ) {
            'stripe_dropdown'   => $this->validate_stripe_icon( 'mode' ),
            'page'              => self::page_button( $field_id, $field_data, $value ),
            default             => ''            
        };

        return sprintf(
            '<div class="buddyclients-admin-field">
                <label for="%1$s">%2$s</label>
                <div class="buddyclients-admin-field-input-wrap">
                    <select name="%1$s">
                        %3$s
                    </select>
                    %4$s
                    <p class="description">%5$s</p>
                </div>
            </div>',
            esc_attr( $field_name ),
            esc_html( $field_data['label'] ),
            $this->get_select_options( $field_data['options'], $value ),
            $append,
            wp_kses_post( $field_data['description'] )
        );
    }

    /**
     * Generates the option elements for a select field.
     *
     * @param   array   $options The array of options.
     * @param   mixed   $value   The selected value.
     * @return  string  The HTML options.
     */
    private function get_select_options( $options, $value ) {
        $output = '';
        foreach ( $options as $option_key => $option_label ) {
            $selected = ( $value == $option_key ) ? ' selected' : '';
            $output .= sprintf(
                '<option value="%1$s"%2$s>%3$s</option>',
                esc_attr( $option_key ),
                esc_attr( $selected ),
                esc_html( $option_label )
            );
        }
        return $output;
    }
    
    /**
     * Renders an input field.
     *
     * @since 0.1.0
     *
     * @param   string  $type       The type of the field.
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The field HTML.
     */
    public function input_field( $type, $field_id, $field_name, $field_data, $value ) {
        $icon = $type === 'stripe_input' ? $this->validate_stripe_icon( 'field', $field_data, $value ) : '';
        return sprintf(
            '<div class="buddyclients-admin-field">
                <label for="%1$s">%2$s</label>
                <div class="buddyclients-admin-field-input-wrap">
                    <input type="%3$s" name="%1$s" value="%4$s" />
                    %5$s
                    <p class="description">%6$s</p>
                </div>
            </div>',
            esc_attr( $field_name ),
            esc_html( $field_data['label'] ?? '' ),
            esc_attr( $type ),
            esc_attr( $value ),
            $icon,
            wp_kses_post( $field_data['description']?? '' )
        );
    }

    /**
     * Checks for Stripe validation and outputs an icon.
     * 
     * @since 1.0.15
     * 
     * @param   string  $type           The type of validation.
     *                                  Accepts 'mode' and 'field'.
     * @param   array   $field_data     The data for field validation.
     * @param   string  $value          Optional. The value of the key to check.
     * 
     * @return  string  Icon html or empty string.
     */
    private function validate_stripe_icon( $type, $field_data = null, $value = null ) {
        // Initialize
        $icon = '';

        // Check for validate url param
        $param_manager = buddyc_param_manager();
        $validate_param = $param_manager->get( 'validate' );

        // Make sure we're validating
        if ( $validate_param !== 'stripe' ) {
            return $icon;
        }

        // Validate full stripe mode
        if ( $type === 'mode' ) {
            $icon = buddyc_stripe_mode_valid_icon();
        }

        // Validate field
        if ( $type === 'field' && is_array( $field_data ) && isset( $field_data['stripe_key'] ) ) {
            $icon = buddyc_stripe_valid_icon( $field_data['stripe_key'], $field_data['stripe_mode'], $value );
        }
        return $icon;
    }

    /**
     * Renders a hidden field.
     *
     * @since 0.1.0
     *
     * @param   string  $type       The type of the field.
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The field HTML.
     */
    public function hidden_field( $type, $field_id, $field_name, $field_data, $value ) {
        return sprintf(
            '<input type="hidden" name="%1$s" value=" %2$s " />',
            esc_attr( $field_name ),
            esc_attr( $value )
        );
    }

    /**
     * Renders a color input field.
     *
     * @since 0.1.0
     *
     * @param   string  $type       The type of the field.
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The field HTML.
     */
    public function color_field( $field_id, $field_name, $field_data, $value ) {
        return sprintf(
            '<div class="buddyclients-admin-field">
                <label for="%1$s">%2$s</label>
                <div class="buddyclients-admin-field-input-wrap">
                    <input type="color" name="%1$s" value="%3$s" class="color-field" />
                    <p class="description">%4$s</p>
                </div>
            </div>',
            esc_attr( $field_name ),
            esc_html( $field_data['label'] ?? '' ),
            esc_attr( $value ),
            wp_kses_post( $field_data['description'] )
        );
    }

    /**
     * Renders the view and edit buttons  page dropdown field.
     * 
     * @since 0.1.0
     * 
     * @param   string  $field_id   The ID of the field.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The page buttons HTML.
     */
    public function page_button( $field_id, $field_data, $value ) {
        
        // Check if page is selected and published
        if ( $value && get_post_status( $value ) === 'publish') {
            
            // Get page permalink
            $selected_page_permalink = ! empty( $value ) ? get_permalink( $value ) : '#';
            
            // Create view page button
            $button = sprintf(
                '<a href="%1$s" target="_blank"><button type="button" class="button button-secondary">%2$s</button></a>',
                esc_url( $selected_page_permalink ),
                esc_html__( 'View Page', 'buddyclients-lite' )
            );

        // Create button
        } else {

            // Define redirect option based on post content
            $is_shortcode = self::is_shortcode( $field_data['post_content'] ?? '');
            $redirect = $is_shortcode ? null : 'edit';

            $atts = [
                'page_key'      => $field_id,
                'settings_key'  => 'pages',
                'post_title'    => $field_data['post_title'] ?? '',
                'post_content'  => $field_data['post_content'] ?? '',
                'post_type'     => 'page',
                'post_status'   => 'publish',
                'redirect'      => $redirect
            ];            

            // Build data atts string
            $data_atts = '';
            foreach ( $atts as $key => $value ) {
                $data_atts .= sprintf(
                    ' data-%s="%s"',
                    str_replace( '_', '-', esc_attr( $key ) ),
                    esc_attr( $value )
                );
            }
            
            // Show create button
            $button = sprintf(
                '<button type="button" class="button button-secondary buddyc-create-page-btn"%1$s>%2$s</button>',
                $data_atts,
                __( 'Create Page', 'buddyclients-lite' )
            );
        }

        // Return the button html
        return $button;
    }

    /**
     * Checks whether a string contains only a shortcode.
     * 
     * @since 1.0.27
     * 
     * @param   string  $content    The string to check.
     * @return  bool    True if the string is a shortcode only, false if it contains other content.
     */
    private static function is_shortcode( $content ) {
        if ( ! is_string( $content ) ) {
            return false;
        }    
        // Trim whitespace and check if the entire content is a single shortcode
        return (bool) preg_match( '/^\s*\[[a-zA-Z0-9_]+[^\]]*\]\s*$/', trim( $content ) );
    }
    
    /**
     * Displays copy-to-clipboard text.
     * 
     * @since 0.1.0
     * 
     * @param   string  $type       The type of the field.
     * @param   string  $field_id   The ID of the field.
     * @param   string  $field_name The name of the field as array access.
     * @param   array   $field_data The array of data used to build the field.
     * @param   mixed   $value      The current value of the field.
     * @return  string  The field HTML.
     */
    public function copy_field( $type, $field_id,  $field_name, $field_data ) {
        return sprintf(
            '<div class="buddyclients-admin-field">
                <label for="%1$s">%2$s</label>
                <div class="buddyclients-admin-field-input-wrap">
                    %3$s
                    <p class="description">%4$s</p>
                </div>
            </div>',
            $field_name,
            $field_data['label'] ?? '',
            buddyc_copy_to_clipboard( $field_data['content'] ?? '', true ),
            $field_data['description'] ?? ''
        );
    }

    /**
     * Defines the allowed html.
     * 
     * @since 1.0.27
     */
    private static function allowed_html() {
        return [
            'a' => [
                'href'   => true,
                'title'  => true,
                'target' => true,
                'rel'    => true,
            ],
            'b' => [],
            'strong' => [],
            'i' => ['class' => true],
            'em' => [],
            'u' => [],
            'span' => [
                'class' => true,
                'style' => true,
            ],
            'br' => [],
            'p' => [
                'id'    => true,
                'class' => true,
                'style' => true,
            ],
            'div' => [
                'class' => true,
                'style' => true,
                'id'    => true,
            ],
            'h2' => [
                'class' => true,
            ],
            'hr' => [
                'class' => true,
            ],
            'label' => [
                'for' => true,
            ],
            'input' => [
                'type'        => true,
                'name'        => true,
                'value'       => true,
                'id'          => true,
                'class'       => true,
                'placeholder' => true,
                'checked'     => true,
                'disabled'    => true,
                'size'        => true,
                'readonly'    => true
            ],
            'select' => [
                'name'  => true,
                'id'    => true,
                'class' => true,
            ],
            'option' => [
                'value'    => true,
                'selected' => true,
            ],
            'textarea' => [
                'name'  => true,
                'id'    => true,
                'class' => true,
                'rows'  => true,
                'cols'  => true,
            ],
            'button' => [
                'type'          => true,
                'class'         => true,
                'id'            => true,
                'data-page-key'      => true,
                'data-settings-key'  => true,
                'data-post-title'    => true,
                'data-post-content'  => true,
                'data-post-type'     => true,
                'data-post-status'   => true,
                'data-redirect'      => true,
                'data-target'        => true
            ],
            'fieldset' => [
                'class' => true,
                'id'    => true,
            ],
            'legend' => [],
            'table' => [
                'class' => true,
            ],
            'thead' => [],
            'tbody' => [],
            'tfoot' => [],
            'tr' => [
                'class' => true,
            ],
            'th' => [
                'scope'   => true,
                'colspan' => true,
                'rowspan' => true,
            ],
            'td' => [
                'class'   => true,
                'colspan' => true,
                'rowspan' => true,
            ],
            'hr' => [
                'class' => true,
            ],
        ];
    }      
}