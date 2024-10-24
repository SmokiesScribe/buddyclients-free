<?php
use BuddyClients\Includes\ObjectHandler;
use BuddyClients\Components\Email\Email;
use BuddyClients\Admin\AdminTable;

/**
 * Callback to display email log content.
 * 
 * @since 0.1.0
 */
function bc_email_log_content() {
    
    // Initialize object handler
    $object_handler = new ObjectHandler( Email::class );
    $emails = $object_handler->get_all_objects();
    
    // Define headers
    $headers = [
        __( 'Sent At', 'buddyclients' ),
        __( 'To', 'buddyclients' ),
        __( 'Email', 'buddyclients' ),
        __( 'Subject', 'buddyclients' ),
        __( 'Content', 'buddyclients' )
    ];
    
    // Define columns
    $columns = [
        'created_at'    => ['created_at' => 'date'],
        'to_user_id'    => ['to_user_id' => 'user_link'],
        'to_email'      => ['to_email' => 'direct'],
        'subject'       => ['subject' => null],
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
function bc_email_log_admin_notice() {
    
    // Get the current screen
    $screen = get_current_screen();
    
    if ( $screen && $screen->id === 'admin_page_bc-email-log' ) {
        
        // Get current setting
        $email_setting = bc_get_setting( 'email', 'email_log_time');
        
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
            'repair_link'       => ['/admin.php?page=bc-email-settings'],
            'dismissable'       => true,
            'repair_link_text'  => [__( 'Change setting.', 'buddyclients' )],
            'message'           => $message,
            'color'             => 'blue'
        ];
        
        // Generate notice
        bc_admin_notice( $notice_args );
    }
}
add_action('admin_enqueue_scripts', 'bc_email_log_admin_notice'); // Fire after screen is available but before admin_notices