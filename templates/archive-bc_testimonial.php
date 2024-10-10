<?php
/**
 * Template Name: Custom BC Template Archive
 * Description: A custom template for the bc_template post type archive.
 */

// Get header
get_header();

// Initialize content
$content = '';

// Define query args
$args = array(
    'post_type'      => 'bc_testimonial',
    'posts_per_page' => -1,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key'     => 'order',
            'compare' => 'EXISTS', // Check if the meta key exists
        ),
        array(
            'key'     => 'order',
            'compare' => 'NOT EXISTS', // Check if the meta key does not exist
        ),
    ),
    'orderby'    => 'meta_value_num', // Order by numeric meta value
    'order'      => 'DESC', // Order in descending order
);

// Get the testimonials
$testimonials = new WP_Query( $args );

// Start building the content
$content .= '<div class="testimonial-cards bc-archive-container">';

// Build title
$content .= '<div class="archive-title-container">';
$content .= '<h1>' . esc_html__('Testimonials', 'buddyclients') . '</h1>';
$content .= '</div>';

// Check if testimonials are found
if ( $testimonials->have_posts() ) :
    $content .= '<div class="testimonial-cards">';
    // Loop through testimonials
    while ( $testimonials->have_posts() ) : $testimonials->the_post();

        // Get testimonial data
        $author_id = get_the_author_meta('ID');
        $author_display_name = bp_core_get_user_displayname($author_id);
        $avatar_args = array(
            'item_id' => $author_id,
            'object' => 'user',
            'type' => 'thumb',
            'html' => false,
        );
        $author_avatar = bp_core_fetch_avatar($avatar_args);
        $last_name_field_id = bp_xprofile_lastname_field_id();
        $last_name = xprofile_get_field_data($last_name_field_id, $author_id);

        // Get post info
        $testimonial_excerpt = get_the_excerpt();
        $testimonial_author = get_post_meta(get_the_id(),'testimonial_author', true);
        $featured_image = get_the_post_thumbnail_url( get_the_id(), 200);
        
        // Determine testimonial image
        $testimonial_image = $featured_image ?? $author_avatar;

        // Determine testimonial author name
        $author_name = $testimonial_author ?? $author_display_name . ' ' . $last_name;

        // Truncate the excerpt if too long
        $testimonial_excerpt = bc_truncate_content_by_char( $testimonial_excerpt, 150 );
        
        // Append testimonial HTML to content variable
        $content .= '<a class="custom-testimonial-link" href="' . esc_url(get_permalink()) . '">';
        
        $content .= '<div class="testimonial-card">';
        
        $content .= '<div class="testimonial-image">';
        $content .= '<img src="' . esc_url($testimonial_image) . '" alt="' . esc_attr($author_name) . '">';
        $content .= '</div>';
        
        $content .= '<div class="testimonial-content">';
        $content .= '<h3 class="testimonial-author">' . esc_html($author_name) . '</h3>';
        $content .= '<div class="testimonial-excerpt">' . esc_html($testimonial_excerpt) . '</div>';
        $content .= '</div>';
        
        $content .= '</div>';
        
        $content .= '</a>';

    endwhile;
    $content .= '</div>'; // Close .testimonial-cards
else :
    $content .= '<p>' . esc_html__('No testimonials available.', 'buddyclients') . '</p>';
endif;

// Reset post data
wp_reset_postdata();

// Close the container
$content .= '</div>';

// Output the content
echo wp_kses_post( $content );

// Get footer
get_footer();
?>
