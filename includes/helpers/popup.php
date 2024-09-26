<?php
/**
 * Get help doc content.
 * 
 * AJAX callback.
 * 
 * @since 0.1.0
 */
function bc_get_popup_content() {
    
    // Get post ID from ajax
    $post_id = $_POST['postId'];
    $url = $_POST['url'];
    $raw_content = $_POST['rawContent'];
    
    // Format post content for popup
    $content = BuddyClients\Includes\Popup::format_content( $post_id, $url, $raw_content );
    
    // Return formatted content
    echo $content;
    
    // Terminate ajax request
    wp_die();
}
add_action('wp_ajax_bc_get_popup_content', 'bc_get_popup_content'); // For logged-in users
add_action('wp_ajax_nopriv_bc_get_popup_content', 'bc_get_popup_content'); // For logged-out users