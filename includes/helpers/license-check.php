<?php
use BuddyClients\Includes\LicenseHandler;
/**
 * Triggers license check on updated license key or site url.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_license_check( $option_name, $old_value, $new_value ) {
    if ( ( $option_name === 'bc_license_settings' || $option_name === 'siteurl' ) && $old_value !== $new_value ) {
        // Run license handler
        $license_handler = new LicenseHandler;
        $license_handler->update_license_and_components();
    }
}
add_action('update_option', 'bc_license_check', 10, 3);