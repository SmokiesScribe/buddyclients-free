<?php
use BuddyClients\Components\Booking\BookedService\Payment;
use BuddyClients\Admin\AdminTable;

/**
 * Callback to display all Payments.
 * 
 * @since 0.1.0
 */
function bc_payments_list() {
    
    // Get all payments
    $payments = Payment::get_all_payments();
    
    // Define headers
    $headers = [
        __( 'Date Created', 'buddyclients-free' ),
        __( 'Status', 'buddyclients-free' ),
        __( 'Type', 'buddyclients-free' ),
        __( 'Payee ID', 'buddyclients-free' ),
        __( 'Amount', 'buddyclients-free' ),
        __( 'Memo', 'buddyclients-free' ),
        __( 'Paid Date', 'buddyclients-free' ),
        __( 'Update Status', 'buddyclients-free' )
    ];
    
    // Define columns
    $columns = [
        'date'          => ['created_at' => 'date'],
        'status'        => ['status' => 'icons'],
        'type'          => ['type' => null],
        'payee_id'      => ['payee_id' => 'user_link'],
        'amount'        => ['amount' => 'usd'],
        'memo'          => ['memo' => 'copy_memo'],
        'paid_date'     => ['paid_date' => 'date'],
        'update_status' => ['status' => 'payment_form']
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