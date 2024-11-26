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
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param array $args
     * 
     * $meta_query optional array key, value, compare, type
     */
    public function __construct( $post_type, $meta_query = null, $compare = null ) {
        
        // Get post type
        $this->post_type = $post_type;
        
        // Build meta query
        $this->meta_query( $meta_query, $compare );
        
        // Retrieve posts
        $this->posts = $this->get_posts();
    }
    
    /**
     * Generates meta query.
     * 
     * @since 0.1.0
     */
    private function meta_query( $meta_query, $compare ) {
        
        if ($meta_query) {
            foreach ($meta_query as $key => $value) {
                $query = [
                    'key' => $key,
                    'value' => $value,
                    'compare' => $compare ?? '='
                ];
            }
            $this->meta_query = $query;
        }
        return $this;
    }
    
    /**
     * Retrieves posts.
     * 
     * @since 0.1.0
     */
    private function get_posts() {
        
        $args = array(
            'post_type'      => $this->post_type,
            'posts_per_page' => -1,
            'meta_query' => array(
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
            'orderby'    => 'meta_value_num',
            'order'      => 'DESC',
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