<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminTable;

/**
 * Manages the data for all admin tables.
 *
 * @since 1.0.28
 */
class AdminTableManager {

    /**
     * The table key.
     * 
     * @var string
     */
    private string $key;

    /**
     * Mapping of table keys to their respective data methods.
     *
     * @var array
     */
    private static array $callbacks = [
        'booking_payments' => 'booking_payments_data',
        'booked_services'  => 'booked_services_data',
        'email_log'        => 'email_log_data',
        'leads'            => 'leads_data',
        'booking_intents'  => 'booking_intents_data',
        'payments'         => 'payments_data',
        'user_agreements'  => 'user_agreements_data',
        'user_list'        => 'user_list_data'
    ];

    /**
     * Constructor method.
     * 
     * @since 1.0.28
     * 
     * @param   string  $key    The admin table key.
     */
    public function __construct( $key ) {
        $this->key = $key;
    }

    /**
     * Builds a table from a key.
     * 
     * @since 1.0.28
     */
    public function build_table() {
        return new AdminTable( $this->get_args() );
    }

    /**
     * Retrieves the args by table key.
     * 
     * @since 1.0.27
     */
    private function get_args() {
        // Fetch args based on callback
        if ( isset( self::$callbacks[$this->key] ) ) {
            return self::{ self::$callbacks[$this->key] }();
        }
        return [];
    }

    /**
     * Defines the data for the Booking Payments table. 
     * 
     * @since 1.0.28
     */
    private static function booking_payments_data() {
        return [
            'key'       => 'booking_payments',
            'headings'  => [
                __( 'Client', 'buddyclients-free' ),
                __( 'Created', 'buddyclients-free' ),
                __( 'Status', 'buddyclients-free' ),
                __( 'Type', 'buddyclients-free' ),
                __( 'Payment Method', 'buddyclients-free' ),
                __( 'Amount', 'buddyclients-free' ),
                __( 'Amount Received', 'buddyclients-free' ),
                __( 'Date Paid', 'buddyclients-free' ),
                __( 'Actions', 'buddyclients-free' ),
            ],
            'columns'   => [
                'client_id'         => ['client_id' => 'user_link'],
                'date'              => ['created_at' => 'date_time'],
                'status'            => ['ID' => 'booking_payment_status'],
                'type'              => ['type_label' => 'direct'],
                'payment_method'    => ['payment_method' => 'payment_method'],
                'amount'            => ['amount' => 'usd'],
                'amount_received'   => ['amount_received' => 'usd'],
                'paid_at'           => ['paid_at' => 'date_time'],
                'actions'           => ['ID' => 'booking_payment_actions'],
            ],
            'items'     => buddyc_get_all_active_payments(),
            'title'     => __( 'Booking Payments', 'buddyclients-free' ),
            'filters'   => [
                'status'    => [
                    'label'     => __( 'Status', 'buddyclients-free' ),
                    'property'  =>  'status',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-free' ),
                        'paid'      => __( 'Paid', 'buddyclients-free' ),
                        'unpaid'    => __( 'Unpaid', 'buddyclients-free' )
                    ],
                    'default'   => ''
                ],
                'type'    => [
                    'label'     => __( 'Type', 'buddyclients-free' ),
                    'property'  =>  'type',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-free' ),
                        'deposit'   => __( 'Deposit', 'buddyclients-free' ),
                        'final'     => __( 'Final Payment', 'buddyclients-free' )
                    ],
                    'default'   => ''
                ],
                'booking_intent'    => [
                    'label'     => __( 'Booking Intent', 'buddyclients-free' ),
                    'property'  =>  'booking_intent_id',
                    'options'   => [],
                    'default'   => ''
                ]
            ],
        ];
    }

    /**
     * Defines the data for the Booked Services table. 
     * 
     * @since 1.0.28
     */
    private static function booked_services_data() {
        return [
            'key'       => 'booked_services',
            'headings'  => [
                __( 'Name', 'buddyclients-free' ),
                __( 'Date', 'buddyclients-free' ),
                __( 'Status', 'buddyclients-free' ),
                __( 'Client', 'buddyclients-free' ),
                __( 'Team Member', 'buddyclients-free' ),
                __( 'Project', 'buddyclients-free' ),
                __( 'Files', 'buddyclients-free' ),
                __( 'Update Status', 'buddyclients-free' ),
                __( 'Reassign', 'buddyclients-free' )
            ],
            'columns'   => [
                'name'          => ['name' => null],
                'date'          => ['created_at' => 'date'],
                'status'        => ['status' => 'icons'],
                'client_id'     => ['client_id' => 'user_link'],
                'team_id'       => ['team_id' => 'user_link'],
                'project_id'    => ['project_id' => 'group_link'],
                'files'         => ['file_ids' => 'files'],
                'updated_status'=> ['status' => 'status_form'],
                'reassign'      => ['team_id' => 'reassign_form']
            ],
            'items'     => buddyc_get_all_booked_services(),
            'title'     => __( 'Bookings', 'buddyclients-free' ),
            'filters'   => [
                'status'    => [
                    'label'     => __( 'Status', 'buddyclients-free' ),
                    'property'  =>  'status',
                    'options'   => [
                        ''                          => __( 'All', 'buddyclients-free' ),
                        'pending'                   => __( 'Pending', 'buddyclients-free' ),
                        'in_progress'               => __( 'In Progress', 'buddyclients-free' ),
                        'complete'                  => __( 'Complete', 'buddyclients-free' ),
                        'cancellation_requested'    => __( 'Cancellation Requested', 'buddyclients-free' ),
                        'canceled'                  => __( 'Canceled', 'buddyclients-free' )
                    ],
                    'default'   => 'succeeded'
                ],
                'booking'    => [
                    'label'     => __( 'Booking', 'buddyclients-free' ),
                    'property'  =>  'booking_intent_id',
                    'options'   => [],
                    'default'   => 'succeeded'
                ]
            ],
        ];
    }

    /**
     * Defines the data for the Email Log table.
     * 
     * @since 1.0.28
     */
    private static function email_log_data() {
        return [
            'key'       => 'emails',
            'headings'  => [
                __( 'Details', 'buddyclients-free' ),
                __( 'Email', 'buddyclients-free' )
            ],
            'columns'   => [
                'details' => ['ID' => 'email_details'],
                'content' => ['content' => 'direct']
            ],
            'items'     => function_exists( 'buddyc_get_all_emails' ) ? buddyc_get_all_emails() : [],
            'title'     => __( 'Email Log', 'buddyclients-free' ),
            'filters'   => [
                'status' => [
                    'label'     => __( 'Status', 'buddyclients-free' ),
                    'property'  => 'status',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-free' ),
                        'sent'      => __( 'Sent', 'buddyclients-free' ),
                        'failed'    => __( 'Failed', 'buddyclients-free' ),
                        'pending'   => __( 'Pending', 'buddyclients-free' )
                    ],
                    'default'   => ''
                ],
                'recipient' => [
                    'label'     => __( 'Recipient', 'buddyclients-free' ),
                    'property'  => 'recipient_email',
                    'options'   => [],
                    'default'   => ''
                ]
            ],
        ];
    }

    /**
     * Defines the data for the Leads table.
     * 
     * @since 1.0.28
     */
    private static function leads_data() {
        return [
            'key'               => 'users',
            'headings'          => [
                __( 'Email', 'buddyclients-free' ),
                __( 'Name', 'buddyclients-free' ),
                __( 'Status', 'buddyclients-free' ),
                __( 'Date', 'buddyclients-free' ),
                __( 'Interests', 'buddyclients-free' ),
                __( 'Auto Email', 'buddyclients-free' )
            ],
            'columns'           => [
                'email'      => ['email' => 'direct'],
                'name'       => ['name' => null],
                'status'     => ['status' => 'lead_status'],
                'date'       => ['created_at' => 'date_time'],
                'interests'  => ['interests' => 'direct'],
                'auto-email' => ['sent' => 'lead_auto_email']
            ],
            'items'             => buddyc_get_all_leads(),
            'title'             => __( 'Leads', 'buddyclients-free' ),
            'items_per_page'    => 20,
            'filters'           => [
                'status' => [
                    'label'     => __( 'Status', 'buddyclients-free' ),
                    'property'  => 'status',
                    'options'   => [
                        ''       => __( 'All', 'buddyclients-free' ),
                        'active' => __( 'Active', 'buddyclients-free' ),
                        'won'    => __( 'Won', 'buddyclients-free' ),
                        'lost'   => __( 'Lost', 'buddyclients-free' )
                    ],
                ]
            ],
        ];
    }

    /**
     * Defines the data for the Booking Intents table.
     * 
     * @since 1.0.28
     */
    private static function booking_intents_data() {
        return [
            'key'       => 'booking_intents',
            'headings'  => [
                __( 'Services', 'buddyclients-free' ),
                __( 'Date', 'buddyclients-free' ),
                __( 'Status', 'buddyclients-free' ),
                __( 'Services', 'buddyclients-free' ),
                __( 'Client', 'buddyclients-free' ),
                __( 'Project', 'buddyclients-free' ),
                __( 'Total Fee', 'buddyclients-free' ),
                __( 'Actions', 'buddyclients-free' ),
            ],
            'classes'   => [
                'column-primary'   => ['Date' => 'date'],
                'secondary'        => [],
                'tertiary'         => [],
            ],
            'columns'   => [
                'services'              => ['service_names' => 'service_names_link'],
                'date'                  => ['created_at' => 'date_time'],
                'status'                => ['status' => 'booking_intent_status'],
                'services_complete'     => ['services_complete' => 'services_complete_status'],
                'client_id'             => ['client_id' => 'booking_user_link_email'],
                'project_id'            => ['project_id' => 'group_link'],
                'total_fee'             => ['total_fee' => 'usd'],
                'actions'               => ['ID' => 'booking_actions']
            ],
            'items'     => buddyc_get_all_booking_intents(),
            'title'     => __( 'Bookings', 'buddyclients-free' ),
            'filters'   => [
                'status'    => [
                    'label'     => __( 'Status', 'buddyclients-free' ),
                    'property'  => 'status',
                    'options'   => [
                        'succeeded'   => __( 'Succeeded', 'buddyclients-free' ),
                        'incomplete'  => __( 'Incomplete', 'buddyclients-free' ),
                    ],
                    'default'   => 'succeeded'
                ]
            ],
        ];
    }

    /**
     * Defines the data for the Payments table.
     * 
     * @since 1.0.28
     */
    private static function payments_data() {
        return [
            'key'       => 'payments',
            'headings'  => [
                __( 'Payee', 'buddyclients-free' ),
                __( 'Date Created', 'buddyclients-free' ),
                __( 'Status', 'buddyclients-free' ),
                __( 'Type', 'buddyclients-free' ),
                __( 'Amount', 'buddyclients-free' ),
                __( 'Payment Method', 'buddyclients-free' ),
                __( 'Memo', 'buddyclients-free' ),
                __( 'Paid Date', 'buddyclients-free' ),
                __( 'Update Status', 'buddyclients-free' ),
            ],
            'columns'   => [
                'payee_id'                  => ['payee_id' => 'user_link'],
                'date'                      => ['created_at' => 'date_time'],
                'status'                    => ['status' => 'icons'],
                'type'                      => ['type' => null],
                'amount'                    => ['amount' => 'usd'],
                'legal_payment_preference'  => ['payee_id' => 'legal_payment_preference'],
                'memo'                      => ['memo' => 'copy_memo'],
                'paid_date'                 => ['paid_date' => 'date_time'],
                'update_status'             => ['status' => 'payment_form'],
            ],
            'items'     => buddyc_get_all_payments(),
            'title'     => __( 'Payments', 'buddyclients-free' ),
            'filters'   => [
                'type'    => [
                    'label'     => __( 'Type', 'buddyclients-free' ),
                    'property'  => 'type',
                    'options'   => [
                        ''              => __( 'All', 'buddyclients-free' ),
                        'team'          => __( 'Team', 'buddyclients-free' ),
                        'affiliate'     => __( 'Affiliate', 'buddyclients-free' ),
                        'sales'         => __( 'Sales', 'buddyclients-free' ),
                    ],
                    'default'   => '',
                ],
                'payment_status'    => [
                    'label'     => __( 'Status', 'buddyclients-free' ),
                    'property'  => 'status',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-free' ),
                        'pending'   => __( 'Pending', 'buddyclients-free' ),
                        'eligible'  => __( 'Eligible', 'buddyclients-free' ),
                        'paid'      => __( 'Paid', 'buddyclients-free' ),
                    ],
                    'default'   => '',
                ],
            ],
        ];
    }

    /**
     * Defines the data for the User Agreements table.
     * 
     * @since 1.0.28
     */
    private static function user_agreements_data() {
        $agreements = buddyc_get_all_user_agreements();

        return [
            'key'       => 'user_agreements',
            'headings'  => [
                __( 'Legal Name', 'buddyclients-free' ),
                __( 'Type', 'buddyclients-free' ),
                __( 'Date', 'buddyclients-free' ),
                __( 'Status', 'buddyclients-free' ),  
                __( 'Email', 'buddyclients-free' ),
                __( 'Download PDF', 'buddyclients-free' ),
            ],
            'columns'   => [
                'legal_name'    => ['legal_name' => null],
                'type'          => ['legal_type' => null],
                'date'          => ['created_at' => 'date_time'],
                'status'        => ['ID' => 'agreement_status'],
                'email'         => ['email' => 'direct'],
                'download_pdf'  => ['pdf' => 'pdf_download'],
            ],
            'items'     => $agreements,
            'title'     => __( 'User Agreements', 'buddyclients-free' ),
            'filters'   => [
                'type'    => [
                    'label'     => __( 'Type', 'buddyclients-free' ),
                    'property'  => 'legal_type',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-free' ),
                        'affiliate' => __( 'Affiliate', 'buddyclients-free' ),
                        'team'      => __( 'Team', 'buddyclients-free' ),
                        'client'    => __( 'Client', 'buddyclients-free' ),
                    ],
                    'default'   => '',
                ],
                'user'    => [
                    'label'     => __( 'User', 'buddyclients-free' ),
                    'property'  => 'user_id',
                    'options'   => self::user_agreement_options( $agreements ),
                ],
            ],
        ];
    }

    /**
     * Builds the array of user options to filter
     * the legal user agreements.
     * 
     * @since 1.0.28
     * 
     * @param   array   $agreements The array of UserAgreement objects.
     */
    private static function user_agreement_options( $agreements ) {
        // Get all user ids
        $user_ids = array_map( function( $agreement ) {
            return $agreement->user_id ?? null; // Return user_id or null if it doesn't exist
        }, $agreements );
        
        // Filter out null values
        $user_ids = array_filter( $user_ids );

        // Initialize user options
        $user_options = ['' => __( 'All', 'buddyclients-free' ) ];

        // Build user options
        if ( ! empty( $user_ids ) ) {
            foreach ( $user_ids as $user_id ) {
                $user_options[$user_id] = bp_core_get_user_displayname( $user_id );
            }
        }
        return $user_options;
    }

    /**
     * Defines the data for the Users table.
     * 
     * @since 1.0.28
     */
    private static function user_list_data() {
        $users_array = [];

        // Get all users
        $users = bp_core_get_users([
            'per_page' => false,
        ]);

        // Loop through users and determine type
        foreach ($users['users'] as $user) {
            if ( buddyc_is_team( $user->ID ) ) {
                $type = 'team';
            } elseif ( buddyc_is_client( $user->ID ) ) {
                $type = 'client';
            } elseif ( function_exists( 'be_is_faculty' ) && be_is_faculty( $user->ID ) ) {
                $type = 'faculty';
            } elseif ( function_exists( 'be_is_attendee' ) && be_is_attendee( $user->ID ) ) {
                $type = 'attendee';
            } else {
                continue;
            }

            // Add user to array
            $users_array[$user->ID] = [
                'ID'                => $user->ID,
                'user_email'        => $user->user_email,
                'date_registered'   => $user->user_registered,
                'fullname'          => $user->fullname,
                'type'              => $type,
            ];
        }

        // Sort users by 'type'
        usort($users_array, fn($a, $b) => strcmp($b['type'], $a['type']));

        return [
            'key'               => 'users',
            'headings'          => [
                __( 'User', 'buddyclients-free' ),
                __( 'Date Registered', 'buddyclients-free' ),
                __( 'Email', 'buddyclients-free' ),
                __( 'Type', 'buddyclients-free' ),
                __( 'Agreements', 'buddyclients-free' ),
            ],
            'columns'           => [
                'id'                => ['ID' => 'user_link'],
                'date'              => ['date_registered' => 'date'],
                'user_email'        => ['user_email' => 'direct'],
                'type'              => ['type' => null],
                'team_agreement'    => ['ID' => 'agreements'],
            ],
            'items'             => $users_array,
            'title'             => __( 'Users', 'buddyclients-free' ),
            'items_per_page'    => 20,
            'filters'           => [
                'type'    => [
                    'label'     => __( 'Type', 'buddyclients-free' ),
                    'property'  => 'type',
                    'options'   => [
                        'team'   => __( 'Team', 'buddyclients-free' ),
                        'client' => __( 'Client', 'buddyclients-free' ),
                    ],
                    'default'   => 'team',
                ],
            ],
        ];
    }   
}
