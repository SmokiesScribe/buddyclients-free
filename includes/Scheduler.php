<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles WP Cron scheduling.
 *
 * @since 1.0.27
 */
class Scheduler {

    /**
     * The event key.
     * 
     * @var string
     */
    public $event_key;

    /**
     * The name of the hook for the event.
     * 
     * @var string
     */
    public $hook;

    /**
     * The timeout unix timestamp.
     * 
     * @var string
     */
    private $timeout;

    /**
     * Args to pass to the callback.
     * 
     * @var array
     */
    private $args;

    /**
     * An identifier to prevent duplicate events.
     * 
     * @var string|int
     */
    public $identifier;

    /**
     * Constructor method. Schedules a new event.
     * 
     * @since 1.0.27
     * 
     * @param   array   $args {
     *     An array of args to construct the scheduled event.
     * 
     *     @param   string      $event_key  The event key.
     *     @param   string      $timeout    The timeout timestamp.
     *     @param   array       $args       Optional. An array of args to pass to the callback.
     *                                      Defaults to empty array.
     *     @param   string|int  $identifier Optional. An identifier to prevent duplicate events.
     * }
     */
    public function __construct( $args ) {
        $this->event_key = $args['event_key'];
        $this->hook = self::build_hook( $this->event_key );
        $this->timeout = $args['timeout'];
        $this->args = $args['args'] ?? [];
        $this->identifier = $args['identifier'] ?? '';
        
        $this->schedule_event();        
    }

    /**
     * Builds the transient name.
     * 
     * @since 1.0.27
     */
    private function build_transient() {
        return sprintf(
            'buddyc_event_scheduled_%1$s_%2$s',
            $this->event_key,
            $this->identifier
        );
    }

    /**
     * Schedules a single event.
     * 
     * @since 1.0.27
     */
    private function schedule_event() {
        // Check if already scheduled
        $transient = $this->build_transient();
        if ( $scheduled ) return;
        
        // Schedule event
        wp_schedule_single_event( $this->timeout, $this->hook, $this->args );

        // Set transient
        $expiration = $this->timeout + DAY_IN_SECONDS;
        set_transient( $transient, true, $expiration );
    }
    
    /**
     * Initializes scheduled events and defines hooks.
     * 
     * @since 1.0.27
     */
    public static function init() {
        $event_data = self::event_data();
        foreach ( $event_data as $key => $data ) {
            if ( isset( $data['callback'] ) && is_callable( $data['callback'] ) ) {
                $hook = self::build_hook( $key );
                $priority = $data['priority'] ?? 10;
                $args_count = $data['args_count'] ?? 0;
                add_action( $hook, $data['callback'], $priority, $args_count );
            }
        }
    }

    /**
     * Defines data for all event keys.
     * 
     * @since 1.0.27
     */
    private static function event_data() {
        return [
            'abandoned_booking' => [
                'callback'      => 'buddyc_abandoned_booking_check',
                'args_count'    => 1
            ],
            'payment_eligible' => [
                'callback'      => 'buddyc_payment_eligible',
                'args_count'    => 3
            ]
        ];
    }

    /**
     * Retrieves data for an event key.
     * 
     * @since 1.0.27
     * 
     * @param   string  $event_key  The key for the event.
     */
    private static function get_event_data( $event_key ) {
        $events = self::event_data();
        return $events[$event_key] ?? [];
    }

    /**
     * Builds the action hook from the event key.
     * 
     * @since 1.0.27
     * 
     * @param   string  $event_key  The key for the event.
     */
    private static function build_hook( $event_key ) {
        return sprintf(
            'buddyc_scheduled_%s',
            $event_key
        );
    }
}