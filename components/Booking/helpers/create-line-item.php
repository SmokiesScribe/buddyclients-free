<?php
use BuddyClients\Components\Booking\LineItems;
/**
 * Handles AJAX calls to create line items.
 * 
 * @since 0.1.0
 */
function bc_create_line_item() {

    // Log the nonce being sent in the AJAX request
    $nonce = isset( $_POST['nonce'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ) : null;
    $nonce_action = isset( $_POST['nonceAction'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonceAction'] ) ) ) : null;

    // Verify nonce
    if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
        return;
    }
    
    $args = [
        'service_id'        => isset($_POST['service_id']) ? intval(wp_unslash($_POST['service_id'])) : null,
        'adjustment_options'=> isset($_POST['adjustments']) ? array_map('sanitize_text_field', wp_unslash($_POST['adjustments'])) : null,
        'rate_count'        => isset($_POST['fee_num']) ? intval(wp_unslash($_POST['fee_num'])) : null,
        'team_id'           => isset($_POST['team_id']) ? intval(wp_unslash($_POST['team_id'])) : null,
        'team_member_role'  => isset($_POST['team_member_role']) ? intval(wp_unslash($_POST['team_member_role'])) : null
    ];
    
    $line_items = new LineItems($args);
    
    // Return encoded line item object
    echo wp_json_encode( $line_items );
    
    wp_die(); // Terminate
}
add_action('wp_ajax_bc_create_line_item', 'bc_create_line_item'); // For logged-in users
add_action('wp_ajax_nopriv_bc_create_line_item', 'bc_create_line_item'); // For logged-out users