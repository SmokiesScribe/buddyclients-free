<?php
use BuddyClients\Components\Legal\UserAgreement;
use BuddyClients\Admin\AdminTable;

/**
 * Displays all user agreements.
 * 
 * @since 0.1.0
 */
function bc_user_agreements_table() {
    
    // Get Booked Services
    $agreements = UserAgreement::get_all_agreements();
    
    // Define headers
    $headers = [
        __( 'Date', 'buddyclients' ),
        __( 'Status', 'buddyclients' ),
        __( 'Type', 'buddyclients' ),
        __( 'Legal Name', 'buddyclients' ),
        __( 'Email', 'buddyclients' ),
        __( 'Download PDF', 'buddyclients' ),
    ];

    // Get all user ids
    $user_ids = array_map( function( $agreement ) {
        return $agreement->user_id ?? null; // Return user_id or null if it doesn't exist
    }, $agreements );
    
    // Filter out null values
    $user_ids = array_filter( $user_ids );

    // Initialize user options
    $user_options = [
        ''                   => __( 'All', 'buddyclients' ),
    ];

    // Build user options
    if ( ! empty( $user_ids ) ) {
        foreach ( $user_ids as $user_id ) {
            $user_options[$user_id] = bp_core_get_user_displayname( $user_id );
        }
    }
    
    // Define columns
    $columns = [
        'date'          => ['created_at' => 'date'],
        'status'        => ['ID' => 'agreement_status'],
        'type'          => ['legal_type' => null],
        'legal_name'    => ['legal_name' => null],
        'email'         => ['email' => 'direct'],
        'download_pdf'  => ['pdf' => 'pdf_download'],
    ];
    
    $args = [
        'key'       => 'user_agreements',
        'headings'  => $headers,
        'columns'   => $columns,
        'items'     => $agreements,
        'title'     => __( 'User Agreements', 'buddyclients' ),
        'filters'   => [
            'type'    => [
                'label'     => __( 'Type', 'buddyclients' ),
                'property'  =>  'legal_type',
                'options'   => [
                    ''                   => __( 'All', 'buddyclients' ),
                    'affiliate'          => __( 'Affiliate', 'buddyclients' ),
                    'team'               => __( 'Team', 'buddyclients' ),
                ],
                'default'   => 'succeeded'
            ],
            'user'    => [
                'label'     => __( 'User', 'buddyclients' ),
                'property'  =>  'user_id',
                'options'   => $user_options
            ]
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}