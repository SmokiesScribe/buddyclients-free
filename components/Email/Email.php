<?php
namespace BuddyClients\Components\Email;

use BuddyClients\Includes\ObjectHandler as ObjectHandler;

/**
 * Handles a single email notification.
 * 
 * Sends and logs plugin email notifications.
 *
 * @since 0.1.0
 */
class Email {
    
    /**
     * The ID of the email record.
     * 
     * @var int
     */
    public $ID;
    
    /**
     * The time created.
     * 
     * @var string
     */
    public $created_at;
    
    /**
     * The ID of the client.
     * 
     * @var int
     */
    public $client_id;
    
    /**
     * The email template key.
     * 
     * @var string
     */
    public $key;
    
    /**
     * Email content.
     * 
     * @var string
     */
    public $content;
    
    /**
     * Email subject.
     * 
     * @var string
     */
    public $subject;
    
    /**
     * Whether the email was sent successfully.
     * 
     * @var bool
     */
    public $sent;
    
    /**
     * ObjectHandler instance.
     * 
     * @var ObjectHandler
     */
    protected static $object_handler = null;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args {
     *     An array of email arguments. Must include 'to_email' or 'to_user_id'.
     *     Include additional args to replace variables in email templates.
     * 
     *     @type    string      $to_email       To email address.
     *     @type    string      $to_user_id     The user to whom the email is being sent.
     *     @type    string      $reply_to       Optional. Reply-to email address.
     *     @type    string      $bcc            Optional. BCC email address.
     *     @type    bool        $do_not_log     Optional. Include to stop logging mechanism.
     * }
     */
    public function __construct( $key, $args ) {
        $this->key = $key;
        
        // Exit if not enabled
        if ( ! $this->is_enabled() ) {
            return;
        }
        
        // Initialize object handler
        self::init_object_handler();
        
        // Send email
        $this->sent = $this->send( $args );
    }
    
    /**
     * Initializes ObjectHandler.
     * 
     * @since 0.1.0
     */
    private static function init_object_handler() {
        if ( ! self::$object_handler ) {
            self::$object_handler = new ObjectHandler( __CLASS__ );
        }
    }
    
    /**
     * Sends email.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args   Array of args from constructor.
     */
    private function send( $args ) {
        
        // Initialize
        $sent = false;
        
        // Get content and subject
        $this->get_content( $args );
            
        // Define headers
        $headers = $this->headers( $args );
        
        // Define to email
        $this->to_email = $this->to_email( $args );
        
        // Include user id if defined
        $this->to_user_id = $this->to_user_id( $args );
        
        // Build message from BuddyBoss template if available
        if (function_exists('bp_email_core_wp_get_template')) {
            $message = bp_email_core_wp_get_template( $this->content,  $this->to_user_id );
        } else {
            $message = $this->content;
        }
        
        // Make sure to email is defined
        if ( $this->to_email ) {
        
            // Use wp_mail to send the email
            $sent = wp_mail( $this->to_email, $this->subject, $message, $headers );
            
            // Check whether to log
            if ( ! isset( $args['do_not_log'] ) || $args['do_not_log'] !== true ) {
                
                // Log the email sending action
                if ( $sent ) {
                    
                    // Create new object in database
                    self::$object_handler->new_object( $this );
                }
            }
        }
        return $sent;
    }
    
    /**
     * Defines to user ID.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args   Array of args from constructor.
     */
    private function to_user_id( $args ) {
        
        // Check args
        if ( isset( $args['to_user_id'] ) ) {
            return $args['to_user_id'];
        }
        
        // Check for admin
        if ( $args['to_email'] === 'admin' ) {
            return __( 'Admin', 'buddyclients-free' );
        }
        
        // No user id
        return '';
    }
    
    /**
     * Defines to email address.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args   Array of args from constructor.
     */
    private function to_email( $args ) {
        
        // Initialize
        $to_email = false;
        
        // Use email if specified
        if (isset($args['to_email'])) {
            
            // Get admin email
            if ($args['to_email'] === 'admin') {
                $to_email = get_option('admin_email');
                
            // Otherwise use direct email
            } else {
                $to_email = $args['to_email'];
            }
        
        // Get email from user id if no email specified
        } else if (isset($args['to_user_id'])) {
            $to_email = bp_core_get_user_email($args['to_user_id']);
            
        // Oops, neither is specified
        } else {
            error_log( __( 'No email recipient information provided.', 'buddyclients-free' ) );
        }
        
        return $to_email;
    }
    
    /**
     * Defines email headers.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args   Array of args from constructor.
     */
    private function headers( $args ) {
        
        // Get settings
        $from_email = buddyc_get_setting( 'email', 'from_email' );
        $from_name = buddyc_get_setting( 'email', 'from_name' );
        
        // Define headers
        $headers = array(
            'From: ' . $from_name . ' <' . $from_email . '>',
            'Content-Type: text/html; charset=UTF-8',
        );
        
        // Add Reply-To email address if applicable
        if (isset($args['reply_to'])) {
            $headers[] = 'Reply-To: ' . $args['reply_to'];
        }
        
        // Add BCC if applicable
        if (isset($args['bcc'])) {
            $headers[] = 'Bcc: ' . $args['bcc'];
        }
        
        return $headers;
    }
    
    /**
     * Retrieves email template content.
     * 
     * @since 0.1.0
     */
    private function get_content( $args ) {
        $templates_array = get_option('buddyc_email_templates', array());
        $template_id = isset($templates_array[$this->key]) ? $templates_array[$this->key] : false;
        
        if ( $template_id ) {
            $this->content = $this->replace_var( 'post_content', $template_id, $args );
            $this->subject = $this->replace_var( 'post_title', $template_id, $args );
        }
    }
    
    /**
     * Maps the variables.
     * 
     * @since 0.1.0
     *
     * @param   array   $args   Array of args from constructor.
     */
    private function map_var( $args ) {
        
        // IN PROGRESS
        
        // Project vars
        if ( isset( $args['project_id'] ) ) {
            $args['project_name'] = bp_get_group_name( groups_get_group( $args['project_id'] ) );
            $args['project_link'] = bp_get_group_permalink( groups_get_group( $args['project_id'] ) );
        }
        
        $vars = [
            'project_id',
            'site_name',
            'brief_type',
            'service_fee',
            'quote_expiration',
            'booking_form_link',
            'payment_status',
            'client_name',
            'admin_bookings_link',
            'user_email',
            'message',
            'admin_testimonials_link',
            'affiliate_link',
            'request_status',
            'availability_link',
            'cancellation_reason',
            'reply_to',
            'sales_checkout_link'
        ];
    }
    
    /**
     * Replaces content variables.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args   Array of args from constructor.
     */
    private function replace_var( $post_field, $template_id, $args ) {
        $key = $this->key;
        
        // Get content from template
        $content = get_post_field( $post_field, $template_id );
        
        // Get settings
        $booking_page_id = buddyc_get_setting( 'pages', 'booking_page' );
        
        // Define constant variables
        $constants = $this->constant_variables();
        
        // Define the regular expression pattern to match keys within double brackets
        $pattern = '/{{(.*?)}}/';
        
        // Replace variables
        $replaced_content = preg_replace_callback($pattern, function ($matches) use ($args, $constants) {
            
            // Extract the key from the matched pattern
            $key = trim($matches[1]);
            
            // Check if the key exists in the constants array
            if (array_key_exists($key, $constants)) {
                // Replace the key with the corresponding value from $constants
                return $constants[$key];
                
            // Check if the key exists in the $args array
            } else if (array_key_exists($key, $args)) {
                // Replace the key with the corresponding value from $args
                return $args[$key];
            } else {
                // If the key doesn't exist, leave it unchanged
                return $matches[0];
            }
        }, $content);
        
        // Decode HTML entities
        return html_entity_decode($replaced_content);
    }

    /**
     * Defines the array of constant variables.
     * 
     * @since 1.0.19
     */
    private function constant_variables() {
        // Define constant variables
        $constants = array(
            'site_name'                 => get_option('blogname'),
            'booking_form_link'         => $booking_page_id ? get_permalink( $booking_page_id ) : site_url(),
            'admin_bookings_link'       => admin_url('/admin.php?page=buddyc-dashboard'),
            'admin_testimonials_link'   => admin_url('/edit.php?post_type=testimonial'),
            'site_url'                  => site_url()
        );

        /**
         * Filters the array of constant variables.
         * 
         * @since 1.0.19
         * 
         * @param   array   $constants      An associative array of variable keys and values.
         */
        $constants = apply_filters( 'buddyc_email_constants' $constants );

        return $constants;
    }
    
    /**
     * Checks if the email notification is enabled.
     * 
     * @since 0.1.0
     */
    private function is_enabled() {
        
        // Get all enabled emails
        $enabled_emails = buddyc_get_setting( 'email', 'send_notifications' ) ?? [];
        
        // Check if the email key is enabled
        if ( in_array( $this->key, $enabled_emails ) ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Cleans up email log.
     * 
     * @since 0.1.0
     */
    public static function cleanup_database() {
        // Initialize object handler
        self::init_object_handler();
        
        // Get email log expiration setting
        $days = buddyc_get_setting( 'email', 'email_log_time' );
        
        // Exit if set to always
        if ( $days === 'always' ) {
            return;
        }
        
        // Get all expired email objects
        $expired_emails = self::$object_handler->get_expired_objects( $days );
        
        // Delete expired email objects
        self::$object_handler->delete_objects( $expired_emails );
    }
    
}