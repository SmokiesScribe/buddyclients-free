<?php
/**
 * Change registration button text.
 * 
 * @since 0.1.0
 */
function bc_registration_button_text() {
    
    // Check settings
    $enable_registration = bc_get_setting( 'general', 'enable_registration' );
    $register_button_text = bc_get_setting( 'general', 'register_button_text' );
    
    /**
     * Filters the register button text.
     * 
     * @since 0.4.0
     * 
     * @param   string  $register_button_text   The text for the register button.
     */
    $register_button_text = apply_filters( 'bc_register_button_text', $register_button_text );
    
    // User registration enabled
    if ( $enable_registration == 'enable' ) {
        // Allow registration
        update_option('users_can_register', true);
        return;
        
    // User registration disabled
    } else {
            
        // Allow registration
        update_option( 'users_can_register', true );
        
        // Change register button text
        $allowed_html = array(
            'script' => array(), // Allow the <script> tag with no attributes
        );
        
        $script_content = '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var signUpButton = document.querySelector("a.button.small.signup");
                if (signUpButton) {
                    signUpButton.textContent = "' . esc_js( $register_button_text ) . '";
                }

                // Login page text
                var createAccountLink = document.querySelector(".login-heading a");
                if (createAccountLink) {
                    createAccountLink.textContent = "' . esc_js( $register_button_text ) . '";
                }
            });
        </script>';
        
        echo wp_kses( $script_content, $allowed_html );        
    }
}
add_action('wp_footer', 'bc_registration_button_text'); // main button
add_action('login_enqueue_scripts', 'bc_registration_button_text'); // login page

/**
 * Change registration button link.
 * 
 * @since 0.1.0
 * 
 * @param   string  $url    The url to modify.
 */
function bc_change_register_url( $url ) {
    
    // Get setting
    $enable_registration = bc_get_setting( 'general', 'enable_registration' );

    // User registration enabled
    if ($enable_registration != 'enable') {
        
        // Get booking page
        $booking_page = bc_get_setting( 'pages', 'booking_page' );
        
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
        $url = apply_filters( 'bc_register_button_url', $url );
    }
    
    return $url;
}
add_filter('register_url', 'bc_change_register_url', 20 );