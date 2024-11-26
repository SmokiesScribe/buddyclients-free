<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Change registration button text.
 * 
 * @since 0.1.0
 */
function buddyc_registration_button_text() {
    
    // Check settings
    $enable_registration = buddyc_get_setting( 'general', 'enable_registration' );
    $register_button_text = buddyc_get_setting( 'general', 'register_button_text' );
    
    /**
     * Filters the register button text.
     * 
     * @since 0.4.0
     * 
     * @param   string  $register_button_text   The text for the register button.
     */
    $register_button_text = apply_filters( 'buddyc_register_button_text', $register_button_text );
    
    // User registration enabled
    if ( $enable_registration == 'enable' ) {
        // Allow registration
        update_option('users_can_register', true);
        return;
        
    // User registration disabled
    } else {
            
        // Allow registration
        update_option( 'users_can_register', true );
        
        $script =
            'document.addEventListener("DOMContentLoaded", function() {
                var signUpButton = document.querySelector("a.button.small.signup");
                if (signUpButton) {
                    signUpButton.textContent = "' . esc_js( $register_button_text ) . '";
                }

                // Login page text
                var createAccountLink = document.querySelector(".login-heading a");
                if (createAccountLink) {
                    createAccountLink.textContent = "' . esc_js( $register_button_text ) . '";
                }
            });';
        
        // Output inline script
        buddyc_inline_script( $script );
    }
}
add_action('init', 'buddyc_registration_button_text'); // main button

/**
 * Change registration button link.
 * 
 * @since 0.1.0
 * 
 * @param   string  $url    The url to modify.
 */
function buddyc_change_register_url( $url ) {
    
    // Get setting
    $enable_registration = buddyc_get_setting( 'general', 'enable_registration' );

    // User registration enabled
    if ($enable_registration != 'enable') {
        
        // Get booking page
        $booking_page = buddyc_get_setting( 'pages', 'booking_page' );
        
        // No booking page
        if ( $booking_page ) {
            
            // Get booking page link
            $booking_page_link = get_permalink( $booking_page );
            
            // Change link
            $url = $booking_page_link;
        }
        
        /**
         * Filters the register button url.
         * 
         * @since 0.4.0
         * 
         * @param   string  $url   The url for the register button.
         */
        $url = apply_filters( 'buddyc_register_button_url', $url );
    }
    
    return $url;
}
add_filter('register_url', 'buddyc_change_register_url', 20 );