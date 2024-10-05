<?php
namespace BuddyClients\Components\Booking;

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
        session_start();
        
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
        //$_SESSION['booking_intent'] = $booking_intent;
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
         do_action( 'bc_booking_form_submission', $booking_intent );
        
        // Redirect to the checkout page
        $checkout_page = bc_get_setting('pages', 'checkout_page');
        $redirect_url = get_permalink($checkout_page);
        header("Location: $redirect_url");
        exit();
    }
}