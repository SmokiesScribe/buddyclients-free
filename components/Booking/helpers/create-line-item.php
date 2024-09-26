<?php
/**
 * Handles AJAX calls to create line items.
 * 
 * @since 0.1.0
 */
function bc_create_line_item() {
    
    $args = [
        'service_id'        => intval($_POST['service_id']),
        'adjustment_options'=> $_POST['adjustments'] ?? null,
        'rate_count'        => intval($_POST['fee_num']),
        'team_id'           => intval($_POST['team_id']) ?? null,
        'team_member_role'  => intval($_POST['team_member_role']) ?? null
    ];
    
    $line_items = new BuddyClients\Components\Booking\LineItems( $args );
    
    // Return encoded line item object
    echo json_encode($line_items);
    
    wp_die(); // Terminate
}
add_action('wp_ajax_bc_create_line_item', 'bc_create_line_item'); // For logged-in users
add_action('wp_ajax_nopriv_bc_create_line_item', 'bc_create_line_item'); // For logged-out users