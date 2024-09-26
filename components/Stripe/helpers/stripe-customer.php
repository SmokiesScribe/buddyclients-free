<?php
/**
 * Stripe customer.
 * 
 * Handles creation of stripe customers.
 * 
 * @since 0.1.0
 */
    /**
     * Retrieve or create Stripe customer.
     * 
     * @since 0.1.0
     * 
     * @param int $user_id
     * @return int $stripe_customer_id
     */
    function bc_stripe_customer( $user_id, $booking_intent_id ) {
        
        // Exit if guest
        if ( $user_id === 'guest' ) {
            return;
        }
        
        // Load Stripe library
        bc_stripe_library();
        
        // Get Stripe keys
        $keys = new BuddyClients\Components\Stripe\StripeKeys;
        
        // Get Client object
        $client = new BuddyClients\Includes\Client( $user_id );
        
        // Build Stripe
        $stripe = new \Stripe\StripeClient( $keys->secret );
        
        // Check if the user already has a Stripe customer ID associated with their account
        $existing_stripe_customer_id = $client->get_customer_id();
        
        // Define customer details
        $user_details = [
            'email' => $client->email,
            'name' => $client->name,
            'metadata' => [
                'user_id' => $client->ID,
                'handle' => $client->handle,
            ],
        ];
    
        try {
            
            // No existing customer id
            if ( empty( $existing_stripe_customer_id ) ) {
                
                // Create a new Stripe customer
                $customer = $stripe->customers->create( $user_details );
                
                // Update customer ID
                $client->update_customer_id( $customer->id );
                
                // Get PaymentIntent ID
                $payment_intent_id = BuddyClients\Components\Booking\BookingIntent::get_payment_intent_id( $booking_intent_id );
                
                // Update PaymentIntent with customer
                $stripe->paymentIntents->update(
                    $payment_intent_id,
                    ['customer' => $customer->id]
                );
            
            // Existing customer ID    
            } else {
                
                try {
                    // Check if the customer exists in Stripe
                    $customer = $stripe->customers->retrieve( $existing_stripe_customer_id );
                    
                    // If customer exists, update the customer's details
                    $customer = $stripe->customers->update( $existing_stripe_customer_id, $user_details );
                    
                } catch (\Stripe\Exception\InvalidRequestException $e) {

                    // Customer not found in Stripe, delete user meta and try again
                    $client->delete_customer_id();
                    
                    // Call the function again to create a new customer
                    return bc_stripe_customer( $user_id );
                }
            }
            
            // Return customer id
            return $client->get_customer_id();
            
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Handle other Stripe API errors
            error_log('Stripe error: ' . $e->getMessage());
            return false; // Indicate failure
            
        } catch (Exception $e) {
            // Handle other exceptions
            error_log('Error: ' . $e->getMessage());
            return false; // Indicate failure
        }
    }
    add_action('bc_created_user', 'bc_stripe_customer', 10, 2); // User created at checkout
    add_action('bc_user_checkout', 'bc_stripe_customer', 10, 2); // User submits booking form