<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin page manager.
 *
 * @since 0.1.0
 */
class PageManager {
	
    /**
     * Constructor.
     *
     * @since 0.1.0
     */
    public function __construct() {
        $this->check_required_pages();
    }
	
	/**
	 * Core page data.
	 * 
	 * @since 0.1.0
	 */
	public static function pages() {
       $pages = [
           'core' => [
                'booking_page' => [
                    'label' => 'Booking Page',
                    'description' => 'The page where the booking form is located. Include shortcode: [buddyc_booking_form]',
                    'post_title' => 'Book Services',
                    'post_content' => '[buddyc_booking_form]',
                    'required_component' => 'Booking'
                ],
                'checkout_page' => [
                    'label' => 'Checkout Page',
                    'description' => 'Where users will pay for services. Include shortcode: [buddyc_checkout]',
                    'post_title' => 'Checkout',
                    'post_content' => '[buddyc_checkout]',
                    'required_component' => 'Booking'
                ],
                'confirmation_page' => [
                    'label' => 'Confirmation Page',
                    'description' => 'Where users are redirected after paying. Include shortcode: [buddyc_confirmation]',
                    'post_title' => 'Confirmation',
                    'post_content' => '[buddyc_confirmation]',
                    'required_component' => 'Booking'
                ],
                'contact_form' => [
                    'label' => 'Contact Form',
                    'description' => 'Public contact form. Include shortcode: [buddyc_contact_form]',
                    'post_title' => 'Contact',
                    'post_content' => '[buddyc_contact_form]',
                    'required_component' => 'Help',
                    'never_require' => true
                ],
                'testimonials_form' => [
                    'label' => 'Testimonials Form',
                    'description' => 'Where users can submit testimonials. Include shortcode: [buddyc_testimonial_form]',
                    'post_title' => 'Submit Testimonial',
                    'post_content' => '[buddyc_testimonial_form]',
                    'required_component' => 'Testimonial'
                ],
            ],
            'legal' => [
                'privacy_policy' => [
                    'label' => 'Privacy Policy',
                    'description' => 'The privacy policy for your website.',
                    'post_title' => 'Privacy Policy',
                    'post_content' => 'Add your privacy policy here.'
                ],
                'terms_of_service' => [
                    'label' => 'Terms of Service',
                    'description' => 'The terms of using your website.',
                    'post_title' => 'Terms of Service',
                    'post_content' => 'Add your terms of service here.'
                ],                
            ],
        ];
        
        /**
         * Filters the plugin page data.
         *
         * @since 0.3.4
         *
         * @param array  $pages    An array of plugin page data.
         */
         $pages = apply_filters( 'buddyc_plugin_pages', $pages );
         
        return $pages;
	}
	
	/**
	 * Returns required pages.
	 * 
	 * @since 0.1.0
	 */
	public static function required_pages() {
	    
	    // Initialize
	    $required_pages = [];
	    
	    // Get all page data
	    $all_pages = self::pages();
	    
	    // Loop through core pages
	    foreach ( $all_pages['core'] as $key => $page_data ) {
	        // Check whether the component is enabled
	        if ( ! isset( $page_data['required_component'] ) || buddyc_component_enabled( $page_data['required_component'] ) ) {
	            $required_pages[$key] = $page_data;
	        }
	    }
	    return $required_pages;
	}
	
	/**
	 * Checks that required pages exist.
	 * 
	 * @since 0.1.0
	 */
	private function check_required_pages() {
	    
	    // Get required pages
	    $required_pages = self::required_pages();

        // Initialize array
        $missing_pages = [];
	    
	    // Loop through required pages
	    foreach ( $required_pages as $page_key => $page_data ) {
            // Check if the page is missing
    	    if ( ! PluginPage::get_page( $page_key ) ) {
                // Add to array
                $missing_pages[] = $page_data['label'];
    	    }
	    }

        // Check if any pages are missing
        if ( ! empty( $missing_pages ) ) {
            // Implode page labels to string
            $missing_pages_string = implode( ', ', $missing_pages );

            // Define args
            $notice_args = [
                'repair_link'       => '/admin.php?page=buddyc-pages-settings',
                'message'           => 'The following BuddyClients pages are missing: ' . $missing_pages_string . '.',
                'color'             => 'orange'
            ];

            // Output notice            
            buddyc_admin_notice( $notice_args );
        }
	}
	
	/**
	 * Creates required pages.
	 * 
	 * @since 0.1.0
	 */
	public static function create_required_pages() {
        $pages = self::required_pages();
        
        foreach ( $pages as $page_key => $data ) {
            $plugin_page = new PluginPage( $page_key );
            $plugin_page->create_page( $data );
        }
	}
	   
}