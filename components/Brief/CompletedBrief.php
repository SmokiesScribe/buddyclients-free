<?php
namespace BuddyClients\Components\Brief;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Single completed brief.
 * 
 * Generates the content of a completed brief.
 * Includes submitted answers and file download links.
 *
 * @since 0.1.0
 * 
 * @see SingleBrief
 */
class CompletedBrief extends SingleBrief {

    /**
     * Empty fields without a submitted value.
     * 
     * @var array
     */
    private $empty_fields;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    protected function __construct() {
        parent::__construct();
    }
    
    /**
     * Generates completed brief.
     * 
     * @since 0.1.0
     */
    public function display() {
        // Initialize
        $content = '';
        
        // Check that field options exist
        if ( ! empty( $this->fields ) && is_array( $this->fields ) ) {
        
            // Loop through the fields and append their values with human-readable names to the custom content
            foreach ( $this->fields as $field_id => $data ) {
                
                // Single field
                $content .= $this->single_field( $field_id, $data );

            }
        }

        // Add empty fields list
        $content .= $this->empty_fields_list();
        
        return $content;
    }
    
    /**
     * Displays a single field.
     * 
     * @since 0.1.0
     * 
     * @param   int     $field_id   The post ID of the field.
     * @param   array   $data       The field data.
     */
    private function single_field( $field_id, $data ) {
        
        // Initialize
        $content = '';
        
        // Get field value
        $field_value = get_post_meta( $this->brief_id, $field_id, true );

        // Skip disabled fields
        if ( $data['type'] === 'disabled' ) {
            return;
        }

        // Add empty field to array and skip
        if ( ! $field_value ) {
            $this->empty_fields[] = $data['label'] ?? '';
            return;
        }
        
        // Open container
        $content .= '<div class="buddyc-box">';

        // Field label
        $content .= '<h4><strong>' . esc_html( $data['label'] ) . '</strong></h4>';

        // Add content by type
        $content .= $this->field_content( $field_id, $data, $field_value );
        
        // Close the field
        $content .= '</div>';
        
        return $content;
    }

    /**
     * Builds the field value based on the field type.
     * 
     * @since 1.0.21
     * 
     * @param   string  $field_id       The ID of the field.
     * @param   array   $data           An array of data for the field.
     * @param   mixed   $field_value    The value of the field.
     */
    private function field_content( $field_id, $data, $field_value ) {
        $type = $data['type'] ?? 'input';

        // Upload field
        if ( $type === 'upload' ) {
            return buddyc_download_links( $field_value, true );
        }

        // Array
        if ( is_array( $field_value ) ) {
            return $this->checkbox_field_content( $field_id, $data );
        }
        
        // Single value
        return $this->single_value( $type, $field_value );
    }

    /**
     * Formats the value for a single value field.
     * 
     * @since 1.0.21
     * 
     * @param   string  $type           The type of field.
     * @pearam  string  $field_value    The value of the field.
     */
    private function single_value( $type, $field_value ) {
        switch ( $type ) {
            case 'text_area':
                // Don't format text area
                $content = trim( $field_value );
                break;
            default:
                $content = $this->format_value( $field_value );
                break;
        }
        return $content;
    }

    /**
     * Builds the content for a checkbox field.
     * 
     * @since 1.0.21
     * 
     * @param   string  $field_id       The ID of the field.
     * @param   array   $data           An array of data for the field.
     */
    private function checkbox_field_content( $field_id, $data ) {

        // Get field value as an array
        $field_value = get_post_meta( $this->brief_id, $field_id, false );
    
        // Initialize the string to store formatted values
        $value_string = '';
    
        if ( ! empty( $field_value ) && is_array( $field_value ) ) {
            // Flatten the multidimensional array
            $flattened_values = array_merge( ...$field_value );
    
            // Format and join the values into a string
            $value_string = implode(
                ', ',
                array_map(
                    function ( $value ) {
                        return $this->format_value( $value );
                    },
                    $flattened_values
                )
            );
        }
        return $value_string;
    }    

    /**
     * Formats a single value.
     * 
     * @since 1.0.21
     */
    private function format_value( $value ) {
        if ( ! empty( $value ) ) {
            return trim( ucwords( str_replace( '_', ' ', $value ) ) );
        }
    }

    /**
     * Builds a list of incomplete fields.
     * 
     * @since 1.0.21
     */
    private function empty_fields_list() {
        // Init content
        $content = '';

        // Get empty field labels
        $empty_fields = $this->empty_fields;

        // Exit if no incomplete fields exist
        if ( empty( $empty_fields ) ) {
            return;
        }

        // Header
        $content .= '<h3>Incomplete Fields</h3>';

        // Open list
        $content .= '<ul>';

        // Loop through incomplete field labels
        foreach ( $empty_fields as $empty_field_label ) {
            $content .= '<li>' . esc_html( $empty_field_label ) . '</li>';
        }

        // Close list
        $content .= '</ul>';

        return $content;
    }
}