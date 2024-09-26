<?php
/**
 * Registers custom archive template files.
 * 
 * @since 0.1.0
 */
function bc_archive_template( $template ) {
    
    $post_types = [
        'bc_service',
        'bc_testimonial'
    ];
    
    // Check if it's an archive for one of the post types
    if ( is_post_type_archive( $post_types ) ) {
        
        // Get the queried object
        $queried_object = get_queried_object();
        
        // Check if this is a post type archive
        if ( is_post_type_archive() && isset( $queried_object->name ) ) {
            // Get the post type
            $post_type = $queried_object->name;
        
            // Specify the path to your custom template file within the plugin directory
            $custom_template = BC_PLUGIN_DIR . 'templates/archive-' . $post_type . '.php';
    
            // Check if the custom template file exists
            if (file_exists($custom_template)) {
                return $custom_template; // Use the custom template file
            }
        }
    }

    // For other cases, return the original template
    return $template;
}
add_filter('template_include', 'bc_archive_template');

/**
 * Registers custom single post template files.
 * 
 * @since 0.1.0
 */
function bc_single_post_template( $template ) {
    
    // Define an array of post types for which you want to create custom single post templates
    $post_types = array(
        'bc_service',
        'bc_testimonial',
        'bc_brief'
    );
    
    // Check if it's a single post of one of the specified post types
    if ( is_singular( $post_types ) ) {
        
        // Get the queried object
        $queried_object = get_queried_object();
        
        // Check if this is a single post
        if ( is_singular() && isset( $queried_object->post_type ) ) {
            // Get the post type
            $post_type = $queried_object->post_type;
        
            // Specify the path to your custom template file within the plugin directory
            $custom_template = BC_PLUGIN_DIR . 'templates/single-' . $post_type . '.php';
    
            // Check if the custom template file exists
            if ( file_exists( $custom_template ) ) {
                return $custom_template; // Use the custom template file
            }
        }
    }

    // For other cases, return the original template
    return $template;
}
add_filter( 'template_include', 'bc_single_post_template' );

