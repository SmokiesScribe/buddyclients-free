<?php
/**
 * Retrieves help docs for the search form.
 * 
 * AJAX callback.
 * 
 * @since 0.1.0
 */
function bc_get_help_docs() {
    // Retrieve search query
    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    // Get help post types
    $help_post_types = bc_get_setting('help', 'help_post_types') ?? array('post');
    $help_popup = bc_get_setting('help', 'help_popup') ?? 'both';

    // Check current user
    $user_id = get_current_user_id();
    $args = array(
        'post_type' => $help_post_types,
        'posts_per_page' => 10, // Limiting to 10 results
    );
    
    if (!$query) { // If no search term
        $args['orderby'] = 'date'; // Order by date
        $args['order'] = 'DESC'; // Descending order
    } else {
        $args['s'] = $query; // Search query
        $args['orderby'] = 'relevance'; // Order by relevance
    }

    // WP Query
    $posts = new WP_Query($args);
    
    if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            // Output post title or any other data you want to display
            echo '<a href="' . get_permalink() . '"><p>' . get_the_title() . '</p></a>';
        }
        // Append contact link to bottom of results
        if ($query && $help_popup !== 'help_only') {
            echo '<a href="#" id="bc-get-in-touch"><p>' . __('Not what you’re looking for? Get in touch.', 'buddyclients') . '</p></a>';
        }
    } else {
        if ($help_popup !== 'help_only') {
            echo '<a href="#" id="bc-get-in-touch"><p>' . __('No luck! Get in touch.', 'buddyclients') . '</p></a>';
        } else {
            echo '<p>' . __('No matching docs found.', 'buddyclients') . '</p>';
        }
    }
    
    // Restore original post data
    wp_reset_postdata();
    
    wp_die(); // This is required to terminate AJAX requests properly.
}
add_action('wp_ajax_bc_get_help_docs', 'bc_get_help_docs'); // For logged-in users
add_action('wp_ajax_nopriv_bc_get_help_docs', 'bc_get_help_docs'); // For logged-out users
