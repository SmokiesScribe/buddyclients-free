<?php
namespace BuddyClients\Components\Email;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Email\EmailTemplateManager;

/**
 * Defines the Email settings.
 *
 * @since 1.0.25
 */
class SettingsEmail {

    /**
     * Defines default Email settings.
     * 
     * @since 1.0.25
     */
    public static function defaults() {
        return [
            'send_notifications'    => self::get_critical(),
            'from_email'            => get_option('admin_email'),
            'from_name'             => get_option('blogname'),
            'notification_email'    => get_option('admin_email'),
        ];
    }
    
   /**
     * Defines the Email settings.
     * 
     * @since 1.0.25
     */
    public static function settings() {
        return [
            'enable' => [
                'title' => __( 'Enable Email Notifications', 'buddyclients-lite' ),
                'description' => __( 'Specify which email notifications to enable.', 'buddyclients-lite' ),
                'fields' => [
                    'send_notifications' => [
                        'label' => __( 'Enable Email Notifications', 'buddyclients-lite' ),
                        'type' => 'checkbox_table',
                        'options' => self::email_options(),
                        'default' => self::email_options( true ),
                        'descriptions' => self::email_descriptions(),
                        'description' => __( 'Select the events you would like to trigger email notifications for users.', 'buddyclients-lite' ) . '<br><span class="buddyc-text-red">*</span> ' . __( 'Disabling starred emails may impact plugin functionality.', 'buddyclients-lite' ),
                    ],
                ],
            ],
            'send' => [
                'title' => __( 'Email Sender', 'buddyclients-lite' ),
                'description' => __( 'Specify the sent-from name and email.', 'buddyclients-lite' ),
                'fields' => [
                    'from_email' => [
                        'label' => __( 'From Email', 'buddyclients-lite' ),
                        'type' => 'email',
                        'default' => get_option('admin_email'),
                        'description' => __( 'Notifications will be sent to users from this email address.', 'buddyclients-lite' ),
                    ],
                    'from_name' => [
                        'label' => __( 'From Name', 'buddyclients-lite' ),
                        'type' => 'text',
                        'default' => get_option('blogname'),
                        'description' => __( 'What name should appear on email notifications?', 'buddyclients-lite' ),
                    ],
                ],
            ],
            'admin' => [
                'title' => __( 'Admin Email Notifications', 'buddyclients-lite' ),
                'description' => __( 'Handle how you receive admin notifications.', 'buddyclients-lite' ),
                'fields' => [
                    'notification_email' => [
                        'label' => __( 'Notification Email', 'buddyclients-lite' ),
                        'type' => 'email',
                        'default' => get_option('admin_email'),
                        'description' => __( 'Where would you like to receive admin email notifications?', 'buddyclients-lite' ),
                    ],
                ],
            ],
            'log' => [
                'title' => __( 'Email Log', 'buddyclients-lite' ),
                'description' => sprintf(
                    /* translators: %s: URL to view the email log */
                    '%s <a href="%s">%s</a>',
                    __( 'Email log settings.', 'buddyclients-lite' ),
                    esc_url( admin_url( 'admin.php?page=buddyc-email-log' ) ),
                    __( 'View the email log.', 'buddyclients-lite' )
                ),
                'fields' => [
                    'email_log_time' => [
                        'label' => __( 'Email Log Time', 'buddyclients-lite' ),
                        'type' => 'dropdown',
                        'default' => '182',
                        'options' => [
                            '30'  => sprintf(
                                '30 %s',
                                __( 'Days', 'buddyclients-lite' )
                            ),
                            '90'  => sprintf(
                                '90 %s',
                                __( 'Days', 'buddyclients-lite' )
                            ),
                            '182'  => sprintf(
                                '6 %s',
                                __( 'Months', 'buddyclients-lite' )
                            ),
                            '365'  => sprintf(
                                '1 %s',
                                __( 'Year', 'buddyclients-lite' )
                            ),
                            'always' => __( 'Forever', 'buddyclients-lite' )
                        ],
                        'description' => __( 'Email log records older than the selected timeframe will be deleted.', 'buddyclients-lite' ),
                    ],
                ],
            ]
        ];
    }

   /**
     * Builds a list of email notification options.
     * 
     * @since 0.1.0
     * 
     * @param bool $defaults Optional. Whether to return an array of default options.
     */
    private static function email_options( $defaults = false ) {
        // Initialize
        $options = [];
        $default_options = [];
        
        // Retrieve all email templates
        $templates = EmailTemplateManager::templates();
        
        foreach ( $templates as $key => $data ) {
            $options[$key] = $data['label'];
            $default_options[] = $options[$key];
        }
        
        // Return defaults or options
        return $defaults ? $default_options : $options;
    }

    /**
     * Retrieves the keys for the critical emails.
     * 
     * @since 1.0.27
     */
    private static function get_critical() {
        $critical_emails = [];
        $templates = EmailTemplateManager::templates();
        foreach ( $templates as $key => $data ) {
            if ( isset($data['critical']) && $data['critical'] ) {
                $critical_emails[$key] = $key;
            }
        }
        return $critical_emails;
    }

    /**
     * Builds the array of descriptions.
     * 
     * @since 1.0.25
     */
    private static function email_descriptions() {
        $descriptions = [];
        $templates = EmailTemplateManager::templates();
        foreach ( $templates as $key => $data ) {
            $description = $data['description'] ?? '';
            if ( isset($data['critical']) && $data['critical'] ) {
                $description = self::critical_email_message( $description );
            }
            $descriptions[$key] = $description;
        }
        return $descriptions;
    }

    /**
     * Builds the critical message to append to the description.
     * 
     * @since 1.0.25
     * 
     * @param   string  $description    The description to modify.
     */
    private static function critical_email_message( $description ) {
        return sprintf(
            '%1$s <span class="buddyc-text-bold"><span class="buddyc-text-red">*</span> %2$s</span>',
            esc_html( $description ),
            esc_html( __( 'Critical to plugin function', 'buddyclients-lite' ) )
        );
    }
}