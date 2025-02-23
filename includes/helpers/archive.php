<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\ArchiveBuilder;

/**
 * Outputs the testimonials archive from a shortcode.
 * 
 * @since 1.0.23
 */
function buddyc_testimonials_shortcode( $atts ) {
    $tags_string = $atts['tags'] ?? '';
    $tags = ! empty( $tags_string ) ? array_filter( array_map( 'trim', explode( ',', $tags_string ) ) ) : null;
    $post_type = 'buddyc_testimonial';
    $args = ! empty( $tags ) ? ['tags' => [ 'buddyc_testimonial_tag' => $tags]] : [];
    
    $max = $atts['max'] ?? null;
    if ( $max ) {
        $args['max'] = $max;
    }
    return buddyc_build_archive( $post_type, $args );
}

/**
 * Outputs the archive from a shortcode.
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
function buddyc_build_archive( $post_type, $args = [] ) {
    $archive_builder = new ArchiveBuilder( $post_type, $args );
    return $archive_builder->build();
}