<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Legal\UserAgreement;
use BuddyClients\Admin\AdminTable;

/**
 * Displays all user agreements.
 * 
 * @since 0.1.0
 */
function buddyc_user_agreements_table() {
    
    // Get Booked Services
    $agreements = UserAgreement::get_all_agreements();
    
    // Define headers
    $headers = [
        __( 'Legal Name', 'buddyclients-free' ),
        __( 'Type', 'buddyclients-free' ),
        __( 'Date', 'buddyclients-free' ),
        __( 'Status', 'buddyclients-free' ),  
        __( 'Email', 'buddyclients-free' ),
        __( 'Download PDF', 'buddyclients-free' ),
    ];
    
    // Define columns
    $columns = [
        'legal_name'    => ['legal_name' => null],
        'type'          => ['legal_type' => null],
        'date'          => ['created_at' => 'date_time'],
        'status'        => ['ID' => 'agreement_status'],
        'email'         => ['email' => 'direct'],
        'download_pdf'  => ['pdf' => 'pdf_download'],
    ];
    
    $args = [
        'key'       => 'user_agreements',
        'headings'  => $headers,
        'columns'   => $columns,
        'items'     => $agreements,
        'title'     => __( 'User Agreements', 'buddyclients-free' ),
        'filters'   => [
            'type'    => [
                'label'     => __( 'Type', 'buddyclients-free' ),
                'property'  =>  'legal_type',
                'options'   => [
                    ''                   => __( 'All', 'buddyclients-free' ),
                    'affiliate'          => __( 'Affiliate', 'buddyclients-free' ),
                    'team'               => __( 'Team', 'buddyclients-free' ),
                    'client'             => __( 'Client', 'buddyclients-free' ),
                ],
                'default'   => 'succeeded'
            ],
            'user'    => [
                'label'     => __( 'User', 'buddyclients-free' ),
                'property'  =>  'user_id',
                'options'   => buddyc_build_agreement_user_options( $agreements )
            ]
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}

/**
 * Builds the array of user options to filter
 * the legal user agreements.
 * 
 * @since 1.0.25
 * 
 * @param   array   $agreements The array of UserAgreement objects.
 */
function buddyc_build_agreement_user_options( $agreements ) {
    // Get all user ids
    $user_ids = array_map( function( $agreement ) {
        return $agreement->user_id ?? null; // Return user_id or null if it doesn't exist
    }, $agreements );
    
    // Filter out null values
    $user_ids = array_filter( $user_ids );

    // Initialize user options
    $user_options = ['' => __( 'All', 'buddyclients-free' ) ];

    // Build user options
    if ( ! empty( $user_ids ) ) {
        foreach ( $user_ids as $user_id ) {
            $user_options[$user_id] = bp_core_get_user_displayname( $user_id );
        }
    }
    return $user_options;
}