<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\{
    Booking\BookingForm,
    Checkout\Checkout,
    Testimonial\Testimonial,
    Testimonial\TestimonialForm,
    Contact\ContactForm,
    Checkout\Confirmation
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
     * Defines shortcodes data. 
     * 
     * @since 1.0.25
     */
    public static function shortcodes_data() {
        $data = [
            'booking' => [
                'shortcode' => 'buddyc_booking_form',
                'class'     => BookingForm::class,
                'method'    => 'build_form'
            ],
            'checkout' => [
                'shortcode' => 'buddyc_checkout',
                'class'     => Checkout::class,
                'method'    => 'build'
            ],
            'submit_testimonial' => [
                'shortcode' => 'buddyc_testimonial_form',
                'class'     => TestimonialForm::class,
                'method'    => 'build'
            ],
            'contact' => [
                'shortcode' => 'buddyc_contact_form',
                'class'     => ContactForm::class,
                'method'    => 'build'
            ],
            'confirmation' => [
                'shortcode' => 'buddyc_confirmation',
                'class'     => Confirmation::class,
                'method'    => 'build'
            ],
            'testimonials' => [
                'shortcode' => 'buddyc_testimonials',
                'class'     => Testimonial::class,
                'function'  => 'buddyc_testimonials_shortcode'
            ],
        ];

        /**
         * Filters the shortcodes.
         *
         * @since 0.3.4
         *
         * @param array  $shortcodes    An associative array of shortcodes and callbacks.
         */
        $data = apply_filters( 'buddyc_shortcodes', $data );

        return $data;
    }
    
    /**
     * Registers all shortcodes.
     * 
     * @since 0.1.0
     */
    public static function register() {
        foreach ( self::shortcodes_data() as $key => $data ) {
            if ( ! isset( $data['class'] ) || class_exists( $data['class'] ) ) {
                $callable = self::build_callable( $data );
                if ( is_callable( $callable ) ) {
                    add_shortcode( $data['shortcode'], $callable );
                }
            }
        }
    }

    /**
     * Builds the callable from the shortcode data.
     * 
     * @since 1.0.25
     *
     * @param array $data The data that includes the class, method, or function details.
     * @return callable|null The callable, or null if no valid callable can be constructed.
     */
    private static function build_callable( $data ) {
        if ( isset( $data['class'], $data['method'] ) ) {
            // Ensure the method is callable within the class.
            if ( method_exists( $data['class'], $data['method'] ) ) {
                return [new $data['class'], $data['method']];
            }
            return null; // Return null if the method doesn't exist in the class.
        } elseif ( isset( $data['function'] ) && is_callable( $data['function'] ) ) {
            return $data['function'];
        }
        
        return null; // Return null if no valid callable can be constructed.
    }
}