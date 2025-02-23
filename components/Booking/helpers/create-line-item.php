<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\LineItems;
/**
 * Handles AJAX calls to create line items.
 * 
 * @since 0.1.0
 */
function buddyc_create_line_item() {    

    // Log the nonce being sent in the AJAX request
    $nonce = isset( $_POST['nonce'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ) : null;
    $nonce_action = isset( $_POST['nonceAction'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['nonceAction'] ) ) ) : null;

    // Verify nonce
    if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
        return;
    }

    // Ensure line items data exists and is an array
    $line_items_data = isset( $_POST['lineItems'] ) ? wp_unslash( $_POST['lineItems'] ) : null;

    // Sanitize data
    if ( is_array( $line_items_data ) ) {
        $line_items_data = array_map( function( $item ) {
            if ( is_array( $item ) ) {
                return [
                    'service_id'        => isset( $item['service_id'] ) ? intval( $item['service_id'] ) : null,
                    'adjustment_options'=> isset( $item['adjustments'] ) ? array_map( 'sanitize_text_field', (array) $item['adjustments'] ) : null,
                    'rate_count'        => isset( $item['fee_num'] ) ? intval( $item['fee_num'] ) : null,
                    'team_id'           => isset( $item['team_id'] ) ? intval( $item['team_id'] ) : null,
                    'team_member_role'  => isset( $item['team_member_role'] ) ? intval( $item['team_member_role'] ) : null,
                ];
            }
            return null; // Handle unexpected non-array items
        }, $line_items_data );
    } else {
        // Unexpected structure
        $line_items_data = null;
    }

    // Initialize array
    $line_items = [];
    
    // Loop through array and create line items
    foreach ( $line_items_data as $line_item_data ) {
        $line_items[] = new LineItems( $line_item_data );
    }
    
    // Return encoded line item objects
    echo wp_json_encode( $line_items );
    
    wp_die(); // Terminate
}
add_action('wp_ajax_buddyc_create_line_item', 'buddyc_create_line_item'); // For logged-in users
add_action('wp_ajax_nopriv_buddyc_create_line_item', 'buddyc_create_line_item'); // For logged-out users