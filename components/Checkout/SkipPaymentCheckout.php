<?php
namespace BuddyClients\Components\Checkout;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles checkout for paid services when Stripe is not enabled.
 * 
 * Updates the booking intent status and redirects to the succeeded confirmation page.
 *
 * @since 0.1.20
 */
class SkipPaymentCheckout {
    
    /**
     * The ID of the BookingIntent.
     * 
     * @var int
     */
    private $booking_intent_id;
    
    /**
     * Creates a new successful checkout without payment.
     * 
     * @since 1.0.20
     * @todo Add support for BuddyEvents.
     */
    public function __construct( $post_data ) {
        $this->booking_intent_id = $post_data['booking_intent_id'];

        // Successful booking
        buddyc_booking_success( $this->booking_intent_id, $status = 'unpaid' );

        /**
         * Fires on submission of a booking that requires payment.
         * 
         * @since 1.0.20
         * 
         * @param   int $booking_intent_id  The ID of the BookingIntent.
         */
        do_action( 'buddyc_unpaid_booking', $this->booking_intent_id );
        
        // Redirect to group
        $this->redirect();
    }
    
    /**
     * Redirects to confirmation page.
     * 
     * @since 0.1.0
     */
    private function redirect() {
        $confirmation_page = get_permalink( buddyc_get_setting( 'pages', 'confirmation_page' ) );
        $confirmation_page = $confirmation_page . '?redirect_status=succeeded&free=false&unpaid=true';
        wp_redirect( $confirmation_page );
        exit;
    }
}