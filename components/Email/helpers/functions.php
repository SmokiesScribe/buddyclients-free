<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Email\Email;
/**
 * Checks whether a certain email is enabled.
 * 
 * @since 0.4.3
 * 
 * @param   string  $key    The email key.
 * @return  bool    True if enabled, false if not.
 */
function buddyc_email_enabled( $key ) {
    if ( ! class_exists( Email::class ) ) {
        return false;
    }
    
    // Get all enabled emails
    $enabled_emails = buddyc_get_setting( 'email', 'send_notifications' ) ?? [];
    
    // Check if the email key is enabled
    if ( in_array( $key, $enabled_emails ) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Retrieves an Email object by ID.
 * 
 * @since 1.0.24
 * 
 * @param   int $email_id   The ID of the Email.
 */
function buddyc_get_email( $email_id ) {
    return Email::get_email( $email_id );
}

/**
 * Retrieves all Email objects.
 * 
 * @since 1.0.24
 */
function buddyc_get_all_emails() {
    return Email::get_all_emails();
}