<?php
/**
 * Handles changes to the BuddyClients Subscription.
 * 
 * @since 0.1.0
 */
// Load WP
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

// Run check
if ( class_exists( 'BuddyClients\Includes\LicenseHandler' ) ) {
    
    // Run license handler
    $licenseHandler = new BuddyClients\Includes\LicenseHandler;
    $licenseHandler->update_license_and_components();
    
    // Five by five
    http_response_code(200); // OK
} else {
    // Class does not exist
    http_response_code(500); // ERROR
}
