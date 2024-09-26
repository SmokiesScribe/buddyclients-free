<?php
@session_start();
/**
 * Create Payment Intent.
 * 
 * Creats a Stripe payment intent on checkout.
 * 
 * @since 0.1.0
 */
    // Load WP
    require_once $_SERVER['DOCUMENT_ROOT'] .'/wp-load.php';
    
    // Load Stripe library
    bc_stripe_library();
    
    // Get Stripe keys
    $keys = new BuddyClients\Components\Stripe\StripeKeys;
    
    // Get the raw POST data
    $post_data = file_get_contents('php://input');
    
    // Decode the JSON data
    $request_data = json_decode($post_data, true);
    
    // Build Stripe
    $stripe = new \Stripe\StripeClient($keys->secret);
    
    // Check if the 'bookingIntent' parameter exists in the decoded data
    if (isset($request_data['bookingIntent'])) {
        // Retrieve the value of the 'bookingIntent' parameter
        $booking_intent = $request_data['bookingIntent'];
        
        // Retrieve the total fee from the booking intent
        $order_amount = $booking_intent['total_fee'];
        
        // Retrieve Stripe customer ID
        $client = new BuddyClients\Includes\Client( $booking_intent['post']['user-id'] );
        $stripe_customer_id = $client->get_customer_id();
        
        header('Content-Type: application/json');
        
        try {
            // Create a PaymentIntent with the items and currency ('usd')
            $paymentIntentParams = [
                'amount' => round($order_amount * 100), // Convert the total amount to cents
                'currency' => 'usd',
                'description' => $booking_intent['service_names'],
                'metadata' => [
                    'ID' => $booking_intent['ID'],
                    'class' => $booking_intent['class'],
                ],
                // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ];
            
            // Check if the customer ID is available
            if ($stripe_customer_id) {
                // Add customer ID to PaymentIntent parameters
                $paymentIntentParams['customer'] = $stripe_customer_id;
            }
            
            // Check if the customer email is available
            if ( isset( $booking_intent['client_email'] ) && $booking_intent['client_email'] ) {
                // Add customer ID to PaymentIntent parameters
                $paymentIntentParams['receipt_email'] = $booking_intent['client_email'];
            }
            
            // Create the PaymentIntent
            $paymentIntent = $stripe->paymentIntents->create($paymentIntentParams);
            
            // Add the PaymentIntent ID to the BookingIntent
            BuddyClients\Components\Booking\BookingIntent::update_payment_intent_id( $booking_intent['ID'], $paymentIntent->id );
        
            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];
        
            echo json_encode($output);
        } catch (Error $e) {
            http_response_code(500);
            error_log(json_encode(['error' => $e->getMessage()]));
        }
        
    } else {
        // Handle the case where data is not available
        error_log('Order information not found in session');
    }
    ?>
