<?php
namespace BuddyClients\Includes;

use BuddyClients\Components\{
    Booking\BookingForm as BookingForm,
    Checkout\Checkout as Checkout,
    Testimonial\TestimonialForm as TestimonialForm,
    Contact\ContactForm as ContactForm,
    Checkout\Confirmation as Confirmation
};

/**
 * Registers plugin shortcodes.
 * 
 * Defines and registers all shortcodes for the plugin.
 *
 * @since 0.1.0
 */
class Shortcodes {
    
    /**
     * Initializes the shortcode registration.
     *
     * @since 0.1.0
     */
    public static function run() {
        
        // Not admin area or login
        if ( ! is_admin() && $GLOBALS['pagenow'] === 'index.php' ) {
            // Register shortcodes
            self::register();
        }
    }
    
    /**
     * Defines shortcodes.
     * 
     * @since 0.1.0
     */
    private static function shortcodes() {
        $shortcodes = [];
    
        // Check if the class exists before instantiating
        if (class_exists(BookingForm::class)) {
            $shortcodes['buddyc_booking_form'] = [new BookingForm, 'build_form'];
        }
    
        if (class_exists(Checkout::class)) {
            $shortcodes['buddyc_checkout'] = [new Checkout, 'build'];
        }
    
        if (class_exists(TestimonialForm::class)) {
            $shortcodes['buddyc_testimonial_form'] = [new TestimonialForm, 'build'];
        }
        
        if (class_exists(ContactForm::class)) {
            $shortcodes['buddyc_contact_form'] = [new ContactForm, 'build'];
        }
        
        if (class_exists(Confirmation::class)) {
            $shortcodes['buddyc_confirmation'] = [new Confirmation, 'build'];
        }
        
        /**
         * Filters the shortcodes.
         *
         * @since 0.3.4
         *
         * @param array  $shortcodes    An associative array of shortcodes and callbacks.
         */
         $shortcodes = apply_filters( 'buddyc_shortcodes', $shortcodes );
    
        return $shortcodes;
    }

    
    /**
     * Registers shortcodes.
     *
     * @since 0.1.0
     */
    public static function register() {
        foreach ( self::shortcodes() as $shortcode => $callable ) {
            if ( is_callable( $callable ) ) {
                add_shortcode( $shortcode, $callable );
            }
        }
    }

    
}