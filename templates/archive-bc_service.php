<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Template Name: Custom BuddyClients Service Archive
 * Description: A custom template for the buddyc_service post type archive.
 */

// Get header
get_header();

// Initialize content
$content = '';

$args = array(
    'post_type'      => 'buddyc_service_type',
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

// Get the services
$types = get_posts($args);

// Check if any terms are returned
if (empty($types) || is_wp_error($types)) {
    $types = array();
}

// Add empty string to array
$types[] = '';

// Start building the content
$content .= '<div class="rate-cards buddyc-archive-container">';

// Build title
$content .= '<div class="archive-title-container">';
$content .= '<h1>' . esc_html__('Services', 'buddyclients') . '</h1>';
$content .= '</div>';

// Loop through each type
foreach ($types as $type) {
    if ($type !== '') {
        $type_id = $type->ID;
        $type_name = $type->post_title;
        $type_slug = $type->name;
    }

    $service_args = array(
        'post_type'      => 'buddyc_service',
        'posts_per_page' => -1, // Retrieve all posts
    );

    if ($type !== '') {
        $service_args['meta_query'] = array(
            array(
                'key' => 'service_type',
                'value'    => $type_id,
            ),
        );
    } else {
        $service_args['meta_query'] = array(
            array(
                'key' => 'service_type',
                'value' => '',
            ),
        );
    }

    // Get the services
    $services = get_posts($service_args);

    if (!$services) {
        continue; // exit if no posts found
    } else {
        foreach ($services as $service) {
            $rate_title = $service->post_title; // Corrected to use post_title
            $post_id = $service->ID; // Corrected to use ID
            $featured_image = get_the_post_thumbnail_url($post_id);

            // Check if hidden @todo

            // Get service type
            $type = get_post_meta($post_id, 'service_type', true);

            // Build service type label
            if ($type) {
                $type_id = get_the_id($type);
                $type_name = get_the_title($type);
                $service_type_label = '<p class="service-type-label service-type-label-' . esc_attr($type_id) . '">' . esc_html($type_name) . '</p>';
            } else {
                $service_type_label = '<p></p>';
            }

            // Truncate the content to a specified number of words (e.g., 25 words)
            $post_info = buddyc_truncate_content($service->post_content, 25);

            // Add the rate card
            $content .= '
                <div class="rate-post">
                    <a class="custom-rate-link" href="' . esc_url(get_the_permalink($post_id)) . '">
                        <div class="rate-card">
                            <div class="rate-content">
                                <h3 class="rate-title">' . esc_html($rate_title) . '</h3>'
                                . $service_type_label .
                                '<div class="rate-excerpt">' . esc_html($post_info) . '<span> <i>' . esc_html__('Learn more.', 'buddyclients') . '</i></span></div>
                            </div>
                        </div>
                    </a>
                </div>';
        }
    }

    wp_reset_postdata(); // Restore the global post data
}

// Close the container
$content .= '</div>';

// Output the content
echo wp_kses_post( $content );

// Get footer
get_footer();
?>