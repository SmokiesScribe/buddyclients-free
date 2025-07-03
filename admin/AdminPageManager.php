<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
                'title' => __('Settings', 'buddyclients-lite'),
                'parent_slug' => 'buddyc-dashboard',
                'buddyc_menu_order' => 26,
                'group' => 'settings'
            ],
            'settings_separator' => [
                'key' => 'separator',
                'settings' => false,
                'title' => '',
                'parent_slug' => 'buddyc-dashboard',
                'buddyc_menu_order' => 25,
                'group' => 'settings'
            ],
            'components' => [
                'key' => 'components',
                'settings' => true,
                'title' => __('Components', 'buddyclients-lite'),
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
                'title' => __('Affiliate Program', 'buddyclients-lite'),
                'parent_slug' => null,
                'required_component' => 'Affiliate'
            ],
            'booking' => [
                'key' => 'booking',
                'settings' => true,
                'title' => __('Bookings', 'buddyclients-lite'),
                'parent_slug' => null,
            ],
            'sales' => [
                'key' => 'sales',
                'settings' => true,
                'title' => __('Sales', 'buddyclients-lite'),
                'parent_slug' => null,
                'required_component' => 'Sales'
            ],
            'help' => [
                'key' => 'help',
                'settings' => true,
                'title' => __('Contact & Leads', 'buddyclients-lite'),
                'parent_slug' => null,
                'required_component' => 'Contact'
            ],
            'pages' => [
                'key' => 'pages',
                'settings' => true,
                'title' => __('Pages', 'buddyclients-lite'),
                'parent_slug' => null,
            ],
            'legal' => [
                'key' => 'legal',
                'settings' => true,
                'title' => __('Legal', 'buddyclients-lite'),
                'parent_slug' => null,
                'required_component' => 'Legal'
            ],
            'email' => [
                'key' => 'email',
                'settings' => true,
                'title' => __('Emails', 'buddyclients-lite'),
                'parent_slug' => null,
                'required_class' => 'BuddyClients\Components\Email\EmailTemplateManager',
                'required_component' => 'Email',
            ],
            'integrations' => [
                'key' => 'integrations',
                'settings' => true,
                'title' => __('Integrations', 'buddyclients-lite'),
                'parent_slug' => null,
            ],
            'license' => [
                'key' => 'license',
                'settings' => true,
                'title' => __('License Keys', 'buddyclients-lite'),
                'parent_slug' => 'buddyc-dashboard',
                'buddyc_menu_order' => 30,
                'group' => 'settings'
            ],
            
            // Other Pages
            'email_log' => [
                'key' => 'email-log',
                'title' => __('Email Log', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'buddyc_admin_table',
                'callback_args' => 'email_log',
                'required_class' => 'BuddyClients\Components\Email\EmailTemplateManager'
            ],
            'bookings_dashboard' => [
                'key' => 'bookings-dashboard',
                'title' => __('Bookings Dashboard', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'buddyc_bookings_dashboard',
            ],
            'payments' => [
                'key' => 'payments',
                'title' => __('Payments', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'buddyc_admin_table',
                'callback_args' => 'payments',
            ],
            'users' => [
                'key' => 'users',
                'title' => __('Users', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'buddyc_admin_table',
                'callback_args' => 'user_list',
            ],
            'leads' => [
                'key' => 'leads',
                'title' => __('Leads', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'buddyc_admin_table',
                'callback_args' => 'leads',
            ],
            'booked_services' => [
                'key' => 'booked-services',
                'title' => __('Booked Services', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'buddyc_admin_table',
                'callback_args' => 'booked_services'
            ],
            'booking_payments' => [
                'key' => 'booking-payments',
                'title' => __('Booking Payments', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'buddyc_admin_table',
                'callback_args' => 'booking_payments'
            ],
            'legal_agreements' => [
                'key' => 'legal-agreements',
                'title' => __('Legal Agreements', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => 'buddyc_legal_agreements_admin',
                'required_component' => 'Legal'
            ],
            'user_agreements' => [
                'key' => 'user-agreements',
                'title' => __('Legal', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => 'buddyc-dashboard',
                'callable' => 'buddyc_admin_table',
                'callback_args' => 'user_agreements',
                'buddyc_menu_order' => 8,
                'required_component' => 'Legal'
            ],
            'upgrade' => [
                'key' => 'upgrade',
                'title' => __('Upgrade BuddyClients', 'buddyclients-lite'),
                'settings' => false,
                'parent_slug' => null,
                'callable' => ['buddyc_upgrade_link', [true]]
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
        $pages = apply_filters('buddyc_admin_pages', $pages);

        return $pages;
    }
}
