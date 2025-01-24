<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\Archive;
use BuddyClients\Includes\Template;

/**
 * Handles custom templates for single posts and archives.
 *
 * @since 1.0.21
 */
class TemplateManager {
    
    /**
     * Whether the active thing is a FSE block theme.
     * 
     * @var bool
     */
     private $wp_is_block_theme;
     
    /**
     * Constructor method.
     *
     * @since 1.0.21
     */
    public function __construct() {
        // Exit if we're in the admin area
        if ( is_admin() ) {
            return;
        }

        // Define properties
        $this->define_properties();

        // Define all hooks
        $this->define_hooks();
    }

    /**
     * Defines state-specific properties.
     * 
     * @since 1.0.21
     */
    private function define_properties() {
        $this->is_block_theme = wp_is_block_theme();
    }

    /**
     * Defines custom post types.
     * 
     * @since 1.0.21
     */
    private static function post_types() {
        return [
            'buddyc_service'        => ['single', 'archive'],
            'buddyc_testimonial'    => ['single', 'archive'],
            'buddyc_brief'          => ['single'],
        ];
    }

    /**
     * Defines hooks.
     * 
     * @since 1.0.21
     */
    private function define_hooks() {
        $this->wp_theme_hooks();
        $this->custom_theme_hooks();

        // Common hooks
        add_action( 'pre_get_posts', [$this, 'modify_buddyc_service_query'] );
        add_filter( 'get_the_archive_title', [$this, 'custom_archive_title'] );
    }

    /**
     * Defines hooks for default WP themes.
     * 
     * @since 1.0.21
     */
    private function wp_theme_hooks() {
        if ( $this->is_block_theme ) {
            add_filter( 'the_content', [$this, 'replace_single_post_content'] );
            add_filter( 'the_posts', [$this, 'replace_archive_post'], 10, 2 );
        }
    }

    /**
     * Defines hooks for custom themes.
     * 
     * @since 1.0.21
     */
    private function custom_theme_hooks() {
        if ( ! $this->is_block_theme ) {
            add_filter( 'template_include', [$this,'replace_template'] );
        }
    }

    /**
     * Fetches the path a custom template.
     * 
     * @since 1.0.21
     * 
     * @param   string  $post_type  The slug of the post type.
     * @param   string  $page_type  The type of page ('single' or 'archive').
     */
    private function get_template( $post_type, $page_type ) {
        // TESTING
        return BUDDYC_PLUGIN_DIR . "templates/dynamic-$page_type.php";


        $template = BUDDYC_PLUGIN_DIR . "templates/$page_type-$post_type.php";
        if ( file_exists( $template ) ) {
            return $template;
        }
    }

    /**
     * Fetches the applicable post types.
     * 
     * @since 1.0.21
     * 
     * @param   string  $type   The type of page ('single' or 'archive').
     * @return  array           The list of applicable post types.
     */
    private function get_post_types( $type ) {
        // Ensure $type is valid ('single' or 'archive').
        $type = in_array( $type, ['single', 'archive'] ) ? $type : 'single';

        // Fetch all post types and filter them based on the $type.
        $all_post_types = self::post_types();
        $filtered_post_types = array_filter( $all_post_types, function( $types ) use ( $type ) {
            return in_array( $type, $types );
        });

        // Return the keys of the filtered array as the applicable post types.
        return array_keys( $filtered_post_types );
    }

    /**
     * Injects custom content for specific post types.
     * 
     * Replaces the content for single posts of custom types.
     * Used instead of custom templates to improve consistency
     * across default and custom themes.
     *
     * @since 1.0.21
     */
    public function replace_single_post_content( $content ) {
        $post_types = $this->get_post_types( 'single' );

        // Check if the current post type matches
        if ( is_singular( $post_types ) ) {

            // Get the queried post type
            $post_type = get_post_type();

            ob_start();
            $single_post = new SinglePost( $post_type );
            $single_post->render();
            return ob_get_clean();
        }

        // Return the default content for other post types
        return $content;
    }

    /**
     *  
     * Replaces the entire post in the archive loop for specific post types.
     * 
     * Used to inject custom archive content and styling. Only runs on custom themes.
     *
     * @param array $posts The list of posts returned by the query.
     * @param WP_Query $query The current query object.
     * @return array The modified list of posts.
     */
    public function replace_archive_post( $posts, $query ) {
        $post_types = $this->get_post_types( 'archive' );

        // Check if it's the main query for post type archives
        if ( $query->is_main_query() && is_post_type_archive( $post_types ) ) {

            $queried_object = get_queried_object();
            $post_type = $queried_object->name;

            $archive = new Archive( $posts, $post_type );
            $content = $archive->build( $hide_title = true );

            // Replace the post content with the custom content
            $posts = array(
                (object) array(
                    'post_content' => $content,
                    //'post_title'   => esc_html__( 'Services', 'buddyclients-free' ),
                    'ID'           => 0, // Optional, you can provide a dummy ID for the container
                )
            );
        }

        return $posts;
    }

    /**
     * Removes pagination for the service archive.
     * 
     * @since 1.0.21
     */
    public function modify_buddyc_service_query( $query ) {
        // Ensure we're modifying the main query on an archive page for the buddyc_service post type
        if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'buddyc_service' ) ) {

            // Remove pagination by setting posts_per_page to -1 (fetch all posts)
            $query->set( 'posts_per_page', -1 );

            // Sort by service type
            $query->set( 'post_type', 'buddyc_service' );
            $query->set( 'meta_key', 'service_type' );
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'order', 'ASC' );
        }
    }

    /**
     * Customize archive titles by removing the 'Archives:' prefix.
     *
     * @since 0.1.0
     *
     * @param string $title The default archive title.
     * @return string The modified archive title.
     */
    public function custom_archive_title( $title ) {
        if ( is_post_type_archive() ) {
            // Get the queried post type object
            $post_type = get_queried_object();

            if ( isset( $post_type->labels->name ) ) {
                return $post_type->labels->name; // Return the plural name of the post type
            }
        }

        return $title; // Return the original title for other archives
    }

    /**
     * Replaces the single or archive template.
     * 
     * @since 1.0.21
     */
    public function replace_template( $template ) {
        $page_type = is_singular() ? 'single' : 'archive';
        $post_types = $this->get_post_types( $page_type );

        // Check for matching post types
        if ( is_singular( $post_types ) || is_post_type_archive( $post_types ) ) {

            // Get queried object and post type slug
            $queried_object = get_queried_object();
            $post_type = $page_type === 'single' ? $queried_object->post_type : $queried_object->name;

            // Specify the path to your custom template file
            $custom_template = $this->get_template( $post_type, $page_type );
    
            // Check if the custom template file exists
            if ( $custom_template ) {
                // Use the custom template file
                return $custom_template;
            }
        }
        // Return original template
        return $template;
    }
}