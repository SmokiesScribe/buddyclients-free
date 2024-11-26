<?php
namespace BuddyClients\Config;

use BuddyClients\Admin\PageManager;
use BuddyClients\Components\Email\EmailTemplateManager;

/**
 * Activation methods.
 * 
 * Initializes actions the first time the plugin is activated.
 * 
 * @since 0.1.0
 */
class Activator {

    /**
     * Handles first-time plugin activation.
     *
     * @since 0.1.0
     */
    public static function activate() {

        // Check if the plugin has been activated before
        if (get_option('buddyclients_activated') !== 'yes') {
            
            // Run activation logic
            self::activate_components();
            self::create_pages();
            self::create_default_posts();
            self::create_email_templates();
            self::create_reference_posts();

            // Set the activation flag
            update_option('buddyclients_activated', 'yes');

            /**
             * Fires when BuddyClients plugin is activated for the first time.
             * 
             * @since 0.1.0
             */
            do_action('buddyc_activated');
        }
    }
    
    /**
     * Activates components.
     * 
     * @since 0.1.0
     */
    public static function activate_components() {
        $license_handler = new LicenseHandler;
        $license_handler->update_license_and_components();
    }
    
    /**
     * Creates core pages.
     * 
     * @since 0.1.0
     */
    public static function create_pages() {
        PageManager::create_required_pages();
    }
    
    /**
     * Creates reference posts.
     * 
     * @since 0.2.10
     */
    public static function create_reference_posts() {
        new ReferencePosts;
    }
    
    /**
     * Defines default posts.
     * 
     * @since 0.1.0
     */
    public static function default_posts() {
        
        return [
            // Rate types
            'buddyc_rate_type' => [
                __( 'Per Word', 'buddyclients-free' ) => [
                    'post_content'  => null,
                    'post_meta'     => [
                        'singular'          => __( 'Word', 'buddyclients-free' ),
                        'plural'            => __( 'Words', 'buddyclients-free' ),
                        'form_description'  => __( 'What is the full word count?', 'buddyclients-free' ),
                        'attach'            => __( 'project', 'buddyclients-free' ),
                        'minimum'           => 0
                    ]
                ],
                __( 'Hourly', 'buddyclients-free' ) => [
                    'post_content'  => null,
                    'post_meta'     => [
                        'singular'          => __( 'Hour', 'buddyclients-free' ),
                        'plural'            => __( 'Hours', 'buddyclients-free' ),
                        'form_description'  => __( 'How many hours would you like to book?', 'buddyclients-free' ),
                        'attach'            => __( 'service', 'buddyclients-free' ),
                        'minimum'           => 1
                    ]
                ],
            ],
        
            // Upload types
            'buddyc_file_upload' => [
                __( 'File', 'buddyclients-free' ) => [
                    'post_content'  => null,
                    'post_meta'     => [
                        'singular'          => __( 'File', 'buddyclients-free' ),
                        'plural'            => __( 'Files', 'buddyclients-free' ),
                        'form_description'  => __( 'Upload your file.', 'buddyclients-free' ),
                        'file_types'        => ['.pdf', '.jpg', '.jpeg', '.png', '.doc', '.docx'],
                        'multiple_files'    => false,
                        'required'          => false
                    ]
                ],
            ],
        ];
    }
    
    /**
     * Creates default posts.
     * 
     * @since 0.1.0
     */
    public static function create_default_posts() {
        // Get default posts
        $defaults = self::default_posts();
    
        // Loop through post types
        foreach ( $defaults as $post_type => $post_data ) {
            // Loop through post data
            foreach ( $post_data as $post_title => $post_data ) {

                // Check if a post with the title already exists
                $post_exists = self::get_post_by_title( $post_type, $post_title );

                if ( $post_exists ) {
                    // Skip creating the post if it already exists                    
                    $error_message = sprintf(
                        /* translators: %s: post title */
                        __( 'Post with title "%s" already exists. Skipping creation.', 'buddyclients-free' ),
                        $post_title
                    );
                    error_log( $error_message );
                    continue;
                }
    
                // Define post args
                $args = [
                    'post_title'   => $post_title,
                    'post_status'  => 'publish',
                    'post_type'    => $post_type,
                ];
    
                if ( isset( $post_data['post_content'] ) ) {
                    $args['post_content'] = $post_data['post_content'];
                }
    
                // Create post
                $post_id = wp_insert_post( $args );
    
                // Check for errors
                if ( is_wp_error( $post_id ) ) {
                    $error_message = sprintf(
                        /* translators: %s: post title */
                        __( 'Error creating post: %s', 'buddyclients-free' ),
                        $post_id->get_error_message()
                    );
                    error_log( $error_message );
                    continue;
                }
    
                // Update meta
                if ( $post_id && isset( $post_data['post_meta'] ) ) {
                    foreach ( $post_data['post_meta'] as $key => $value ) {
                        update_post_meta( $post_id, $key, $value );
                    }
                }
            }
        }
    }

    /**
     * Checks whether a post with the title already exists.
     * 
     * @since 1.0.4
     * 
     * @param   string  $post_type   The post type.
     * @param   string  $post_title  The title to search for.
     * 
     * @return bool True if the post exists.
     */
    private static function get_post_by_title( $post_type, $post_title ) {
        $args = array(
            'post_type'      => $post_type,
            'title'          => $post_title, // Use a custom query to match the title
            'posts_per_page' => 1,
            'fields'         => 'ids', // Only return post IDs for better performance
        );
    
        // Retrieve the posts
        $existing_posts = get_posts( $args );
    
        // Check if posts were found
        return ! empty( $existing_posts );
    }
    
    /**
     * Creates all email templates.
     * 
     * @since 0.1.0
     */
    public static function create_email_templates() {
        if ( class_exists( EmailTemplateManager::class ) ) {
            EmailTemplateManager::create();
        }
    }
}