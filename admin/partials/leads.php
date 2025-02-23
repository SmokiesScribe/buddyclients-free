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
        __( 'Email', 'buddyclients' ),
        __( 'Name', 'buddyclients' ),
        __( 'Status', 'buddyclients' ),
        __( 'Date', 'buddyclients' ),
        __( 'Interests', 'buddyclients' ),
        __( 'Auto Email', 'buddyclients' ),        
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
            'label'     => __( 'Status', 'buddyclients' ),
            'property'  => 'status',
            'options'   => [
                ''       => __( 'All', 'buddyclients' ),
                'active' => __( 'Active', 'buddyclients' ),
                'won'    => __( 'Won', 'buddyclients' ),
                'lost'   => __( 'Lost', 'buddyclients' )
            ],
        ]
    ];
    
    $args = [
        'key'               => 'users',
        'headings'          => $headers,
        'columns'           => $columns,
        'items'             => $leads,
        'title'             => __( 'Leads', 'buddyclients' ),
        'items_per_page'    => 20,
        'filters'           => $filters
    ];
    
    new AdminTable( $args );
    
    return;
}