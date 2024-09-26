<?php
/**
 * Template Name: Single BC Testimonial
 * Description: A custom template for a single bc_testimonial post.
 */

    // Get header
    get_header();
    
    // Initialize
    $content = '<div class="bc-single-testimonial">';
    
    // Get author info
    $author_id = get_the_author_meta('ID');
    $author_display_name = bp_core_get_user_displayname($author_id);
    $avatar_args = array(
        'item_id' => $author_id,
        'object' => 'user',
        'type' => 'thumb',
        'html' => false,
    );
    $author_avatar = bp_core_fetch_avatar($avatar_args);
    $pen_name = xprofile_get_field_data('Pen Name', $author_id);
    $last_name_field_id = bp_xprofile_lastname_field_id();
    $last_name = xprofile_get_field_data($last_name_field_id, $author_id);
    
    // Get post info
    $manual_author = get_post_meta(get_the_id(), 'testimonial_author', true);
    $featured_image = get_the_post_thumbnail_url();
    
    // Define image
    $testimonial_image = $featured_image ?? $author_avatar;
    
    // Define author name
    $author_name = $manual_author ?? $author_display_name . ' ' . $last_name;
    
    $content .= '<div class="testimonial-image">';
    $content .= '<img src="' . esc_url($testimonial_image) . '" alt="' . esc_attr($author_name) . '">';
    $content .= '</div>';
    
    $content .= '<div class="testimonial-content">';
    $content .= '<h1 class="testimonial-author">' . esc_html($author_name) . '</h1>';
    $content .= wp_kses_post(get_the_content());
    $content .= '</div>';
    
    $testimonials_page = esc_url(site_url('/testimonials/'));
    $more_testimonials_button = '<div class="more-testimonials-button-container"><a href="' . $testimonials_page . '" class="more-testimonials-button">' . esc_html__('All Testimonials', 'buddyclients') . '</a></div>';
    
    // Append the additional content to the post content
    $content .= $more_testimonials_button;
    
    // Close the container
    $content .= '</div>';
    
    // Output the content
    echo $content;
    
    // Get footer
    get_footer();
    ?>