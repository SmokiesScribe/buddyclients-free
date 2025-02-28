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
                'title' => __('Bookings', 'buddyclients-free'),
                'description' => __('Determine the parameters for new bookings. Enable manual bookings in Sales settings.', 'buddyclients-free'),
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
            'payments' => [
                'title' => __('Payment Structure', 'buddyclients-free'),
                'description' => __('Determine whether to require a deposit or the full fee up front when clients book services. If a deposit is charged, clients will receive an email to pay the remainder of the fee on completion of all services. Ensure the "Final Payment" email is enabled in Email settings.', 'buddyclients-free'),
                'fields' => [
                    'enable_deposits' => [
                        'label' => __('Enable Deposits', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => [
                            'enable' => __('Enable - Require partial fee up front', 'buddyclients-free'),
                            'disable' => __('Disable - Require full fee up front', 'buddyclients-free'),
                        ],
                        'description' => __('If deposits are enabled, clients will be charged the deposit percentage specified below on checkout.', 'buddyclients-free'),
                    ],
                    'deposit_percentage' => [
                        'label' => __('Deposit Percentage', 'buddyclients-free'),
                        'type' => 'number',
                        'description' => __('Set the percentage of the full fee clients are charged up front. The remaining percentage will be billed on completion of services.', 'buddyclients-free'),
                    ],
                    'deposit_flat' => [
                        'label' => __('Deposit Flat', 'buddyclients-free'),
                        'type' => 'number',
                        'description' => __('Enter a flat fee to require up front. If both a percentage and flat fee are specified, the sum of the two will be charged on booking. If the deposit amount is greater than the full fee, the deposit will be reduced to the value of the full fee.', 'buddyclients-free'),
                    ],
                ],
            ],
            'abandoned_bookings' => [
                'title' => __('Abandoned Booking Email', 'buddyclients-free'),
                'description' => __('Send emails to users who begin booking services but exit before submitting payment. Note that the Abandoned Booking email must be enabled in Email settings.', 'buddyclients-free'),
                'fields' => [
                    'abandoned_timeout' => [
                        'label' => __('Timeout (minutes)', 'buddyclients-free'),
                        'type' => 'number',
                        'description' => __('How many minutes after beginning a booking should the abandoned booking email be sent?', 'buddyclients-free'),
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
                'description' => __( 'Set the rules for team member selection on the booking form. Team members will also be filtered by their Filter Field selections.', 'buddyclients-free' ),
                'fields' => [
                    'lock_team' => [
                        'label' => __('Lock Team Members', 'buddyclients-free'),
                        'type' => 'dropdown',
                        'options' => [
                            'lock' => __('Lock', 'buddyclients-free'),
                            'unlock' => __('Unlock', 'buddyclients-free'),
                        ],
                        'description' => __('Lock team members to require future services for each project to use the same team member for each role. When locked, the team member of each role will automatically be assigned when new services are booked for the same project. When unlocked, clients will be able to select new team members on subsequent bookings for the same project.', 'buddyclients-free'),
                    ],
                    'require_agreement' => [
                        'label' => __( 'Require Active Team Member Agreement', 'buddyclients-free' ),
                        'type' => 'dropdown',
                        'options' => [
                            'yes' => __( 'Yes - Require agreement', 'buddyclients-free' ),
                            'no' => __( 'No - Do not require agreement', 'buddyclients-free' ),
                        ],
                        'description' => __( 'Should team members without active agreements be disallowed from accepting new projects? This setting only applies if the Legal component is enabled and a team member agreement exists.', 'buddyclients-free' ),
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
        $default = [ '' => __( 'OFF', 'buddyclients-free' ) ];
        $options = buddyc_options( 'users' );
        return $default + $options;
    }
}