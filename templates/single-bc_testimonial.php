<?php
use BuddyClients\Components\Testimonial\Testimonial;
/**
 * Template Name: Single BC Testimonial
 * Description: A custom template for a single buddyc_testimonial post.
 */

    // Get header
    get_header();
    
    // Initialize
    $content = '<div class="buddyc-single-testimonial">';

    // Build testimonial
    $post_id = get_the_id();
    $testimonial = new Testimonial( $post_id );
    
    $content .= $testimonial->image_html;
    
    $content .= '<div class="testimonial-content">';
    $content .= '<h1 class="testimonial-author">' . esc_html( $testimonial->author_name ) . '</h1>';
    $content .= wp_kses_post( $testimonial->content );
    $content .= '</div>';
    
    $testimonials_page = esc_url( site_url( '/testimonials/' ) );
    $more_testimonials_button = '<div class="more-testimonials-button-container"><a href="' . $testimonials_page . '" class="more-testimonials-button">' . esc_html__('All Testimonials', 'buddyclients') . '</a></div>';
    
    // Append the additional content to the post content
    $content .= $more_testimonials_button;
    
    // Close the container
    $content .= '</div>';
    
    // Output the content
    echo wp_kses_post( $content );
    
    // Get footer
    get_footer();
    ?>