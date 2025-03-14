<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Config\LicenseHandler;
/**
 * Displays a message about the current license status.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function buddyc_license_message() {

    // Initialize
    $license_message = '';
    
    // Get current license    
    $license = buddyc_get_license();

    // No license found
    if ( empty( $license ) ) {
        return buddyc_upgrade_link();
    }
    
    // Check if a license key has been entered
    $license_key = buddyc_get_setting( 'license', 'license_key' );
    if ( empty( $license_key ) ) {
        $license_message .= __( 'Enter your license key.', 'buddyclients-free' );
    }
    
    // Current license
    $license_message = sprintf(
        /* translators: %s: the name of the licensed BuddyClients product */
        '<h3>' . __( 'Current License: %s', 'buddyclients-free' ) . '</h3>',
        $license->product_name
    );

    
    // Error
    if ( $license->error ) {
        $license_message .= buddyc_admin_icon('error') . ' ' . $license->error;
    }
    
    // Upgrade link
    if ( strpos( $license->product, 'buddyc_premium' ) === false ) {
        $license_message .= buddyc_upgrade_link();
    }
    
    // Account link
    if ( $license_key && $license_key !== '' ) {
        $license_message .= buddyc_account_link();
    }
   
   return $license_message;
}