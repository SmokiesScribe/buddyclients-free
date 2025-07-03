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
                __( 'Client', 'buddyclients-lite' ),
                __( 'Created', 'buddyclients-lite' ),
                __( 'Status', 'buddyclients-lite' ),
                __( 'Type', 'buddyclients-lite' ),
                __( 'Payment Method', 'buddyclients-lite' ),
                __( 'Amount', 'buddyclients-lite' ),
                __( 'Amount Received', 'buddyclients-lite' ),
                __( 'Date Paid', 'buddyclients-lite' ),
                __( 'Actions', 'buddyclients-lite' ),
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
            'title'     => __( 'Booking Payments', 'buddyclients-lite' ),
            'filters'   => [
                'status'    => [
                    'label'     => __( 'Status', 'buddyclients-lite' ),
                    'property'  =>  'status',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-lite' ),
                        'paid'      => __( 'Paid', 'buddyclients-lite' ),
                        'unpaid'    => __( 'Unpaid', 'buddyclients-lite' )
                    ],
                    'default'   => ''
                ],
                'type'    => [
                    'label'     => __( 'Type', 'buddyclients-lite' ),
                    'property'  =>  'type',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-lite' ),
                        'deposit'   => __( 'Deposit', 'buddyclients-lite' ),
                        'final'     => __( 'Final Payment', 'buddyclients-lite' )
                    ],
                    'default'   => ''
                ],
                'booking_intent'    => [
                    'label'     => __( 'Booking Intent', 'buddyclients-lite' ),
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
                __( 'Name', 'buddyclients-lite' ),
                __( 'Date', 'buddyclients-lite' ),
                __( 'Status', 'buddyclients-lite' ),
                __( 'Client', 'buddyclients-lite' ),
                __( 'Team Member', 'buddyclients-lite' ),
                __( 'Project', 'buddyclients-lite' ),
                __( 'Files', 'buddyclients-lite' ),
                __( 'Update Status', 'buddyclients-lite' ),
                __( 'Reassign', 'buddyclients-lite' )
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
            'title'     => __( 'Bookings', 'buddyclients-lite' ),
            'filters'   => [
                'status'    => [
                    'label'     => __( 'Status', 'buddyclients-lite' ),
                    'property'  =>  'status',
                    'options'   => [
                        ''                          => __( 'All', 'buddyclients-lite' ),
                        'pending'                   => __( 'Pending', 'buddyclients-lite' ),
                        'in_progress'               => __( 'In Progress', 'buddyclients-lite' ),
                        'complete'                  => __( 'Complete', 'buddyclients-lite' ),
                        'cancellation_requested'    => __( 'Cancellation Requested', 'buddyclients-lite' ),
                        'canceled'                  => __( 'Canceled', 'buddyclients-lite' )
                    ],
                    'default'   => 'succeeded'
                ],
                'booking'    => [
                    'label'     => __( 'Booking', 'buddyclients-lite' ),
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
                __( 'Details', 'buddyclients-lite' ),
                __( 'Email', 'buddyclients-lite' )
            ],
            'columns'   => [
                'details' => ['ID' => 'email_details'],
                'content' => ['content' => 'direct']
            ],
            'items'     => function_exists( 'buddyc_get_all_emails' ) ? buddyc_get_all_emails() : [],
            'title'     => __( 'Email Log', 'buddyclients-lite' ),
            'filters'   => [
                'status' => [
                    'label'     => __( 'Status', 'buddyclients-lite' ),
                    'property'  => 'status',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-lite' ),
                        'sent'      => __( 'Sent', 'buddyclients-lite' ),
                        'failed'    => __( 'Failed', 'buddyclients-lite' ),
                        'pending'   => __( 'Pending', 'buddyclients-lite' )
                    ],
                    'default'   => ''
                ],
                'recipient' => [
                    'label'     => __( 'Recipient', 'buddyclients-lite' ),
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
                __( 'Email', 'buddyclients-lite' ),
                __( 'Name', 'buddyclients-lite' ),
                __( 'Status', 'buddyclients-lite' ),
                __( 'Date', 'buddyclients-lite' ),
                __( 'Interests', 'buddyclients-lite' ),
                __( 'Auto Email', 'buddyclients-lite' )
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
            'title'             => __( 'Leads', 'buddyclients-lite' ),
            'items_per_page'    => 20,
            'filters'           => [
                'status' => [
                    'label'     => __( 'Status', 'buddyclients-lite' ),
                    'property'  => 'status',
                    'options'   => [
                        ''       => __( 'All', 'buddyclients-lite' ),
                        'active' => __( 'Active', 'buddyclients-lite' ),
                        'won'    => __( 'Won', 'buddyclients-lite' ),
                        'lost'   => __( 'Lost', 'buddyclients-lite' )
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
                __( 'Services', 'buddyclients-lite' ),
                __( 'Date', 'buddyclients-lite' ),
                __( 'Status', 'buddyclients-lite' ),
                __( 'Services', 'buddyclients-lite' ),
                __( 'Client', 'buddyclients-lite' ),
                __( 'Project', 'buddyclients-lite' ),
                __( 'Total Fee', 'buddyclients-lite' ),
                __( 'Actions', 'buddyclients-lite' ),
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
            'title'     => __( 'Bookings', 'buddyclients-lite' ),
            'filters'   => [
                'status'    => [
                    'label'     => __( 'Status', 'buddyclients-lite' ),
                    'property'  => 'status',
                    'options'   => [
                        'succeeded'   => __( 'Succeeded', 'buddyclients-lite' ),
                        'incomplete'  => __( 'Incomplete', 'buddyclients-lite' ),
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
                __( 'Payee', 'buddyclients-lite' ),
                __( 'Date Created', 'buddyclients-lite' ),
                __( 'Status', 'buddyclients-lite' ),
                __( 'Type', 'buddyclients-lite' ),
                __( 'Amount', 'buddyclients-lite' ),
                __( 'Payment Method', 'buddyclients-lite' ),
                __( 'Memo', 'buddyclients-lite' ),
                __( 'Paid Date', 'buddyclients-lite' ),
                __( 'Update Status', 'buddyclients-lite' ),
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
            'title'     => __( 'Payments', 'buddyclients-lite' ),
            'filters'   => [
                'type'    => [
                    'label'     => __( 'Type', 'buddyclients-lite' ),
                    'property'  => 'type',
                    'options'   => [
                        ''              => __( 'All', 'buddyclients-lite' ),
                        'team'          => __( 'Team', 'buddyclients-lite' ),
                        'affiliate'     => __( 'Affiliate', 'buddyclients-lite' ),
                        'sales'         => __( 'Sales', 'buddyclients-lite' ),
                    ],
                    'default'   => '',
                ],
                'payment_status'    => [
                    'label'     => __( 'Status', 'buddyclients-lite' ),
                    'property'  => 'status',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-lite' ),
                        'pending'   => __( 'Pending', 'buddyclients-lite' ),
                        'eligible'  => __( 'Eligible', 'buddyclients-lite' ),
                        'paid'      => __( 'Paid', 'buddyclients-lite' ),
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
                __( 'Legal Name', 'buddyclients-lite' ),
                __( 'Type', 'buddyclients-lite' ),
                __( 'Date', 'buddyclients-lite' ),
                __( 'Status', 'buddyclients-lite' ),  
                __( 'Email', 'buddyclients-lite' ),
                __( 'Download PDF', 'buddyclients-lite' ),
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
            'title'     => __( 'User Agreements', 'buddyclients-lite' ),
            'filters'   => [
                'type'    => [
                    'label'     => __( 'Type', 'buddyclients-lite' ),
                    'property'  => 'legal_type',
                    'options'   => [
                        ''          => __( 'All', 'buddyclients-lite' ),
                        'affiliate' => __( 'Affiliate', 'buddyclients-lite' ),
                        'team'      => __( 'Team', 'buddyclients-lite' ),
                        'client'    => __( 'Client', 'buddyclients-lite' ),
                    ],
                    'default'   => '',
                ],
                'user'    => [
                    'label'     => __( 'User', 'buddyclients-lite' ),
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
        $user_options = ['' => __( 'All', 'buddyclients-lite' ) ];

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
                __( 'User', 'buddyclients-lite' ),
                __( 'Date Registered', 'buddyclients-lite' ),
                __( 'Email', 'buddyclients-lite' ),
                __( 'Type', 'buddyclients-lite' ),
                __( 'Agreements', 'buddyclients-lite' ),
            ],
            'columns'           => [
                'id'                => ['ID' => 'user_link'],
                'date'              => ['date_registered' => 'date'],
                'user_email'        => ['user_email' => 'direct'],
                'type'              => ['type' => null],
                'team_agreement'    => ['ID' => 'agreements'],
            ],
            'items'             => $users_array,
            'title'             => __( 'Users', 'buddyclients-lite' ),
            'items_per_page'    => 20,
            'filters'           => [
                'type'    => [
                    'label'     => __( 'Type', 'buddyclients-lite' ),
                    'property'  => 'type',
                    'options'   => [
                        'team'   => __( 'Team', 'buddyclients-lite' ),
                        'client' => __( 'Client', 'buddyclients-lite' ),
                    ],
                    'default'   => 'team',
                ],
            ],
        ];
    }   
}
