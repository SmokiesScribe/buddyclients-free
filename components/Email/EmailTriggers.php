<?php
namespace BuddyClients\Components\Email;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Booking\{
    BookingIntent,
    BookedService\BookedService,
    BookedService\Payment
};

/**
 * Manages email triggers.
 * 
 * Defines hooks for plugin emails.
 * Defines args and data for each email event.
 *
 * @since 0.1.0
 */
class EmailTriggers {
    
    /**
     * Initializes hooks.
     * 
     * @since 0.1.0
     */
    public static function run() {
        // Define hooks
        self::define_hooks();
    }
    
    /**
     * Defines hooks.
     * 
     * @since 0.1.0
     */
    private static function define_hooks() {
        
        $hooks = [
            'buddyc_successful_booking'        => [self::class, 'successful_booking_emails'],
            'buddyc_cancel_request'            => [self::class, 'cancel_request_email'],
            'buddyc_service_status_updated'    => [self::class, 'service_status_email'],
            'buddyc_brief_updated'             => [self::class, 'brief_updated_email'],
            'buddyc_new_quote'                 => [self::class, 'new_quote_email'],
            'buddyc_new_booked_service'        => [self::class, 'new_assignment_email'],
            'buddyc_abandoned_booking'         => [self::class, 'abandoned_booking_email'],
            'buddyc_payment_paid'              => [self::class, 'payment_paid_email'],
            'buddyc_new_testimonial'           => [self::class, 'new_testimonial_email'],
            'buddyc_new_affiliate'             => [self::class, 'new_affiliate_email'],
            'availability_reminder'        => [self::class, 'availability_reminder_email'],
            'buddyc_booking_form_submission'   => [self::class, 'assisted_booking_email'],
        ];
        
        /**
         * Filters the email hooks and callbacks.
         *
         * @since 0.4.0
         *
         * @param array  $callbacks    An array of email hooks and callbacks.
         */
         $hooks = apply_filters( 'buddyc_email_hooks', $hooks );
        
        // Loop through hooks and call callable
        foreach ( $hooks as $hook => $callable ) {
            add_action( $hook, $callable, 10, 1 );
        }
    }
    
    /**
     * Handles successful booking emails.
     * 
     * @since 0.1.0
     * 
     * @param   object      $successful_booking     The SuccessfulBooking object.
     */
    public static function successful_booking_emails( $successful_booking ) {
        
        // Define emails to send
        $args = [
            'new_booking_admin' => [
                'to_email'      => 'admin',
                'client_name'   => bp_core_get_user_displayname( $successful_booking->client_id ),
                'project_id'    => $successful_booking->booking_intent->project_id,
                'service_name'  => $successful_booking->booking_intent->service_names,
                'project_name'  => bp_get_group_name( groups_get_group( $successful_booking->booking_intent->project_id ) ),
            ],
        ];
        
        // Loop through and send emails
        foreach ( $args as $key => $email_args ) {
            new Email( $key, $email_args );
        }
    }
    
    /**
     * Sends email on cancellation request.
     * 
     * @since 0.1.0
     * 
     * @param   object  $cancel_request  The CancelRequest object.
     */
    public static function cancel_request_email( $cancel_request ) {
        
        // Get BookedService object
        $booked_service = BookedService::get_booked_service( $cancel_request->booked_service_id );
        
        $args = [
            'to_email'              => 'admin',
            'client_name'           => bp_core_get_user_displayname( $booked_service->client_id ),
            'service_name'          => $booked_service->name,
            'project_id'            => $booked_service->project_id,
            'project_name'          => bp_get_group_name( groups_get_group( $booked_service->project_id ) ),
            'cancellation_reason'   => $cancel_request->cancellation_reason,
            'project_link'          => bp_get_group_permalink( groups_get_group( $booked_service->project_id ) ),
        ];
        new Email( 'cancellation_request_admin', $args );
    }
    
    /**
     * Sends email on service status change.
     * 
     * @since 0.1.0
     * 
     * @param object $booked_service    The BookedService object.
     * @param string $old_status        The old status.
     * @param string $new_status        The new status.
     */
    public static function service_status_email( $booked_service ) {
        $args = [
            'to_user_id'            => $booked_service->client_id,
            'service_name'          => $booked_service->name,
            'project_id'            => $booked_service->project_id,
            'project_name'          => bp_get_group_name( groups_get_group( $booked_service->project_id ) ),
            'service_status'        => $booked_service->status,
            'project_link'          => bp_get_group_permalink( groups_get_group( $booked_service->project_id ) ),
        ];
        new Email( 'service_status', $args );
    }
    
    /**
     * Sends email on service status change.
     * 
     * @since 0.1.0
     * 
     * @param   object  The BriefSubmission object.
     */
    public static function brief_updated_email( $brief_submission ) {
        
        // Get group members
        $args = array(
            'group_id' => $brief_submission->project_id,
            'per_page' => -1,
            'exclude_admins_mods' => false,
            'exclude_banned' => false,
        );
        
        // Get group members data
        $members_data = groups_get_group_members($args);
        
        // Extract member IDs from the returned data
        $member_ids = array();
        if ( ! empty($members_data['members'] ) ) {
            foreach ( $members_data['members'] as $member ) {
                $member_ids[] = $member->ID;
            }
        }
        
        // Loop through group members and send email
        foreach ( $member_ids as $member_id ) {
        
            $args = [
                'to_user_id'        => $member_id,
                'brief_type'        => $brief_submission->brief_type,
                'project_id'        => $brief_submission->project_id,
                'project_name'      => bp_get_group_name( groups_get_group( $brief_submission->project_id ) ),
                'project_link'      => bp_get_group_permalink( groups_get_group( $brief_submission->project_id ) ),
            ];
            new Email( 'updated_brief', $args );
        }
    }
    
    /**
     * Sends email to team member on new assignment.
     * 
     * @since 0.1.0
     * 
     * @param object $booked_service    The BookedService object.
     */
    public static function new_assignment_email( $booked_service ) {
        $args = [
            'to_user_id'            => $booked_service->team_id,
            'service_name'          => $booked_service->name,
            'project_id'            => $booked_service->project_id,
            'project_name'          => bp_get_group_name( groups_get_group( $booked_service->project_id ) ),
            'project_link'          => bp_get_group_permalink( groups_get_group( $booked_service->project_id ) ),
        ];
        new Email( 'new_assignment', $args );
    }
    
    /**
     * Sends email on new assisted booking.
     * 
     * @since 0.1.0
     * 
     * @param object $booking_intent    The BookingIntent object.
     */
    public static function assisted_booking_email( $booking_intent ) {
        // Check if a sales id exists and the booking was not previously paid
        if ( $booking_intent->sales_id && ! $booking_intent->previously_paid ) {
            // Email the client
            $args = [
                'to_email'              => $booking_intent->client_email,
                'sales_checkout_link'   => $booking_intent->build_checkout_link()
            ];
            new Email( 'sales_sub', $args );
        }
    }

    /**
     * Sends email on payment status changed to 'paid'.
     * 
     * @since 0.1.0
     * 
     * @param int $payment_id    The ID of the Payment object.
     */
    public static function payment_paid_email( $payment_id ) {
        
        // Get payment object
        $payment = Payment::get_payment( $payment_id );
        
        // Get booking intent
        $booking_intent = BookingIntent::get_booking_intent( $payment->booking_intent_id );
        
        // Email the payee
        $args = [
            'to_email'              => $booking_intent->client_email,
            'sales_checkout_link'   => $booking_intent->build_checkout_link()
        ];
        new Email( 'sales_sub', $args );
    }
    
    /**
     * Sends email to admin on new testimonial submission.
     * 
     * @since 0.1.0
     * 
     * @param   int     $testimonial_id         The ID of the testimonial post.
     * @param   string  $client_name            The name of the testimonial author.
     */
    public static function new_testimonial_email( $testimonial_id ) {

        // Get the testimonial author
        $client_id = get_post_field( 'post_author', $testimonial_id );
        $client_name = bp_core_get_user_displayname( $client_id );
        
        // Email the admin
        $args = [
            'to_email'      => 'admin',
            'client_name'   => $client_name,
        ];
        new Email( 'new_testimonial', $args );
    }
    
    /**
     * Sends email when a user joins the affiliate program.
     * 
     * @since 0.1.0
     * 
     * @param   int     $affiliate_id         The ID of new affiliate.
     */
    public static function new_affiliate_email( $affiliate_id ) {
        $args = [
            'to_email'          => buddyc_affiliate_email( $affiliate_id ),
            'affiliate_link'    => buddyc_affiliate_link( $affiliate_id ),
        ];
        new Email( 'new_affiliate', $args );
    }
    
    /**
     * Sends email when a user joins the affiliate program.
     * 
     * @since 0.1.0
     * 
     * @param   object     $cancel_request         The CancelRequestSubmission object.
     */
    public static function new_cancel_request( $cancel_request ) {
        $args = [
            'to_email'          => buddyc_affiliate_email( $affiliate_id ),
            'affiliate_link'    => buddyc_affiliate_link( $affiliate_id ),
        ];
        new Email( 'new_affiliate', $args );
    }
    
    /**
     * Sends email to remind team members to update their avaialbility.
     * 
     * @since 0.1.0
     * 
     * @param   array     $args {
     *     An array of arguments.
     * 
     *     @type    int     $user_id    The ID of the team member.
     * }
     */
    public static function availability_reminder_email( $args ) {
        $user_id = $args['user_id'];
        
        $args = [
            'to_user_id'        => $user_id,
            'availability_link' => buddyc_profile_ext_link( 'availability' ),
        ];
        new Email( 'new_affiliate', $args );
    }
    
    /**
     * Sends email when a new quote is created.
     * 
     * @since 0.1.0
     * 
     * @param   object  $quote  The Quote object.
     */
    public static function new_quote_email( $quote ) {
        $args = [
            'to_user_id'            => $quote->client_id,
            'service_name'          => $quote->title
        ];
        new Email( 'custom_quote', $args );
    }
    
    /**
     * Sends email when a booking is abandoned.
     * 
     * @since 0.1.0
     * 
     * @param   object  $booking_intent  The BookingIntent object.
     */
    public static function abandoned_booking_email( $booking_intent ) {
        // Make sure we have an email
        if ( ! $booking_intent->client_email ) {
            return;
        }
        $args = [
            'to_email'      => $booking_intent->client_email,
            'to_user_id'    => $booking_intent->client_id,
        ];
        new Email( 'abandoned_booking', $args );
    }
}