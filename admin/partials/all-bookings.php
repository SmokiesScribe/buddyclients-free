<?php
use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Admin\AdminTable;

/**
 * Callback to display all BookingIntents.
 * 
 * @since 0.1.0
 */
function bc_dashboard_content() {
    
    // Get all booking intents
    $booking_intents = BookingIntent::get_all_booking_intents();
    
    // Define headers
    $headers = [
        __( 'Date', 'buddyclients' ),
        __( 'Status', 'buddyclients' ),
        __( 'Services', 'buddyclients' ),
        __( 'Client', 'buddyclients' ),
        __( 'Client Email', 'buddyclients' ),
        __( 'Project', 'buddyclients' ),
        __( 'Total Fee', 'buddyclients' ),
        __( 'Files', 'buddyclients' ),
        __( 'Agreement', 'buddyclients' ),
        __( 'Delete', 'buddyclients' )
    ];
    
    // Define columns
    $columns = [
        'date'          => ['created_at' => 'date'],
        'status'        => ['status' => 'icons'],
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