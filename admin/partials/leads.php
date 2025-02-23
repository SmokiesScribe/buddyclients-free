<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminTable;
use BuddyClients\Components\Contact\Lead\Lead;

/**
 * Callback to display all team and clients.
 * 
 * @since 0.2.6
 */
function buddyc_leads_list() {
    
    // Get all leads
    $leads = Lead::get_all_leads();
    
    // Define headers
    $headers = [
        __( 'Email', 'buddyclients-free' ),
        __( 'Name', 'buddyclients-free' ),
        __( 'Status', 'buddyclients-free' ),
        __( 'Date', 'buddyclients-free' ),
        __( 'Interests', 'buddyclients-free' ),
        __( 'Auto Email', 'buddyclients-free' ),        
    ];
    
    // Define columns
    $columns = [
        'email'             => ['email' => 'direct'],
        'name'              => ['name' => null],
        'status'            => ['status' => 'lead_status'],
        'date'              => ['created_at' => 'date_time'],
        'interests'         => ['interests' => 'direct'],
        'auto-email'        => ['sent' => 'lead_auto_email'],
    ];
    
    // Define filters
    $filters = [
        'status'    => [
            'label'     => __( 'Status', 'buddyclients-free' ),
            'property'  => 'status',
            'options'   => [
                ''       => __( 'All', 'buddyclients-free' ),
                'active' => __( 'Active', 'buddyclients-free' ),
                'won'    => __( 'Won', 'buddyclients-free' ),
                'lost'   => __( 'Lost', 'buddyclients-free' )
            ],
        ]
    ];
    
    $args = [
        'key'               => 'users',
        'headings'          => $headers,
        'columns'           => $columns,
        'items'             => $leads,
        'title'             => __( 'Leads', 'buddyclients-free' ),
        'items_per_page'    => 20,
        'filters'           => $filters
    ];
    
    new AdminTable( $args );
    
    return;
}