<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\ArchiveFilterForm;
use BuddyClients\Components\Service\Service;
use BuddyClients\Components\Testimonial\Testimonial;
use BuddyClients\Components\Brief\Brief;
use BuddyClients\Components\Brief\SingleBrief;
use BuddyClients\Components\Service\ServicePost;

/**
 * A custom single post.
 * 
 * Generates the content for a single post.
 *
 * @since 1.0.21
 */
class SinglePost {
    
    /**
     * The ID of the post.
     * 
     * @var int
     */
     public $post_id;

    /**
     * The post type.
     * 
     * @var string
     */
    public $post_type;

    /**
     * The post title.
     * 
     * @var string
     */
    public $post_title;

    /**
     * The object based on the post type.
     * Testimonial, Brief, or Service.
     * 
     * @var object
     */
    public $object;

    /**
     * Whether we are replacing content.
     * 
     * True if we're replacing content,
     * false if we're replacing the template.
     * 
     * @var bool
     */
    public $replacing_content;
     
    /**
     * Constructor method.
     *
     * @since 1.0.21
     *
     * @param   array    $posts   The array of service posts.
     */
    public function __construct( $post_type ) {
        $this->post_type = $post_type;
        $this->post_id = get_the_id();
        $this->object = $this->get_object();

        // Set additional properties
        $this->set_var();
    }

    /**
     * Retrieves the object based on the post type.
     * 
     * @since 1.0.21
     */
    private function get_object() {
        switch ( $this->post_type ) {
            case 'buddyc_testimonial':
                return new Testimonial( $this->post_id );
            case 'buddyc_service':
                return new Service( $this->post_id );
            case 'buddyc_brief':
                return new Brief( $this->post_id );
        }
    }

    /**
     * Sets properties based on post type.
     * 
     * @since 1.0.21
     */
    private function set_var() {
        $this->replacing_content = $this->is_replacing_content();
        $this->post_title = $this->get_post_title();
    }

    /**
     * Checks whether we're replacing content.
     * 
     * @since 1.0.21
     */
    private function is_replacing_content() {
        global $buddyc_is_replacing_content;
        return isset( $buddyc_is_replacing_content );
    }

    /**
     * Retrieves the title of the current post.
     * 
     * @since 1.0.21
     */
    private function get_post_title() {
        // Testimonial author name
        if ( $this->post_type === 'buddyc_testimonial' ) {
            return $this->object->author_name;
        // Other post types
        } else {
            return get_the_title( $this->post_id );
        }        
    }

    /**
     * Generates the html for the archive.
     * 
     * @since 1.0.21
     */
    public function render() {
        $content = $this->get_content();
        $allowed_html = $this->allowed_html();
        echo wp_kses( $content, $allowed_html );
    }

    /**
     * Retreives the content for the post type.
     * 
     * @since 1.0.21
     */
    public function get_content() {
        $content = '';        

        // Open wrap
        $classes = $this->container_classes();
        $content .= '<div class="' . $classes . '">';

        // Breadcrumbs
        $content .= $this->build_breadcrumbs();

        // Get content by post type
        $content .= $this->content_by_post_type();

        // Close wrap
        $content .= '</div>';

        // Return content
        return $content;
    }

    /**
     * Defines the post container classes.
     * 
     * @since 1.0.21
     * 
     * @return  string  Formatted string of classes.
     */
    private function container_classes() {
        // Get max width by post type
        $max_width = $this->get_max_width();

        // Build array of classes
        $classes = [
            'buddyc-single-post',
            'buddyc-max-' . $max_width,
            $this->post_type
        ];

        // Return formatted string
        return implode( ' ', $classes );
    }

    /**
     * Retrieves the content by post type.
     * 
     * @since 1.0.21
     */
    private function content_by_post_type() {
        switch ( $this->post_type ) {
            case 'buddyc_testimonial':
                return $this->testimonial();
            case 'buddyc_service':
                return $this->service();
            case 'buddyc_brief':
                return $this->brief();
            default:
                return '';
        }
    }

    /**
     * Retrieves the max width based on post type.
     * 
     * @since 1.0.21
     * 
     * @return  int The default width for the post type. Defaults to 1200.
     */
    private function get_max_width() {
        $max_widths = [
            'buddyc_testimonial'    => 800,
        ];
        return $max_widths[$this->post_type] ?? 1200;
    }

    /**
     * Retreives the allowed html for the post type.
     * 
     * @since 1.0.21
     */
    public function allowed_html() {
        // Initialize
        $allowed_html = wp_kses_allowed_html( 'post' );

        // Switch by post type
        switch ( $this->post_type ) {
            case 'buddyc_testimonial':
                // Post html
                break;
            case 'buddyc_service':
                // Post html
                break;
            case 'buddyc_brief':
                $allowed_html = buddyc_allowed_html_form();
                $allowed_html['ul'] = ['class' => []];
                $allowed_html['li'] = [];
                break;
        }
        return $allowed_html;
    }

    /**
     * Builds breadcrumbs for the post.
     * 
     * @since 1.0.21
     */
    private function build_breadcrumbs() {
        // Define separator
        $sep = '<i class="fa-solid fa-angle-right"></i>';

        // Define archive info
        $archives = [
            'buddyc_service' => [
                'link' => site_url( '/services' ),
                'label' => __( 'Services', 'buddyclients-lite' )
            ],
            'buddyc_testimonial' => [
                'link' => site_url( '/testimonials' ),
                'label' => __( 'Testimonials', 'buddyclients-lite' )
            ],
            'buddyc_brief' => [
                'link' => bp_get_loggedin_user_link() . 'groups',
                'label' => __( 'Projects', 'buddyclients-lite' )
            ],
        ];

        // Get archive info for post type
        $archive_info = $archives[$this->post_type] ?? [];

        // Initialize items with archive
        $items = [[$archive_info['label'] ?? null => $archive_info['link'] ?? null]];

        // Build service items
        if ( $this->post_type === 'buddyc_service' ) {
            $service_type = get_the_title( $this->object->service_type );
            $items[] = [$service_type => null];
        }

        // Build brief items
        if ( $this->post_type === 'buddyc_brief' ) {

            $project_id = $this->object->project_id;
            $group_name = $this->object->project_name;

            $group_obj = groups_get_group( $project_id );
            $group_link = bp_get_group_permalink( $group_obj );

            // Group
            $items[] = [$group_name => $group_link];

            // Group briefs
            $items[] = [ __( 'Briefs', 'buddyclients-lite' ) => $group_link . 'brief'];
        }

        // Add post title to breadcrumbs
        $items[] = [$this->post_title => null];

        // Loop through items and build breadcrumb        
        $links = [];
        foreach ( $items as $item ) {
            foreach ( $item as $label => $link ) {
                if ( ! $label ) {
                    continue;
                }
                $item_content = '';
                $item_content .= $link ? '<a href="' . esc_url( $link ) . '">' : '';
                $item_content .= esc_html( $label );
                $item_content .= $link ? '</a>' : '';

                $links[] =  $item_content;
            }
        }

        // Build final content
        $content = '<p class="buddyc-breadcrumbs">';
        $content .= implode( $sep, $links );
        $content .= '</p>';

        return $content;
    }

    /**
     * Outputs a single testimonial post.
     * 
     * @since 1.0.21
     */
    private function testimonial() {
        // Initialize and open wrap
        $content = '<div class="buddyc-top-margin-large">';
        
        // Image
        $content .= $this->object->image_html;

        // Content        
        $content .= '<div>';
        $content .= '<h1 class="buddyc-text-center">' . esc_html( $this->object->author_name ) . '</h1>';
        $content .= $this->object->content;
        $content .= '</div>';

        // Close wrap
        $content .= '</div>';

        return $content;
    }

    /**
     * Outputs a single service post.
     * 
     * @since 1.0.21
     */
    private function service() {
        if ( class_exists( ServicePost::class ) ) {
            $service_post = new ServicePost( $this->post_id );
            return $service_post->render();
        }
    }

    /**
     * Outputs a single brief post.
     * 
     * @since 1.0.21
     */
    private function brief() {
        // Class does not exist
        if ( ! class_exists( SingleBrief::class ) ) {
            return '<p>' . __( 'Briefs are not enabled.', 'buddyclients-lite' ) . '</p>';
        } else {
            $brief = new SingleBrief( $this->post_id );
            return $brief->display();
        }
    }
}