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
        __( 'Date', 'buddyclients-free' ),
        __( 'Status', 'buddyclients-free' ),
        __( 'Services', 'buddyclients-free' ),
        __( 'Client', 'buddyclients-free' ),
        __( 'Client Email', 'buddyclients-free' ),
        __( 'Project', 'buddyclients-free' ),
        __( 'Total Fee', 'buddyclients-free' ),
        __( 'Files', 'buddyclients-free' ),
        __( 'Agreement', 'buddyclients-free' ),
        __( 'Delete', 'buddyclients-free' )
    ];
    
    // Define columns
    $columns = [
        'date'          => ['created_at' => 'date'],
        'status'        => ['status' => 'booking_intent_status'],
        'services'      => ['service_names' => 'service_names_link'],
        'client_id'     => ['client_id' => 'user_link'],
        'client_email'  => ['client_email' => null],
        'project_id'    => ['project_id' => 'group_link'],
        'total_fee'     => ['total_fee' => 'usd'],
        'files'         => ['file_ids' => 'files'],
        'terms_pdf'     => ['terms_pdf' => 'terms_pdf'],
        'delete'        => ['ID' => 'delete']
    ];
    
    $args = [
        'key'       => 'booking_intents',
        'headings'  => $headers,
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