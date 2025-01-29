<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\ArchiveFilterForm;
use BuddyClients\Components\Service\Service;
use BuddyClients\Components\Testimonial\Testimonial;

/**
 * A custom archive.
 * 
 * Generates the content for the sevices and testimonials archives.
 *
 * @since 1.0.21
 */
class Archive {
    
    /**
     * An array of posts.
     * 
     * @var array
     */
     public $posts;

    /**
     * The post type for the archive.
     * 
     * @var string
     */
    public $post_type;

    /**
     * An array of tags.
     * 
     * @var array
     */
    private $tags = [];
     
    /**
     * Constructor method.
     *
     * @since 1.0.21
     *
     * @param   array    $posts   The array of service posts.
     */
    public function __construct( $posts, $post_type ) {
        $this->posts = $posts ?? [];
        $this->post_type = $post_type;
    }

    /**
     * Generates the html for the archive.
     * 
     * @since 1.0.21
     */
    public function build() {
        // Open container
        $content = '<div class="buddyc-archive-container ">';

        // Add title content
        $content .= $this->pre_posts_content();

        // Make sure posts exist
        if ( empty( $this->posts ) || ! is_array( $this->posts ) ) {

            // No posts found
            $content .= $this->no_posts_content();

        // Posts exist
        } else {

            // Loop through posts
            foreach ( $this->posts as $post ) {

                // Add single service item
                if ( $this->post_type === 'buddyc_service' ) {
                    $content .= $this->handle_service( $post );

                // Single testimonial
                } else if ( $this->post_type === 'buddyc_testimonial' ) {
                    $content .= $this->handle_testimonial( $post );
                }
            }
        }
        
        // Close the container
        $content .= '</div>';

        // Return content
        return $content;
    }

   /**
     * Retrieves the plural name for the post type.
     * 
     * @since 1.0.22
     */
    private function get_plural_name() {
        $post_type_object = get_post_type_object( $this->post_type );
        if ( $post_type_object ) {
            return $post_type_object->labels->name;
        }
        // Default to 'posts'
        return __( 'posts', 'buddyclients-free' );
    }

    /**
     * Generates the content when no posts are found.
     * 
     * @since 1.0.22
     */
    private function no_posts_content() {
        $plural_name = $this->get_plural_name();
        /* translators: %s: the plural name for the post type (e.g. posts) */
        $message = __( sprintf( 'No %s found.',
                    strtolower( $plural_name ) ),
                    'buddyclients-free' );
        $content = '<p>' . $message . '</p>';
        return $content;
    }

    /**
     * Generates the content to appear before the posts.
     * 
     * @since 1.0.21
     */
    private function pre_posts_content() {
        // Open title container
        $content = '<div class="buddyc-archive-title-container">';

        // Title
        $post_type_object = get_post_type_object( $this->post_type );
        $plural_name = isset( $post_type_object->labels->name ) ? $post_type_object->labels->name : '';

        $content .= '<h1>' . esc_html( $plural_name ) . '</h1>';

        // Filter form
        $content .= $this->filter_form();

        // Close title container
        $content .= '</div>';

        return $content;
    }

    /**
     * Builds the filter form.
     * 
     * @since 1.0.21
     */
    private function filter_form() {
        $queried_object = get_queried_object();
        $post_type = $queried_object->name;

        if ( $post_type ) {
            $form = new ArchiveFilterForm( $post_type );
            return $form->build();
        }
    }

    /**
     * Handles the display of a single service post.
     *
     * @since 1.0.21
     *
     * @param object $post The service post object.
     * @return string|null The HTML content for the service post or null if not visible.
     */
    private function handle_service( $post ) {
        // Initialize the service object.
        $service = new Service( $post->ID );

        // Skip rendering if the service is not visible.
        if ( $service->visible !== 'visible' ) {
            return null;
        }

        // Retrieve the featured image URL.
        $featured_image = get_the_post_thumbnail_url( $post->ID );

        // Retrieve the service type.
        $type = $service->service_type;

        if ( ! in_array( $type, $this->tags ) ) {
            $this->tags[] = $type;
        }
        
        // Build the service type label.
        $service_type_label = '';
        if ( $type ) {
            $type_name = get_the_title( $type );
            $type_class = array_search( $type, $this->tags );
            $service_type_label = sprintf(
                /* translators: %1$s: the ID of the item; %2$s: the index of the item; %3$s: the label for the item */
                '<p class="buddyc-tag-label %1$s tag-%2$s">%3$s</p>',
                esc_attr( $type ),
                esc_attr( $type_class ),
                esc_html( $type_name )
            );
        }

        // Truncate the content to a specified number of words (e.g., 25 words).
        $post_info = buddyc_truncate_content( $post->post_content, 25 );

        $args = [
            'data'      => 'data-archive-filter="' . esc_attr( $type ) . '"',
            'link'      => esc_url( get_the_permalink( $service->ID ) ),
            'image'     => '',
            'title'     => esc_html( $service->title ),
            'label'     => $service_type_label,
            'excerpt'   => esc_html( $post_info ),
            'cta'       => ''
        ];

        return $this->single_item( $args );

        return $content;
    }

    /**
     * Handles the display of a single testimonial post.
     *
     * @since 1.0.21
     *
     * @param object $post The testimonial post object.
     * @return string|null The HTML content for the testimonial post or null if not visible.
     */
    private function handle_testimonial( $post ) {
        // Initialize the Testimonial object.
        $testimonial = new Testimonial( $post->ID );
        $excerpt = get_the_excerpt( $testimonial->ID );

        $args = [
            'data'      => '',
            'link'      => esc_url( get_permalink( $testimonial->ID ) ),
            'image'     => $testimonial->image_html,
            'title'     => esc_html( $testimonial->author_name ),
            'label'     => '',
            'excerpt'   => esc_html( $excerpt ),
            'cta'       => ''
        ];

        return $this->single_item( $args );
    }

    /**
     * Generates the html for a single item.
     * 
     * @since 1.0.21
     */
    private function single_item( $args ) {
        return sprintf(
            /* translators: %1$s: additional data to identify the element with javascript (e.g. data-archive-filter); %2$s: the link to the testimonial; %3$s: image html; %4$s: the post title; %5$s: the label or tag name; %6$s: the excerpt content; %7$s: a call to action following the truncated content (e.g. Learn more) */
            '<div class="buddyc-archive-post" %1$s>
                <a href="%2$s">
                    <div>
                        %3$s
                        <div class="buddyc-archive-post-content">
                            <h3>%4$s</h3>
                            %5$s
                            <div class="buddyc-font-med">%6$s <span><i>%7$s</i></span></div>
                        </div>
                    </div>
                </a>
            </div>',
            $args['data'],
            $args['link'],
            $args['image'],
            $args['title'],
            $args['label'],
            $args['excerpt'],
            $args['cta'],
        );
    }
}