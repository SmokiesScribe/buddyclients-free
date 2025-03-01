<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Displays info messages in the admin area.
 * 
 * @since 0.3.0
 * @since 1.0.14 Require enabled components.
 */
class AdminInfo {

    /**
     * The formatted tab label. 
     * 
     * @var string
     */
    private $key;

    /**
     * The required component. 
     * 
     * @var ?string
     */
    private $required_component;
    
    /**
     * Constructor method.
     * 
     * @since 0.3.0
     * 
     * @param   string  $tab_label  The label of the active nav tab.
     */
    public function __construct( $tab_label ) {
        $this->key = strtolower( $tab_label );
        $this->required_component = self::required_component( $this->key );
        
        // Make sure admin info notices are enabled
        if ( buddyc_get_setting( 'general', 'admin_info' ) === 'disable' ) {
            return;
        }

        // Check for required component
        $disabled = $this->component_disabled();
        if ( $disabled ) {
            return;
        }
        
        // Retrieve message
        $message = $this->info_message();
        
        // Generate notice
        if ( $message ) {
            $this->generate_notice( $message );
        }
    }
    
    /**
     * Generates the admin notice.
     * 
     * @since 0.3.0
     * 
     * @param   string  $message    The message for the notice.
     */
    private function generate_notice( $message ) {
        // Make sure message exists
        if ( ! empty( $message ) ) {
            
            // Define notice args
            $notice_args = [
                'key'               => 'admin_info',
                'repair_link'       => ['/admin.php?page=buddyc-general-settings', 'https://buddyclients.com/help'],
                'dismissable'       => true,
                'repair_link_text'  => [__( 'Disable tips', 'buddyclients-free' ), __( 'Help docs', 'buddyclients-free' )],
                'message'           => $message,
                'color'             => 'blue',
                'classes'           => 'buddyc-tip-notice',
                'icon'              => 'admin-info'
            ];
            
            // Generate notice
            buddyc_admin_notice( $notice_args );
        }
    }

    /**
     * Returns the message by the nav tab label.
     * 
     * @since 1.0.14
     * 
     * @param   string  $key    The nav label to retrieve the message for.
     */
    private static function get_message( $key ) {
        $messages = [            
            // Dashboard
            'all bookings' => [
                __( 'This page displays all successful and abandoned bookings. You can filter bookings by status.', 'buddyclients-free' ),
                __( 'Click each booking\'s services to view detailed information about each of the services, including each service\'s team member, status, and files.', 'buddyclients-free' ),
                __( 'Please be cautious when deleting bookings. They cannot be recovered.', 'buddyclients-free' ),
            ],
            'overview' => [
                __( 'Evaluate your business with this overview of all bookings for a specified timeframe.', 'buddyclients-free' ),
                __( 'The net fee represents the total fees minus team member payments, affiliate commission, and sales commission.', 'buddyclients-free' ),
            ],
            'payments' => [
                __('View and manage all outgoing payments for your business.', 'buddyclients-free' ),
                __( 'These payments are generated automatically when bookings are completed. You will find payments for team members, affiliates, and salespeople.', 'buddyclients-free' ),
                __( 'The payment status will update to "eligible" automatically at the end of your cancellation window. When you have processed a payment, update the status to "paid" to keep track of your outgoing payments.', 'buddyclients-free' ),
                sprintf(
                    /* translators: %s: the link to the booking settings */
                    __( '%s to define how long clients have to request to cancel a service.', 'buddyclients-free'),
                    self::link( __('Update your cancellation window', 'buddyclients-free'), admin_url('admin.php?page=buddyc-booking-settings' ) )
                ),
            ],
            'users' => [
                __( 'Use this page to quickly view and manage all team members, clients, and affiliates.', 'buddyclients-free' ),
                __( 'Download PDFs of users\' agreements.', 'buddyclients-free' ),
                self::link( __('Update your legal agreements', 'buddyclients-free'), admin_url('admin.php?page=buddyc-legal-settings' )                
            ),
        ],

            // Settings
            'general'               => __( 'General settings for the BuddyClients plugin.', 'buddyclients-free' ) . ' <i>' . __( 'Remove these messages by changing the "Admin Info Messages" setting below to "disable."', 'buddyclients-free' ) . '</i>',
            'components'            => __( 'Enable or disable individual components. The components available to you here depend on your BuddyClients subscription level.', 'buddyclients-free' ),
            'pages'                 => [
                __( 'Select or create the pages used by the BuddyClients plugin.', 'buddyclients-free' ),
                __( 'Clicking the "Create Page" button will automatically insert the shortcode into the page.', 'buddyclients-free' ),
                __( 'If you create the pages manually, be sure to add each specified shortcode.', 'buddyclients-free' ),
            ],
            'styles'                => __( 'Adjust the plugin styles to match your brand.', 'buddyclients-free' ),
            'bookings'              => [
                __( 'Update these settings to change the way BuddyClients manages bookings.', 'buddyclients-free' ),
                __( 'If you are the only member of your team, enabling Freelancer Mode simplifies the BuddyClients interface and assigns all services to you.', 'buddyclients-free' ),
            ],
            'stripe'                => [
                __( 'Connect your Stripe account to BuddyClients in order to accept payments.', 'buddyclients-free' ),
                __( 'Once you have added the information, click "Validate Stripe" to test your keys.', 'buddyclients-free' ),
                __( 'It is recommended to first enable Test Mode to check that your Stripe integration is working properly.', 'buddyclients-free' ),
                __( 'The webhooks setup allows Stripe to "talk" to your website and notify you of a successful payment. If webhooks are not properly set up, your bookings will not change to "succeeded" when payments are successful.', 'buddyclients-free' ),
                __( 'If payments have been processed without webhooks set up correctly, use the "Check for Payments" button to repair bookings.', 'buddyclients-free' ),
            ],
            'sales'                 => [
                __( 'Enable sales mode to allow manual and assisted bookings.', 'buddyclients-free' ),
                __( 'When this setting is enabled, users with permission will see a form at the top of the booking page allowing the creation of a booking on behalf of a client.', 'buddyclients-free' ),
                __( 'On submission, the client will receive an email with a link to submit payment and finalize the booking.', 'buddyclients-free' ),
                __( 'If sales commission is enabled, a commission payment will be generated for the salesperson based on the percentage set below.', 'buddyclients-free' ),
            ],
            'emails'                => [
                __( 'Update these settings to define which emails are sent and the appropriate email addresses.', 'buddyclients-free' ),
                sprintf(
                    /* translators: %s: the link to customize email templates */
                    __( 'Customize your %s.', 'buddyclients-free' ),
                    self::link( __( 'email templates', 'buddyclients-free' ), admin_url('admin.php?page=edit.php?post_type=buddyc_email' ) )
                ),
            ],
            'legal' => [
                __( 'When you create new legal agreements, users will be prompted to accept the new agreement within the timeframe below.', 'buddyclients-free' ),
                sprintf(
                    /* translators: %s: link to user list */
                    __( 'Manage your %s.', 'buddyclients-free' ),
                    self::link( __( 'legal agreements', 'buddyclients-free' ), admin_url( 'admin.php?page=buddyc-legal-agreements' ) )
                )
            ],
            'affiliate'             =>  [
                __( 'Manage the settings for your affiliate program, including the available payout methods and the affiliate commission percentage.', 'buddyclients-free' ),
                __( 'Lifetime affiliate commission applies to all services new clients ever book, while first-sale commission applies only to the client\'s first booking.', 'buddyclients-free' ),
            ],
            'contact'               =>  [
                __( 'Define the rules and content for the help popup and lead generation popup.', 'buddyclients-free' ),
                __( 'Options for popup content include a help docs live search and a contact form. If both are selected, the user will first see the search. If their search returns no results, they will have the option to complete the contact form.', 'buddyclients-free' ),
            ],
            
            // Services
            'services'              =>  __( 'Create your services to get started. Only valid services will appear on the booking form.', 'buddyclients-free' ),
            'service types'         =>  __( 'Define your service types. Services will be grouped by type on the booking form.', 'buddyclients-free' ),
            'adjustment fields'     =>  [
                __( 'Create adjustment fields for the most flexibility in your services\' customization and pricing.', 'buddyclients-free' ),
                __( 'When a user selects a service on the booking form, the attached adjustment fields will become visible.', 'buddyclients-free' ),
                __( 'Adjustment fields are excellent for add-ons, customization, and additional details that impact service pricing.', 'buddyclients-free' ),
                __( 'You can automatically add, subtract, or multiply the service fee based on the selected adjustment.', 'buddyclients-free' ),
            ],
            'rate types'            =>  [
                __( 'In addition to flat-rate services, custom rate types allow for specific, flexible pricing.', 'buddyclients-free' ),
                __( 'Each service is assigned a rate type. Examples of potential rate types including per word, hourly, and per project.', 'buddyclients-free' ),
                __( 'Rate types can be attached to the service or the project. Attaching a rate type to the service means the client will be prompted to enter separate numbers (such as word count, hours, etc.) for each service.', 'buddyclients-free' ),
                __( 'Attaching a rate type to the project means the client will enter the number once, and that figure will be applied to all selected services using that rate type.', 'buddyclients-free' ),
            ],
            'team roles'            =>  [
                __( 'Team member roles allow you to group team members based on the type of services they complete.', 'buddyclients-free' ),
                __( 'For example, a team member who completes proofreading and copyediting may be assigned a team role called "Editor."', 'buddyclients-free' ),
                __( 'Team roles allow you to easily specify which team members may be assigned to certain services without identifying every service individually.', 'buddyclients-free' ),
                __( 'This simplifies the process of expanding your team and services.', 'buddyclients-free' ),
            ],
            'filter fields'         =>  [
                __( 'Refine which team members are available to clients based on these fields.', 'buddyclients-free' ),
                __( 'For each filter field, select a corresponding profile field.', 'buddyclients-free' ),
                __( 'Only team members whose response to the profile question aligns with the required response from the client will be visible on the booking form.', 'buddyclients-free' ),
            ],
            'file upload types'     =>  __( 'Create file upload types to require different types of files based on the service(s) selected.', 'buddyclients-free' ),
            
            // Emails
            'email templates'       =>  [
                __( 'Customize the BuddyClients plugin emails using these templates. Dynamic values are enclosed in double brackets, e.g. {{site_name}}.', 'buddyclients-free' ),
                __( 'To restore default email templates, delete the templates you wish to restore and click "Repair Email Templates."', 'buddyclients-free' ),
            ],
            'email log'             =>  [
                sprintf(
                    /* translators: %s: the link to update the email settings */
                    __( 'This page lists all emails sent by the BuddyClients plugin during the timeframe specified in the %s.', 'buddyclients-free' ),
                    sprintf(
                        '<a href="%1$s">%2$s</a>',
                        admin_url( 'admin.php?page=buddyc-email-settings' ),
                        __( 'email settings', 'buddyclients-free' )
                    )
                ),
                sprintf(
                    /* translators: %s: the time to retain email logs (e.g. '30 days' or 'always') */
                    __( 'Currently retaining email logs for <strong>%s</strong>.', 'buddyclients-free' ),
                    buddyc_get_setting( 'email', 'email_log_time' ) !== 'always' ? sprintf( '%1$s %2$s', buddyc_get_setting( 'email', 'email_log_time' ), __( 'days', 'buddyclients-free' ) ) : __( 'all time', 'buddyclients-free' )
                )
            ],

            // Briefs
            'briefs'                =>  [
                __( 'Briefs are generated automatically when clients book services.', 'buddyclients-free' ),
                __( 'Use briefs to request additional info your team may need to complete the service.', 'buddyclients-free' ),
                __( 'To set up briefs, first add brief types. Then create brief fields for each type. Finally, select the applicable brief type(s) within the settings for your services.', 'buddyclients-free' ),
            ],
            'brief types'           =>  [
                __( 'Create brief types here. When creating services, select the applicable brief type(s). A brief of each type will automatically be generated when the service is booked.', 'buddyclients-free' ),
                __( 'For example, you could create a brief type called "Editing" and connect it to "Copyediting" and "Proofreading" services.', 'buddyclients-free' ),
            ],
            'brief fields'          =>  __( 'Create fields for each brief type. For each field, specify how the field should be displayed on the brief form, including the field type, file upload settings, and checkbox or dropdown options, as applicable.', 'buddyclients-free' ),

            // Testimonials
            'testimonials'          =>  [
                __( 'Testimonial drafts are created from submissions to the testimonials form. Publish a draft to display it on your testimonials page.', 'buddyclients-free' ),
                __( 'You can also create testimonials manually, adding the client\'s name and photo yourself.', 'buddyclients-free' ),
            ],

            // Legal
            'user agreements'       => [
                __( 'Team and affiliate agreements will appear here after they are signed and submitted.', 'buddyclients-free' ),
                sprintf(
                    /* translators: %1$s: the link to the legal settings; %2$s: the link to add legal modifications */
                    __( 'Manage your %1$s. Customize agreements for specific users by adding %2$s.', 'buddyclients-free' ),
                    self::link( __( 'legal agreements', 'buddyclients-free' ), admin_url('admin.php?page=buddyc-legal-agreements' ) ),
                    self::link( __( 'modifications', 'buddyclients-free' ), admin_url('edit.php?post_type=buddyc_legal_mod' ) )
                ),
            ],
            'legal modifications'   => [
                __( 'Customize legal agreements for specific individuals. The content of your legal modification will be appended to the legal agreement for the user(s) you select.', 'buddyclients-free' ),
            ],
            'legal agreements' => [
                __( 'Create legal agreements for your team members, affiliates, and clients.', 'buddyclients-free' ),
                __( 'Team members and affiliates will provide signatures, and clients will check a box accepting the service agreement when booking services.', 'buddyclients-free' ),
                sprintf(
                    /* translators: %s: link to user list */
                    __( 'You can download PDF versions of these agreements from the %s.', 'buddyclients-free' ),
                    self::link( __( 'user list', 'buddyclients-free' ), admin_url( 'admin.php?page=buddyc-users' ) )
                )
            ],

            // Files
            'file list'             => [
                __( 'View all files associated with services and bookings here.', 'buddyclients-free' ),
                __( 'Click each file to view and manage it. If a file is not showing, ensure it has been attached correctly.', 'buddyclients-free' ),
            ],
            'upload files'          => [
                __( 'Upload files that may be required for bookings or team member profiles.', 'buddyclients-free' ),
                __( 'Specify the file types and sizes allowed for uploads. Ensure these settings align with your needs and storage limits.', 'buddyclients-free' ),
            ],
            'import files'          => [
                __( 'Import files in bulk by selecting a CSV file with your data.', 'buddyclients-free' ),
                __( 'Ensure the CSV file is formatted correctly and contains the appropriate fields.', 'buddyclients-free' ),
            ],
            
            // Misc
            'misc'                  => [
                __( 'Manage various settings for the BuddyClients plugin that do not fall into other categories.', 'buddyclients-free' ),
                __( 'Ensure that all necessary configurations are made to ensure the proper functionality of the plugin.', 'buddyclients-free' )
            ]
        ];
        return $messages[$key] ?? null;
    }

    /**
     * Generates html for a link. 
     * 
     * @since 1.0.25
     * 
     * @param   string  $text   The link text.
     * @param   string  $url    The link url.
     */
    private static function link( $text, $url ) {
        return sprintf(
            '<a href="%s">%s</a>',
            esc_url( $url ),
            esc_html( $text )
        );
    }
    
    /**
     * Defines the info messages by nav tab label.
     * 
     * @since 0.3.0
     */
    private function info_message() {        
        // Get message
        $message = self::get_message( $this->key );

        if ( $message ) {
            if ( is_array( $message ) ) {
                return '<p>' . implode( '</p><p>', $message ) . '</p>';
            } else {
                return '<p>' . $message . '</p>';
            }
        }
    }

    /**
     * Checks whether a required component is disabled.
     * 
     * @since 1.0.14
     * 
     * @return  bool    True if the component exists and is disabled, false if not.
     */
    private function component_disabled() {
        if ( $this->required_component ) {
            return ! buddyc_component_enabled( $this->required_component );
        } else {
            return false;
        }
    }

    /**
     * Retrieves the required component for a tab label. 
     * 
     * @since 1.0.14
     * 
     * @param   string  $key  The formatted label of the active nav tab.
     */
    private static function required_component( $key ) {
        $required_components = [
            'stripe'        => 'Stripe',
            'briefs'        => 'Brief',
            'legal'         => 'Legal',
            'sales'         => 'Sales',
            'testimonials'  => 'Testimonial',
            'affiliate'     => 'Affiliate',
            'availability'  => 'Availability',
            'emails'        => 'Email',
            'bookings'      => 'Booking',
            //'contact'       => 'Contact',
            //'quote'         => 'Quote',
        ];

        return $required_components[$key] ?? null;
    }

    /**
     * Updates the setting to dismiss admin tips.
     * 
     * @since 1.0.27
     */
    public static function dismiss() {
        buddyc_update_setting('general', 'admin_info', 'disable' );
    }
}
