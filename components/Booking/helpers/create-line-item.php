<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\LineItems;
/**
 * Handles AJAX calls to create line items.
 * 
 * @since 0.1.0
 */
function buddyc_create_line_item() {

    // Verify nonce
    $valid = buddyc_verify_ajax_nonce( 'booking_form' );
    if ( ! $valid ) return;

    // Sanitize line items
    $line_items_data = isset( $_POST['lineItems'] ) && is_array( $_POST['lineItems'] )
        ? array_map( 'buddyc_sanitize_line_item', wp_unslash( $_POST['lineItems'] ) )
        : null;

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

/**
 * Sanitizes a single line item from ajax request.
 *
 * @param   array   $item
 * @return  array|null
 */
function buddyc_sanitize_line_item( $item ) {
    if ( ! is_array( $item ) ) {
        return null;
    }

    return [
        'service_id'         => isset( $item['service_id'] ) ? intval( $item['service_id'] ) : null,
        'adjustment_options' => isset( $item['adjustments'] ) ? array_map( 'sanitize_text_field', (array) $item['adjustments'] ) : null,
        'rate_count'         => isset( $item['fee_num'] ) ? intval( $item['fee_num'] ) : null,
        'team_id'            => isset( $item['team_id'] ) ? intval( $item['team_id'] ) : null,
        'team_member_role'   => isset( $item['team_member_role'] ) ? intval( $item['team_member_role'] ) : null,
    ];
}