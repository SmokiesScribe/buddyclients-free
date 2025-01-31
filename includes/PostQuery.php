<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Queries the database for post data.
 * 
 * Retrieves posts of various types.
 *
 * @since 0.1.0
 */
class PostQuery {
    
    /**
     * Post type or array of post types.
     * 
     * @var string|array
     */
     private $post_type;
     
    /**
     * Meta queries.
     * 
     * @var array
     */
     private $meta_query;
     
    /**
     * The result of the post query
     * 
     * @var array
     */
     public $posts;
     
    /**
     * The number of posts per page.
     * Defaults to -1 for no limit.
     * 
     * @var int
     */
     public $max;
     
    /**
     * Constructor method.
     *
     * @since 0.1.0
     * 
     * @param   string  $post_type  The slug of the post type.
     * @param   array   $args {}
     *     An optional array of args for the post query.
     * 
     *     @type    array   $meta           An associative array of meta keys and values.
     *     @type    string  $compare        The compare operator for the meta queries.
     *                                      Defaults to '='.
     *     @type    array   $tax            An associative arary of tax names and tags.
     * }
     */
    public function __construct( $post_type, $args = [] ) {
        
        // Get post type
        $this->post_type = $post_type;
        
        // Get max posts
        $this->max = $args['max'] ?? -1;
        
        // Build meta query
        $this->meta_query = $this->build_meta_query( $args );

        // Build tax query
        $this->tax_query = $this->build_tax_query( $args );
        
        // Retrieve posts
        $this->posts = $this->get_posts();
    }
    
    /**
     * Generates meta query.
     * 
     * @since 0.1.0
     */
    private function build_meta_query( $args ) {
        $meta_query = [];
        $meta_array = $args['meta'] ?? null;
        $compare = $args['compare'] ?? null;
        
        if ( ! empty( $meta_array ) && is_array( $meta_array ) ) {
            foreach ( $meta_array as $key => $value ) {
                $query[] = [
                    'key' => $key,
                    'value' => $value,
                    'compare' => $compare ?? '='
                ];
            }
            $meta_query = $query;
        }
        return $meta_query;
    }
    
    /**
     * Generates tax query.
     * 
     * @since 1.0.23
     */
    private function build_tax_query( $args ) {
        $tax_query = [];
        $tax_array = $args['tax'] ?? null;

        if ( ! empty( $tax_array ) && is_array( $tax_array ) ) {
            foreach ( $tax_array as $tax => $slugs ) {
                $tax_query[] = [
                    'taxonomy' => $tax,
                    'field'    => 'slug',
                    'terms'    => $slugs,
                    'operator' => 'IN',
                ];
            }
        }
        return $tax_query;
    }
    
    /**
     * Retrieves posts.
     * 
     * @since 0.1.0
     */
    private function get_posts() {
        
        $args = array(
            'post_type'      => $this->post_type,
            'posts_per_page' => $this->max,
            'meta_query'     => array(
                'relation' => 'AND',
        
                // Custom key value from args
                $this->meta_query ?? array(),
        
                // Hidden not selected
                array(
                    'key'     => 'hide',
                    'compare' => 'NOT EXISTS',
                ),
        
                // Order by 'order' if it exists
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'order',
                        'compare' => 'EXISTS',
                    ),
                    array(
                        'key'     => 'order',
                        'compare' => 'NOT EXISTS',
                    ),
                ),
            ),
            // Build tax query
            'tax_query' => $this->tax_query ?? [],

            // Order by  meta field
            'orderby' => 'meta_value_num',
            'order'   => 'DESC',
        );
        
        // Get the posts
        return get_posts( $args );        
    }
    
    /**
     * Retrieves post IDs.
     * 
     * @since 0.1.0
     * 
     * @return  array   An array of post IDs.
     */
    public function get_post_ids() {
        // Initialize the array
        $post_ids = [];
        
        // Make sure posts exist
        if ( $this->posts ) {
            // Loop through the posts
            foreach ( $this->posts as $post ) {
                // Add to array
                $post_ids[] = $post->ID;
            }
        }
        
        // Return the array
        return $post_ids;
    }
}