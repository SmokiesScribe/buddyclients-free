<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Admin\PageManager as PageManager;
use BuddyClients\Components\Stripe\StripeKeys as StripeKeys;
use BuddyClients\Components\Email\EmailTemplateManager as EmailTemplateManager;
use BuddyClients\Config\ComponentsHandler as ComponentsHandler;

/**
 * Settings manager.
 * 
 * Organizes all settings data.
 * Retreives and updates settings values.
 */
class Settings {
	
	/**
	 * Defines all setting group callbacks.
	 * 
	 * @since 0.1.0
	 */
	private static function callbacks() {
	    
        // Core callbacks
        $callbacks = [
            'stripe'        => [self::class, 'stripe'],
            'components'    => [self::class, 'components'],
            'general'       => [self::class, 'general'],
            'affiliate'     => [self::class, 'affiliate'],
            'booking'       => [self::class, 'booking'],
            'help'          => [self::class, 'help'],
            'style'         => [self::class, 'style'],
            'legal'         => [self::class, 'legal'],
            'pages'         => [self::class, 'pages'],
            'email'         => [self::class, 'email'],
            'license'       => [self::class, 'license'],
            'sales'         => [self::class, 'sales'],
            'integrations'  => [self::class, 'integrations'],
        ];
        
        /**
         * Filters the Settings callbacks.
         * 
         * Callbacks should return an array of settings args.
         *
         * @since 0.1.0
         *
         * @param array  $callbacks An array of callables keyed by settings group.
         */
         $callbacks = apply_filters( 'buddyc_settings_groups', $callbacks );
         
         return $callbacks;
    }
	
	/**
	 * Retrieves the callback for a settings group.
	 * 
	 * @since 0.1.0
	 * 
	 * @param   string  $settings_group     The name of the settings group.
	 */
	public static function get_callback( $settings_group ) {
	    $callbacks = self::callbacks();
	    return $callbacks[$settings_group] ?? null;
	}
	
	/**
	 * Retrieves all data for a settings group.
	 * 
	 * @since 0.1.0
	 */
	public static function get_data( $settings_group ) {
	    // Initialize
	    $data = [];
	    
        // Get callback
        $callback = self::get_callback( $settings_group );
        
        // Get settings data
        if ( is_callable( $callback ) ) {
            $data = call_user_func( $callback );
            return $data;
        }
	}
	
	/**
	 * Retrieves default values for a settings group.
	 * 
	 * @since 0.1.0
	 * 
     * @param   string  $settings_group     The name of the settings group.
     * @param   string  $settings_field     Optional. The name of the settings field.
	 */
	public static function get_defaults( $settings_group, $settings_field = null ) {
	    // Initialize
	    $defaults = [];
	    
        // Get callback
        $callback = self::get_callback( $settings_group );
        
        // Get settings data
        if ( is_callable( $callback ) ) {
            $defaults = call_user_func( $callback, true );
        
            // Get specific field default
            if ( $settings_field ) {
                return $defaults[$settings_field] ?? '';
            } else {
                return $defaults;
            }
        }
        return $defaults;
	}
    
    /**
     * Retrieves current value of a setting.
     * 
     * @since 0.1.0
     * 
     * @param   string  $settings_group     The name of the settings group.
     * @param   string  $settings_field     Optional. The name of the settings field.
     * @return  mixed   The field value if defined or an array of all values in the settings group.
     */
    public static function get_value( $settings_group, $settings_field = null ) {
        
        // No field is defined
        if ( ! $settings_field ) {
            
            // Get all settings group data
            $data = self::get_data( $settings_group );
            
            // Return all group data
            return $data;
        
        // Field is defined    
        } else {
                    
            // Get the current setting
            $curr_settings = get_option('buddyc_' . $settings_group . '_settings');
            
            // Fallback to defaults
            $field_value = $curr_settings[$settings_field] ?? self::get_defaults( $settings_group, $settings_field ) ?? '';
            
            // Return the value once found
            return $field_value;
        }
    }
    
    /**
     * Retrieves current value of a setting.
     * 
     * @since 0.1.0
     */
    public static function update_value( $settings_key, $field_key, $value ) {
        $settings_name = 'buddyc_' . $settings_key . '_settings';
        $settings = get_option($settings_name);
        $settings[$field_key] = $value;
        update_option($settings_name, $settings);
    }
    
    /**
     * General settings.
     * 
     * @since 0.1.0
     */
    public static function general( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'client_types'          => [],
                'team_types'            => [],
                'self_select_roles'     => 'no',
                'enable_registration'   => 'disable',
                'register_button_text'  => __('Get Started', 'buddyclients-free'),
                'admin_info'            => 'enable'
            ];
            
        // Otherwise return settings data
        } else {
           return [
                'user_types' => [
                    'title' => __('User Types and Permissions', 'buddyclients-free'),
                    'description' => __('Select the member types for clients, team members, and site admins.', 'buddyclients-free'),
                    'fields' => [
                        'client_types' => [
                            'label' => __('Client Types', 'buddyclients-free'),
                            'type' => 'checkboxes',
                            'options' => buddyc_member_types(),
                            'description' => __('Select the types for clients.', 'buddyclients-free'),
                        ],
                        'default_client_type' => [
                            'label' => __('Default Client Type', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => buddyc_member_types(),
                            'description' => __('Select the default member type for new clients.', 'buddyclients-free'),
                        ],
                        'team_types' => [
                            'label' => __('Team Types', 'buddyclients-free'),
                            'type' => 'checkboxes',
                            'options' => buddyc_member_types(),
                            'description' => __('Select the types for team members.', 'buddyclients-free'),
                        ],
                    ],
                ],
                'self_select_roles' => [
                    'title' => __('Allow Team to Self-Select Roles', 'buddyclients-free'),
                    'description' => __('Should team members be allowed to select their own roles?', 'buddyclients-free'),
                    'fields' => [
                        'self_select_role' => [
                            'label' => __('Allow Self-Selection', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => [
                                'no' => __('No', 'buddyclients-free'),
                                'yes' => __('Yes', 'buddyclients-free'),
                            ],
                            'description' => __('Allow team members to select their own roles.', 'buddyclients-free'),
                        ],
                    ],
                ],
                'registration' => [
                    'title' => __('Registration', 'buddyclients-free'),
                    'description' => __('Change the text and link for the signup button.', 'buddyclients-free'),
                    'fields' => [
                        'enable_registration' => [
                            'label' => __('User Registration', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => [
                                'disable' => __('Disable', 'buddyclients-free'),
                                'enable' => __('Enable', 'buddyclients-free'),
                            ],
                            'description' => __('Should users be allowed to register for an account before booking services?', 'buddyclients-free'),
                        ],
                        'register_button_text' => [
                            'label' => __('Booking Button Text', 'buddyclients-free'),
                            'type' => 'text',
                            'description' => __('The text will appear on the button linking to the booking form when user registration is disabled.', 'buddyclients-free'),
                        ],
                    ],
                ],
                'admin' => [
                    'title' => __('Admin', 'buddyclients-free'),
                    'description' => '',
                    'fields' => [
                        'admin_info' => [
                            'label' => __('Info Messages', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => [
                                'disable' => __('Disable', 'buddyclients-free'),
                                'enable' => __('Enable', 'buddyclients-free'),
                            ],
                            'description' => __('Display plugin info messages in the admin area.', 'buddyclients-free'),
                        ],
                    ],
                ],
            ];
        }
    }
    
    /**
     * Components settings.
     * 
     * @since 0.1.0
     */
    public static function components( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'components' => self::components_options(), // enable all by default
            ];
            
        // Otherwise return settings data
        } else {
           return [
                'components' => [
                    'title' => __('Components', 'buddyclients-free'),
                    'description' => __('Enable BuddyClients components.', 'buddyclients-free'),
                    'fields' => [
                        'components' => [
                            'label' => __('Components', 'buddyclients-free'),
                            'type' => 'checkbox_table',
                            'options' => self::components_options(),
                            'descriptions' => self::components_descriptions(),
                            'required_options' => ComponentsHandler::required_components(),
                        ],
                    ],
                ],
            ];
        }
    }
    
    /**
     * Retrieves components options.
     * 
     * @since 0.1.0
     */
    private static function components_options() {
        
        // Initialize array
        $options = [];
        
        // Get components
        $components = ComponentsHandler::get_components();
        
        // Loop through post types
        foreach ( $components as $component ) {
            // Add to array
            if ( function_exists( 'buddyc_component_name' ) ) {
                $options[$component] = buddyc_component_name( $component );
            }
        }
        return $options;
    }
    
    /**
     * Defines components descriptions.
     * 
     * @since 0.1.0
     */
    private static function components_descriptions() {
        return [
            // Required
            'Booking'       => __('Allow clients to book services.', 'buddyclients-free'),
            'Checkout'      => __('Allow clients to check out on your website.', 'buddyclients-free'),
            'Service'       => __('Create services.', 'buddyclients-free'),
            // Core
            'Email'         => __('Send email notifications to clients, team members, and admins.', 'buddyclients-free'),
            'Brief'         => __('Request information from clients after booking.', 'buddyclients-free'),
            'Stripe'        => __('Accept payments at checkout.', 'buddyclients-free'),
            // Premium
            'Affiliate'     => __('Allow users to earn commission for referring clients.', 'buddyclients-free'),
            'Availability'  => __('Display the next date each team member is available.', 'buddyclients-free'),
            'Contact'       => __('Accept messages through a contact page and a floating contact button.', 'buddyclients-free'),
            'Legal'         => __('Manage legal agreements for clients, team members, and affiliates.', 'buddyclients-free'),
            'Quote'         => __('Create custom quotes for one-off projects.', 'buddyclients-free'),
            'Sales'         => __('Allow team members to create bookings on behalf of clients and earn commission.', 'buddyclients-free'),
            'Testimonial'   => __('Accept and display testimonials from clients.', 'buddyclients-free'),
        ];
    }
    
    /**
     * Stripe settings.
     * 
     * @since 0.1.0
     * 
     * @param   ?bool   $defaults   Whether to return an array of default values.
     */
    public static function stripe( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'mode' => 'test',
            ];
            
        // Otherwise return settings data
        } else {
            return [
                'mode' => [
                    'title' => __('Stripe Mode', 'buddyclients-free'),
                    'description' => sprintf(
                        __('<a href="https://dashboard.stripe.com/register" target="_blank">Create a Stripe account</a> to activate the payment integration.</p><p class="description">Set the Stripe Mode below to "Live" to accept real payments. <a href="https://docs.stripe.com/testing" target="_blank">Learn about test payments</a>.', 'buddyclients-free')
                    ),
                    'fields' => [
                        'stripe_mode' => [
                            'label' => __('Stripe Mode', 'buddyclients-free'),
                            'type' => 'stripe_dropdown',
                            'default' => 'test',
                            'options' => [
                                'test' => __('Test', 'buddyclients-free'),
                                'live' => __('Live', 'buddyclients-free')
                            ],
                            'description' => __('Select the Stripe mode.', 'buddyclients-free'),
                            'stripe_key'    => null
                        ],
                    ],
                ],
                'live_keys' => [
                    'title' => __('Stripe Live Keys', 'buddyclients-free'),
                    'description' => sprintf(
                        __('Input your live keys to accept payments. <a href="https://support.stripe.com/questions/locate-api-keys-in-the-dashboard" target="_blank">Find your API keys</a>.', 'buddyclients-free')
                    ),
                    'fields' => [
                        'secret_key_live' => [
                            'label' => __('Live Stripe Secret Key', 'buddyclients-free'),
                            'type' => 'stripe_input',
                            'default' => '',
                            'description' => '',
                            'stripe_key'    => 'secret',
                            'stripe_mode'   => 'live'
                        ],
                        'public_key_live' => [
                            'label' => __('Live Stripe Publishable Key', 'buddyclients-free'),
                            'type' => 'stripe_input',
                            'default' => '',
                            'description' => '',
                            'stripe_key'    => 'publish',
                            'stripe_mode'   => 'live'
                        ],
                    ],
                ],
                'test_keys' => [
                    'title' => __('Stripe Test Keys', 'buddyclients-free'),
                    'description' => __('Input your test keys to test the payment system.', 'buddyclients-free'),
                    'fields' => [
                        'secret_key_test' => [
                            'label' => __('Test Stripe Secret Key', 'buddyclients-free'),
                            'type' => 'stripe_input',
                            'default' => '',
                            'description' => '',
                            'stripe_key'    => 'secret',
                            'stripe_mode'   => 'test'
                        ],
                        'public_key_test' => [
                            'label' => __('Test Stripe Publishable Key', 'buddyclients-free'),
                            'type' => 'stripe_input',
                            'default' => '',
                            'description' => '',
                            'stripe_key'    => 'publish',
                            'stripe_mode'   => 'test'
                        ],
                    ],
                ],
                'webhooks' => [
                    'title' => __('Stripe Webhooks', 'buddyclients-free'),
                    'description' => sprintf(
                        __('Set up webhooks to retrieve successful Stripe payments.
                            <ol>
                                <li>Log into your <a href="https://dashboard.stripe.com/" target="_blank">Stripe Dashboard</a>.</li>
                                <li>Go to "Developers" -> "Webhooks" -> "Add endpoint"</li>
                                <li>Paste the URL below in the Endpoint URL field.</li>
                                <li>Click "Select events."</li>
                                <li>Select "payment_intent.succeeded" and "customer.created."</li>
                                <li>Click "Add endpoint."</li>
                                <li>Click into the newly created webhook. Under "Signing secret", click "Reveal". Copy and paste the secret code into the field below.</li>
                            </ol>', 'buddyclients-free')
                    ),
                    'fields' => [
                        'endpoint_url' => [
                            'label' => __('Endpoint URL', 'buddyclients-free'),
                            'type' => 'copy',
                            'content' => buddyc_stripe_endpoint_url(), // use helper function to get url
                            'description' => __('Copy this link to your the Endpoint URL field for your Stripe webhook.', 'buddyclients-free'),
                        ],
                        'signing_live' => [
                            'label' => __('Live Signing Secret', 'buddyclients-free'),
                            'type' => 'stripe_input',
                            'default' => '',
                            'description' => __('Add the signing secret for your live mode webhook.', 'buddyclients-free'),
                            'stripe_key'    => 'signing',
                            'stripe_mode'   => 'live'
                        ],
                        'signing_test' => [
                            'label' => __('Test Signing Secret', 'buddyclients-free'),
                            'type' => 'stripe_input',
                            'default' => '',
                            'description' => __('Add the signing secret for your test mode webhook.', 'buddyclients-free'),
                            'stripe_key'    => 'signing',
                            'stripe_mode'   => 'test'
                        ],
                    ],
                ],
            ];
        }
    }

    
    /**
     * Affiliate settings.
     * 
     * @since 0.1.0
     */
    public static function affiliate( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'affiliate_percentage'  => 0,
            ];
            
        // Otherwise return settings data
        } else {
            return [
                'affiliate_program' => [
                    'title' => __('Affiliate Program', 'buddyclients-free'),
                    'description' => __('Define the payment methods and commission percentage for affiliates.', 'buddyclients-free'),
                    'fields' => [
                        'payment_options' => [
                            'label' => __('Payment Methods', 'buddyclients-free'),
                            'type' => 'checkboxes',
                            'options' => [
                                'paypal' => __('PayPal', 'buddyclients-free'),
                                'digital_check' => __('Digital Check', 'buddyclients-free'),
                                'physical_check' => __('Physical Check', 'buddyclients-free'),
                                'venmo' => __('Venmo', 'buddyclients-free'),
                            ],
                            'description' => __('Select the payment methods that are available to affiliates.', 'buddyclients-free'),
                        ],
                        'affiliate_percentage' => [
                            'label' => __('Affiliate Percentage', 'buddyclients-free'),
                            'type' => 'number',
                            'description' => __('What percentage of the full fee do affiliates receive?', 'buddyclients-free'),
                        ],
                        'commission_type' => [
                            'label' => __('Commission Type', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => [
                                'lifetime' => __('Lifetime', 'buddyclients-free'),
                                'first_sale' => __('First Sale', 'buddyclients-free'),
                            ],
                            'description' => __('Should affiliates receive commission for the first booking only or for every service the client books?', 'buddyclients-free'),
                        ],
                    ],
                ],
            ];
        }
    }
    
    /**
     * Booking settings.
     * 
     * @since 0.1.0
     */
    public static function booking( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'freelancer_id'         => '',
                'cancellation_window'   => 0,
                'minimum_fee'           => 1,
                'accept_bookings'       => 'open',
                'lock_team'             => 'lock',
                'skip_payment'          => 'no',
                'enable_projects'       => 'yes',
            ];
            
        // Otherwise return settings data
        } else {
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
                            'description' => __('Select this to skip the payment and make every submitted booking successful.<br>Use this setting if you process payments elsewhere.', 'buddyclients-free'),
                        ],
                        'cancellation_window' => [
                            'label' => __('Cancellation Window', 'buddyclients-free'),
                            'type' => 'number',
                            'description' => __('How many days do clients have to cancel bookings?<br>Team and commission payments will be marked as "eligible" after this timeframe.', 'buddyclients-free'),
                        ],
                        'minimum_fee' => [
                            'label' => __('Minimum Fee', 'buddyclients-free'),
                            'type' => 'number',
                            'description' => __('What is the minimum dollar amount per booking?<br>Note that paid bookings of less than $1 will fail.', 'buddyclients-free'),
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
    }

    
    /**
     * Sales settings.
     * 
     * @since 0.1.0
     */
    public static function sales( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'sales_types'                   => buddyc_get_setting( 'general', 'team_types' ),
                'sales_team'                    => 'yes',
                'sales_commission_percentage'   => 0,
                'self_bookings'                 => 'yes',
                'sales_team_mode'               => 'yes'
            ];
            
        // Otherwise return settings data
        } else {
            return [
                'assisted_bookings' => [
                    'title' => __('Assisted Bookings', 'buddyclients-free'),
                    'description' => __('Allow your team to create bookings on behalf of clients.', 'buddyclients-free'),
                    'fields' => [
                        'sales_team_mode' => [
                            'label' => __('Enable Sales Mode', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => [
                                'yes' => __('Yes', 'buddyclients-free'),
                                'no' => __('No', 'buddyclients-free'),
                            ],
                            'description' => __('Allow team members to create bookings for clients?', 'buddyclients-free'),
                        ],
                        'self_bookings' => [
                            'label' => __('Self-Bookings', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => [
                                'yes' => __('Yes', 'buddyclients-free'),
                                'no' => __('No', 'buddyclients-free'),
                            ],
                            'description' => __('Allow users to book their own services?', 'buddyclients-free'),
                        ],
                    ],
                ],
                'sales_team' => [
                    'title' => __('Sales Team', 'buddyclients-free'),
                    'description' => __('Allow your sales team to earn commission.', 'buddyclients-free'),
                    'fields' => [
                        'sales_types' => [
                            'label' => __('Sales Types', 'buddyclients-free'),
                            'type' => 'checkboxes',
                            'options' => buddyc_member_types( 'team' ),
                            'description' => __('In addition to the site admin, which team members can book services on behalf of clients?', 'buddyclients-free'),
                        ],
                        'sales_commission_percentage' => [
                            'label' => __('Sales Team Commission', 'buddyclients-free'),
                            'type' => 'number',
                            'description' => __('What percentage of the full fee should salespeople receive?', 'buddyclients-free'),
                        ],
                    ],
                ],
            ];
        }
    }
    
    /**
     * Help settings.
     * 
     * @since 0.1.0
     */
    public static function help( $defaults = null ) {
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'help_post_types'         => ['post'],
                'help_popup_content'      => 'both',
                'help_popup_display'      => 'always_show'
            ];
            
        // Otherwise return settings data
        } else {
            return [
                'help' => [
                    'title' => __('Help Posts', 'buddyclients-free'),
                    'description' => __('Select all post types you wish to link to help articles for clients.', 'buddyclients-free'),
                    'fields' => [
                        'help_post_types' => [
                            'label' => __('Help Post Types', 'buddyclients-free'),
                            'type' => 'checkboxes',
                            'options' => self::help_post_types(),
                            'description' => '',
                        ],
                    ],
                ],
                'help_popup' => [
                    'title' => __('Help Popup', 'buddyclients-free'),
                    'description' => __('Floating help button.', 'buddyclients-free'),
                    'fields' => [
                        'help_popup_content' => [
                            'label' => __('Help Popup Content', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => [
                                'both' => __('Help Docs and Contact Form', 'buddyclients-free'),
                                'help_only' => __('Help Docs Only', 'buddyclients-free'),
                                'contact_only' => __('Contact Form Only', 'buddyclients-free'),
                            ],
                            'description' => __('By default, the user will search help docs and have the option to contact.', 'buddyclients-free'),
                        ],
                        'help_popup_display' => [
                            'label' => __('Help Popup Display', 'buddyclients-free'),
                            'type' => 'dropdown',
                            'options' => [
                                'always_show' => __('Always Show', 'buddyclients-free'),
                                'always_hide' => __('Always Hide', 'buddyclients-free'),
                                'desktop_only' => __('Hide on Mobile', 'buddyclients-free'),
                                'mobile_only' => __('Hide on Desktop', 'buddyclients-free'),
                            ],
                            'description' => __('When to show the floating help button.', 'buddyclients-free'),
                        ],
                    ],
                ],
            ];
        }
    }
    
    /**
     * Style settings.
     * 
     * @since 0.1.0
     */
    public static function style( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'primary_color'     => '#072d68',
                'accent_color'      => '#56AEFF',
                'tertiary_color'    => '#3f719f',
            ];
            
        // Otherwise return settings data
        } else {
            return [
                'style' => [
                    'title' => __('Style Settings', 'buddyclients-free'),
                    'description' => __('Adjust global buddyclients styles to match your brand.', 'buddyclients-free'),
                    'fields' => [
                        'primary_color' => [
                            'label' => __('Primary Color', 'buddyclients-free'),
                            'type' => 'color',
                            'class' => 'color-field',
                            'description' => '',
                        ],
                        'accent_color' => [
                            'label' => __('Accent Color', 'buddyclients-free'),
                            'type' => 'color',
                            'description' => '',
                        ],
                        'tertiary_color' => [
                            'label' => __('Tertiary Color', 'buddyclients-free'),
                            'type' => 'color',
                            'description' => '',
                        ],
                    ]
                ],
            ];
        }
    }

    
    /**
     * Legal settings.
     * 
     * @since 0.1.0
     */
    public static function legal( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'legal_deadline'        => '30',
                'require_agreement'     => 'no'
            ];
            
        // Otherwise return settings data
        } else {
            return [
                'current_legal' => [
                    'title' => __( 'Current Legal Agreements', 'buddyclients-free' ),
                    'description' => sprintf(
                        /* translators: %1$s: URL to add content to legal agreements; %2$s: link text */
                        __('Select the current version of each legal agreement type. If transitioning to a new version, select the previous version and add a deadline for accepting the new agreement.<br><a href="%1$s">%2$s</a>', 'buddyclients-free'),
                        esc_url(admin_url('edit.php?post_type=buddyc_legal_mod')),
                        __('Add content to legal agreements for individual users.', 'buddyclients-free')
                    ),
                    'fields' => self::current_legal_fields(),
                ],
                'deadline' => [
                    'title' => __( 'Deadline', 'buddyclients-free' ),
                    'description' => __( 'How long do users have to accept new versions of agreements?', 'buddyclients-free' ),
                    'fields' => [
                        'legal_deadline' => [
                            'label' => __( 'Deadline', 'buddyclients-free' ),
                            'type' => 'dropdown',
                            'options' => [
                                '7' => __( '7 Days', 'buddyclients-free' ),
                                '14' => __( '14 Days', 'buddyclients-free' ),
                                '30' => __( '30 Days', 'buddyclients-free' ),
                                '60' => __( '60 Days', 'buddyclients-free' ),
                                '' => __( 'Forever', 'buddyclients-free' ),
                            ],
                            'description' => '',
                        ],
                    ]
                ],
                'require_team_agreement' => [
                    'title' => __( 'Booking Requirements', 'buddyclients-free' ),
                    'description' => '',
                    'fields' => [
                        'require_agreement' => [
                            'label' => __( 'Require Active Team Member Agreement', 'buddyclients-free' ),
                            'type' => 'dropdown',
                            'options' => [
                                'yes' => __( 'Yes', 'buddyclients-free' ),
                                'no' => __( 'No', 'buddyclients-free' ),
                            ],
                            'description' => __( 'Should team members without active agreements be disallowed from accepting new projects?', 'buddyclients-free' ),
                        ],
                    ]
                ]
            ];
        }
    }
    
    /**
     * Email settings.
     * 
     * @since 0.1.0
     */
    public static function email( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'send_notifications'    => self::email_options( true ),
                'from_email'            => get_option('admin_email'),
                'from_name'             => get_option('blogname'),
                'notification_email'    => get_option('admin_email'),
            ];
            
        // Otherwise return settings data
        } else {
            return [
                'enable' => [
                    'title' => __( 'Enable Email Notifications', 'buddyclients-free' ),
                    'description' => __( 'Specify which email notifications to enable.', 'buddyclients-free' ),
                    'fields' => [
                        'send_notifications' => [
                            'label' => __( 'Enable Email Notifications', 'buddyclients-free' ),
                            'type' => 'checkboxes',
                            'options' => self::email_options(),
                            'default' => self::email_options( true ),
                            'description' => __( 'Select the events you would like to trigger email notifications for users.<br><span style="color: red">*</span> Disabling starred emails may impact plugin functionality.', 'buddyclients-free' ),
                        ],
                    ],
                ],
                'send' => [
                    'title' => __( 'Email Sender', 'buddyclients-free' ),
                    'description' => __( 'Specify the sent-from name and email.', 'buddyclients-free' ),
                    'fields' => [
                        'from_email' => [
                            'label' => __( 'From Email', 'buddyclients-free' ),
                            'type' => 'email',
                            'default' => get_option('admin_email'),
                            'description' => __( 'Notifications will be sent to users from this email address.', 'buddyclients-free' ),
                        ],
                        'from_name' => [
                            'label' => __( 'From Name', 'buddyclients-free' ),
                            'type' => 'text',
                            'default' => get_option('blogname'),
                            'description' => __( 'What name should appear on email notifications?', 'buddyclients-free' ),
                        ],
                    ],
                ],
                'admin' => [
                    'title' => __( 'Admin Email Notifications', 'buddyclients-free' ),
                    'description' => __( 'Handle how you receive admin notifications.', 'buddyclients-free' ),
                    'fields' => [
                        'notification_email' => [
                            'label' => __( 'Notification Email', 'buddyclients-free' ),
                            'type' => 'email',
                            'default' => get_option('admin_email'),
                            'description' => __( 'Where would you like to receive admin email notifications?', 'buddyclients-free' ),
                        ],
                    ],
                ],
                'log' => [
                    'title' => __( 'Email Log', 'buddyclients-free' ),
                    'description' => sprintf(
                        /* translators: %s: URL to view the email log */
                        __('Email log settings. <a href="%s">View the email log.</a>', 'buddyclients-free'),
                        esc_url(admin_url('/admin.php?page=buddyc-email-log')),
                    ),
                    'fields' => [
                        'email_log_time' => [
                            'label' => __( 'Email Log Time', 'buddyclients-free' ),
                            'type' => 'dropdown',
                            'default' => '182',
                            'options' => [
                                '30'  => __( '30 Days', 'buddyclients-free' ),
                                '90'  => __( '90 Days', 'buddyclients-free' ),
                                '182' => __( '6 Months', 'buddyclients-free' ),
                                '365' => __( '1 Year', 'buddyclients-free' ),
                                'always' => __( 'Forever', 'buddyclients-free' )
                            ],
                            'description' => __( 'Email log records older than the selected timeframe will be deleted.', 'buddyclients-free' ),
                        ],
                    ],
                ]
            ];
        }
    }
    
    /**
     * Integrations settings.
     * 
     * @since 0.4.0
     */
    public static function integrations( $defaults = null ) {
        
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                
            ];
            
        // Otherwise return settings data
        } else {
            return [
                'meta' => [
                    'title' => __( 'Meta Ads Integration', 'buddyclients-free' ),
                    'description' => __( 'Set up the API integration to send conversion events to Meta (Facebook).', 'buddyclients-free' ),
                    'fields' => [
                        'meta_access_token' => [
                            'label' => __( 'Access Token', 'buddyclients-free' ),
                            'type' => 'text',
                            'description' => __( 'Enter your access token.', 'buddyclients-free' ),
                        ],
                        'meta_pixel_id' => [
                            'label' => __( 'Pixel ID', 'buddyclients-free' ),
                            'type' => 'text',
                            'description' => __( 'Enter your pixel ID.', 'buddyclients-free' ),
                        ],
                    ],
                ],
            ];
        }
    }

        
    /**
     * License settings.
     * 
     * @since 0.1.0
     */
    public static function license( $defaults = null ) {
        // Check whether we want defaults
        if ( $defaults ) {
            return [
                'license_key'     => '',
            ];
            
        // Otherwise return settings data
        } else {
            $license_message = function_exists( 'buddyc_license_message' ) ? buddyc_license_message() : '';
            return [
                'license_key' => [
                    'title' => __( 'License Activation', 'buddyclients-free' ),
                    'description' => __( 'Activate your BuddyClients license.', 'buddyclients-free' ),
                    'fields' => [
                        'license_key' => [
                            'label' => __( 'License Key', 'buddyclients-free' ),
                            'type' => 'text',
                            'description' => $license_message,
                        ],
                    ],
                ]
            ];
        }
    }
    
    /**
     * Builds pages settings array.
     * 
     * @since 0.1.0
     */
    public static function pages( $defaults = null ) {
        
        // Get page list
        $pages = PageManager::pages();
        
        // Check whether we want defaults
        if ( $defaults ) {
            
            // Initialize
            $defaults = [];
            
            // Loop through page types
            foreach ($pages as $page_type => $pages) {
                foreach ($pages as $page_key => $page_data) {
                    $defaults[$page_key] = null;
                }
            }
            
            return $defaults;
            
        // Otherwise return settings data
        } else {
        
            // Initialize settings array
            $settings = [];
            
            // Loop through page types
            foreach ($pages as $page_type => $pages) {
                $settings[$page_type] = [
                    'title' => sprintf(
                        /* translators: %s: page type (e.g., 'Service', 'Product') */
                        __('%s Pages.', 'buddyclients-free'),
                        esc_html( ucfirst( $page_type ) ),
                    ),
                    'description' => sprintf(
                        /* translators: %s: page type (e.g., 'service', 'product') */
                        __('Choose or create your %s pages.', 'buddyclients-free'),
                        esc_html( $page_type )
                    ),
                ];
                
                foreach ($pages as $page_key => $page_data) {
                    $settings[$page_type]['fields'][$page_key] = [
                        'label' => $page_data['label'],
                        'type' => 'page',
                        'options' => self::page_options(),
                        'post_title' => $page_data['post_title'] ?? '',
                        'post_content' => $page_data['post_content'] ?? '',
                        'required_component' => $page_data['required_component'] ?? null,
                        'description' => $page_data['description'] ?? '',
                    ];
                }
            }
            return $settings;
        }
    }
    
    /**
     * Builds a list of all page options.
     * 
     * @since 0.1.0
     */
    private static function page_options() {
        // Initialize
        $options = [ '' => __( 'Select a Page', 'buddyclients-free' ) ];
        
        // Retrieve all pages
        $all_pages = get_pages(); // wp function
        
        // Loop through pages
        foreach ($all_pages as $single_page) {
            // Add to array
            $options[$single_page->ID] = $single_page->post_title;
        }
        return $options;
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
    
    /**
     * Builds a list of email notification options.
     * 
     * @since 0.1.0
     * 
     * @param bool $defaults Optional. Whether to return an array of default options.
     */
    private static function email_options( $defaults = null ) {
        // Initialize
        $options = [];
        $default_options = [];
        
        // Retrieve all email templates
        $templates = EmailTemplateManager::templates();
        
        foreach ( $templates as $key => $data ) {
            if ( isset($data['required']) && $data['required'] ) {
                $required = '<span style="color: red"> *</span>';
            } else {
                $required = '';
            }
            $options[$key] = $data['label'] . $required;
            $default_options[] = $options[$key];
        }
        
        // Return defaults or options
        return $defaults ? $default_options : $options;
    }
    
    /**
     * Defines Xprofile match type fields.
     * 
     * @since 0.1.0
     */
    private static function match_type_fields() {
        
        // Get selected xprofile filter fields
        $settings = get_option('buddyc_booking_settings', array());
        $selected_fields = isset($settings['xprofile_fields']) ? $settings['xprofile_fields'] : array();
        
        // Get all xprofile field data
        $all_xprofile_fields = XprofileManager::all_xprofile();
        
        // Initialize
        $fields = [];
        
        foreach ($selected_fields as $selected_field) {
        
            $fields['match_types_' . $selected_field] = [
                'label' => __( 'Match Type', 'buddyclients-free' ),
                'type' => 'dropdown',
                'options' => [
                    'exact' => __( 'Exact Match', 'buddyclients-free' ),
                ],
                'description' => __( 'Select from dropdown and checkbox fields that are available to team members.', 'buddyclients-free' ),
            ];
            
            $fields['multiple_options_' . $selected_field] = [
                'label' => __( 'Multiple Options', 'buddyclients-free' ),
                'type' => 'dropdown',
                'options' => [
                    'no' => __( 'No', 'buddyclients-free' ),
                ],
                'description' => __( 'Can clients select multiple options for this field?', 'buddyclients-free' ),
            ];
                
            // Check field type
            if ($all_xprofile_fields[$selected_field]['type'] = 'checkbox') {
                $fields['match_types_' . $selected_field]['options'] = [
                    'exact' => __( 'Exact Match', 'buddyclients-free' ),
                    'include_any' => __( 'Include Any', 'buddyclients-free' ),
                    'include_all' => __( 'Include All', 'buddyclients-free' ),
                    'exclude' => __( 'Exclude', 'buddyclients-free' ),
                ];
                $fields['multiple_options_' . $selected_field]['options'] = [
                    'yes' => __( 'Yes', 'buddyclients-free' ),
                    'no' => __( 'No', 'buddyclients-free' ),
                ];
            }
        }
        return $fields;
    }
    
    /**
     * Retrieves help post type options.
     * 
     * @since 0.1.0
     */
    private static function help_post_types() {
        
        // Initialize array
        $options = [];
        
        // Get all post types
        $post_types = get_post_types();
        
        // Loop through post types
        foreach ($post_types as $post_type) {
            // Skip buddyclients post types
            if (strpos($post_type, 'buddyclients-free') !== false ||
            strpos($post_type, 'buddyc_') !== false ||
            
                // Skip native WP post types
                $post_type === 'revision' ||
                $post_type === 'nav_menu_item' ||
                $post_type === 'custom_css' ||
                $post_type === 'customize_changeset' ||
                $post_type === 'wp_block' ||
                $post_type === 'wp_template_part' ||
                $post_type === 'wp_global_styles' ||
                $post_type === 'oembed_cache' ||
                $post_type === 'wp_navigation' ||
                $post_type === 'user_request' ||
                $post_type === 'attachment' ||
                $post_type === 'wp_template' ||
                
                // Skip buddypress/buddyboss post types
                $post_type === 'bp-email' ||
                $post_type === 'bp-group-type' ||
                $post_type === 'bp-member-type' ||
                $post_type === 'buddyboss_fonts' ||
                $post_type === 'bp_ps_form'
                ) {
                continue;
            }
            
            // Get post type label
            $labels = get_post_type_labels(get_post_type_object($post_type));
            $post_type_name = $labels->name;
            
            // Add to array
            $options[$post_type] = $post_type_name;
        }
        return $options;
    }
    
    /**
     * Outputs the legal types.
     * 
     * @since 0.4.0
     */
    public static function legal_types() {
        
        // Define legal agreement types
        $types = array(
            'team' => __( 'Team Member Agreement', 'buddyclients-free' ),
            'client' => __( 'Service Agreement', 'buddyclients-free' ),
            'affiliate' => __( 'Affiliate Agreement', 'buddyclients-free' ),
        );
        
        /**
         * Filters the legal types.
         *
         * @since 0.4.0
         *
         * @param array  $types An associative array of legal types.
         */
         $types = apply_filters( 'buddyc_legal_types', $types );
         
         return $types;
    }
    
    /**
     * Builds fields for current legal agreements.
     * 
     * @since 0.1.0
     */
    private static function current_legal_fields() {
        
        // Initialize settings array
        $fields = [];
        
        // Define legal agreement types
        $types = self::legal_types();
        
        // Build settings field array
        foreach ($types as $type_key => $type_label) {
            $fields[$type_key . '_legal_version'] = [
                'label' => $type_label,
                'type' => 'legal',
                'description' => '',
            ];
            $fields[$type_key . '_legal_version_draft'] = [
                'label' => '',
                'type' => 'hidden',
                'description' => '',
            ];
            $fields[$type_key . '_legal_version_prev'] = [
                'label' => '',
                'type' => 'hidden',
                'description' => '',
            ];
        }
        
        return $fields;
    }
    
    /**
     * Builds fields for current legal agreements.
     * 
     * @since 0.1.0
     */
    private static function current_legal_fields_old() {
        
        // Initialize settings array
        $fields = [];
        
        // Define legal agreement types
        $types = array(
            'team' => 'Team Member Agreement',
            'client' => 'Service Agreement',
            'affiliate' => 'Affiliate Agreement',
        );
        
        // Build settings field array
        foreach ($types as $type_key => $type_label) {
            $fields[$type_key . '_legal_version'] = [
                'label' => $type_label,
                'type' => 'legal',
                'description' => '',
            ];
            $fields[$type_key . '_legal_version_draft'] = [
                'label' => '',
                'type' => 'hidden',
                'description' => '',
            ];
            $fields[$type_key . '_legal_version_prev'] = [
                'label' => '',
                'type' => 'hidden',
                'description' => '',
            ];
        }
        return $fields;
    }

}