<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\WelcomeMessage;
/**
 * Initializes the WelcomeMessage.
 * 
 * @since 1.0.25
 */
function buddyc_init_welcome_message() {
    if ( class_exists( WelcomeMessage::class ) ) {
        new WelcomeMessage;
    }
}
add_action( 'init', 'buddyc_init_welcome_message' );