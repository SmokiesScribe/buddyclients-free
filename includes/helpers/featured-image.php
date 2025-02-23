<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\FileHandler;
/**
 * Sets featured image for a post.
 * 
 * @since 0.1.0
 * 
 * @param   int     $post_id    The ID of the post.
 * @param   File    $file       The File object.
 */
function buddyc_set_featured_image( $post_id, $file ) {
    if ( ! is_object( $file ) || ! $file->file_path ) {
        return;
    }
    
    // Get the file URL and upload directory
    $file_url = FileHandler::path_to_url( $file->file_path );
    $file_path = $file->file_path;
    $file_name = $file->file_name;
    
    // Check if the file exists
    if ( ! file_exists( $file->file_path ) ) {
        return new WP_Error('file_not_found', __( 'File does not exist at the provided path.', 'buddyclients-free' ) );
    }

    // Prepare attachment data
    $attachment = array(
        'guid' => $file_url,
        'post_mime_type' => mime_content_type( $file_path ),
        'post_title' => $file_name,
        'post_content' => '',
        'post_status' => 'inherit',
    );

    // Insert the attachment
    $attachment_id = wp_insert_attachment( $attachment, $file_path, $post_id );

    if ( is_wp_error( $attachment_id ) ) {
        return $attachment_id;
    }

    // Generate attachment metadata
    $attach_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
    wp_update_attachment_metadata( $attachment_id, $attach_data );

    // Set the attachment as the post's featured image
    set_post_thumbnail($post_id, $attachment_id);

    return true;
}