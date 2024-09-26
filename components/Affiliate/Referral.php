<?php
namespace BuddyClients\Components\Affiliate;

use BuddyClients\Components\Booking\{
    BookingIntent   as BookingIntent
};


/**
 * Referral generated on a successful booking.
 * 
 * Attaches the affiliate ID to the booking.
 *
 * @since 0.1.0
 * 
 * @see SuccessfulBooking
 * @deprecated
 */
class Referral {
    
    /**
     * The ID of the client.
     * 
     * @var int|string
     */
    public $client_id;
    
    /**
     * The type of commission from settings.
     * Accepts 'lifetime' or 'first_sale'.
     * 
     * @var string
     */
    public $commission_type;
    
    /**
     * The ID of the affiliate.
     * 
     * @var ?int
     */
    public $affiliate_id;
    
    /**
     * The ID of the BookingIntent.
     * 
     * @var int
     */
    private $booking_intent_id;
    
    /**
     * The BookingIntent object.
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
        @session_start();
        
        // Get booking intent
        $this->booking_intent_id = $booking_intent_id;
        $this->booking_intent = BookingIntent::get_booking_intent( $this->booking_intent_id );
        
        // Get client ID
        $this->client_id = $this->booking_intent->client_id;
        
        // Get affiliate ID
        $this->affiliate_id = $this->booking_intent->affiliate_id;
        
        // Update BookingIntent
        if ( $this->affiliate_id ) {
            $this->update_booking_intent();
        }
    }
    
    /**
     * Adds affiliate ID to BookingIntent.
     * 
     * @since 0.1.0
     */
    private function update_booking_intent() {
        BookingIntent::update_booking_intent( $this->booking_intent_id, 'affiliate_id', $this->affiliate_id );
    }
}