/**
 * Handles email entered on new account form.
 * 
 * @since 0.1.0
 */
document.addEventListener("DOMContentLoaded", function() {
    const createAccountEmailInput = document.querySelector('input[name="create-account-email"]');
    const bookingIntentIdInput = document.querySelector('input[name="booking-intent-id"]');
    const registrationIntentIdInput = document.querySelector('input[name="registration-intent-id"]');
    
    // Exit if no email input
    if ( ! createAccountEmailInput ) {
        return;
    }
    
    // Function to handle email input change
    function handleEmailInputChange() {
        const enteredEmail = createAccountEmailInput.value;
        
        if ( isValidEmailAddress( enteredEmail ) ) {

            // AJAX request to update booking intent email
            jQuery.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'bc_update_booking_intent_email',
                    email: enteredEmail,
                    booking_intent_id: bookingIntentIdInput.value,
                    registration_intent_id: registrationIntentIdInput.value,
                },
                success: function(response) {
                    // Check if the response was successful
                    if (response.success) {
                        console.log(response);
                    }
                }
            });
        }
    }

    // Attach event listener to document for email input changes
    document.addEventListener('change', function(event) {
        // Check if the changed element is the email input in the create account form
        if (event.target && event.target.matches('input[name="create-account-email"]')) {
            // Call the function to handle email input change
            handleEmailInputChange();
        }
    });
    
    // Function to validate email address
    function isValidEmailAddress(email) {
        // Regular expression for email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
