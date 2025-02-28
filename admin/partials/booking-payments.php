<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingPayment;
use BuddyClients\Admin\AdminTable;

/**
 * Displays the admin table for booking payments.
 * 
 * @since 1.0.27
 */
function buddyc_booking_payments_table() {
    
    // Get BookingPayment objects
    $booking_payments = BookingPayment::get_all_active_payments();
    
    // Define headers
    $headers = [
        __( 'Client', 'buddyclients-free' ),
        __( 'Created', 'buddyclients-free' ),
        __( 'Status', 'buddyclients-free' ),
        __( 'Type', 'buddyclients-free' ),
        __( 'Payment Method', 'buddyclients-free' ), // Stripe sends an id
        __( 'Amount', 'buddyclients-free' ),
        __( 'Amount Received', 'buddyclients-free' ),
        __( 'Date Paid', 'buddyclients-free' ),
        __( 'Actions', 'buddyclients-free' ),
    ];
    
    // Define columns
    $columns = [
        'client_id'         => ['client_id' => 'user_link'],
        'date'              => ['created_at' => 'date_time'],
        'status'            => ['ID' => 'booking_payment_status'],
        'type'              => ['type_label' => 'direct'],
        'payment_method'    => ['payment_method' => 'payment_method'],
        'amount'            => ['amount' => 'usd'],
        'amount_received'   => ['amount_received' => 'usd'],
        'paid_at'           => ['paid_at' => 'date_time'],
        'actions'           => ['ID' => 'booking_payment_actions'],
    ];
    
    $args = [
        'key'       => 'booking_payments',
        'headings'  => $headers,
        'columns'   => $columns,
        'items'     => $booking_payments,
        'title'     => __( 'Booking Payments', 'buddyclients-free' ),
        'filters'   => [
            'status'    => [
                'label'     => __( 'Status', 'buddyclients-free' ),
                'property'  =>  'status',
                'options'   => [
                    ''          => __( 'All', 'buddyclients-free' ),
                    'paid'      => __( 'Paid', 'buddyclients-free' ),
                    'unpaid'    => __( 'Unpaid', 'buddyclients-free' )
                ],
                'default'   => ''
            ],
            'type'    => [
                'label'     => __( 'Type', 'buddyclients-free' ),
                'property'  =>  'type',
                'options'   => [
                    ''          => __( 'All', 'buddyclients-free' ),
                    'deposit'   => __( 'Deposit', 'buddyclients-free' ),
                    'final'     => __( 'Final Payment', 'buddyclients-free' )
                ],
                'default'   => ''
            ],
            'booking_intent'    => [
                'label'     => __( 'Booking Intent', 'buddyclients-free' ),
                'property'  =>  'booking_intent_id',
                'options'   => [],
                'default'   => ''
            ]
        ],
    ];
    
    new AdminTable( $args );
    
    return;
}