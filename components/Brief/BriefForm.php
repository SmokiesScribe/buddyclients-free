<?php
namespace BuddyClients\Components\Brief;

use BuddyClients\Includes\Form\Form;


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
        $form = new Form( $args );
        return $form->build();
    }
    
    /**
     * Generates form fields.
     * 
     * @since 0.1.0
     */
    public function form_fields() {
        
        // Initialize
        $args = [];
        
        // Add hidden project field
        $args[] = [
            'key'           => 'brief_id',
            'type'          => 'hidden',
            'value'         => $this->brief_id,
        ];
        
        // Loop through fields from SingleBrief
        foreach ( $this->fields as $field_id => $field_data ) {
            
            // Build help link
            $help_link = ( isset( $field_data['help_post_id'] ) && ! empty( $field_data['help_post_id'] ) ) ? buddyc_help_link( $field_data['help_post_id'] ) : '';

            // Format options
            $options = isset( $field_data['options'] ) && ! empty( $field_data['options'] ) ? $this->format_options( $field_data['options'], $field_data['type'], $field_id ) : [];

            // Retrieve submitted value
            $value = get_post_meta( $this->brief_id, $field_id, true );
            if ( is_array( $value ) ) {
                $value = $value[0];
            }
            
            // Build args
            $args[] = [
                'key'           => $field_id,
                'type'          => $field_data['type'] ?? null,
                'label'         => $field_data['label'] ?? '',
                'description'   => ( $field_data['description'] ?? '' ) . $help_link,
                'options'       => $options,
                'file_types'    => $field_data['file_types'] ?? [],
                'multiple_files'=> $field_data['multiple_files'] ?? 'no',
                'value'         => $value
            ];
        }
        
        // Return the array
        return $args;
    }

    
    /**
     * Formats options.
     * 
     * @since 0.1.0
     * 
     * @param   string  $options    Options string from post meta.
     * @param   string  $field_type The type of field.
     * @param   string  $field_id   The ID of the field.
     */
    private function format_options( $options, $field_type, $field_id ) {
        
        // Exit if no options
        if ( ! $options ) {
            return;
        }
        
        // Initialize
        $options_data = [];
        if ( $field_type === 'dropdown' ) {
            $options_data[] = ['label' => __( 'Select One', 'buddyclients' ), 'value' => ''];
        }
        
        // Get options array
        $options_array = explode(',', $options);
        
        // Loop through each option and format the key-value pairs
        if (is_array($options_array)) {
            foreach ($options_array as $option) {
                // Format the key by replacing spaces with underscores and converting to lowercase
                $option_key = trim(strtolower(str_replace(' ', '_', $option)));
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