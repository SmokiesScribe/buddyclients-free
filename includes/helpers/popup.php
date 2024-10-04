<?php
use BuddyClients\Includes\Popup;
/**
 * Get help doc content.
 * 
 * AJAX callback.
 * 
 * @since 0.1.0
 */
function bc_get_popup_content() {
    
    // Get post ID from ajax
    $post_id = isset( $_POST['postId'] ) ? intval( wp_unslash( $_POST['postId'] ) ) : null;
    $url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : null;
    $raw_content = isset( $_POST['rawContent'] ) ? sanitize_text_field( wp_unslash( $_POST['rawContent'] ) ) : null;
    
    // Format post content for popup
    $content = Popup::format_content( $post_id, $url, $raw_content );
    
    // Return formatted content
    echo $content;
    
    // Terminate ajax request
    wp_die();
}
add_action('wp_ajax_bc_get_popup_content', 'bc_get_popup_content'); // For logged-in users
add_action('wp_ajax_nopriv_bc_get_popup_content', 'bc_get_popup_content'); // For logged-out users