<?php
/**
 * Formats status value for display.
 * 
 * @since 0.1.0
 * 
 * @param   string  $value  The value to format.
 */
function bc_format_status( $value, $add_class = null ) {
    
    // Replace underscores and hyphens
    $formatted_value = str_replace( '_', ' ', $value );
    $formatted_value = str_replace( '-', ' ', $formatted_value );
    
    // Capitalize words
    $formatted_value = ucwords( $formatted_value );
    
    // Add original value as class
    if ( $add_class ) {
        $formatted_value = '<span class="bc-status ' . $value . '">' . $formatted_value . '</span>';
    }
    
    return $formatted_value;
}