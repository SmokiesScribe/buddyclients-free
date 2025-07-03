<?php
namespace BuddyClients\Components\Brief;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Generates the brief form content.
 *
 * @since 0.1.0
 */
class BriefForm extends SingleBrief {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    protected function __construct() {
        parent::__construct();
    }
    
    /**
     * Generates form.
     * 
     * @since 0.1.0
     */
    protected function form() {
        $args = [
            'key'                   => 'brief',
            'fields_callback'       => [$this, 'form_fields'],
            'submission_class'      => __NAMESPACE__ . '\BriefSubmission',
        ];
        return buddyc_build_form( $args );
    }
    
    /**
     * Generates form fields.
     * 
     * @since 0.1.0
     */
    public function form_fields() {

        // Initialize
        $args = [];
        
        // Add project field
        $args[] = $this->hidden_project_field();
        
        // Loop through fields from SingleBrief
        foreach ( $this->fields as $field_id => $field_data ) {
            
            // Build args
            $args[] = [
                'key'           => $field_id,
                'type'          => $field_data['type'] ?? null,
                'label'         => $field_data['label'] ?? '',
                'description'   => $this->build_description( $field_data ),
                'options'       => $this->format_options( $field_id, $field_data ),
                'file_types'    => $field_data['file_types'] ?? [],
                'multiple_files'=> $field_data['multiple_files'] ?? 'no',
                'value'         => $this->get_field_value( $field_id )
            ];
        }
        
        // Return the array of field data
        return $args;
    }

    /**
     * Builds the hidden project field.
     * 
     * @since 1.0.21
     */
    private function hidden_project_field() {
        return [
            'key'           => 'brief_id',
            'type'          => 'hidden',
            'value'         => $this->brief_id,
        ];
    }

    /**
     * Retrieves the value of a single field.
     * 
     * @since 1.0.21
     * 
     * @param   string  $field_id   The ID of the field.
     */
    private function get_field_value( $field_id ) {
        $value = get_post_meta( $this->brief_id, $field_id, true );
        return is_array( $value ) ? $value[0] : $value;
    }

    /**
     * Builds the description html for a single field.
     * 
     * @since 1.0.21
     * 
     * @param   array   $field_data     The array of field data. 
     */
    private function build_description( $field_data ) {
        $help_link = ( isset( $field_data['help_post_id'] ) && ! empty( $field_data['help_post_id'] ) ) ? buddyc_help_link( $field_data['help_post_id'] ) : '';
        $description = $field_data['description'] ?? '';
        return $description . $help_link;
    }

    
    /**
     * Formats options.
     * 
     * @since 0.1.0
     * 
     * @param   string  $field_id   The ID of the field.
     * @param   array   $field_data The array of field data.
     */
    private function format_options( $field_id, $field_data ) {
        $options = $field_data['options'] ?? null;
        $field_type = $field_data['type'] ?? null;
        
        // Exit if no options
        if ( empty( $options ) ) {
            return;
        }
        
        // Initialize
        $options_data = [];

        // Add empty option for dropdown
        if ( $field_type === 'dropdown' ) {
            $options_data[] = ['label' => __( 'Select One', 'buddyclients-lite' ), 'value' => ''];
        }
        
        // Get options array
        $options_array = explode( ',', $options );
        
        // Loop through each option and format the key-value pairs
        if ( is_array( $options_array )) {
            foreach ( $options_array as $option ) {
                // Format the key by replacing spaces with underscores and converting to lowercase
                $option_key = trim( strtolower( str_replace( ' ', '_', $option ) ) );
                // Store the formatted option in the array
                $options_data[$option_key] = [
                    'label' => $option,
                    'value' => $option_key,
                ];
            }
        }
        return $options_data;
    }    
}