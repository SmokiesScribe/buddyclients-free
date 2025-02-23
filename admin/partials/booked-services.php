<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookedService\BookedService;
use BuddyClients\Admin\AdminTable;

/**
 * Displays Booked Services.
 * 
 * @since 0.1.0
 */
function buddyc_booked_services_table() {
    
    // Get Booked Services
    $booked_services = BookedService::get_all_services();
    
    // Define headers
    $headers = [
        __( 'Name', 'buddyclients' ),
        __( 'Date', 'buddyclients' ),
        __( 'Status', 'buddyclients' ),
        __( 'Client', 'buddyclients' ),
        __( 'Team Member', 'buddyclients' ),
        __( 'Project', 'buddyclients' ),
        __( 'Files', 'buddyclients' ),
        __( 'Update Status', 'buddyclients' ),
        __( 'Reassign', 'buddyclients' )
    ];
    
    // Define columns
    $columns = [
        'name'          => ['name' => null],
        'date'          => ['created_at' => 'date'],
        'status'        => ['status' => 'icons'],
        'client_id'     => ['client_id' => 'user_link'],
        'team_id'       => ['team_id' => 'user_link'],
        'project_id'    => ['project_id' => 'group_link'],
        'files'         => ['file_ids' => 'files'],
        'updated_status'=> ['status' => 'status_form'],
        'reassign'      => ['team_id' => 'reassign_form']
    ];
    
    $args = [
        'key'       => 'booking_intents',
        'headings'  => $headers,
        'columns'   => $columns,
        'items'     => $booked_services,
        'title'     => __( 'Bookings', 'buddyclients' ),
        'filters'   => [
            'status'    => [
                'label'     => __( 'Status', 'buddyclients' ),
                'property'  =>  'status',
                'options'   => [
                    ''                          => __( 'All', 'buddyclients' ),
                    'pending'                   => __( 'Pending', 'buddyclients' ),
                    'in_progress'               => __( 'In Progress', 'buddyclients' ),
                    'complete'                  => __( 'Complete', 'buddyclients' ),
                    'cancellation_requested'    => __( 'Cancellation Requested', 'buddyclients' ),
                    'canceled'                  => __( 'Canceled', 'buddyclients' )
                ],
                'default'   => 'succeeded'
            ],
            'booking'    => [
                'label'     => __( 'Booking', 'buddyclients' ),
                'property'  =>  'booking_intent_id',
                'options'   => [],
                'default'   => 'succeeded'
            ]
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}