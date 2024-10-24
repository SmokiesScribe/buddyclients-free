<?php
namespace BuddyClients\Admin;

/**
 * Admin page manager.
 *
 * Organizes all admin pages.
 */
class AdminPageManager {

    /**
     * Instance of the class.
     *
     * @var AdminPageManager|null The single instance of the class.
     * @since 0.1.0
     */
    protected static $instance = null;

    /**
     * Retrieves the instance of the class.
     *
     * @since 0.1.0
     * @static
     * @return AdminPageManager The instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Creates all admin pages.
     * 
     * @since 0.1.0
     */
    public function run() {
        foreach (self::admin_pages() as $key => $data) {
            new AdminPage( $key, $data );
        }
    }

    /**
     * Retrieves an array of admin pages.
     * 
     * @since 0.1.0
     * @return array An associative array of admin pages info.
     */
    public static function admin_pages() {
        $pages = [
            // Settings Pages
            'general' => [
                'key' => 'general',
                'settings' => true,
                'title' => __('Settings', 'buddyclients'),
                'parent_slug' => 'bc-dashboard',
                'bc_menu_order' => 26,
                'group' => 'settings'
            ],
            'settings_separator' => [
                'key' => 'separator',
                'settings' => false,
                'title' => '',
                'parent_slug' => 'bc-dashboard',
                'bc_menu_order' => 25,
                'group' => 'settings'
            ],
            'components' => [
                'key' => 'components',
                'settings' => true,
                'title' => __('Components', 'buddyclients'),
                'parent_slug' => null,
            ],
            'stripe' => [
                'key' => 'stripe',
                'settings' => true,
                'title' => 'Stripe',
                'parent_slug' => null,
                'required_class' => 'BuddyClients\Components\Stripe\StripeKeys',
                'required_component' => 'Stripe'
            ],
            'affiliate' => [
                'key' => 'affiliate',
                'settings' => true,
                'title' => __('Affiliate Program', 'buddyclients'),
                'parent_slug' => null,
                'required_component' => 'Affiliate'
            ],
            'booking' => [
                'key' => 'booking',
                'settings' => true,
                'title' => __('Bookings', 'buddyclients'),
                'parent_slug' => null,
            ],
            'sales' => [
                'key' => 'sales',
                'settings' => true,
                'title' => __('Sales', 'buddyclients'),
                'parent_slug' => null,
                'required_component' => 'Sales'
            ],
            'help' => [
                'key' => 'help',
                'settings' => true,
                'title' => __('Help Posts', 'buddyclients'),
                'parent_slug' => null,
            ],
            'style' => [
                'key' => 'style',
                'settings' => true,
                'title' => __('Style', 'buddyclients'),
                'parent_slug' => null,
            ],
            'pages' => [
                'key' => 'pages',
                'settings' => true,
                'title' => __('Pages', 'buddyclients'),
                'parent_slug' => null,
            ],
            'legal' => [
                'key' => 'legal',
                'settings' => true,
                'title' => __('Legal', 'buddyclients'),
                'parent_slug' => null,
                'required_component' => 'Legal'
            ],
            'email' => [
                'key' => 'email',
                'settings' => true,
                'title' => __('Emails', 'buddyclients'),
                'parent_slug' => null,
                'required_class' => 'BuddyClients\Components\Email\EmailTemplateManager',
                'required_component' => 'Email',
            ],
            'integrations' => [
                'key' => 'integrations',
                'settings' => true,
                'title' => __('Integrations', 'buddyclients'),
                'parent_slug' => null,
            ],
            'license' => [
                'key' => 'license',
                'settings' => true,
                'title' => __('License Keys', 'buddyclients'),
                'parent_slug' => 'bc-dashboard',
                'bc_menu_order' => 30,
                'group' => 'settings'
            ],
            
            // Other Pages
            'email_log' => [
                'key' => 'email-log',
                'title' => __('Email Log', 'buddyclients'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'bc_email_log_content',
                'required_class' => 'BuddyClients\Components\Email\EmailTemplateManager'
            ],
            'bookings_dashboard' => [
                'key' => 'bookings-dashboard',
                'title' => __('Bookings Dashboard', 'buddyclients'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'bc_bookings_dashboard'
            ],
            'payments' => [
                'key' => 'payments',
                'title' => __('Payments', 'buddyclients'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'bc_payments_list'
            ],
            'users' => [
                'key' => 'users',
                'title' => __('Users', 'buddyclients'),
                'settings' => false,
                'parent_slug' => null,
                'callback' => 'bc_user_list',
                'callable' => 'bc_user_list'
            ],
            'booked_services' => [
                'key' => 'booked-services',
                'title' => __('Booked Services', 'buddyclients'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'bc_booked_services_table'
            ],
            'user_agreements' => [
                'key' => 'user-agreements',
                'title' => __('Legal', 'buddyclients'),
                'settings' => false,
                'parent_slug' => 'bc-dashboard',
                'callable' => 'bc_user_agreements_table',
                'bc_menu_order' => 8,
            ],
            'upgrade' => [
                'key' => 'upgrade',
                'title' => __('Upgrade BuddyClients', 'buddyclients'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => ['bc_upgrade_link', [true]]
            ],
        ];

        /**
         * Filters the admin pages.
         *
         * @since 0.3.4
         *
         * @param array $pages An array of admin pages info.
         * @return array Modified array of admin pages info.
         */
        $pages = apply_filters('bc_admin_pages', $pages);

        return $pages;
    }
}
