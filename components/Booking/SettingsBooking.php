<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the Booking settings.
 *
 * @since 1.0.25
 */
class SettingsBooking {

    /**
     * Defines default Booking settings.
     * 
     * @since 1.0.25
     */
    public static function defaults() {
        return [
            'freelancer_id'         => '',
            'cancellation_window'   => 0,
            'minimum_fee'           => 1,
            'accept_bookings'       => 'open',
            'lock_team'             => 'lock',
            'skip_payment'          => 'no',
            'enable_projects'       => 'yes',
        ];
    }
    
   /**
     * Defines the Booking settings.
     * 
     * @since 1.0.25
     */
    public static function settings() {
        return [
            'booking' => [
                'title' => __('Bookings', 'buddyclients-free'),
                'description' => __('General booking settings.', 'buddyclients-free'),
                'fields' => [
                    'accept_bookings' => [
                        'label' => __('Accept Bookings', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => [
                            'open' => __('Open', 'buddyclients-free'),
                            'closed' => __('Closed', 'buddyclients-free'),
                        ],
                        'description' => __('Are you currently accepting bookings?', 'buddyclients-free'),
                    ],
                    'skip_payment' => [
                        'label' => __('Skip Payment', 'buddyclients-free'),
                        'type' => 'hidden',
                        'options' => [
                            'no' => __('No', 'buddyclients-free'),
                            'yes' => __('Yes', 'buddyclients-free'),
                        ],
                        'description' => __( 'Select this to skip the payment and make every submitted booking successful.', 'buddyclients-free' ) . '<br>' . __( 'Use this setting if you process payments elsewhere.', 'buddyclients-free'),
                    ],
                    'cancellation_window' => [
                        'label' => __('Cancellation Window', 'buddyclients-free'),
                        'type' => 'number',
                        'description' => __( 'How many days do clients have to cancel bookings?', 'buddyclients-free' ) . '<br>' . __( 'Team and commission payments will be marked as "eligible" after this timeframe.', 'buddyclients-free'),
                    ],
                    'minimum_fee' => [
                        'label' => __('Minimum Fee', 'buddyclients-free'),
                        'type' => 'number',
                        'description' => __( 'What is the minimum dollar amount per booking?', 'buddyclients-free' ) . '<br>' . __( 'Note that paid bookings of less than $1 will fail.', 'buddyclients-free'),
                    ],
                ],
            ],
            'freelancer_mode' => [
                'title' => __('Freelancer Mode', 'buddyclients-free'),
                'description' => __('Turn on Freelancer Mode to assign all services to one person. Team member payments will be disabled.', 'buddyclients-free'),
                'fields' => [
                    'freelancer_id' => [
                        'label' => __('Freelancer', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => self::freelancer_options(),
                        'description' => __('All services will be assigned to this person. This overrides all other assigned team member settings.', 'buddyclients-free'),
                    ],
                ],
            ],
            'team' => [
                'title' => __('Team', 'buddyclients-free'),
                'description' => '',
                'fields' => [
                    'lock_team' => [
                        'label' => __('Lock Team Members', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => [
                            'lock' => __('Lock', 'buddyclients-free'),
                            'unlock' => __('Unlock', 'buddyclients-free'),
                        ],
                        'description' => __('Lock team members to require future services for each project to use the same team member for each role.', 'buddyclients-free'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Builds a list of freelancer id options.
     * 
     * @since 0.1.0
     */
    private static function freelancer_options() {
        $default = [ '' => __( 'OFF', 'buddyclients-free' ) ];
        $options = buddyc_options( 'users' );
        return $default + $options;
    }
}