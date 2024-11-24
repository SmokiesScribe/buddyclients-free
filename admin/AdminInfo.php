<?php
namespace BuddyClients\Admin;

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
                'repair_link'       => ['/admin.php?page=buddyc-general-settings', 'https://buddyclients.com/help'],
                'dismissable'       => true,
                'repair_link_text'  => [__( 'Disable tips', 'buddyclients' ), __( 'Help docs', 'buddyclients' )],
                'message'           => '<i style="margin-right: 10px; color: #7face1" class="fa-solid fa-circle-info"></i>' . $message,
                'color'             => 'blue'
            ];
            
            // Generate notice
            buddyc_admin_notice( $notice_args );
        }
    }

    /**
     * Defines the array of info messages.
     * 
     * @since 1.0.14
     */
    private static function messages() {
        return [            
            // Dashboard
            'all bookings'          => __( 'This page displays all successful and abandoned bookings. You can filter bookings by status.<p>Click each booking\'s services to view detailed information about each of the services, including each service\'s team member, status, and files.</p><p>Please be cautious when deleting bookings. They cannot be recovered.</p>', 'buddyclients' ),
            'overview'              => __( 'Evaluate your business with this overview of all bookings for a specified timeframe.<p>The net fee represents the total fees minus team member payments, affiliate commission, and sales commission.</p>', 'buddyclients' ),
            'payments'              => sprintf(
                /* translators: %1$s: URL to booking settings */
                __('View and manage all outgoing payments for your business.<p>These payments are generated automatically when bookings are completed. You will find payments for team members, affiliates, and salespeople.</p><p>The payment status will update to "eligible" automatically at the end of your cancellation window. When you have processed a payment, update the status to "paid" to keep track of your outgoing payments.</p><p><a href="%1$s">%2$s</a> to define how long clients have to request to cancel a service.</p>', 
                'buddyclients'),
                esc_url(admin_url('/admin.php?page=buddyc-booking-settings')),
                __('Update your cancellation window', 'buddyclients')
            ),
            'users'                 => sprintf(
                /* translators: %1$s: URL to legal settings */
                __('Use this page to quickly view and manage all team members, clients, and affiliates.<p>Download PDFs of users\' agreements.</p><p><a href="%1$s">%2$s</a>.</p>', 
                'buddyclients'),
                esc_url(admin_url('/admin.php?page=buddyc-legal-settings')),
                __('Update your legal agreements', 'buddyclients')
            ),

            // Settings
            'settings'              => __( 'General settings for the BuddyClients plugin.<p><i>Remove these messages by changing the "Admin Info Messages" setting below to "disable."</i>', 'buddyclients' ),
            'components'            => __( 'Enable or disable individual components. The components available to you here depend on your BuddyClients subscription level.', 'buddyclients' ),
            'pages'                 => __( 'Select or create the pages used by the BuddyClients plugin.<p>Clicking the "Create Page" button will automatically insert the shortcode into the page.</p><p>If you create the pages manually, be sure to add each specified shortcode.</p>', 'buddyclients' ),
            'styles'                => __( 'Adjust the plugin styles to match your brand.', 'buddyclients' ),
            'bookings'              => __( 'Update these settings to change the way BuddyClients manages bookings.<p>If you are the only member of your team, enabling Freelancer Mode simplifies the BuddyClients interface and assigns all services to you.</p>', 'buddyclients' ),
            'stripe'                => __( 'Connect your Stripe account to BuddyClients in order to accept payments.<p>Once you have added the information, click "Validate Stripe" to test your keys.</p><p>It is recommended to first enable Test Mode to check that your Stripe integration is working properly.</p><p>The webhooks setup allows Stripe to "talk" to your website and notify you of a successful payment. If webhooks are not properly set up, your bookings will not change to "succeeded" when payments are successful.</p><p>If payments have been processed without webhooks set up correctly, use the "Check for Payments" button to repair bookings.</p>', 'buddyclients' ),
            'sales'                 => __( 'Enable sales mode to allow manual and assisted bookings.<p>When this setting is enabled, users with permission will see a form at the top of the booking page allowing the creation of a booking on behalf of a client.</p><p>On submission, the client will receive an email with a link to submit payment and finalize the booking.</p><p>If sales commission is enabled, a commission payment will be generated for the salesperson based on the percentage set below.</p>', 'buddyclients' ),
            'emails'                => sprintf(
                /* translators: %1$s: URL to email templates */
                __('Update these settings to define which emails are sent and the appropriate email addresses.<p>Customize your <a href="%1$s">email templates</a>.</p>', 
                'buddyclients'),
                esc_url(admin_url('/admin.php?page=edit.php?post_type=buddyc_email'))
            ),
            'legal'                 => sprintf(
                /* translators: %s: link to user list */
                __('These legal agreements are used for team members, affiliates, and clients.<p>Team members and affiliates will provide signatures, and clients will check a box accepting the service agreement when booking services.</p><p>When you create new legal agreements, users will be prompted to accept the new agreement within the timeframe below.</p><p>You can download PDF versions of these agreements from the <a href="%s">user list</a>.</p>', 'buddyclients'),
                esc_url( admin_url( '/admin.php?page=buddyc-users' ) )
            ),
            'affiliate'             => __( 'Manage the settings for your affiliate program, including the available payout methods and the affiliate commission percentage.<p>Lifetime affiliate commission applies to all services new clients ever book, while first-sale commission applies only to the client\'s first booking.</p>', 'buddyclients' ),
            'help posts'            => __( 'Define the post type(s) of your help documentation, and specify the popup\'s display rules and content.<p>Options for popup content include a help docs live search and a contact form.</p><p>If both are selected, the user will first see the search. If their search returns no results, they will have the option to complete the contact form.</p>', 'buddyclients' ),
            
            // Services
            'services'              => __( 'Create your services to get started. Only valid services will appear on the booking form.', 'buddyclients' ),
            'service types'         => __( 'Define your service types. Services will be grouped by type on the booking form.', 'buddyclients' ),
            'adjustment fields'     => __( 'Create adjustment fields for the most flexibility in your services\' customization and pricing.<p>When a user selects a service on the booking form, the attached adjustment fields will become visible.</p><p>Adjustment fields are excellent for add-ons, customization, and additional details that impact service pricing.</p><p>You can automatically add, subtract, or multiply the service fee based on the selected adjustment.</p>', 'buddyclients' ),
            'rate types'            => __( 'In addition to flat-rate services, custom rate types allow for specific, flexible pricing.<p>Each service is assigned a rate type. Examples of potential rate types including per word, hourly, and per project.</p><p>Rate types can be attached to the service or the project. Attaching a rate type to the service means the client will be prompted to enter separate numbers (such as word count, hours, etc.) for each service.</p><p>Attaching a rate type to the project means the client will enter the number once, and that figure will be applied to all selected services using that rate type.</p>', 'buddyclients' ),
            'team roles'            => __( 'Team member roles allow you to group team members based on the type of services they complete.<p>For example, a team member who completes proofreading and copyediting may be assigned a team role called "Editor."</p><p>Team roles allow you to easily specify which team members may be assigned to certain services without identifying every service individually.</p><p>This simplifies the process of expanding your team and services.</p>', 'buddyclients' ),
            'filter fields'         => __( 'Refine which team members are available to clients based on these fields.<p>For each filter field, select a corresponding profile field.</p><p>Only team members whose response to the profile question aligns with the required response from the client will be visible on the booking form.</p>', 'buddyclients' ),
            'file upload types'     => __( 'Create file upload types to require different types of files based on the service(s) selected.', 'buddyclients' ),
            
            // Emails
            'email templates'       => __( 'Customize the BuddyClients plugin emails using these templates. Dynamic values are enclosed in double brackets, e.g. {{site_name}}.<p>To restore default email templates, delete the templates you wish to restore and click "Repair Email Templates."</p>', 'buddyclients' ),
            'email log'             => __( 'This page lists all emails sent by the BuddyClients plugin during the timeframe specified in the plugin settings.', 'buddyclients' ),

            // Briefs
            'briefs'                => __( 'Briefs are generated automatically when clients book services.<p>Use briefs to request additional info your team may need to complete the service.</p><p>To set up briefs, first add brief types. Then create brief fields for each type. Finally, select the applicable brief type(s) within the settings for your services.</p>', 'buddyclients' ),
            'brief types'           => __( 'Create brief types here. When creating services, select the applicable brief type(s). A brief of each type will automatically be generated when the service is booked.<p>For example, you could create a brief type called "Editing" and connect it to "Copyediting" and "Proofreading" services.</p>', 'buddyclients' ),
            'brief fields'          => __( 'Create fields for each brief type. For each field, specify how the field should be displayed on the brief form, including the field type, file upload settings, and checkbox or dropdown options, as applicable.', 'buddyclients' ),

            // Testimonials
            'testimonials'          => __( 'Testimonial drafts are created from submissions to the testimonials form. Publish a draft to display it on your testimonials page.<p>You can also create testimonials manually, adding the client\'s name and photo yourself.</p>', 'buddyclients' ),

            // Files
            'file list'             => __( 'View all files associated with services and bookings here.<p>Click each file to view and manage it. If a file is not showing, ensure it has been attached correctly.</p>', 'buddyclients' ),
            'upload files'          => __( 'Upload files that may be required for bookings or team member profiles.<p>Specify the file types and sizes allowed for uploads. Ensure these settings align with your needs and storage limits.</p>', 'buddyclients' ),
            'import files'          => __( 'Import files in bulk by selecting a CSV file with your data.<p>Ensure the CSV file is formatted correctly and contains the appropriate fields.</p>', 'buddyclients' ),
            
            // Misc
            'misc'                  => __( 'Manage various settings for the BuddyClients plugin that do not fall into other categories.<p>Ensure that all necessary configurations are made to ensure the proper functionality of the plugin.', 'buddyclients' )
        ];
    }
    
    /**
     * Defines the info messages by nav tab label.
     * 
     * @since 0.3.0
     */
    private function info_message() {        
        // Define messages
        $messages = self::messages();
        
        // Return message
        return $messages[ $this->key ] ?? null;
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
}
