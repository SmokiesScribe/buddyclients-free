<?php
use BuddyClients\Components\Booking\BookingIntent   as BookingIntent;
use BuddyClients\Includes\Project                   as Project;

use BuddyEvents\Includes\{
    Registration\RegistrationIntent as RegistrationIntent,
    SponsorIntent as SponsorIntent
};

/**
 * Checkout page new account form submission.
 * 
 * @since 0.1.0
 */
    /**
     * Handle create account form submission.
     * 
     * Called by AJAX script.
     * 
     * @since 0.1.0
     */
    function bc_checkout_create_account() {
        // Ensure the request is from an authenticated user if required
        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
            wp_send_json_error(__('Invalid request', 'buddyclients'));
            wp_die();
        }
        
        // Make sure all data is present
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])
        && ( isset( $_POST['booking_intent_id'] ) || isset( $_POST['registration_intent_id'] ) || isset( $_POST['sponsor_intent_id'] ) ) ) {
            $user_name = $_POST['username'];
            $user_email = $_POST['email'];
            $user_password = $_POST['password'];
            $booking_intent_id = $_POST['booking_intent_id'] ?? null;
            $registration_intent_id = $_POST['registration_intent_id'] ?? null;
            $sponsor_intent_id = $_POST['sponsor_intent_id'] ?? null;
            
            // Validate password strength
            $password_error = bc_validate_password_strength( $user_password );
            if ( $password_error ) {
                wp_send_json_error($password_error);
                wp_die();
            }
            
            // Build login name
            $user_login = bc_generate_login_name($user_name);
        
            // Create account
            $new_user_id = wp_create_user($user_login, $user_password, $user_email);
        
            // Check if user creation was successful
            if (!is_wp_error($new_user_id)) {
                
                // Update first name with wp
                wp_update_user([
                    'ID' => $new_user_id,
                    'first_name' => $user_name,
                ]);
                
                // Update Booking info
                if ( $booking_intent_id && $booking_intent_id !== '' ) {
                    // Update client type
                    $client_type = bc_get_setting( 'general', 'default_client_type' );
                    bp_set_member_type( $new_user_id, $client_type, true ); // append to existing
                    
                    // Update BookingIntent
                    BookingIntent::update_client_id( $booking_intent_id, $new_user_id );
                    BookingIntent::update_client_email( $booking_intent_id, $user_email );
                }
                
                // Attendee type
                if ( $registration_intent_id && $registration_intent_id !== '' && class_exists( RegistrationIntent::class ) ) {
                    $attendee_type = bc_get_setting( 'event', 'attendee_type' );
                    bp_set_member_type( $new_user_id, $attendee_type, true ); // append to existing
                    
                    // Update RegistrationIntent
                    RegistrationIntent::update_attendee_id( $registration_intent_id, $new_user_id );
                    RegistrationIntent::update_attendee_email( $registration_intent_id, $user_email );
                }
                
                // Update SponsorIntent
                if ( $sponsor_intent_id && $sponsor_intent_id !== ''  && class_exists( SponsorIntent::class ) ) {
                    // Update SponsorIntent
                    SponsorIntent::update_user_id( $sponsor_intent_id, $new_user_id );
                    SponsorIntent::update_user_email( $sponsor_intent_id, $user_email );
                }
                
                /**
                 * Fires after creation of new user during checkout.
                 * 
                 * @since 0.1.0
                 * 
                 * @param int $user_id The ID of the newly created user.
                 */
                do_action( 'bc_created_user', $new_user_id, $booking_intent_id );
                
                // Return new user ID on success
                wp_send_json_success(['user_id' => $new_user_id]);
                
            } else {
                // Return error message on failure
                $error_message = $new_user_id->get_error_message();
                wp_send_json_error(__('Error creating user: ', 'buddyclients') . $error_message);
            }
            
            // Terminate script execution
            wp_die();
        }
    }
    // Hook to ajax
    add_action('wp_ajax_bc_checkout_create_account', 'bc_checkout_create_account');
    add_action('wp_ajax_nopriv_bc_checkout_create_account', 'bc_checkout_create_account');
    
    /**
     * Validates password strength.
     *
     * Checks if the password meets strength requirements.
     *
     * @since 0.1.0
     *
     * @param string $password The password to validate.
     * @return string|null Returns error message if validation fails, otherwise null.
     */
    function bc_validate_password_strength( $password ) {
        // Define basic strength criteria
        $min_length = 8; // Minimum length
        $has_uppercase = preg_match('/[A-Z]/', $password); // At least one uppercase letter
        $has_lowercase = preg_match('/[a-z]/', $password); // At least one lowercase letter
        $has_number = preg_match('/\d/', $password); // At least one digit
        $has_special = preg_match('/[\W]/', $password); // At least one special character
    
        // Check password against criteria
        if (strlen($password) < $min_length) {
            return sprintf(__('Password must be at least %d characters long.', 'buddyclients'), $min_length);
        }
        if (!$has_uppercase) {
            return __('Password must include at least one uppercase letter.', 'buddyclients');
        }
        if (!$has_lowercase) {
            return __('Password must include at least one lowercase letter.', 'buddyclients');
        }
        if (!$has_number) {
            return __('Password must include at least one number.', 'buddyclients');
        }
        if (!$has_special) {
            return __('Password must include at least one special character.', 'buddyclients');
        }
    
        // Return null if all criteria are met
        return null;
    }
    
    /**
     * Generates login name.
     * 
     * Checks against existing users to ensure unique handle.
     * 
     * @since 0.1.0
     */
    function bc_generate_login_name($first_name) {
        // Convert first name to lowercase and remove special characters
        $login_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $first_name));
        
        // Check if the login name already exists
        $suffix = 1;
        $original_login_name = $login_name; // Store the original login name without suffix
        while (username_exists($login_name)) {
            // If the login name exists, add a random number suffix and try again
            $login_name = $original_login_name . rand(1, 999); // Add a random number
            $suffix++;
        }
        return $login_name;
    }