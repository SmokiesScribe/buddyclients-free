<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Admin\AdminTable;

use BuddyClients\Components\Booking\SuccessfulBooking; // TEMPORARY

/**
 * Callback to display all BookingIntents.
 * 
 * @since 0.1.0
 */
function buddyc_dashboard_content() {
    
    // Get all booking intents
    $booking_intents = BookingIntent::get_all_booking_intents();

    new SuccessfulBooking($booking_intents[0]->ID);
    
    // Define headers
    $headers = [
        __( 'Services', 'buddyclients' ),
        __( 'Date', 'buddyclients' ),
        __( 'Status', 'buddyclients' ),
        __( 'Client', 'buddyclients' ),
        __( 'Project', 'buddyclients' ),
        __( 'Total Fee', 'buddyclients' ),
        __( 'Actions', 'buddyclients' ),
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
        'title'     => __( 'Bookings', 'buddyclients' ),
        'filters'   => [
            'status'    => [
                'label'     => __( 'Status', 'buddyclients' ),
                'property'  =>  'status',
                'options'   => [
                    'succeeded'   => __( 'Succeeded', 'buddyclients' ),
                    'incomplete'  => __( 'Incomplete', 'buddyclients' ),
                ],
                'default'   => 'succeeded'
            ]
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}