<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Config\LicenseHandler;

/**
 * Initializes the LicenseHandler.
 * 
 * @since 1.0.25
 */
function buddyc_license_handler() {
    if ( class_exists( LicenseHandler::class ) ) {
        return LicenseHandler::get_instance();
    }
}
add_action( 'init', 'buddyc_license_handler' );

/**
 * Retrieves the license from the LicenseHandler.
 * 
 * @since 1.0.25
 */
function buddyc_get_license() {
    if ( class_exists( LicenseHandler::class ) ) {
        return LicenseHandler::get_license();
    }
}

/**
 * Retrieves the product from the LicenseHandler.
 * 
 * @since 1.0.27
 */
function buddyc_get_product() {
    if ( class_exists( LicenseHandler::class ) ) {
        return LicenseHandler::get_product();
    }
    return 'free';
}