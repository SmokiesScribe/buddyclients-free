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
        __( 'Payee', 'buddyclients-free' ),
        __( 'Date Created', 'buddyclients-free' ),
        __( 'Status', 'buddyclients-free' ),
        __( 'Type', 'buddyclients-free' ),
        __( 'Amount', 'buddyclients-free' ),
        __( 'Payment Method', 'buddyclients-free' ),
        __( 'Memo', 'buddyclients-free' ),
        __( 'Paid Date', 'buddyclients-free' ),
        __( 'Update Status', 'buddyclients-free' )
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
        'title'     => __( 'Payments', 'buddyclients-free' ),
        'filters'   => [
            'type'    => [
                'label'     => __( 'Type', 'buddyclients-free' ),
                'property'  => 'type',
                'options'   => [
                    ''              => __( 'All', 'buddyclients-free' ),
                    'team'          => __( 'Team', 'buddyclients-free' ),
                    'affiliate'     => __( 'Affiliate', 'buddyclients-free' ),
                    'sales'         => __( 'Sales', 'buddyclients-free' ),
                ],
                'default'   => ''
            ],
            'payment_status'    => [
                'label'     => __( 'Status', 'buddyclients-free' ),
                'property'  => 'status',
                'options'   => [
                    ''          => __( 'All', 'buddyclients-free' ),
                    'pending'   => __( 'Pending', 'buddyclients-free' ),
                    'eligible'  => __( 'Eligible', 'buddyclients-free' ),
                    'paid'      => __( 'Paid', 'buddyclients-free' ),
                ],
                'default'   => ''
            ],
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}