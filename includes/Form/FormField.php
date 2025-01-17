<?php
namespace BuddyClients\Includes\Form;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Form Field.
 * 
 * Generate individual form fields.
 *
 * @since 0.1.0
 * 
 * @todo Refactor into separate classes.
 */
class FormField {
    
    /**
     * The field key.
     * 
     * @var string
     */
    private $key;
    
    /**
     * The field ID.
     * 
     * Optional. Defaults to field key.
     * 
     * @var string
     */
    private $id;
    
    /**
     * The field attributes string.
     * 
     * @var string
     */
    private $field_atts_string;
    
    /**
     * Field type.
     * 
     * Accepts 'upload', 'text', 'hidden', 'number', 'date', 'dropdown', 'checkbox'
     * 
     * @var string
     */
    private $type;
    
    /**
     * The field value.
     * 
     * @var string
     */
    private $value;
    
    /**
     * Field label.
     * 
     * @var string
     */
    private $label;
    
    /**
     * Field description.
     * 
     * Optional. Description to display below field.
     * 
     * @var string
     */
    private $description;
    
    /**
     * Field placeholder.
     * 
     * Optional. Used only for tinyMCE.
     * 
     * @var string
     */
    private $placeholder;
    
    /**
     * Field options.
     * 
     * Associative array of options for select and checkbox fields.
     * 
     * @var array
     */
    private $options;
    
    /**
     * Field classes. Optional.
     * 
     * Classes separated by spaces.
     * 
     * @var string
     */
    private $field_classes;
    
    /**
     * The callable for direct output.
     * 
     * @var callable
     */
    private $callback;
    
    /**
     * Style.
     * 
     * @var string
     */
    private $style;
    
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param array $args
     */
    public function __construct( array $args ) {
        $this->key = $args['key'] ?? null;
        
        // Make sure we have a key
        if ( ! $this->key ) {
            return;
        }
        
        // Extract variables from args
        $this->extract_var( $args );
        
        // Build field atts string
        $this->field_atts_string = $this->attributes_string( $args );
    }
    
    /**
     * Extracts variables from args.
     * 
     * @since 0.1.0
     */
    private function extract_var( $args ) {
        $this->type             = $args['type'];
        $this->style            = $args['style'] ?? '';
        $this->label            = $args['label'] ?? '';
        $this->options          = $args['options'] ?? array();
        $this->description      = $args['description'] ?? '';
        $this->field_classes    = $this->format_classes( $args ) ?? '';
        $this->callback         = $args['callback'] ?? null;
        $this->id               = $args['id'] ?? $this->key;
        $this->value            = $args['value'] ?? null;
        $this->placeholder      = $args['placeholder'] ?? null;
        $this->required         = $args['required'] ?? null;
    }
    
    /**
     * Builds the field attributes string.
     * 
     * @since 1.0.0
     * 
     * @param   array   $args
     * @param   bool    $option     Optional. Whether it is an att string for an option.
     *                              Defaults to false, indicating a field atts string.
     */
    private function attributes_string( $args, $option = false ) {
        // Initialize
        $atts_array = [];
        
         // Missing args or key
         if ( empty( $args ) || ! isset( $args['key'] ) ) {
             return '';
         }
        
        // Build array of keyed attributes
        $keyed_atts = [
            'name'          => $this->build_name( $args, $option ),
            'id'            => $args['id'] ?? $args['key'],
            'value'         => isset( $args['value'] ) ? $args['value'] : null,
            'placeholder'   => $args['placeholder'] ?? null,
            'min'           => $args['minimum'] ?? null,
            'rows'          => $args['rows'] ?? null,
            'pattern'       => $args['pattern'] ?? null,
            'title'         => $args['title'] ?? null,
            'accept'        => $this->format_file_types( $args ),
            'class'         => $this->format_classes( $args ),
        ];
        
        // Build array of non-keyed attributes
        $single_atts = [
            'multiple'      => $args['multiple_files'] ?? null,
            'disabled'      => $args['disabled'] ?? null,
            'required'      => $args['required'] ?? null,
            'checked'       => $args['checked'] ?? null
        ];
        
        // Loop through keyed atts
        foreach ( $keyed_atts as $key => $value ) {
            if ( $value !== null ) {
                $atts_array[] = $key . '="' . esc_html( $value ) . '"';
            }
        }
        
        // Loop through single atts
        foreach ( $single_atts as $value => $bool ) {
            if ( $bool ) {
                $atts_array[] = esc_html( $value );
            }
        }
        
        // Add formatted data atts
        if ( isset( $args['data_atts'] ) ) {
            $atts_array[] = $this->format_data_atts( $args['data_atts'] );
        }
        
        // Implode array
        return ! empty( $atts_array ) ? implode( ' ', $atts_array ) : '';
    }
    
    /**
     * Builds the field or option name.
     * 
     * @since 1.0.0
     * 
     * @param   array   $args
     * @param   bool    $option     Optional. Whether it is an att string for an option.
     *                              Defaults to false, indicating a field atts string.
     */
    private function build_name( $args, $option ) {
        // Field name and checkbox type
        if ( ! $option && $this->type === 'checkbox' ) {
            
            // Append '[]' to key
            return $this->key . '[]';
            
        // Otherwise return key
        } else {
            return $args['key'];
        }
    }
    
    /**
     * Formats file types.
     * 
     * @since 1.0.0
     * 
     * @param   array   $args   The array of args passed to the constructor.
     */
    private function format_file_types( $args ) {
        if ( isset( $args['file_types'] ) ) {
            // Array provided
            if ( is_array($args['file_types'] ) ) {
                // Implode and return
                return implode( ',', $args['file_types'] );
                
            // String provided
            } else {
                // Return string
                return $args['file_types'];
            }
        }
    }
    
    /**
     * Formats data attributes.
     * 
     * @since 0.1.0
     * 
     * @param array|string  $data_atts  String or associative array of data attributes.
     */
     private function format_data_atts( $data_atts ) {
        // Initialize
        $formatted_atts = '';
        
        // Check if it's a string
        if ( is_string( $data_atts ) ) {
            return $data_atts;
        }
        
        // Loop through key value pairs
        foreach ( $data_atts as $key => $value ) {
            // Add to string
            $formatted_atts .= 'data-' . $key . '="' . $value . '" ';
        }
        return $formatted_atts;
     }
     
    /**
     * Formats classes.
     * 
     * @since 0.1.0
     * 
     * @param array  $args  Array of arguments.
     */
     private function format_classes( $args ) {
         // Option classes or field classes
         $classes = $args['classes'] ?? $args['field_classes'] ?? null;
         
         // Exit if no classes defined
         if ( ! $classes ) {
             return;
         }
        
        // Check if it's a string
        if ( is_string( $classes ) ) {
            return $classes;
            
        // Implode array
        } else {
            return implode( ' ', $classes );
        }
     }
    
    /**
     * Builds field based on type.
     *
     * @since 0.1.0
     */
    public function build() {
        
        switch ( $this->type ) {
            case 'direct':
                $field = $this->output_directly();
                break;
            case 'text':
            case 'number':
            case 'date':
            case 'email':
            case 'url':
                $field = $this->input_field();
                break;
            case 'textarea':
            case 'text_area':
                $field = $this->textarea_field();
                break;
            case 'editor':
                $field = $this->tinymce_field();
                break;
            case 'upload':
                $field = $this->upload_field();
                break;
            case 'signature':
                $field = $this->signature_field();
                break;
            case 'dropdown':
                $field = $this->dropdown_field();
                break;
            case 'checkbox':
                $field = $this->checkbox_field();
                break;
            case 'hidden':
                $field = $this->hidden_field();
                break;
            case 'verify':
                $field = $this->verification_field();
                break;
            case 'display':
                $field = $this->display_field();
                break;
            case 'nonce':
                $field = $this->nonce_field();
                break;
            case 'submit':
                $field = $this->submit_field();
                break;
            default:
                $field = $this->input_field();
                break;
                
        }
        return $field;
    }
    
    /**
     * Output content from provided callback.
     * 
     * @since 0.3.4
     */
    private function output_directly() {
        if ( is_callable( $this->callback ) ) {
            return call_user_func( $this->callback );
        }
    }
    
    /**
     * Input field.
     * 
     * @since 0.1.0
     */
    private function input_field() {
        
        $field = '<div class="form-group form-group field_type_textbox buddyc-form-group-container" style="' . $this->style .'">';
        $field .=   '<legend>' . $this->label . '</legend>';
        $field .=   '<p class="description">' . $this->description . '</p>';
        $field .=   '<input type="' . $this->type . '" ' . $this->field_atts_string . '>';
        $field .= '</div>';

        return $field;
    }
    
    /**
     * Textarea field.
     * 
     * @since 0.1.0
     */
    private function textarea_field() {
        
        $field = '<div class="form-group form-group field_type_textarea buddyc-form-group-container" style="' . $this->style .'">';
        $field .=   '<legend>' . $this->label . '</legend>';
        $field .=   '<p class="description">' . $this->description . '</p>';
        $field .=   '<textarea ' . $this->field_atts_string . '>' . $this->value . '</textarea>';
        $field .= '</div>';
    
        return $field;
    }
    
    /**
     * TinyMCE editor field.
     * 
     * @since 0.1.0
     */
    private function tinymce_field() {
        
        $field = '<div class="form-group form-group field_type_tinymce buddyc-form-group-container" style="' . $this->style .'">';
        $field .=   '<legend>' . $this->label . '</legend>';
        $field .=   '<p class="description">' . $this->description . '</p>';
        
        $editor_settings = array(
            'textarea_name' => $this->key, // Name of the textarea field
            'media_buttons' => false, // Show media upload buttons
            'textarea_rows' => $this->rows ?? 5, // Number of rows
            'teeny' => true, // Use the "teeny" mode, which is a simplified version of the editor
            'quicktags' => false,
            'tinymce' => array(
                'toolbar1' => __( 'bold italic underline | undo redo', 'buddyclients-free' ), // Customize the buttons in the first row
                'toolbar2' => '', // Customize the buttons in the second row (empty for none)
            ),
        );
        ob_start(); // Start output buffering
        wp_editor( $this->placeholder, $this->key, $editor_settings );
        $editor_content = ob_get_clean(); // Get the buffered output and clean the buffer
        
        $field .= $editor_content;
        
        $field .= '</div>';
    
        return $field;
    }


    
    /**
     * Hidden field.
     * 
     * @since 0.1.0
     */
    private function hidden_field() {
        $field = '<input type="hidden" ' . $this->field_atts_string . '>';
        return $field;
    }
    
    /**
     * Dropdown field.
     * 
     * @since 0.1.0
     */
    private function dropdown_field() {
        // Start building field
        $field = '<div class="form-group form-group field_type_selectbox buddyc-form-group-container" style="' . $this->style .'">';
        $field .=   '<legend>' . $this->label . '</legend>';
        $field .=   '<p class="description">' . $this->description . '</p>';
        $field .=   '<select ' . $this->field_atts_string . '>';
    
        // Build options
        foreach ($this->options as $option_key => $option_data) {
            $option_args = [
                'key' => $option_key,
                'value' => isset( $option_data['value'] ) ? $option_data['value'] : null,
                'classes' => $option_data['classes'] ?? '',
                'disabled' => $option_data['disabled'] ?? '',
                'data_atts' => $option_data['data_atts'] ?? '',
            ];
    
            // Check if the current option matches the provided value
            $selected = ( $option_args['value'] == $this->value ) ? 'selected' : '';
    
            $field .=   '<option ' . $this->attributes_string( $option_args ) . ' ' . $selected . '>' . ($option_data['label'] ?? '') . '</option>';
        }
    
        // Close select and container 
        $field .=   '</select>';
        $field .= '</div>';
    
        return $field;
    }

    
    /**
     * Checkbox field.
     * 
     * @since 0.1.0
     */
    private function checkbox_field() {

        // Start building field
        $field = '<div class="form-group field_type_checkbox buddyc-form-group-container" style="' . esc_attr( $this->style ) .'">';
        $field .= '<fieldset>';
        $field .= '<legend>' . esc_html( $this->label ) . '</legend>';
        $field .= '<p class="description">' . $this->description . '</p>';
        $field .= '<div class="form-group input-options checkbox-options ' . esc_attr( $this->field_classes ) . '" id="' . esc_attr( $this->key ) . '" ' . $this->field_atts_string . '>';
    
        // Build checkbox options
        foreach ($this->options as $option_key => $option_data) {
            $checked = $this->checked_checkbox( $option_data['value'] );
            $args = [
                'key' => $option_key,
                'type' => 'checkbox',
                'value' => $option_data['value'],
                'classes' => [$option_data['classes'] ?? '', 'bs-styled-checkbox'],
                'disabled' => $option_data['disabled'] ?? '',
                'data_atts' => $option_data['data_atts'] ?? '',
                'checked'   => $checked,
                'required'  => $this->required
            ];
    
            // Check if the current option's value is in the preselected values
            $is_checked = $args['checked'] ? 'checked' : '';
    
            $field .= '<div class="bp-checkbox-wrap">';
            $field .=   '<input type="checkbox" id="' . esc_attr( $option_key ) . '" ' . $this->attributes_string( $args ) . '>';
            $field .=   '<label for="' . esc_attr( $option_key ) . '">' . $option_data['label'] . '</label>';
            $field .= '</div>';
        }
    
        // Close fieldset and container 
        $field .= '</fieldset>';
        $field .= '</div>';
    
        return $field;
    }

    /**
     * Checks whether a checkbox field should be checked.
     * 
     * @since 1.0.20
     * 
     * @param   string  $option_value   The value of the option.
     * @return  bool    True if the checkbox should be checked, false if not.
     */
    private function checked_checkbox( $option_value ) {
        // Initialize
        $checked = false;

        // Make sure a value was passed
        if ( empty( $option_value ) ) {
            return $checked;
        }

        // Cast to array
        $values = is_array( $this->value ) ? $this->value : [$this->value];
        $preselected_values = $values ?? [];

        // Check if value is in array
        $checked = in_array( $option_value, $preselected_values );

        return $checked;
    }
    
    
    /**
     * Upload field.
     * 
     * @since 0.1.0
     */
    private function upload_field() {
        
        $uploaded_files_list = $this->value ? buddyc_file_names( $this->value ) : '';
        
        $field = '<div class="buddyc-file-upload-container buddyc-form-group-container" style="' . $this->style .'">';
        
        $field .= '<div class="form-group buddyc-file-upload">';
        $field .=   '<div class="media-uploader-wrapper">';
        $field .=       '<legend for="manuscript-upload">' . $this->label . '</legend>';
        $field .=       '<p class="description">' . $this->description . '</p>';
        $field .=       '<p id="file-upload-note"></p>';
        $field .=       '<div class="dropzone document-dropzone dz-clickable" id="media-uploader">';
        $field .=           '<div class="dz-default dz-message">';
        $field .=           '<button class="dz-button buddyc-file-upload-button" type="button"><strong>' . __( 'Select File', 'buddyclients-free' ) . '</strong></button>';
        $field .=       '</div>';
        $field .=   '</div>';
        $field .=   '<input type="file" class="opacity-0 ' . $this->field_classes . '" ' . $this->field_atts_string . '>';
        $field .=   '<p id="selected-file-name">' . $uploaded_files_list . '</p>';
        $field .=   '</div>';
        $field .= '</div>';
            
        $field .= '</div>';
        
        return $field;
        
    }
    
    /**
     * Submit button.
     * 
     * @since 0.1.0
     */
    private function submit_field() {
        
        $field = '<div class="form-group form-group" style="' . $this->style . '">';
        $field .= '<input type="submit" class="' . $this->field_classes . '" name="' . $this->key . '[]" id="' . $this->key . '" ' . $this->field_atts_string . '>';
        $field .= '</div>';
        
        return $field;
    }
    
    /**
     * Field to verify form submission.
     * 
     * @since 0.1.0
     */
    private function verification_field() {
        $field = '<input type="hidden" name="buddyc_submission" value="' . $this->key . '">';
        return $field;
    }

    /**
     * Generates the nonce field.
     * 
     * @since 0.1.0
     */
    private function nonce_field() {
        $prefix = 'buddyclients_';
        $action_name = $this->key;
        $field_name = $prefix . $action_name . '_nonce';
        $nonce_field = wp_nonce_field( $action_name, $field_name );
        return $nonce_field;
    }
    
    /**
     * Displays value directly.
     * 
     * @since 0.1.0
     */
    private function display_field() {
        $field = '<div class="form-group form-group" style="' . $this->style .'">';
        $field .= '<legend>' . $this->label .'</legend>';
        $field .= $this->value;
        $field .= '</div>';

        return $field;
    }
    
    /**
     * Generates signature field.
     * 
     * @since 0.1.0
     */
    private function signature_field() {
        $field = '<div class="form-group form-group style="' . $this->style .'">';
        $field .= '<legend>' . __( 'Sign Here', 'buddyclients-free' ) . '</legend>';
        $field .= '<canvas id="signatureCanvas" width="600" height="200" style="border-radius: 5px; border: 1px solid #D4D6D8;" data-signature="signature-data"></canvas><br>';
        $field .= '<button type="button" id="signature-clear-button">' . __( 'Clear Signature', 'buddyclients-free' ) . '</button>';
        
        $field .= '<input type="hidden" id="signature-data" name="signature-data" value="">';
        $field .= '</div>';
        return $field;
    }
}