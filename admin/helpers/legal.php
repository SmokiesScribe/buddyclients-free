<?php
use BuddyClients\Admin\Settings as Settings;
/**
 * Handle newly published legal agreement.
 * 
 * @since 0.1.0
 */
function bc_publish_legal_agreement( $new_status, $old_status, $post ) {
    
    if ($post->post_type === 'bc_legal' && $old_status === 'draft' && $new_status === 'publish') {
        $post_id = $post->ID;
        
        // Get setting key
        $key = get_post_meta($post_id, 'bc_page_key', true);
        
        // Get current version
        $curr_version = bc_get_setting( 'legal', $key ) ?? false;

        // Set previous version
        if ($curr_version) {
            bc_update_setting( 'legal', $key . '_prev', $curr_version );
        }
        
        // Update current version
        bc_update_setting( 'legal', $key, $post_id );
        
        // Clear draft
        bc_update_setting( 'legal', $key . '_draft', false );
     }
}
add_action('transition_post_status', 'bc_publish_legal_agreement', 10, 3);