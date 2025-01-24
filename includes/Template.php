<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\Archive;

/**
 * Defines custom templates for single posts and archives.
 *
 * @since 1.0.21
 */
class Template {

    /**
     * The slug of the post type.
     * 
     * @var string
     */
    private $post_type;

    /**
     * The type of page.
     * Accepts 'single' or 'archive'. 
     * 
     * @var string
     */
    private $page_type;

    /**
     * Whether we are replacing content.
     * 
     * @var bool
     */
    private $replacing_content;

    /**
     * Constructor method.
     * 
     * @since 1.0.21
     * 
     * @param   string  $post_type  The slug of the post type.
     * @param   string  $page_type  The type of page ('single' or 'archive').
     */
    public function __construct( $post_type, $page_type ) {
        $this->post_type = $post_type;
        $this->page_type = $page_type;
    }

    /**
     * Magic setter.
     * 
     * @since 1.0.21
     */
    public function __set( $name, $value ) {
        // Dynamically assign the value
        $this->properties[$name] = $value;
    }

    /**
     * Renders the template. 
     * 
     * @since 1.0.21
     */
    public function render() {
        return $this->get_template_content();
    }

    /**
     * Retrieves the template content. 
     * 
     * @since 1.0.21
     */
    private function get_template_content() {
        switch ( $this->page_type ) {
            case 'archive':
                return $this->archive_template( $this->post_type );
            case 'single':
                return $this->single_template( $this->post_type );
        }        
    }

    /**
     * Outputs the archive template.
     * 
     * @since 1.0.21
     * 
     * @param   string  $post_type  The slug of the post type.
     */
    private function archive_template( $post_type ) {
        get_header();

        $posts = buddyc_post_query( $post_type );

        $archive = new Archive( $posts, $post_type );
        $content = $archive->build( $hide_title = false );
        
        // Output the content
        echo wp_kses_post( $content );

        get_footer();
    }
    
}