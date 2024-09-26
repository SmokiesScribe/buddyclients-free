<?php
/**
 * Generates the payment method options.
 * 
 * @TODO Populate the settings with these options.
 * 
 * @since 0.1.0
 */
function bc_payment_method_options() {
    return [
        'paypal'            => __( 'PayPal', 'buddyclients' ),
        'digital_check'     => __( 'Digital Check', 'buddyclients' ),
        'physical_check'    => __( 'Physical Check', 'buddyclients' ),
        'venmo'             => __( 'Venmo', 'buddyclients' ),
    ];
}