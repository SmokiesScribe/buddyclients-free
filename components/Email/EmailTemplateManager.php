<?php
namespace BuddyClients\Components\Email;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Manages plugin email templates.
 * 
 * Handles the creation and repair of email templates.
 *
 * @since 0.1.0
 */
class EmailTemplateManager {
    
    /**
     * Templates.
     * 
     * @var array
     */
    public $templates;
    
    /**
     * Defines all email templates.
     * 
     * @since 0.1.0
     */
    public static function templates() {

        $templates = [
            'service_status' => [
                'label'         => __('Service Status Updated', 'buddyclients-lite'),
                'subject'       => sprintf(
                    /* translators: %1$s: the name of the service; %2$s: the status of the service */
                    __('Your %1$s is %2$s', 'buddyclients-lite'),
                    '{{service_name}}',
                    '{{service_status}}'
                ),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the name of the service; %2$s: the project name; %3$s: the service status; %4$s: the link to view the project */
                        __('The status of your %1$s for %2$s is now %3$s. %4$s', 'buddyclients-lite' ),
                        '{{service_name}}',
                        '{{project_name}}',
                        '{{service_status}}',
                        self::link( '{{project_link}}', __( 'View your project.', 'buddyclients-lite' ) )
                    ),
                    sprintf(
                        /* translators: %s: the site name */
                        __('Thank you for choosing %s!', 'buddyclients-lite'),
                        '{{site_name}}'
                    ),
                ],
                'description'   => __( 'Notifies the client when the status of their service changes.', 'buddyclients-lite' )
            ],
            'booking_services_complete' => [
                'label'         => __('All Booking Services Complete', 'buddyclients-lite'),
                'subject'       => __( 'Your services are complete', 'buddyclients-lite' ),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the names of the services; %2$s: the project name; %3$s: the service status; %4$s: the link to view the project */
                        __('All services booked on %1$s for %2$s have been completed: %3$s.', 'buddyclients-lite' ),
                        '{{booking_date}}',
                        '{{project_name}}',
                        '{{service_names}}',
                    ),
                    '{{payment_info}}',
                    sprintf(
                        /* translators: %s: the site name */
                        __('Thank you for choosing %s!', 'buddyclients-lite'),
                        '{{site_name}}'
                    ),
                ],
                'description'   => __( 'Sends an email to the client when all services for a single booking have been completed. Includes a link to pay any unpaid fees, including final payments if deposits are enabled.', 'buddyclients-lite' )
            ],
            'updated_brief' => [
                'label'         => __('Project Brief Updated', 'buddyclients-lite'),
                'subject'       => sprintf(
                    /* translators: %s: the name of the project */
                    __( 'Brief updated for %s', 'buddyclients-lite' ),
                    '{{project_name}}'
                ),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the type of brief; %2$s: the project name; %3$s: the link to the project briefs page */
                        __('The %1$s for %2$s has been updated. %3$s', 'buddyclients-lite'),
                        '{{brief_type}}',
                        '{{project_name}}',
                        self::link('{{project_link}}/brief', __('Go to project briefs.', 'buddyclients-lite'))
                    ),
                    __( 'Thank you for using {{site_name}}!', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Notifies team members when a brief is updated.', 'buddyclients-lite' )
            ],
            'custom_quote' => [
                'label'         => __('New Custom Quote', 'buddyclients-lite'),
                'subject'       => __('New custom quote', 'buddyclients-lite'),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the service name; %2$s: the quote expiration date */
                        __('You have a new custom quote available: %1$s. This quote expires %2$s.', 'buddyclients-lite'),
                        '{{service_name}}',
                        '{{quote_expiration}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the link to the booking form */
                        __('You can book this service anytime using the %1$s.', 'buddyclients-lite'),
                        self::link('{{booking_form_link}}', __('booking form', 'buddyclients-lite'))
                    ),
                    sprintf(
                        /* translators: %s: the name of the site */
                        __( 'Thank you for using %s!', 'buddyclients-lite' ),
                        '{{site_name}}'
                    )
                ],
                'description'   => __( 'Notifies the client when they have a new custom quote available.', 'buddyclients-lite' )
            ],
            'new_assignment' => [
                'label'         => __('New Team Member Assignment', 'buddyclients-lite'),
                'subject'       => sprintf(
                    /* translators: %s: the name of the service */
                    __('New Assignment: %s', 'buddyclients-lite'),
                    '{{service_name}}'
                ),
                'content'       => [
                    __( 'You have a new assignment!', 'buddyclients-lite' ),
                    sprintf(
                        /* translators: %s: the link to the project */
                        __('Access the project: %s', 'buddyclients-lite'),
                        self::link('{{project_link}}', __('Go to the project', 'buddyclients-lite'))
                    ),
                    __( 'Thank you for your continued contribution to the team!', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Notifies a team member when they have a new assignment.', 'buddyclients-lite' )
            ],
            'abandoned_booking' => [
                'label'         => __('Abandoned Booking', 'buddyclients-lite'),
                'subject'       => __('Need some help?', 'buddyclients-lite'),
                'content'       => [
                    sprintf(
                        /* translators: %s: the site name */
                        __('Looks like you didn’t finish booking your services with %s. If you have questions, please respond to this email.', 'buddyclients-lite'),
                        '{{site_name}}'
                    ),
                    __( 'We’re here to help!', 'buddyclients-lite' ),
                    sprintf(
                        /* translators: %s: the site name */
                        __('Thank you for choosing %s!', 'buddyclients-lite'),
                        '{{site_name}}'
                    ),
                ],
                'description'   => __( 'Sends an email to convert users who have abandoned bookings before submitting payment.', 'buddyclients-lite' )
            ],
            'payment' => [
                'label'         => __('Payment Status Updated', 'buddyclients-lite'),
                'subject'       => __('Payment Status Updated', 'buddyclients-lite'),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the service name; %2$s: the project name; %3$s: the payment status; %4$s: additional payment details */
                        __('The status of your payment for %1$s for %2$s is now %3$s. %4$s', 'buddyclients-lite'),
                        '{{service_name}}',
                        '{{project_name}}',
                        '{{payment_status}}',
                        __('You can check the status and more details on your project.', 'buddyclients-lite')
                    ),
                    sprintf(
                        /* translators: %1$s: the link to view the project */
                        __('View the project: %1$s', 'buddyclients-lite'),
                        self::link('{{project_link}}', __('Click here', 'buddyclients-lite'))
                    ),
                    __( 'Thank you for your business!', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Notifies a team member or affiliate when the status of their payment changes.', 'buddyclients-lite' )
            ],
            'new_booking_admin' => [
                'label'         => __('New Booking Admin Notification', 'buddyclients-lite'),
                'subject'       => __('Woo hoo! New Booking', 'buddyclients-lite'),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the client name; %2$s: the service name; %3$s: additional details */
                        __('A new booking has been confirmed. %1$s successfully booked %2$s. %3$s', 'buddyclients-lite'),
                        '{{client_name}}',
                        '{{service_name}}',
                        __('You can view more details and manage bookings in the admin area.', 'buddyclients-lite')
                    ),
                    sprintf(
                        /* translators: %1$s: the link to admin bookings */
                        __('View all bookings: %1$s', 'buddyclients-lite'),
                        self::link('{{admin_bookings_link}}', __('Click here', 'buddyclients-lite'))
                    ),
                    __( 'Thank you for your attention!', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Notifies the site admin when a new booking is completed.', 'buddyclients-lite' )
            ],
            'contact_form_confirmation' => [
                'label'         => __('Contact Form User Confirmation', 'buddyclients-lite'),
                'subject'       => sprintf(
                    /* translators: %1$s: the site name */
                    __('Thank you for contacting %1$s', 'buddyclients-lite'),
                    '{{site_name}}'
                ),
                'content'       => [
                    __( 'We have received your message and will be in touch as soon as possible.', 'buddyclients-lite' ),
                    '<strong>' . __( 'Your Submitted Information:', 'buddyclients-lite' ) . '</strong>',
                    sprintf(
                        /* translators: %1$s: the client name */
                        __('Name: %1$s', 'buddyclients-lite'),
                        '{{client_name}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the user email */
                        __('Email: %1$s', 'buddyclients-lite'),
                        '{{user_email}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the message */
                        __('Message: %1$s', 'buddyclients-lite'),
                        '{{message}}'
                    ),
                    __( 'Thank you for reaching out to us!', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Sends a confirmation message after a user submits the contact form.', 'buddyclients-lite' )
            ],
            'new_testimonial' => [
                'label'         => __('New Testimonial Submission', 'buddyclients-lite'),
                'subject'       => sprintf(
                    /* translators: %1$s: the site name */
                    __('New Testimonial Submission on %1$s', 'buddyclients-lite'),
                    '{{site_name}}'
                ),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the client name; %2$s: the link to admin testimonials */
                        __('You have received a new testimonial submission from %1$s. %2$s', 'buddyclients-lite'),
                        '{{client_name}}',
                        self::link('{{admin_testimonials_link}}', __('View all submissions.', 'buddyclients-lite'))
                    ),
                    __( 'Thank you for your continued support!', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Sends a confirmation message when a user submits a testimonial.', 'buddyclients-lite' )
            ],
            'new_affiliate' => [
                'label'         => __('New Affiliate', 'buddyclients-lite'),
                'subject'       => __('Welcome to the Affiliate Program!', 'buddyclients-lite'),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the site name */
                        __('Welcome to %1$s’s affiliate program!', 'buddyclients-lite'),
                        '{{site_name}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the affiliate link */
                        __('Share your unique affiliate link to begin earning commission: %1$s', 'buddyclients-lite'),
                        '{{affiliate_link}}'
                    ),
                    __( 'We’re excited to have you with us!', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Sends a welcome message when a user joins the affiliate program.', 'buddyclients-lite' )
            ],
            'availability_reminder' => [
                'label'         => __('Update Availability Reminder', 'buddyclients-lite'),
                'subject'       => __('Update Your Availability', 'buddyclients-lite'),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the link to availability page; %2$s: the site name */
                        __('Your availability date is expiring. %1$s', 'buddyclients-lite'),
                        self::link('{{availability_link}}', __('Log in to update.', 'buddyclients-lite'))
                    ),
                    __( 'Please update your availability as soon as possible.', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Notifies a team member when their set availability date is about to expire.', 'buddyclients-lite' )
            ],
            'lead_gen' => [
                'label'         => __('New Lead', 'buddyclients-lite'),
                'subject'       => __('New Lead', 'buddyclients-lite'),
                'content'       => [
                    '<strong>' . __( 'New Lead from {{site_name}}', 'buddyclients-lite' ) . '</strong>',
                    sprintf(
                        /* translators: %1$s: the lead name */
                        __('Name: %1$s', 'buddyclients-lite'),
                        '{{lead_name}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the lead email */
                        __('Email: %1$s', 'buddyclients-lite'),
                        '{{lead_email}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the lead interest */
                        __('Interests: %1$s', 'buddyclients-lite'),
                        '{{lead_interest}}'
                    )
                ],
                'description'   => __( 'Notifies the site admin of a new submission to the lead generation form.', 'buddyclients-lite' ),
                'critical'      => false
            ],
            'cancellation_request_admin' => [
                'label'         => __('Cancellation Request Admin Notification', 'buddyclients-lite'),
                'subject'       => __('Cancellation Request', 'buddyclients-lite'),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the name of the client; %2$s: the name of the service; %3$s: the link to the project */
                        __('%1$s has requested to cancel %2$s for %3$s.', 'buddyclients-lite'),
                        '{{client_name}}',
                        '{{service_name}}',
                        self::link( '{{project_link}}', '{{project_name}}' )
                    ),
                    sprintf(
                        /* translators: %1$s: the cancellation reason */
                        __('Reason: %1$s', 'buddyclients-lite'),
                        '{{cancellation_reason}}'
                    ),
                    __( 'Please review the cancellation request and take appropriate action.', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Notifies the site admin when a client requests the cancellation of a service.', 'buddyclients-lite' ),
                'critical'      => true
            ],
            'contact_form_admin' => [
                'label'         => __('Contact Form Admin Notification', 'buddyclients-lite'),
                'subject'       => sprintf(
                    /* translators: %s: the name of the site */
                    __('%s Contact Form', 'buddyclients-lite'),
                    '{{site_name}}'
                ),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the message content */
                        __('Message: %1$s', 'buddyclients-lite'),
                        '{{message}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the client name */
                        __('From: %1$s', 'buddyclients-lite'),
                        '{{client_name}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the reply-to address */
                        __('Reply To: %1$s', 'buddyclients-lite'),
                        '{{reply_to}}'
                    ),
                    __( 'Please review the contact form submission and respond accordingly.', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Notifies the site admin when the contact form is submitted.', 'buddyclients-lite' ),
                'critical'      => true
            ],
            'sales_sub' => [
                'label'         => __('Sales Team Booking', 'buddyclients-lite'),
                'subject'       => __('Ready for Checkout', 'buddyclients-lite'),
                'content'       => [
                    sprintf(
                        /* translators: %1$s: the site name; %2$s: the link to finalize booking */
                        __('Your services from %1$s are ready! %2$s', 'buddyclients-lite'),
                        '{{site_name}}',
                        self::link('{{sales_checkout_link}}', __('Click here to finalize your booking.', 'buddyclients-lite'))
                    ),
                    __( 'Please complete the checkout process to confirm your booking.', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Sends a message with a link to submit payment when a booking is created manually by the sales team or site admin.', 'buddyclients-lite' ),
                'critical'      => true
            ],
            'lead_auto' => [
                'label'         => __('Lead Auto Response', 'buddyclients-lite'),
                'subject'       => __('Thank you for contacting {{site_name}}', 'buddyclients-lite'),
                'content'       => [
                    __( 'Thank you for contacting {{site_name}}. We would love to hear more about your project.', 'buddyclients-lite' ),
                    '<strong>' . __( 'Your Submitted Info:', 'buddyclients-lite' ) . '</strong>',
                    sprintf(
                        /* translators: %1$s: the lead name */
                        __('Name: %1$s', 'buddyclients-lite'),
                        '{{lead_name}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the lead email */
                        __('Email: %1$s', 'buddyclients-lite'),
                        '{{lead_email}}'
                    ),
                    sprintf(
                        /* translators: %1$s: the lead interest */
                        __('Interests: %1$s', 'buddyclients-lite'),
                        '{{lead_interest}}'
                    ),
                    __( 'We will get back to you as soon as possible to discuss your project further.', 'buddyclients-lite' ),
                ],
                'description'   => __( 'Sends an auto-response to the user when they submit the lead generation form.', 'buddyclients-lite' ),
                'critical'      => true
            ],
        ];
        
        /**
         * Filters the email templates.
         *
         * @since 0.3.4
         *
         * @param array  $pages    An array of email template data.
         */
         $templates = apply_filters( 'buddyc_email_templates', $templates );
         
        return $templates;
    }
    
    /**
     * Creates all email temmplates.
     * 
     * @since 0.1.0
     */
    public static function create() {
    
        // Get all templates
        $templates = self::templates();
    
        // Get existing setting
        $assigned_emails = get_option( 'buddyc_email_templates', [] );

        // Initialize array
        $emails = [];
    
        // Create email posts
        foreach ( $templates as $key => $data ) {
            
            // Check if email template exists and is already published
            if ( isset( $assigned_emails[$key] ) ) {
                
                // Get post status
                $post_status = get_post_status($assigned_emails[$key]);
                
                // Skip if already published
                if ($post_status === 'publish') {
                    continue;
                }
            }
    
            // Define post args
            $args = array(
                'post_title'    => $data['label'],
                'post_content'  => self::format_content( $data['content'] ?? '' ),
                'post_status'   => 'publish',
                'post_type'     => 'buddyc_email'
            );
    
            $post_id = wp_insert_post($args);

            if ( $post_id ) {
    
                // Add post id to array
                $emails[$key] = $post_id;

                // Update post meta
                update_post_meta( $post_id, '_buddyc_email_key', $key );
                update_post_meta( $post_id, '_buddyc_email_subject', $templates[$key]['subject'] ?? '' );
                update_post_meta( $post_id, '_buddyc_email_description', ( $templates[$key]['description'] ?? '' ) );

            }
        }
        
        // Merge the newly created email IDs with the existing settings
        $updated_emails = array_merge( $assigned_emails, $emails );
    
        // Save merged array in settings
        update_option( 'buddyc_email_templates', $updated_emails );
    }

    /**
     * Formats the email content.
     * 
     * Breaks an array of text items into paragraphs.
     * 
     * @since 1.0.25
     * 
     * @param   array|string  $content    The original content to format.
     * @return  string  The formatted content.
     */
    private static function format_content( $content ) {
        $content = (array) $content;
        $formatted_content = '';
        foreach ( $content as $paragraph ) {
            $formatted_content .= sprintf(
                '<p>%s</p>',
                $paragraph
            );
        }
        return $formatted_content;
    }

    /**
     * Outputs a formatted link. 
     * 
     * @since 1.0.25
     * 
     * @param   string  $url    The link url or placeholder.
     * @param   string  $text   The link text.
     * @return  string  The formatted link.
     */
    private static function link( $url, $text ) {
        return sprintf(
            '<a href="%1$s">%2$s</a>',
            $url,
            $text
        );
    }

    /**
     * Adds a meta box to display available placeholders for an email template.
     * 
     * @since 1.0.25
     */
    public static function add_placeholder_meta_box() {
        add_meta_box(
            'buddyc_email_placeholders', // Meta box ID
            __( 'Available Placeholders', 'buddyclients-lite' ), // Title
            [EmailTemplateManager::class, 'display_placeholders_meta_box'], // Callback function
            'buddyc_email', // Post type (email template)
            'side', // Context (sidebar)
            'default' // Priority
        );
    }

    /**
     * Displays the placeholders meta box content.
     * 
     * @since 1.0.25
     */
    public static function display_placeholders_meta_box( $post ) {
        // Get the template key dynamically
        $template_key = get_post_meta( $post->ID, '_buddyc_email_key', true );
    
        // Fetch the available placeholders
        $placeholders = self::get_placeholders( $template_key );
    
        // Check if placeholders exist
        if ( ! empty( $placeholders ) ) {
            $content = '<ul>';
            foreach ( $placeholders as $placeholder ) {
                $content .= '<li>';
                $content .= sprintf(
                    '<span>{{%s}}</span>',
                    esc_html( $placeholder )
                );
                $content .= '</li>';
            }
            echo '</ul>';
        } else {
            $content = sprintf(
                '<p>%s</p>',
                __('No placeholders available for this template.', 'buddyclients-lite')
            );
        }

        $allowed_html = [
            'p' => [],
            'ul' => [],
            'li' => [],
            'span' => [ 'class' => [] ],
        ];
        echo wp_kses( $content, $allowed_html );
    }

    /**
     * Generates an array of placeholders available for an email template.
     * 
     * @since 1.0.25
     * 
     * @param   string  $template_key   The key for the email template.
     */
    private static function get_placeholders( $template_key ) {
        $templates = self::templates();
        $data = $templates[$template_key] ?? null;
        if ( ! $data ) return;

        $content = self::format_content( $data['content'] ?? '' );
        $subject = $data['subject'] ?? '';

        // Extract placeholders from content and subject using a regular expression
        preg_match_all('/{{(.*?)}}/', $content . ' ' . $subject, $matches);

        // Return unique placeholders
        return array_unique($matches[1]);
    }
}