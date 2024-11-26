<?php
use BuddyClients\Admin\AdminTable;

/**
 * Callback to display all team and clients.
 * 
 * @since 0.2.6
 */
function buddyc_user_list() {
    
    // Initialize array
    $users_array = [];
    
    // Get all users
    $users = bp_core_get_users(array(
        'per_page' => false,
    ));
    
    // Loop through users
    foreach ($users['users'] as $user) {
        // Determine type and skip iteration if user type is unknown
        if ( buddyc_is_team( $user->ID ) ) {
            $type = 'team';
        } else if ( buddyc_is_client( $user->ID ) ) {
            $type = 'client';
        } else if ( function_exists( 'be_is_faculty' ) && be_is_faculty( $user->ID ) ) {
            $type = 'faculty';
        } else if ( function_exists( 'be_is_attendee' ) && be_is_attendee( $user->ID ) ) {
            $type = 'attendee';
        } else {
            continue;
        }
        
        // Build array item
        $users_array[$user->ID] = [
            'ID'                => $user->ID,
            'user_email'        => $user->user_email,
            'date_registered'   => $user->user_registered,
            'fullname'          => $user->fullname,
            'type'              => $type  // Include type in the array
        ];
    }
    
    // Sort the users_array by 'type'
    usort( $users_array, function( $a, $b ) {
        return strcmp( $b['type'], $a['type'] );
    });
    
    // Define headers
    $headers = [
        __( 'Date Registered', 'buddyclients-free' ),
        __( 'User', 'buddyclients-free' ),
        __( 'Email', 'buddyclients-free' ),
        __( 'Type', 'buddyclients-free' ),
        __( 'Agreements', 'buddyclients-free' )
    ];
    
    // Define columns
    $columns = [
        'date'              => ['date_registered' => 'date'],
        'id'                => ['ID' => 'user_link'],
        'user_email'        => ['user_email' => 'direct'],
        'type'              => ['type' => null],
        'team_agreement'    => ['ID' => 'agreements']
    ];
    
    // Define filters
    $filters = [
        'type'    => [
            'label'     => __( 'Type', 'buddyclients-free' ),
            'property'  => 'type',
            'options'   => [
                'team'   => __( 'Team', 'buddyclients-free' ),
                'client' => __( 'Client', 'buddyclients-free' ),
            ],
            'default'   => 'team'
        ]
    ];
    
    $args = [
        'key'               => 'users',
        'headings'          => $headers,
        'columns'           => $columns,
        'items'             => $users_array,
        'title'             => __( 'Users', 'buddyclients-free' ),
        'items_per_page'    => 20,
        'filters'           => $filters
    ];
    
    new AdminTable( $args );
    
    return;
}