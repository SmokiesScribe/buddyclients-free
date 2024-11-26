<?php
namespace BuddyClients\Components\Checkout;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
        // Fetch all ids
        $url_ids = $this->fetch_url_intent_ids();
        $session_ids = $this->fetch_session_intent_ids();

        // Check url params then session
        $booking_id = $url_ids['booking_id'] ?? $session_ids['booking_id'] ?? null;
        $registration_id = $url_ids['registration_id'] ?? $session_ids['registration_id'] ?? null;
        $sponsor_id = $url_ids['sponsor_id'] ?? $session_ids['sponsor_id'] ?? null;

        // Booking
        if ( $booking_id ) {
            $this->intent_id = $booking_id;
            $this->intent_type = 'booking';
            $this->intent_class = BookingIntent::class;
        
        // Registration
        } else if ( $registration_id ) {
            $this->intent_id = $registration_id;
            $this->intent_type = 'registration';
            $this->intent_class = RegistrationIntent::class;
        
        // Sponsor
        } else if ( $sponsor_id ) {
            $this->intent_id = $sponsor_id;
            $this->intent_type = 'sponsor';
            $this->intent_class = SponsorIntent::class;
        }
    }

    /**
     * Fetches the intent id from the url param.
     * 
     * @since 1.0.15
     */
    private function fetch_url_intent_ids() {
        return [
            'booking_id'        => buddyc_get_param( 'booking_id' ),
            'registration_id'   => buddyc_get_param( 'registration_id' ),
            'sponsor_id'        => buddyc_get_param( 'sponsor_id' )
        ];
    }

    /**
     * Fetches the intent id from the session data.
     * 
     * @since 1.0.15
     */
    private function fetch_session_intent_ids() {
        return [
            'booking_id'      => isset( $_SESSION['booking_id'] ) ? absint( wp_unslash( $_SESSION['booking_id'] ) ) : null,
            'registration_id' => isset( $_SESSION['registration_id'] ) ? absint( wp_unslash( $_SESSION['registration_id'] ) ) : null,
            'sponsor_id'      => isset( $_SESSION['sponsor_id'] ) ? absint( wp_unslash( $_SESSION['sponsor_id'] ) ) : null
        ];
    }

    /**
     * Fetches an intent by ID and type.
     * 
     * @since 1.0.17
     */
    public static function get_intent( $intent_id, $intent_type ) {
        $intent = null;
        switch ( $intent_type ) {
            case 'booking':
                $intent = BookingIntent::get_booking_intent( $intent_id );
                break;
            case 'registration':
                $intent = RegistrationIntent::get_registration_intent( $intent_id );
                break;
            case 'sponsor':
                $intent = SponsorIntent::get_sponsor_intent( $intent_id );
                break;
        }
        return $intent;
    }

}