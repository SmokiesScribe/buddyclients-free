<?php
namespace BuddyClients\Components\Checkout;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Booking\SuccessfulBooking;

/**
 * Handles checkout for free services.
 * 
 * Creates a successful booking and redirects to the succeeded confirmation page.
 *
 * @since 0.1.0
 * 
 * @see SuccessfulBooking
 */
class FreeCheckout {
    
    /**
     * The ID of the BookingIntent.
     * 
     * @var int
     */
    private $booking_intent_id;
    
    /**
     * Creates a new free checkout.
     * 
     * @since 0.1.0
     */
    public function __construct( $post_data ) {
        $this->booking_intent_id = $post_data['booking_intent_id'];
        
        // Create successful booking
        $this->successful_booking();
        
        // Redirect to group
        $this->redirect();
    }
    
    /**
     * Creates successful booking.
     * 
     * @since 0.1.0
     */
    private function successful_booking() {
        new SuccessfulBooking( $this->booking_intent_id );
    }
    
    /**
     * Redirects to group.
     * 
     * @since 0.1.0
     */
    private function redirect() {
        $confirmation_page = get_permalink( buddyc_get_setting( 'pages', 'confirmation_page' ) );
        $confirmation_page = $confirmation_page . '?redirect_status=succeeded&free=true';
        wp_redirect( $confirmation_page );
        exit;
    }
}