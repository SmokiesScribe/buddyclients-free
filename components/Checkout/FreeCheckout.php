<?php
namespace BuddyClients\Components\Checkout;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles checkout for free services.
 * 
 * Creates a successful booking and redirects to the succeeded confirmation page.
 *
 * @since 0.1.0
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

        // Create successful payment
        $this->successful_payment();
        
        // Redirect to group
        $this->redirect();
    }
    
    /**
     * Creates successful booking.
     * 
     * @since 0.1.0
     */
    private function successful_booking() {
        buddyc_booking_success( $this->booking_intent_id );
    }

    /**
     * Creates successful BookingPayment.
     * 
     * @since 1.0.27
     */
    private function successful_payment() {
        $booking_intent = buddyc_get_booking_intent( $this->booking_intent_id );
        $payment_ids = $booking_intent->payment_ids;
        if ( ! empty( $payment_ids ) ) {
            buddyc_payment_success( $payment_ids[0] );
        }
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