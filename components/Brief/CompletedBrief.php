<?php
namespace BuddyClients\Components\Brief;

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
        $brief_content = '';
        
        // Add the container (wraps both columns)
        $brief_content .= '<div class="custom-content">';
        
        // Add hidden header to anchor auto TOC
        $brief_content .= '<h2 style="display: none"></h2>';
        
        // Check that field options exist
        if ( $this->fields ) {
        
            // Loop through the fields and append their values with human-readable names to the custom content
            foreach ( $this->fields as $field_id => $data ) {
                
                // Single field
                $brief_content .= $this->single_field( $field_id, $data );

            }
        }
        
        // Close the content container
        $brief_content .= '</div>';
        
        return $brief_content;
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
        $brief_content = '';
        
        // Get field value
        $field_value = get_post_meta($this->brief_id, $field_id, true);
        
        // Skip empty and disabled fields
        if (!$field_value || $data['type'] === 'disabled') {
            return;
        }
        
        // Begin building field
        $brief_content .= '<div class="single-brief-field">';
        $brief_content .= '<h4><strong>' . esc_html($data['label']) . '</strong></h4>';
            
        // Upload field
        if ($data['type'] === 'upload') {
            
            // Handle upload field
            $brief_content .= buddyc_download_links( $field_value, true );
        
        // Not upload field    
        } else {
            
            // Checkboxes
            if (is_array($field_value)) { // handle checkboxes
                
                // Get field value as an array
                $field_value = get_post_meta($this->brief_id, $field_id, false);  
                
                // Initialize the string to store formatted values
                $formatted_field_value = '';
                
                // Loop through each array of values
                foreach ($field_value as $values_array) {
                    // Loop through each individual value in the array
                    foreach ($values_array as $value) {
                        // Format the value and append it to the formatted string
                        $formatted_field_value .= trim(ucwords(str_replace('_', ' ', esc_html($value)))) . ', ';
                    }
                }
                
                // Remove the trailing comma and space
                $formatted_field_value = rtrim($formatted_field_value, ', ');
            
            // Single value    
            } else {
                
                if ($data['type'] === 'text_area' || $data['type'] === 'input') {
                    // No formatting
                    $formatted_field_value = trim($field_value);
                } else {
                    // Format single value
                    $formatted_field_value = trim(ucwords(str_replace('_', ' ', esc_html($field_value))));
                }
            }
                
            // Output the field content
            $brief_content .= '<p>' . $formatted_field_value . '</p>';

        }
        
        // Close the field
        $brief_content .= '</div>';
        
        return $brief_content;
    }
}