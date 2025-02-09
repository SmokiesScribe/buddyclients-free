<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Booking form submission.
 * 
 * Handles submission of the booking form.
 * Creates a BookingIntent and sends the client to checkout.
 *
 * @since 0.1.0
 */
class BookingFormSubmission {
    
    /**
     * Constructor method.
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     * 
     * @since 0.1.0
     */
    public function __construct(array $post_data, ?array $files_data) {
        // Handle the submission
        $this->handle_form_submission($post_data, $files_data);
    }
    
    /**
     * Handles form submission.
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     * 
     * @since 0.1.0
     */
    private function handle_form_submission(array $post_data, ?array $files_data) {
        
        // Create booking intent
        $booking_intent = new BookingIntent($post_data, $files_data);
        
        // Store in session
        $_SESSION['booking_id'] = $booking_intent->ID;
        
        /**
         * Fires on Booking Form submission.
         * 
         * Fires after a Booking Intent is created and before redirect to checkout.
         *
         * @since 0.1.0
         * 
         * @param   object  $booking_intent     The created BookingIntent.
         */
         do_action( 'buddyc_booking_form_submission', $booking_intent );
        
        // Retrieve the checkout page url
        $checkout_url = buddyc_get_page_link( 'checkout_page' );

        // Output popup if no checkout page set
        if ( $checkout_url === '#' ) {
            $message = '<p>';
            $message .= __( 'Checkout is unavailable at this time.', 'buddyclients-free' );
            $message .= '</p>';

            $message = '<p>';
            $message .= sprintf(
                /* translators: %s: the contact us link */
                __( 'Please %s for assistance', 'buddyclients-free' ),
                buddyc_contact_message( false, true ) );
            $message .= '</p>';
            
            buddyc_output_popup( $message );
            return;
        }

        // Redirect to checkout page
        wp_redirect( $checkout_url );
        exit();
    }
}