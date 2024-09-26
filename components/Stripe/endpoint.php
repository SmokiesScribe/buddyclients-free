<?php
/**
 * Stripe endpoint.
 *
 * Receives all events from Stripe webhook.
 *
 * @since 0.1.0
 */
    // Bail if it's not Stripe
    if (!isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
        return;
    }
    
    // Load WP
    require_once $_SERVER['DOCUMENT_ROOT'] .'/wp-load.php';
    
    // Load Stripe library
    require_once ABSPATH . 'wp-content/plugins/buddyclients/vendor/stripe/init.php';
    
    // Get Stripe keys
    $keys = new BuddyClients\Components\Stripe\StripeKeys;
    
    // Initialize Stripe PHP SDK
    \Stripe\Stripe::setApiKey( $keys->secret );
    
    // Get payload
    $payload = @file_get_contents('php://input');
    
    // Initialize event
    $event = null;
    
    // Retrieve payload contents
    try {
      $event = \Stripe\Event::constructFrom(
        json_decode($payload, true)
      );
    } catch(\UnexpectedValueException $e) {
      // Invalid payload
      echo '⚠️  Webhook error while parsing basic request.';
      http_response_code(400);
      exit();
    }
    
    // Verify endpoint secret
    if ( $keys->signing ) {
      $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
      try {
        $event = \Stripe\Webhook::constructEvent(
          $payload, $sig_header, $keys->signing
        );
      } catch(\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        echo '⚠️  Webhook error while validating signature.';
        http_response_code(400);
        exit();
      }
    }

    // Get Stripe event object
    $event_object = $event->data->object;
    
    // Get metadata
    $metadata = isset($event_object->metadata) ? $event_object->metadata : array();
    
    // Get event type
    $event_type = $event->type;
    
    switch ( $event_type ) {
        case 'payment_intent.succeeded':
            
            // Successful booking
            if ( $metadata['class'] === 'BookingIntent' ) {
                // New successful booking
                new BuddyClients\Components\Booking\SuccessfulBooking( $metadata['ID'] );
                
            // Successful registration
            } else if ( $metadata['class'] === 'RegistrationIntent' ) {
                // New successful registration
                new BuddyEvents\Includes\Registration\SuccessfulRegistration( $metadata['ID'] );

            // Successful sponsorship
            } else if ( $metadata['class'] === 'SponsorIntent' ) {
                // New successful sponsorship
                new BuddyEvents\Includes\Sponsor\SuccessfulSponsor( $metadata['ID'] );
            }
            
            /**
             * Fires on Stripe payment intent succeeded.
             * 
             * Also fires on successful free checkout.
             * 
             * @since 0.1.0
             * 
             * @param array $metadata The metadata from the Stripe event object.
             */
            do_action('bc_stripe_payment_success', $metadata);
            break;
            
        case 'customer.created':
        case 'customer.updated':
        case 'customer.deleted':
    }

    /**
     * Five by five.
     */
    http_response_code(200);