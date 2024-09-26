<?php
use BuddyClients\Config\LicenseHandler;
/**
 * Displays a message about the current license status.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_license_message() {

    // Initialize
    $license_message = '';
    
    // Get current license status
    $license_handler = new LicenseHandler;
    $license_handler->update_license_and_components();
    
    if ( ! isset( $license_handler->license ) || empty( $license_handler->license ) ) {
        return $license_message;
    }
    
    $license = $license_handler->license;
    
    // Check if a license key has been entered
    $license_key = bc_get_setting( 'license', 'license_key' );
    if ( empty( $license_key ) ) {
        $license_message .= __( 'Enter your license key.', 'buddyclients' );
    }
    
    // Current license
    $license_message = sprintf(
        '<h3 style="margin-top: 20px">' . __( 'Current License: %s', 'buddyclients' ) . '</h3>',
        $license->product_name
    );

    
    // Error
    if ( $license->error ) {
        $license_message .= bc_admin_icon('error') . ' ' . $license->error;
       //return $license_message;
    }
    
    // Upgrade link
    if ( strpos( $license->product, 'bc_premium' ) === false ) {
        $license_message .= bc_upgrade_link();
    }
    
    // Account link
    if ( $license_key && $license_key !== '' ) {
        $license_message .= bc_account_link();
    }
    
    // TESTING
    $license_message .= bc_upgrade_link();
   
   return $license_message;
   
}