<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Testimonial\Testimonial;
/**
 * Template Name: Custom BuddyClients Template Archive
 * Description: A custom template for the buddyc_testimonial post type archive.
 */

// Get header
get_header();

// Initialize content
$content = '';

// Get all testimonials
$testimonials = Testimonial::get_all_testimonials();

// Start building the content
$content .= '<div class="testimonial-cards buddyc-archive-container">';

// Build title
$content .= '<div class="archive-title-container">';
$content .= '<h1>' . esc_html__('Testimonials', 'buddyclients') . '</h1>';
$content .= '</div>';

// Check if testimonials are found
if ( ! empty( $testimonials) ) {

    $content .= '<div class="testimonial-cards">';

    // Loop through testimonials
    foreach ( $testimonials as $testimonial ) {
        
        // Append testimonial HTML to content variable
        $content .= '<a class="custom-testimonial-link" href="' . esc_url( get_permalink( $testimonial->ID ) ) . '">';
        
        $content .= '<div class="testimonial-card">';

        $content .= $testimonial->image_html;
        
        $content .= '<div class="testimonial-content">';
        $content .= '<h3 class="testimonial-author">' . esc_html( $testimonial->author_name ) . '</h3>';
        $content .= '<div class="testimonial-excerpt">' . esc_html( $testimonial->excerpt ) . '</div>';
        $content .= '</div>';
        
        $content .= '</div>';
        
        $content .= '</a>';

    }
    $content .= '</div>'; // Close .testimonial-cards
} else {
    $content .= '<p>' . esc_html__('No testimonials available.', 'buddyclients') . '</p>';
}

// Close the container
$content .= '</div>';

// Output the content
echo wp_kses_post( $content );

// Get footer
get_footer();
?>
