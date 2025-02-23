<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\Archive;

/**
 * Fetches posts and generates new instances of the Archive class.
 *
 * @since 1.0.23
 */
class ArchiveBuilder {

    /**
     * The post type for the archive.
     * 
     * @var string
     */
    public $post_type;
    
    /**
     * The maximum number of posts.
     * 
     * @var int
     */
    public $max;
    
    /**
     * An array of tags to filter posts by.
     * 
     * @var array
     */
     private $tags;
     
    /**
     * Constructor method.
     *
     * @since 1.0.23
     *
     * @param   string    $post_type    The slug of the post type.
     * @param   array     $args {
     *     An optional array of args.
     * 
     *     @type    string  $tags   An associative array of tags to filter by.
     * }
     */
    public function __construct( $post_type, $args = [] ) {
        $this->post_type = $post_type;
        $this->extract_args( $args );
    }

    /**
     * Extracts the args from the constructor.
     * 
     * @since 1.0.23
     * 
     * @param   array   $args   An array of args. See constructor.
     */
    private function extract_args( $args ) {
        $this->tags = $args['tags'] ?? null;
        $this->max = $args['max'] ?? -1;
    }

    /**
     * Builds args for the post query.
     * 
     * @since 1.0.23
     */
    private function build_args() {
        $args = ['max' => $this->max];

        // Add taxonomy tags
        if ( ! empty( $this->tags ) && is_array( $this->tags ) ) {
            foreach ( $this->tags as $tax => $slugs ) {
                $args['tax'][$tax] = $slugs;
            }
        }

        return $args;
    }

    /**
     * Fetches the posts for the Archive.
     * 
     * @since 1.0.23
     */
    private function get_posts() {
        $args = $this->build_args();
        $posts = buddyc_post_query( $this->post_type, $args );
        return $posts ?? [];
    }

    /**
     * Builds the Archive.
     * 
     * @since 1.0.23
     */
    private function archive() {
        $posts = $this->get_posts();
        return new Archive( $posts, $this->post_type );
    }

    /**
     * Outputs the content of the Archive.
     * 
     * @since 1.0.23
     */
    public function build() {
        $archive = $this->archive();
        return $archive->build();
    }
}