<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Generates an admin notice for the email log.
 * 
 * @since 1.0.3
 */
function buddyc_email_log_admin_notice() {
    
    // Get the current screen
    $screen = get_current_screen();
    
    if ( $screen && $screen->id === 'hidden-menu_page_buddyc-email-log' ) {
        
        // Get current setting
        $email_setting = buddyc_get_setting( 'email', 'email_log_time');
        
        // Format setting value
        $email_setting = is_numeric( $email_setting )
            /* translators: %d: the numnber of days emails are retained */
            ? sprintf( __( 'for %d days', 'buddyclients-lite' ), $email_setting ) 
            : __( 'forever', 'buddyclients-lite' );
            
        // Build note
        $message = sprintf(
            /* translators: %s: the amount of time emails are stored (e.g. for 90 days or forever) */
            __('Emails are currently stored %s.', 'buddyclients-lite'),
            $email_setting
        );
        
        // Define notice args
        $notice_args = [
            'repair_link'       => ['/admin.php?page=buddyc-email-settings'],
            'dismissable'       => true,
            'repair_link_text'  => [__( 'Change setting.', 'buddyclients-lite' )],
            'message'           => $message,
            'color'             => 'blue'
        ];
        
        // Generate notice
        buddyc_admin_notice( $notice_args );
    }
}
add_action('admin_enqueue_scripts', 'buddyc_email_log_admin_notice'); // Fire after screen is available but before admin_notices