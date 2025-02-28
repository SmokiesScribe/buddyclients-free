<?php
namespace BuddyClients\Components\Checkout;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Components\Booking\BookingPayment;

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
     * The BookingPayment object.
     * 
     * @var object
     */
    public $payment;

    /**
     * The ID of the BookingIntent.
     * 
     * @var int
     */
    public $intent_id;

    /**
     * The ID of the BookingPayment.
     * 
     * @var int
     */
    public $payment_id;

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
        $this->fetch_intent_data();
    }

    /**
     * Fetches the intent info and object.
     * 
     * @since 1.0.27
     */
    private function fetch_intent_data() {
        $this->intent_type = 'booking';
        $this->intent_class = BookingIntent::class;

        // Get booking and payment intent ids
        $this->intent_id = self::fetch_id( 'intent' );
        $this->payment_id = self::fetch_id( 'payment' );

        // Get the intent object
        $this->intent = self::fetch_object( $this->intent_id, 'intent' );
        $this->payment = self::fetch_object( $this->payment_id, 'payment' );
    }

    /**
     * Defines the cache key.
     * 
     * @since 1.0.27
     * 
     * @param   string  $object_type    The type of object or id being stored ('intent' or 'payment').
     * @param   string  $data_type      The type of data being stored ('object' or 'id').
     * @param   int     $intent_id      The ID of the object.
     */
    private static function cache_key( $object_type, $data_type, $intent_id ) {
        return sprintf(
            '_buddyc_intent_cache_%1$s_%2$s_%3$s',
            $object_type,
            $data_type,
            $intent_id
        );
    }

    /**
     * Stores an item in the cache.
     * 
     * @since 1.0.27
     * 
     * @param   mixed   $value          The value to cache.
     * @param   string  $object_type    The type of object or id being stored ('intent' or 'payment').
     * @param   string  $data_type      The type of data being stored ('object' or 'id').
     * @param   int     $intent_id      Optional. The ID of the object.
     */
    private static function set_cache( $value, $object_type, $data_type, $intent_id = null ) {
        $cache_key = self::cache_key( $object_type, $data_type, $intent_id );
        wp_cache_set( $cache_key, $value, 'buddyclients-free', 3600 );
    }

    /**
     * Retrieves an item from the cache.
     * 
     * @since 1.0.27
     * 
     * @param   string  $object_type    The type of object or id being stored ('intent' or 'payment').
     * @param   string  $data_type      The type of data being stored ('object' or 'id').
     * @param   int     $intent_id      Optional. The ID of the object.
     */
    private static function get_cached( $object_type, $data_type, $intent_id = null ) {
        $cache_key = self::cache_key( $object_type, $data_type, $intent_id );
        return wp_cache_get( $cache_key, 'buddyclients-free' );
    }

    /**
     * Fetches the intent id and payment id.
     * 
     * Sets variables based on the intent type.
     * 
     * @since 1.0.15
     * 
     * @param   string  $type   The type of ID ('intent' or 'payment').
     */
    private function fetch_id( $object_type ) {

        // Check transient
        $cached = self::get_cached( $object_type, 'id' );
        if ( $cached ) return $cached;

        // Check url param
        $id = $this->get_url_id( $object_type );

        // Check session if necessary
        if ( empty( $id ) ) {
            $id = $this->get_session_id( $object_type );
        }

        // Check if the id was found
        if ( ! empty( $id ) ) {

            // Set the cache
            self::set_cache( $id, $object_type, 'id' );

            // Return the id
            return $id;
        }
    }

    /**
     * Fetches the intent object from the database.
     * 
     * @since 1.0.15
     * 
     * @param   int     $intent_id  The ID of the intent.
     * @param   string  $type       The type of obejct ('intent' or 'payment').
     */
    public static function fetch_object( $intent_id, $type ) {
        if ( ! $intent_id ) return;

        // Check transient
        $cached = self::get_cached( $type, 'object', $intent_id );
        if ( $cached ) return $cached;

        // Get class
        $class = match ( $type ) {
            'intent'    => BookingIntent::class,
            'payment'   => BookingPayment::class,
            default     => null
        };

        // Make sure intent class exists
        if ( $class && class_exists( $class ) ) {

            // Get intent by type
            $intent = match ( $type ) {
                'intent'    => BookingIntent::get_booking_intent( $intent_id ),
                'payment'   => BookingPayment::get_payment( $intent_id ),
                default     => null
            };

            // Set cache
            self::set_cache( $intent, $type, 'object', $intent_id );

            // Return object
            return $intent;
        }
    }

    /**
     * Defines the url and session param key.
     * 
     * @since 1.0.27
     * 
     * @param   string  $object_type    The type of object whose ID we're fetching ('intent' or 'payment').
     */
    private static function param_key( $object_type ) {
        return match ( $object_type ) {
            'intent'    => 'booking_id',
            'payment'   => 'payment_id',
            default     => null
        };
    }

    /**
     * Fetches the intent id from the url param.
     * 
     * @since 1.0.15
     * 
     * @param   string  $object_type    The type of object whose ID we're fetching ('intent' or 'payment').
     */
    private function get_url_id( $object_type ) {

        // Define url param
        $param = self::param_key( $object_type );
        if ( ! $param ) return;

        // Get the param value
        return buddyc_get_param( $param );
    }

    /**
     * Fetches the intent id from the session param.
     * 
     * @since 1.0.15
     * 
     * @param   string  $object_type    The type of object whose ID we're fetching ('intent' or 'payment').
     */
    private function get_session_id( $object_type ) {

        // Define session param
        $param = self::param_key( $object_type );
        if ( ! $param ) return;

        // Retrieve and sanitize the session data
        return isset( $_SESSION[$param] ) ? absint( wp_unslash( $_SESSION[$param] ) ) : null;
    }
}