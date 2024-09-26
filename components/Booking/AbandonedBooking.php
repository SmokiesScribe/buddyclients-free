<?php
namespace BuddyClients\Components\Booking;

/**
 * Abandoned booking.
 * 
 * Handles a booking that was abandoned before checkout was complete.
 * Schedules an abandoned booking email.
 *
 * @since 0.1.0
 */
class AbandonedBooking {
    
    /**
     * The BookingIntent.
     * 
     * @var BookingIntent
     */
    private $booking_intent;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $booking_intent_id ) {
        $this->booking_intent = BookingIntent::get_booking_intent( $booking_intent_id );
        if ( $this->booking_intent->status !== 'succeeded' ) {
            
            /**
             * Fires 10 minutes after an incomplete booking attempt.
             * 
             * @since 0.1.0
             * 
             * @param   object  $booking_intent   The BookingIntent object.
             */
            do_action( 'bc_abandoned_booking', $this->booking_intent );
        }
    }
    
    /**
     * Sends the email.
     * 
     * @since 0.1.0
     */
    private function send_email() {
        
        // Make sure we have an email
        if ( ! $this->booking_intent->client_email ) {
            return;
        }
        
        // Build email args
        $args = [
            'to_email'      => $this->booking_intent->client_email,
            'to_user_id'    => $this->booking_intent->client_id,
        ];
        
        // Send and log email
        $email = new Email( 'abandoned_booking', $args );
    }
    
}