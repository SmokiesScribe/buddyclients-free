<?php
namespace BuddyClients\Config;

use BuddyClients\Admin\PostType;

/**
 * Creates reference posts.
 * 
 * Sets up reference posts for use in help links throughout the plugin.
 * 
 * @since 0.2.10
 */
class ReferencePosts {
    
    /**
     * Constructor method.
     * 
     * @since 0.2.10
     */
    public function __construct() {
        $this->register_post_type();
        add_action( 'init', [$this, 'create_posts'] );
    }
    
    /**
     * Registers the post type.
     * 
     * @since 0.2.10
     */
    private function register_post_type() {
        $args = [
            'singular_name'         => __( 'Reference', 'buddyclients-free' ),
            'plural_name'           => __( 'References', 'buddyclients-free' ),
            'show_in_menu'          => false,
            'public'                => false,
            'has_archive'           => false,
            'supports'              => ['title'],
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'show_in_nav_menus'     => false,
            'show_in_rest'          => false,
        ];
        new PostType( 'bc_reference', $args );
    }
    
    /**
     * Retrieves the ID of a reference post by key.
     * 
     * @since 0.4.0
     * 
     * @param   string  $key    The key of the post ID to retrieve.
     */
    public static function get_reference_post_id( $key ) {
        // Get array from settings
        $post_ids = get_option( 'bc_reference_posts' );
        
        // Get post ID by key
        $post_id = $post_ids[$key] ?? null;
        
        // Return ID
        return $post_id;
    }
    
    /**
     * Retrieves an array of published reference post IDs.
     * 
     * @since 0.4.0
     */
    public static function get_all_reference_post_ids() {
        return get_option( 'bc_reference_posts' );
    }
    
    /**
     * Defines reference post content.
     * 
     * @since 0.2.10
     */
    private static function post_content() {
        return [
            'team_select' => [
                'title'     => __( 'Team Availability', 'buddyclients-free' ),
                'content'   => __( '<p>The dropdown includes team members whose expertise are a fit for your project.</p><p>The displayed availability indicates the date the team member can begin work if you book your services today. If no availability is displayed, the team member has not specified a date but is still accepting new projects.</p>', 'buddyclients-free' ),
            ]
        ];
    }
    
    /**
     * Creates reference posts.
     * 
     * @since 0.2.10
     */
    private function create_posts() {
        
        // Get post info
        $posts = self::post_content();
        
        // Loop thorugh posts
        foreach ( $posts as $key => $args ) {
            
            // Create the post and upate settings
            self::add_post( $key, $args );
        }
    }
    
    /**
     * Adds a new reference post.
     * 
     * @since 0.4.0
     * 
     * @param   string  $key    The unique key for the reference post.
     * @param   array   $args {
     *     An array of arguments for creating the new reference post.
     * 
     *     @string  $content    The content of the post.
     *     @string  $title      The title of the post.
     * }
     */
    public static function add_post( $key, $args ) {
        
        // Get existing post ids
        $post_ids = self::get_all_reference_post_ids();
        
        // Initialize to array
        $post_ids = is_array( $post_ids ) ? $post_ids : [];
        
        // Make sure the post does not exist
        if ( isset( $post_ids[$key] ) && get_post_status( $post_ids[$key] ) === 'publish' ) {
            return;
        }
            
        // Define post args
        $post_args = [
            'ID'            => self::get_reference_post_id( $key ) ?? 0,
            'post_content'  => $args['content'],
            'post_title'    => $args['title'],
            'post_status'   => 'publish',
            'post_type'     => 'bc_reference'
        ];
        
        // Create post and get new ID
        $post_id = wp_insert_post( $post_args );
        
        // Check if successful
        if ( $post_id ) {
            // Add to array
            $post_ids[$key] = $post_id;
            
            // Update setting
            update_option( 'bc_reference_posts', $post_ids );
        }
    }
}