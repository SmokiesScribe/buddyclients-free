<?php
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
        __( 'Date Created', 'buddyclients' ),
        __( 'Status', 'buddyclients' ),
        __( 'Type', 'buddyclients' ),
        __( 'Payee ID', 'buddyclients' ),
        __( 'Amount', 'buddyclients' ),
        __( 'Memo', 'buddyclients' ),
        __( 'Paid Date', 'buddyclients' ),
        __( 'Update Status', 'buddyclients' )
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