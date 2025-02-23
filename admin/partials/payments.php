<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookedService\Payment;
use BuddyClients\Admin\AdminTable;

/**
 * Callback to display all Payments.
 * 
 * @since 0.1.0
 */
function buddyc_payments_list() {
    
    // Get all payments
    $payments = Payment::get_all_payments();
    
    // Define headers
    $headers = [
        __( 'Payee', 'buddyclients' ),
        __( 'Date Created', 'buddyclients' ),
        __( 'Status', 'buddyclients' ),
        __( 'Type', 'buddyclients' ),
        __( 'Amount', 'buddyclients' ),
        __( 'Payment Method', 'buddyclients' ),
        __( 'Memo', 'buddyclients' ),
        __( 'Paid Date', 'buddyclients' ),
        __( 'Update Status', 'buddyclients' )
    ];
    
    // Define columns
    $columns = [
        'payee_id'                  => ['payee_id' => 'user_link'],
        'date'                      => ['created_at' => 'date_time'],
        'status'                    => ['status' => 'icons'],
        'type'                      => ['type' => null],
        'amount'                    => ['amount' => 'usd'],
        'legal_payment_preference'  => ['payee_id' => 'legal_payment_preference'],
        'memo'                      => ['memo' => 'copy_memo'],
        'paid_date'                 => ['paid_date' => 'date_time'],
        'update_status'             => ['status' => 'payment_form']
    ];
    
    $args = [
        'key'       => 'payments',
        'headings'  => $headers,
        'columns'   => $columns,
        'items'     => $payments,
        'title'     => __( 'Payments', 'buddyclients' ),
        'filters'   => [
            'type'    => [
                'label'     => __( 'Type', 'buddyclients' ),
                'property'  => 'type',
                'options'   => [
                    ''              => __( 'All', 'buddyclients' ),
                    'team'          => __( 'Team', 'buddyclients' ),
                    'affiliate'     => __( 'Affiliate', 'buddyclients' ),
                    'sales'         => __( 'Sales', 'buddyclients' ),
                ],
                'default'   => ''
            ],
            'payment_status'    => [
                'label'     => __( 'Status', 'buddyclients' ),
                'property'  => 'status',
                'options'   => [
                    ''          => __( 'All', 'buddyclients' ),
                    'pending'   => __( 'Pending', 'buddyclients' ),
                    'eligible'  => __( 'Eligible', 'buddyclients' ),
                    'paid'      => __( 'Paid', 'buddyclients' ),
                ],
                'default'   => ''
            ],
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}