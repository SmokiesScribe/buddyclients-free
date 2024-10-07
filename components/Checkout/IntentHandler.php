<?php
namespace BuddyClients\Components\Checkout;

use BuddyClients\Components\Booking\BookingIntent;
use BuddyEvents\Includes\Registration\RegistrationIntent;
use BuddyEvents\Includes\Sponsor\SponsorIntent;

/**
 * Fetches intents for checkout and confirmation.
 *
 * @since 1.0.15
 */
class IntentHandler {

    /**
     * The intent object.
     * 
     * @var object
     */
    public $intent;

    /**
     * The ID of the intent.
     * 
     * @var int
     */
    public $intent_id;

    /**
     * The type of intent.
     * Accepts 'booking', 'registration', and 'sponsor'.
     * 
     * @var string
     */
    public $intent_type;

    /**
     * The object class of the intent.
     * 
     * @var string
     */
    public $intent_class;

    /**
     * Constructor method.
     * 
     * @since 1.0.15
     */
    public function __construct() {
        $this->fetch_intent_info();
        $this->fetch_intent();
    }

    /**
     * Fetches the intent object.
     * 
     * @since 1.0.15
     */
    private function fetch_intent() {
        // Make sure intent class exists
        if ( $this->intent_class && class_exists( $this->intent_class ) ) {
            // Get intent object by type
            switch ( $this->intent_type ) {
                case 'booking':
                    $this->intent = BookingIntent::get_booking_intent( $this->intent_id );
                    break;
                case 'registration':
                    $this->intent = RegistrationIntent::get_registration_intent( $this->intent_id );
                    break;
                case 'sponsor':
                    $this->intent = Sponsor_intent::get_sponsor_intent( $this->intent_id );
                    break;
            }
        }
    }

    /**
     * Fetches the intent id.
     * 
     * Sets variables based on the intent type.
     * 
     * @since 1.0.15
     */
    private function fetch_intent_info() {
        $booking_id = bc_get_param( 'booking_id' );
        $registration_id = bc_get_param( 'registration_id' );
        $sponsor_id = bc_get_param( 'sponsor_id' );

        if ( $booking_id ) {
            $this->intent_id = $booking_id;
            $this->intent_type = 'booking';
            $this->intent_class = BookingIntent::class;

        } else if ( $registration_id ) {
            $this->intent_id = $registration_id;
            $this->intent_type = 'registration';
            $this->intent_class = RegistrationIntent::class;

        } else if ( $sponsor_id ) {
            $this->intent_id = $sponsor_id;
            $this->intent_type = 'sponsor';
            $this->intent_class = SponsorIntent::class;
        }
    }

}