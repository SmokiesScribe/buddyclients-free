<?php
use BuddyClients\Components\Email\EmailTriggers;
/**
 * Initializes email triggers.
 * 
 * @since 0.1.0
 */
function bc_email_triggers() {
    if ( class_exists( EmailTriggers::class ) ) {
        EmailTriggers::run();
    }
}
add_action('init', 'bc_email_triggers');