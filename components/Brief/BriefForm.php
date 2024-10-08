<?php
namespace BuddyClients\Components\Brief;

use BuddyClients\Includes\{
    Form\Form,
    Form\FormField,
    PostQuery
};


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
        return (new Form( $args ) )->build();
    }
    
    /**
     * Generates form fields.
     * 
     * @since 0.1.0
     */
    public function form_fields() {
        
        // Initialize
        $args = array(); // Initialize as an empty array
        
        // Add hidden project field
        $args[] = [
            'key'           => 'brief_id',
            'type'          => 'hidden',
            'value'         => $this->brief_id,
        ];
        
        // Build fields
        foreach ( $this->fields as $field_id => $field_data ) {
            
            $help_link = $field_data['help_post_id'] ? bc_help_link( $field_data['help_post_id'] ) : '';
                
            $args[] = [
                'key'           => $field_id,
                'type'          => $field_data['type'],
                'label'         => $field_data['label'],
                'description'   => $field_data['description'] . $help_link,
                'options'       => $this->format_options( $field_data['options'], $field_data['type'], $field_id ),
                'file_types'    => $field_data['file_types'],
                'multiple_files'=> $field_data['multiple_files'],
                'value'         => esc_html( get_post_meta( $this->brief_id, $field_id, true) )
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