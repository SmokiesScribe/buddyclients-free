<?php
use BuddyClients\Components\Booking\BookedService\BookedService;
use BuddyClients\Admin\AdminTable;

/**
 * Displays Booked Services.
 * 
 * @since 0.1.0
 */
function bc_booked_services_table() {
    
    // Get Booked Services
    $booked_services = BookedService::get_all_services();
    
    // Define headers
    $headers = [
        __( 'Date', 'buddyclients-free' ),
        __( 'Status', 'buddyclients-free' ),
        __( 'Name', 'buddyclients-free' ),
        __( 'Client', 'buddyclients-free' ),
        __( 'Team Member', 'buddyclients-free' ),
        __( 'Project', 'buddyclients-free' ),
        __( 'Files', 'buddyclients-free' ),
        __( 'Update Status', 'buddyclients-free' ),
        __( 'Reassign', 'buddyclients-free' )
    ];
    
    // Define columns
    $columns = [
        'date'          => ['created_at' => 'date'],
        'status'        => ['status' => 'icons'],
        'name'          => ['name' => null],
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
        'title'     => __( 'Bookings', 'buddyclients-free' ),
        'filters'   => [
            'status'    => [
                'label'     => __( 'Status', 'buddyclients-free' ),
                'property'  =>  'status',
                'options'   => [
                    ''                          => __( 'All', 'buddyclients-free' ),
                    'pending'                   => __( 'Pending', 'buddyclients-free' ),
                    'in_progress'               => __( 'In Progress', 'buddyclients-free' ),
                    'complete'                  => __( 'Complete', 'buddyclients-free' ),
                    'cancellation_requested'    => __( 'Cancellation Requested', 'buddyclients-free' ),
                    'canceled'                  => __( 'Canceled', 'buddyclients-free' )
                ],
                'default'   => 'succeeded'
            ],
            'booking'    => [
                'label'     => __( 'Booking', 'buddyclients-free' ),
                'property'  =>  'booking_intent_id',
                'options'   => [],
                'default'   => 'succeeded'
            ]
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}