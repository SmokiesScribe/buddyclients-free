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
     * The nonce action.
     * 
     * @var string
     */
    private $nonce_action = 'bc_action';

    /**
     * The nonce name.
     * 
     * @var string
     */
    private $nonce_name = '_bc_nonce';
    
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
        return bc_curr_url();                
    }

    /**
     * Adds a nonce to the URL.
     * 
     * @since 1.0.4
     * 
     * @return string The URL with the nonce added.
     */
    public function add_nonce() {
        $nonce = wp_create_nonce( $this->nonce_action );
        return $this->add_param( $this->nonce_name, $nonce, $this->url );
    }

    /**
     * Adds multiple parameters to the URL.
     * 
     * @since 1.0.4
     * 
     * @param   array   $params     An associative array of params and values.
     * @param   string  $url        Optional. The url to modify.
     *                              Defaults to the current url.
     * 
     * @return  string  The new url.
     */
    public function add_params( $params, $url = null ) {
        $url = $url ?? $this->url;
        if ( is_array( $params ) && ! empty( $params ) ) {
            foreach ( $params as $param => $value ) {
                $url = $this->add_param( $param, $value, $url );
            }
        }
        return $url;
    }
    
    /**
     * Adds a parameter to the URL.
     * 
     * @since 1.0.3
     * 
     * @param   string  $param      The parameter to add.
     * @param   mixed   $value      The value of the parameter.
     * @param   string  $url        Optional. The url to modify.
     *                              Defaults to the current url.
     * 
     * @return  string  The new url.
     */
    public function add_param( $param, $value, $url = null ) {
        $url = $url ?? $this->url;

        // Parse existing query parameters from the URL
        $parsed_url = wp_parse_url( $url );
        $query_params = array();

        // Check if 'query' exists and parse it
        if ( isset( $parsed_url['query'] ) ) {
            parse_str( $parsed_url['query'], $query_params );
        }

        // Check if the url includes nonce
        if ( ! isset( $query_params[$this->nonce_name] ) ) {
            // Define nonce
            $nonce = wp_create_nonce( $this->nonce_action );

            // Add nonce to params
            $query_params[$this->nonce_name] = $nonce;
        }

        // Add the new parameter
        $query_params[$param] = $value;
        
        // Rebuild the query string
        $new_query_string = http_build_query( $query_params );
        
        // Rebuild the URL with updated query parameters
        $url = ( isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '' )
                   . ( isset( $parsed_url['host'] ) ? $parsed_url['host'] : '' )
                   . ( isset( $parsed_url['path'] ) ? $parsed_url['path'] : '' )
                   . '?' . $new_query_string;

        // Add hash fragment if it existed in the original URL
        if ( isset( $parsed_url['fragment'] ) ) {
            $url .= '#' . $parsed_url['fragment'];
        }
        
        return $url;
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
        $parsed_url = wp_parse_url( $this->url );
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
     * Retrieves the value of a url param.
     * 
     * @since 1.0.4
     * 
     * @param string $param  The param key.
     */
    public function get( $param ) {

        // Verify nonce
        if ( isset( $_GET[$this->nonce_name] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $_GET[$this->nonce_name] ) );
            if ( ! wp_verify_nonce( $nonce, $this->nonce_action ) ) {
                // Exit if nonce fails
                return;
            }
        }

        // Get value of url param
        if ( isset( $_GET[$param] ) ) {
            return sanitize_text_field( wp_unslash ($_GET[$param] ) ) ?? null;
        }
    }

    /**
     * Retrieves all url parameters.
     * 
     * @since 1.0.15
     * 
     * @return  array   An array of url params.
     */
    public function get_all_params() {
        
        // Verify nonce
        if ( isset( $_GET[$this->nonce_name] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $_GET[$this->nonce_name] ) );
            if ( ! wp_verify_nonce( $nonce, $this->nonce_action ) ) {
                // Exit if nonce fails
                return;
            }
        }

        // Return all url params        
        return $_GET;
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
