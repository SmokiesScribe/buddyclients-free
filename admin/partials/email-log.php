<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminTable;

/**
 * Callback to display email log content.
 * 
 * @since 0.1.0
 */
function buddyc_email_log_content() {
    
    // Get all Email objects    
    $emails = function_exists( 'buddyc_get_all_emails' ) ? buddyc_get_all_emails() : [];
    
    // Define headers
    $headers = [
        __( 'Details', 'buddyclients' ),
        __( 'Email', 'buddyclients' )
    ];
    
    // Define columns
    $columns = [
        'details'       => ['ID' => 'email_details'],
        'content'       => ['content' => 'direct']
    ];
    
    $args = [
        'key'           => 'emails',
        'headings'      => $headers,
        'columns'       => $columns,
        'items'         => $emails,
        'title'         => __( 'Email Log', 'buddyclients' ),
    ];
    
    new AdminTable( $args );
    
    return;
}

/**
 * Generates an admin notice for the email log.
 * 
 * @since 1.0.3
 */
function buddyc_email_log_admin_notice() {
    
    // Get the current screen
    $screen = get_current_screen();
    
    if ( $screen && $screen->id === 'admin_page_buddyc-email-log' ) {
        
        // Get current setting
        $email_setting = buddyc_get_setting( 'email', 'email_log_time');
        
        // Format setting value
        $email_setting = is_numeric( $email_setting )
            /* translators: %d: the numnber of days emails are retained */
            ? sprintf( __( 'for %d days', 'buddyclients' ), $email_setting ) 
            : __( 'forever', 'buddyclients' );
            
        // Build note
        $message = sprintf(
            /* translators: %s: the amount of time emails are stored (e.g. for 90 days or forever) */
            __('Emails are currently stored %s.', 'buddyclients'),
            $email_setting
        );
        
        // Define notice args
        $notice_args = [
            'repair_link'       => ['/admin.php?page=buddyc-email-settings'],
            'dismissable'       => true,
            'repair_link_text'  => [__( 'Change setting.', 'buddyclients' )],
            'message'           => $message,
            'color'             => 'blue'
        ];
        
        // Generate notice
        buddyc_admin_notice( $notice_args );
    }
}
add_action('admin_enqueue_scripts', 'buddyc_email_log_admin_notice'); // Fire after screen is available but before admin_notices