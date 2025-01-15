<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Admin\AdminTable;

/**
 * Callback to display all BookingIntents.
 * 
 * @since 0.1.0
 */
function buddyc_dashboard_content() {
    
    // Get all booking intents
    $booking_intents = BookingIntent::get_all_booking_intents();
    
    // Define headers
    $headers = [
        __( 'Services', 'buddyclients-free' ),
        __( 'Date', 'buddyclients-free' ),
        __( 'Status', 'buddyclients-free' ),
        __( 'Client', 'buddyclients-free' ),
        __( 'Project', 'buddyclients-free' ),
        __( 'Total Fee', 'buddyclients-free' ),
        __( 'Actions', 'buddyclients-free' ),
    ];
    
    // Define columns
    $columns = [
        'services'              => ['service_names' => 'service_names_link'],
        'date'                  => ['created_at' => 'date_time'],
        'status'                => ['status' => 'booking_intent_status'],
        'client_id'             => ['client_id' => 'booking_user_link_email'],
        'project_id'            => ['project_id' => 'group_link'],
        'total_fee'             => ['total_fee' => 'usd'],
        'actions'               => ['ID' => 'booking_actions']
    ];
    
    // Define classes for headers and columns
    $classes = [
        'column-primary'   => ['Date' => 'date'],
        'secondary' => [],
        'tertiary'  => [],
    ];
    
    $args = [
        'key'       => 'booking_intents',
        'headings'  => $headers,
        'classes'   => $classes,
        'columns'   => $columns,
        'items'     => $booking_intents,
        'title'     => __( 'Bookings', 'buddyclients-free' ),
        'filters'   => [
            'status'    => [
                'label'     => __( 'Status', 'buddyclients-free' ),
                'property'  =>  'status',
                'options'   => [
                    'succeeded'   => __( 'Succeeded', 'buddyclients-free' ),
                    'incomplete'  => __( 'Incomplete', 'buddyclients-free' ),
                ],
                'default'   => 'succeeded'
            ]
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}