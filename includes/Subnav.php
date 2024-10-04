<?php
namespace BuddyClients\Includes;

/**
 * Subnav content.
 * 
 * Builds a public subnav.
 * 
 * @since 0.1.0
 */
class Subnav {
    
    /**
     * The current URL.
     * 
     * @var string
     */
    private $curr_url;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        $this->curr_url = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
    }
    
    /**
     * Builds the subnav.
     * 
     * @since 0.1.0
     * 
     * @param   array   $items
     *     Keyed array of items to build the subnav.
     * 
     *     @type    string      $label      The item label.
     *     @type    ?string     $link       Optional. A specific link.
     */
    public function build( $items ) {
        
        // Initialize
        $output = '';
        
        // Open container
        $output .= '<div class="bc_subnav">';
        
        // Loop through items
        foreach ( $items as $key => $data ) {
            // Generate link
            $link = $data['link'] ?? $this->generate_link( $key );
            // Check if the item is active
            $class = $this->is_active( $key ) ? 'active' : '';
            // Build subnav item
            $output .= '<a class="' . $class . '" style="margin-right: 20px" href="' . htmlspecialchars( $link ) . '">' . $data['label'] . '</a>';
        }
            
        // Close container
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Generates a single link.
     * 
     * @since 0.1.0
     */
    private function generate_link( $param_value ) {
        
        // Parse the current URL
        $url_parts = wp_parse_url( $this->curr_url );
    
        // Add or replace the specified parameter
        if (isset($url_parts['query'])) {
            $query = $url_parts['query'];
            parse_str($query, $params);
            $params['subnav'] = $param_value;
            $query = http_build_query($params);
        } else {
            $query = 'subnav=' . $param_value;
        }
    
        // Generate the new URL
        $new_url = $url_parts['path'] . '?' . $query;
        
        return $new_url;
    }
    
    /**
     * Checks if the item link is active.
     * 
     * @since 0.1.0
     * 
     * @param   string      $key   The param value to check.
     */
    private function is_active( $key ) {
        // Initialize
        $active = false;
        
        // Check if the param is set
        if ( isset( $_GET['subnav'] ) ) {
            $active = $_GET['subnav'] == $key ? true : false;
        }
        return $active;
    }
    
}