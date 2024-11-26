<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Popup;
/**
 * Retrieves help doc content.
 * 
 * AJAX callback.
 * 
 * @since 0.1.0
 */
function buddyc_get_popup_content() {

    // Log the nonce being sent in the AJAX request
    $nonce = isset( $_POST['nonce'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ) : null;
    $nonce_action = isset( $_POST['nonceAction'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonceAction'] ) ) ) : null;

    // Verify nonce
    if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
        return;
    }

    // Get post ID from ajax
    $post_id = isset( $_POST['postId'] ) ? intval( wp_unslash( $_POST['postId'] ) ) : null;
    $url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : null;
    $raw_content = isset( $_POST['rawContent'] ) ? wp_kses_post( wp_unslash( $_POST['rawContent'] ) ) : null;
    
    // Format post content for popup
    $content = Popup::format_content( $post_id, $url, $raw_content );
    
    // Return formatted content
    echo wp_kses_post( $content );
    
    // Terminate ajax request
    wp_die();
}
add_action('wp_ajax_buddyc_get_popup_content', 'buddyc_get_popup_content'); // For logged-in users
add_action('wp_ajax_nopriv_buddyc_get_popup_content', 'buddyc_get_popup_content'); // For logged-out users

/**
 * Outputs a popup link.
 * 
 * @since 0.1.0
 * 
      * @param  int     $post_id        The ID of the post from which to retrieve the content.
      * @param  string  $link_text      Optional. The text to display. Defaults to ? icon.
      * @param  string  $url            Optional. The full url of the page to display.
 */
function buddyc_help_link( $post_id = null, $link_text = null, $url = null, $raw_content = null ) {
    return Popup::link( $post_id, $link_text, $url, $raw_content );
}

/**
 * Manually outputs a popup.
 * 
 * @since 1.0.20
 * 
 * @param   $content    string  The content of the popup.
 */
function buddyc_output_popup( $content ) {
    if ( ! empty ( $content ) ) {
        $popup = new Popup;
        $popup->output( $content );
    }
}