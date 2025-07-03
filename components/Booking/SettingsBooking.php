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
            'enable_deposits'       => 'disable',
            'deposit_percentage'    => 0,
            'deposit_flat'          => 0,
            'abandoned_timeout'     => 15
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
                'title' => __('Bookings', 'buddyclients-lite'),
                'description' => __('Determine the parameters for new bookings. Enable manual bookings in Sales settings.', 'buddyclients-lite'),
                'fields' => [
                    'accept_bookings' => [
                        'label' => __('Accept Bookings', 'buddyclients-lite'),
                        'type' => 'dropdown',
                        'options' => [
                            'open' => __('Open', 'buddyclients-lite'),
                            'closed' => __('Closed', 'buddyclients-lite'),
                        ],
                        'description' => __('Are you currently accepting bookings?', 'buddyclients-lite'),
                    ],
                    'skip_payment' => [
                        'label' => __('Skip Payment', 'buddyclients-lite'),
                        'type' => 'hidden',
                        'options' => [
                            'no' => __('No', 'buddyclients-lite'),
                            'yes' => __('Yes', 'buddyclients-lite'),
                        ],
                        'description' => __( 'Select this to skip the payment and make every submitted booking successful.', 'buddyclients-lite' ) . '<br>' . __( 'Use this setting if you process payments elsewhere.', 'buddyclients-lite'),
                    ],
                    'cancellation_window' => [
                        'label' => __('Cancellation Window', 'buddyclients-lite'),
                        'type' => 'number',
                        'description' => __( 'How many days do clients have to cancel bookings?', 'buddyclients-lite' ) . '<br>' . __( 'Team and commission payments will be marked as "eligible" after this timeframe.', 'buddyclients-lite'),
                    ],
                    'minimum_fee' => [
                        'label' => __('Minimum Fee', 'buddyclients-lite'),
                        'type' => 'number',
                        'description' => __( 'What is the minimum dollar amount per booking?', 'buddyclients-lite' ) . '<br>' . __( 'Note that paid bookings of less than $1 will fail.', 'buddyclients-lite'),
                    ],
                ],
            ],
            'payments' => [
                'title' => __('Payment Structure', 'buddyclients-lite'),
                'description' => __('Determine whether to require a deposit or the full fee up front when clients book services. If a deposit is charged, clients will receive an email to pay the remainder of the fee on completion of all services. Ensure the "Final Payment" email is enabled in Email settings.', 'buddyclients-lite'),
                'fields' => [
                    'enable_deposits' => [
                        'label' => __('Enable Deposits', 'buddyclients-lite'),
                        'type' => 'dropdown',
                        'options' => [
                            'enable' => __('Enable - Require partial fee up front', 'buddyclients-lite'),
                            'disable' => __('Disable - Require full fee up front', 'buddyclients-lite'),
                        ],
                        'description' => __('If deposits are enabled, clients will be charged the deposit percentage specified below on checkout.', 'buddyclients-lite'),
                    ],
                    'deposit_percentage' => [
                        'label' => __('Deposit Percentage', 'buddyclients-lite'),
                        'type' => 'number',
                        'description' => __('Set the percentage of the full fee clients are charged up front. The remaining percentage will be billed on completion of services.', 'buddyclients-lite'),
                    ],
                    'deposit_flat' => [
                        'label' => __('Deposit Flat', 'buddyclients-lite'),
                        'type' => 'number',
                        'description' => __('Enter a flat fee to require up front. If both a percentage and flat fee are specified, the sum of the two will be charged on booking. If the deposit amount is greater than the full fee, the deposit will be reduced to the value of the full fee.', 'buddyclients-lite'),
                    ],
                ],
            ],
            'abandoned_bookings' => [
                'title' => __('Abandoned Booking Email', 'buddyclients-lite'),
                'description' => __('Send emails to users who begin booking services but exit before submitting payment. Note that the Abandoned Booking email must be enabled in Email settings.', 'buddyclients-lite'),
                'fields' => [
                    'abandoned_timeout' => [
                        'label' => __('Timeout (minutes)', 'buddyclients-lite'),
                        'type' => 'number',
                        'description' => __('How many minutes after beginning a booking should the abandoned booking email be sent?', 'buddyclients-lite'),
                    ],
                ],
            ],
            'freelancer_mode' => [
                'title' => __('Freelancer Mode', 'buddyclients-lite'),
                'description' => __('Turn on Freelancer Mode to assign all services to one person. Team member payments will be disabled.', 'buddyclients-lite'),
                'fields' => [
                    'freelancer_id' => [
                        'label' => __('Freelancer', 'buddyclients-lite'),
                        'type' => 'dropdown',
                        'options' => self::freelancer_options(),
                        'description' => __('All services will be assigned to this person. This overrides all other assigned team member settings.', 'buddyclients-lite'),
                    ],
                ],
            ],
            'team' => [
                'title' => __('Team', 'buddyclients-lite'),
                'description' => __( 'Set the rules for team member selection on the booking form. Team members will also be filtered by their Filter Field selections.', 'buddyclients-lite' ),
                'fields' => [
                    'lock_team' => [
                        'label' => __('Lock Team Members', 'buddyclients-lite'),
                        'type' => 'dropdown',
                        'options' => [
                            'lock' => __('Lock', 'buddyclients-lite'),
                            'unlock' => __('Unlock', 'buddyclients-lite'),
                        ],
                        'description' => __('Lock team members to require future services for each project to use the same team member for each role. When locked, the team member of each role will automatically be assigned when new services are booked for the same project. When unlocked, clients will be able to select new team members on subsequent bookings for the same project.', 'buddyclients-lite'),
                    ],
                    'require_agreement' => [
                        'label' => __( 'Require Active Team Member Agreement', 'buddyclients-lite' ),
                        'type' => 'dropdown',
                        'options' => [
                            'yes' => __( 'Yes - Require agreement', 'buddyclients-lite' ),
                            'no' => __( 'No - Do not require agreement', 'buddyclients-lite' ),
                        ],
                        'description' => __( 'Should team members without active agreements be disallowed from accepting new projects? This setting only applies if the Legal component is enabled and a team member agreement exists.', 'buddyclients-lite' ),
                    ],
                ],
            ]
        ];
    }

    /**
     * Builds a list of freelancer id options.
     * 
     * @since 0.1.0
     */
    private static function freelancer_options() {
        $default = [ '' => __( 'OFF', 'buddyclients-lite' ) ];
        $options = buddyc_options( 'users' );
        return $default + $options;
    }
}