<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Testimonial\Testimonial;
use BuddyClients\Includes\Archive;
/**
 * Template Name: Dynamic BuddyClients Archive Template
 * Description: Dynamically generates a custom template for a post type archive.
 */

// Get header
get_header();

$queried_object = get_queried_object();
$post_type = $queried_object->name;
$posts = buddyc_post_query( $post_type );

$archive = new Archive( $posts, $post_type );
$content = $archive->build();

// Output the content
$allowed_html_form = buddyc_allowed_html_form();
$allowed_html_post = wp_kses_allowed_html( 'post' );
$allowed_html = array_merge( $allowed_html_form, $allowed_html_post );

//echo wp_kses( $content, $allowed_html );
echo wp_kses( $content, $allowed_html );

// Get footer
get_footer();
