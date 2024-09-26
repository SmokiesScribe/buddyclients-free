<?php
use BuddyClients\Components\{
    Booking\BookingIntent as BookingIntent,
    Booking\SuccessfulBooking as SuccessfulBooking,
    Stripe\StripeKeys as StripeKeys
};
use Stripe\Stripe;
use Stripe\PaymentIntent;
/**
 * Checks for successful Stripe payments.
 * 
 * @since 0.3.1
 */
function bc_check_for_payments() {
    
    // Initialize count
    $count = 0;
    
    // Load Stripe library
    bc_stripe_library();
    
    // Get stripe keys
    $stripe_keys = new StripeKeys;
    
    // Check if the secret key is set
    if ( empty( $stripe_keys->secret ) ) {
        return;
    }
    
    // Get all booking intents
    $booking_intents = BookingIntent::get_all_booking_intents();
    
    // Exit if no booking intents
    if ( ! $booking_intents ) {
        return;
    }
    
    // Initialize Stripe with the secret key
    $stripe = new \Stripe\StripeClient( $stripe_keys->secret );
    
    // Get all payment intents
    $payment_intents = $stripe->paymentIntents->all();
    
    // Loop through booking intents
    foreach ( $booking_intents as $booking_intent ) {
        
        // Make sure booking is not succeeded
        if ( $booking_intent->status !== 'succeeded' ) {
            
            // Loop through payment intents
            foreach ( $payment_intents as $payment_intent ) {

                // Skip if payment is not succeeded
                if ( $payment_intent->status !== 'succeeded' ) {
                    continue;
                }
                
                // Compare booking intent ID
                if ( $booking_intent->ID == $payment_intent->metadata['ID'] ) {
                    // New successful booking
                    new SuccessfulBooking( $payment_intent->metadata['ID'] );
                    
                    // Add to count
                    $count += 1;
                    
                    // Break loop
                    break;
                }
            }
        }
    }
    return $count;
}
add_action('admin_init', 'bc_check_for_payments');