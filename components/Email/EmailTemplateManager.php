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
                'label'         => __('Service Status Updated', 'buddyclients-free'),
                'subject'       => __('Your {{service_name}} is {{service_status}}', 'buddyclients-free'),
                'content'       => [
                    sprintf(
                        /* translators: %s: the link to view project */
                        __('The status of your service has changed. Your {{service_name}} for {{project_name}} is now {{service_status}}. %s', 'buddyclients-free' ),
                        self::link( '{{project_link}}', __( 'View your project.', 'buddyclients-free' ) )
                    ),
                    __( 'Thank you for choosing {{site_name}}!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies the client when the status of their service changes.', 'buddyclients-free' )
            ],
            'updated_brief' => [
                'label'         => __('Project Brief Updated', 'buddyclients-free'),
                'subject'       => __('Brief updated for {{project_name}}', 'buddyclients-free'),
                'content'       => [
                    sprintf(
                        /* translators: %s: the link to the project briefs page */
                        __('The {{brief_type}} for {{project_name}} has been updated. %s', 'buddyclients-free'),
                        self::link('{{project_link}}/brief', __('Go to project briefs.', 'buddyclients-free'))
                    ),
                    __( 'Thank you for using {{site_name}}!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies team members when a brief is updated.', 'buddyclients-free' )
            ],
            'custom_quote' => [
                'label'         => __('New Custom Quote', 'buddyclients-free'),
                'subject'       => __('New custom quote', 'buddyclients-free'),
                'content'       => [
                    sprintf(
                        /* translators: %s: the service name */
                        __('You have a new custom quote available: {{service_name}}. %s', 'buddyclients-free'),
                        __('This quote expires {{quote_expiration}}.', 'buddyclients-free')
                    ),
                    sprintf(
                        /* translators: %s: the link to the booking form */
                        __('You can book this service anytime using the %s.', 'buddyclients-free'),
                        self::link('{{booking_form_link}}', __('booking form', 'buddyclients-free'))
                    ),
                    __( 'Thank you for using {{site_name}}!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies the client when they have a new custom quote available.', 'buddyclients-free' )
            ],
            'new_assignment' => [
                'label'         => __('New Team Member Assignment', 'buddyclients-free'),
                'subject'       => __('New Assignment: {{service_name}}', 'buddyclients-free'),
                'content'       => [
                    __( 'You have a new assignment!', 'buddyclients-free' ),
                    sprintf(
                        /* translators: %s: the link to the project */
                        __('Access the project: %s', 'buddyclients-free'),
                        self::link('{{project_link}}', __('Go to the project', 'buddyclients-free'))
                    ),
                    __( 'Thank you for your continued contribution to the team!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies a team member when they have a new assignment.', 'buddyclients-free' )
            ],
            'abandoned_booking' => [
                'label'         => __('Abandoned Booking', 'buddyclients-free'),
                'subject'       => __('Need some help?', 'buddyclients-free'),
                'content'       => [
                    __( 'Looks like you didn’t finish booking your services with {{site_name}}. If you have questions, please respond to this email.', 'buddyclients-free' ),
                    __( 'We’re here to help!', 'buddyclients-free' ),
                    __( 'Thank you for choosing {{site_name}}!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Sends an email to convert users who have abandoned bookings before submitting payment.', 'buddyclients-free' )
            ],
            'payment' => [
                'label'         => __('Payment Status Updated', 'buddyclients-free'),
                'subject'       => __('Payment Status Updated', 'buddyclients-free'),
                'content'       => [
                    sprintf(
                        /* translators: %s: the payment status */
                        __('The status of your payment for {{service_name}} for {{project_name}} is now {{payment_status}}. %s', 'buddyclients-free'),
                        __('You can check the status and more details on your project.', 'buddyclients-free')
                    ),
                    sprintf(
                        /* translators: %s: the link to view the project */
                        __('View the project: %s', 'buddyclients-free'),
                        self::link('{{project_link}}', __('Click here', 'buddyclients-free'))
                    ),
                    __( 'Thank you for your business!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies a team member or affiliate when the status of their payment changes.', 'buddyclients-free' )
            ],
            'new_booking_admin' => [
                'label'         => __('New Booking Admin Notification', 'buddyclients-free'),
                'subject'       => __('Woo hoo! New Booking', 'buddyclients-free'),
                'content'       => [
                    sprintf(
                        /* translators: %s: the client name */
                        __('A new booking has been confirmed. {{client_name}} successfully booked {{service_name}}. %s', 'buddyclients-free'),
                        __('You can view more details and manage bookings in the admin area.', 'buddyclients-free')
                    ),
                    sprintf(
                        /* translators: %s: the link to admin bookings */
                        __('View all bookings: %s', 'buddyclients-free'),
                        self::link('{{admin_bookings_link}}', __('Click here', 'buddyclients-free'))
                    ),
                    __( 'Thank you for your attention!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies the site admin when a new booking is completed.', 'buddyclients-free' )
            ],
            'contact_form_confirmation' => [
                'label'         => __('Contact Form User Confirmation', 'buddyclients-free'),
                'subject'       => __('Thank you for contacting {{site_name}}', 'buddyclients-free'),
                'content'       => [
                    __( 'We have received your message and will be in touch as soon as possible.', 'buddyclients-free' ),
                    __( '<strong>Your Submitted Information:</strong>', 'buddyclients-free' ),
                    sprintf(__('Name: %s', 'buddyclients-free'), '{{client_name}}'),
                    sprintf(__('Email: %s', 'buddyclients-free'), '{{user_email}}'),
                    sprintf(__('Message: %s', 'buddyclients-free'), '{{message}}'),
                    __( 'Thank you for reaching out to us!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Sends a confirmation message after a user submits the contact form.', 'buddyclients-free' )
            ],
            'new_testimonial' => [
                'label'         => __('New Testimonial Submission', 'buddyclients-free'),
                'subject'       => __('New Testimonial Submission on {{site_name}}', 'buddyclients-free'),
                'content'       => [
                    sprintf(__('You have received a new testimonial submission from {{client_name}}. %s', 'buddyclients-free'),
                    self::link('{{admin_testimonials_link}}', __('View all submissions.', 'buddyclients-free'))),
                    __( 'Thank you for your continued support!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Sends a confirmation message when a user submits a testimonial.', 'buddyclients-free' )
            ],
            'new_affiliate' => [
                'label'         => __('New Affiliate', 'buddyclients-free'),
                'subject'       => __('Welcome to the Affiliate Program!', 'buddyclients-free'),
                'content'       => [
                    __( 'Welcome to {{site_name}}’s affiliate program!', 'buddyclients-free' ),
                    sprintf(__('Share your unique affiliate link to begin earning commission: %s', 'buddyclients-free'), '{{affiliate_link}}'),
                    __( 'We’re excited to have you with us!', 'buddyclients-free' ),
                ],
                'description'   => __( 'Sends a welcome message when a user joins the affiliate program.', 'buddyclients-free' )
            ],
            'availability_reminder' => [
                'label'         => __('Update Availability Reminder', 'buddyclients-free'),
                'subject'       => __('Update Your Availability', 'buddyclients-free'),
                'content'       => [
                    sprintf(__('Your availability date is expiring. %s', 'buddyclients-free'),
                    self::link('{{availability_link}}', __('Log in to {{site_name}}', 'buddyclients-free'))),
                    __( 'Please update your availability as soon as possible.', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies a team member when their set availability date is about to expire.', 'buddyclients-free' )
            ],
            'lead_gen' => [
                'label'         => __('New Lead', 'buddyclients-free'),
                'subject'       => __('New Lead', 'buddyclients-free'),
                'content'       => [
                    __( '<strong>New Lead from {{site_name}}</strong>', 'buddyclients-free' ),
                    sprintf(__('Name: %s', 'buddyclients-free'), '{{lead_name}}'),
                    sprintf(__('Email: %s', 'buddyclients-free'), '{{lead_email}}'),
                    sprintf(__('Interests: %s', 'buddyclients-free'), '{{lead_interest}}'),
                    __( 'Please follow up with this lead as soon as possible.', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies the site admin of a new submission to the lead generation form.', 'buddyclients-free' ),
                'critical'      => false
            ],
            'cancellation_request_admin' => [
                'label'         => __('Cancellation Request Admin Notification', 'buddyclients-free'),
                'subject'       => __('Cancellation Request', 'buddyclients-free'),
                'content'       => [
                    sprintf(__('{{client_name}} has requested to cancel {{service_name}} for %s.', 'buddyclients-free'), self::link('{{project_link}}', '{{project_name}}')),
                    sprintf(__('Reason: %s', 'buddyclients-free'), '{{cancellation_reason}}'),
                    __( 'Please review the cancellation request and take appropriate action.', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies the site admin when a client requests the cancellation of a service.', 'buddyclients-free' ),
                'critical'      => true
            ],
            'contact_form_admin' => [
                'label'         => __('Contact Form Admin Notification', 'buddyclients-free'),
                'subject'       => __('{{site_name}} Contact Form', 'buddyclients-free'),
                'content'       => [
                    sprintf(__('Message: %s', 'buddyclients-free'), '{{message}}'),
                    sprintf(__('From: %s', 'buddyclients-free'), '{{client_name}}'),
                    sprintf(__('Reply To: %s', 'buddyclients-free'), '{{reply_to}}'),
                    __( 'Please review the contact form submission and respond accordingly.', 'buddyclients-free' ),
                ],
                'description'   => __( 'Notifies the site admin when the contact form is submitted.', 'buddyclients-free' ),
                'critical'      => true
            ],
            'sales_sub' => [
                'label'         => __('Sales Team Booking', 'buddyclients-free'),
                'subject'       => __('Ready for Checkout', 'buddyclients-free'),
                'content'       => [
                    sprintf(__('Your services from %s are ready! %s', 'buddyclients-free'), '{{site_name}}', self::link('{{sales_checkout_link}}', __('Click here to finalize your booking.', 'buddyclients-free'))),
                    __( 'Please complete the checkout process to confirm your booking.', 'buddyclients-free' ),
                ],
                'description'   => __( 'Sends a message with a link to submit payment when a booking is created manually by the sales team or site admin.', 'buddyclients-free' ),
                'critical'      => true
            ],
            'lead_auto' => [
                'label'         => __('Lead Auto Response', 'buddyclients-free'),
                'subject'       => __('Thank you for contacting {{site_name}}', 'buddyclients-free'),
                'content'       => [
                    __( 'Thank you for contacting {{site_name}}. We would love to hear more about your project.', 'buddyclients-free' ),
                    __( '<strong>Your Submitted Info:</strong>', 'buddyclients-free' ),
                    sprintf(__('Name: %s', 'buddyclients-free'), '{{lead_name}}'),
                    sprintf(__('Email: %s', 'buddyclients-free'), '{{lead_email}}'),
                    sprintf(__('Interests: %s', 'buddyclients-free'), '{{lead_interest}}'),
                    __( 'We will get back to you as soon as possible to discuss your project further.', 'buddyclients-free' ),
                ],
                'description'   => __( 'Sends an auto-response to the user when they submit the lead generation form.', 'buddyclients-free' ),
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
}