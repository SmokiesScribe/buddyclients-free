<?php
namespace BuddyClients\Includes;

/**
 * Handles modifications to URL params.
 * 
 * @since 1.0.3
 */
class ParamManager {
    
    /**
     * The URL.
     * 
     * @var string
     */
    private $url;
    
    /**
     * Constructor method.
     * 
     * @since 1.0.3
     * 
     * @param   string  $url    Optional. The URL to modify.
     *                          Defaults to the current URL.
     */
    public function __construct( $url = null ) {
        $this->url = $url ?? $this->current_url();
    }
    
    /**
     * Retrieves the current URL.
     * 
     * @since 1.0.3
     */
    private function current_url() {
        // Get the full URL with query parameters
        return ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) 
                      . '://' . $_SERVER['HTTP_HOST'] 
                      . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Adds a parameter to the URL.
     * 
     * @since 1.0.3
     * 
     * @param   string  $param      The parameter to add.
     * @param   mixed   $value      The value of the parameter.
     * 
     * @return  string  The new url.
     */
    public function add_param( $param, $value ) {
        // Parse existing query parameters from the URL
        $parsed_url = parse_url( $this->url );
        $query_params = array();

        // Check if 'query' exists and parse it
        if ( isset( $parsed_url['query'] ) ) {
            parse_str( $parsed_url['query'], $query_params );
        }

        // Add the new parameter
        $query_params[$param] = $value;
        
        // Rebuild the query string
        $new_query_string = http_build_query( $query_params );
        
        // Rebuild the URL with updated query parameters
        $this->url = ( isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '' )
                   . ( isset( $parsed_url['host'] ) ? $parsed_url['host'] : '' )
                   . ( isset( $parsed_url['path'] ) ? $parsed_url['path'] : '' )
                   . '?' . $new_query_string;

        // Add hash fragment if it existed in the original URL
        if ( isset( $parsed_url['fragment'] ) ) {
            $this->url .= '#' . $parsed_url['fragment'];
        }
        
        return $this->url;
    }
    
    /**
     * Removes a parameter from the URL.
     * 
     * @since 1.0.3
     * 
     * @param   string  $param      The parameter to remove.
     * 
     * @return  string  The new url.
     */
    public function remove_param( $param ) {
        // Parse existing query parameters from the URL
        $parsed_url = parse_url( $this->url );
        $query_params = array();

        // Check if 'query' exists and parse it
        if ( isset( $parsed_url['query'] ) ) {
            parse_str( $parsed_url['query'], $query_params );
        }

        // Remove the parameter if it exists
        if ( isset( $query_params[$param] ) ) {
            unset( $query_params[$param] );
        }

        // Rebuild the query string
        $new_query_string = http_build_query( $query_params );

        // Rebuild the URL with updated query parameters
        $this->url = ( isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '' )
                   . ( isset( $parsed_url['host'] ) ? $parsed_url['host'] : '' )
                   . ( isset( $parsed_url['path'] ) ? $parsed_url['path'] : '' )
                   . '?' . $new_query_string;

        // Add hash fragment if it existed in the original URL
        if ( isset( $parsed_url['fragment'] ) ) {
            $this->url .= '#' . $parsed_url['fragment'];
        }
        
        return $this->url;
    }

    /**
     * Retrieves the modified URL.
     * 
     * @since 1.0.3
     * 
     * @return string The modified URL.
     */
    public function get_url() {
        return $this->url;
    }
}
