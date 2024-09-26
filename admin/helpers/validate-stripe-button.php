<?php
/**
 * Generates a button to validate Stripe keys.
 * 
 * @since 0.1.0
 * 
 * @param   string  $settings_key   The key of the settings group.
 */
function bc_validate_stripe_button( $settings_key ) {

    // Exit if not stripe settings
    if ( $settings_key !== 'stripe' ) {
        return;
    }
    
    // Check if we're validating
    if ( isset( $_GET['validate'] ) && $_GET['validate'] === 'stripe' ) {
        bc_admin_loading( __( 'Validating Stripe Keys...', 'buddyclients' ) );
    }
    
    // Generate button
    $link = admin_url( 'admin.php?page=bc-stripe-settings&validate=stripe' );
    echo '<a href="' . $link . '"><button type="button" class="button button-secondary">' . __( 'Validate Stripe Keys', 'buddyclients' ) . '</button></a>';
    
    // Hide loading message
    echo '<script>bcLoadingIndicator( false );</script>';

}
add_action( 'bc_before_settings', 'bc_validate_stripe_button', 10, 1 );

/**
 * Generates a button to check for new payments.
 * 
 * @since 0.3.1
 * 
 * @param   string  $settings_key   The key of the settings group.
 */
function bc_check_for_payments_button( $settings_key ) {
    
    // Initialize message
    $message = '';

    // Exit if not stripe settings
    if ( $settings_key !== 'stripe' ) {
        return;
    }
    
    // Check if we're validating
    if ( isset( $_GET['validate'] ) && $_GET['validate'] === 'payments' ) {
        bc_admin_loading( __( 'Checking for New Payments...', 'buddyclients' ) );
        // Check for payments and return count
        $count = bc_check_for_payments();
        // Build message
        if ( $count ) {
            $message = $count . ' ' . __( 'Bookings Updated', 'buddyclients' );
        }
    }
    
    // Generate button
    $link = admin_url( 'admin.php?page=bc-stripe-settings&validate=payments' );
    echo '<br><br><a href="' . $link . '"><button type="button" class="button button-secondary">' . __( 'Check for Payments', 'buddyclients' ) . '</button></a>';
    echo '<br>' . $message;
    
    // Hide loading message
    echo '<style>#bc-admin-loading {display: none;}</style>';
    
}
add_action( 'bc_before_settings', 'bc_check_for_payments_button', 10, 1 );