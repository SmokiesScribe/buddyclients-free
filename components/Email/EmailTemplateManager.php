<?php
namespace BuddyClients\Components\Email;

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
                'label' => __('Service Status Updated', 'buddyclients'),
                'subject' => __('Your {{service_name}} is {{service_status}}', 'buddyclients'),
                'content' => __('<p>The status of your service has changed. Your {{service_name}} for {{project_name}} is now {{service_status}}. <a href="{{project_link}}">View your project.</a><p>Thank you for choosing {{site_name}}!</p>', 'buddyclients'),
            ],
            'updated_brief' => [
                'label' => __('Project Brief Updated', 'buddyclients'),
                'subject' => __('Brief updated for {{project_name}}', 'buddyclients'),
                'content' => __('<p>The {{brief_type}} for {{project_name}} has been updated.</p><p><a href="{{project_link}}/brief">Go to project briefs.</a></p>', 'buddyclients'),
            ],
            'custom_quote' => [
                'label' => __('New Custom Quote', 'buddyclients'),
                'subject' => __('New custom quote', 'buddyclients'),
                'content' => __('<p>You have a new custom quote available: {{service_name}}.</p>This quote expires {{quote_expiration}}.</p><p>You can book this service anytime using the <a href="{{booking_form_link}}">booking form</a>.</p>', 'buddyclients'),
            ],
            'new_assignment' => [
                'label' => __('New Team Member Assignment', 'buddyclients'),
                'subject' => __('New Assignment: {{service_name}}', 'buddyclients'),
                'content' => __('<p>You have a new assignment!</p><p><a href="{{project_link}}">Access the project.</a></p>', 'buddyclients'),
            ],
            'abandoned_booking' => [
                'label' => __('Abandoned Booking', 'buddyclients'),
                'subject' => __('Need some help?', 'buddyclients'),
                'content' => '<p>' . __( 'Looks like you didn’t finish booking your services with {{site_name}}. If you have questions, please respond to this email.', 'buddyclients' ) . '</p>',
            ],
            'payment' => [
                'label' => __('Payment Status Updated', 'buddyclients'),
                'subject' => __('Payment Status Updated', 'buddyclients'),
                'content' => __('<p>The status of your payment for {{service_name}} for {{project_name}} is now {{payment_status}}.</p><p><a href="{{project_link}}">View the project.</a></p>', 'buddyclients'),
            ],
            'new_booking_admin' => [
                'label' => __('New Booking Admin Notification', 'buddyclients'),
                'subject' => __('Woo hoo! New Booking', 'buddyclients'),
                'content' => __('<p>A new booking has been confirmed. {{client_name}} successfully booked {{service_name}}.</p><p><a href="{{admin_bookings_link}}">View all bookings.</a></p>', 'buddyclients'),
            ],
            'contact_form_confirmation' => [
                'label' => __('Contact Form User Confirmation', 'buddyclients'),
                'subject' => __('Thank you for contacting {{site_name}}', 'buddyclients'),
                'content' => __('<p>We have received your message and will be in touch as soon as possible.</p><p><strong>Your Submitted Information:</strong></p><p>Name: {{client_name}}</p><p>Email: {{user_email}}</p><p>Message: {{message}}</p>', 'buddyclients'),
            ],
            'new_testimonial' => [
                'label' => __('New Testimonial Submission', 'buddyclients'),
                'subject' => __('New Testimonial Submission on {{site_name}}', 'buddyclients'),
                'content' => __('You have received a new testimonial submission from {{client_name}}. <a href="{{admin_testimonials_link}}">View all submissions.</a>', 'buddyclients'),
            ],
            'new_affiliate' => [
                'label' => __('New Affiliate', 'buddyclients'),
                'subject' => __('Welcome to the Affiliate Program!', 'buddyclients'),
                'content' => __('<p>Welcome to {{site_name}}’s affiliate program!</p><p>Share your unique affiliate link to begin earning commission: {{affiliate_link}}.</p>', 'buddyclients'),
            ],
            'availability_reminder' => [
                'label' => __('Update Availability Reminder', 'buddyclients'),
                'subject' => __('Update Your Availability', 'buddyclients'),
                'content' => '<p>' . __('Your availability date is expiring. <a href="{{availability_link}}">Log in to {{site_name}}</a> to add the date you are next available.', 'buddyclients') . '</p>',
            ],
            'cancellation_request_admin' => [
                'label' => __('Cancellation Request Admin Notification', 'buddyclients'),
                'subject' => __('Cancellation Request', 'buddyclients'),
                'content' => __('<p>{{client_name}} has requested to cancel {{service_name}} for <a href="{{project_link}}">{{project_name}}</a>.</p><p>Reason: {{cancellation_reason}}</p>', 'buddyclients'),
                'required' => true
            ],
            'contact_form_admin' => [
                'label' => __('Contact Form Admin Notification', 'buddyclients'),
                'subject' => __('{{site_name}} Contact Form', 'buddyclients'),
                'content' => __('<p>{{message}}.</p><p>{{client_name}}<br>{{reply_to}}</p>', 'buddyclients'),
                'required' => true
            ],
            'sales_sub' => [
                'label' => __('Sales Team Booking', 'buddyclients'),
                'subject' => __('Ready for Checkout', 'buddyclients'),
                'content' => '<p>' . __('Your services from {{site_name}} are ready! <a href="{{sales_checkout_link}}">Click here to finalize your booking.</a>', 'buddyclients') . '</p>',
                'required' => true
            ],
        ];
        
        /**
         * Filters the email templates.
         *
         * @since 0.3.4
         *
         * @param array  $pages    An array of email template data.
         */
         $templates = apply_filters( 'bc_email_templates', $templates );
         
        return $templates;
        
    }
    
    /**
     * Creates all email temmplates.
     * 
     * @since 0.1.0
     */
    public static function create() {
        
        // Initialize array
        $emails = array();
    
        // Get all templates
        $templates = self::templates();
    
        // Get existing setting
        $assigned_emails = get_option('bc_email_templates', array());
    
        // Create email posts
        foreach ($templates as $key => $data) {
            
            // Check if email template exists and is already published
            if (isset($assigned_emails[$key])) {
                
                // Get post status
                $post_status = get_post_status($assigned_emails[$key]);
                
                // Skip if already published
                if ($post_status === 'publish') {
                    continue;
                }
            }
    
            // Define post args
            $args = array(
                'post_title'    => $data['subject'],
                'post_content'  => $data['content'],
                'post_status'   => 'publish',
                'post_type'     => 'bc_email'
            );
    
            $post_id = wp_insert_post($args);
    
            // Add post id to array
            $emails[$key] = $post_id;
        }
        
        // Merge the newly created email IDs with the existing settings
        $updated_emails = array_merge($assigned_emails, $emails);
    
        // Save merged array in settings
        update_option('bc_email_templates', $updated_emails);
    }
    
}