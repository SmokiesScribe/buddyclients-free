/**
 * Checkout email.
 * 
 * @since 0.1.0
 */
    /**
     * Handles email entered on checkout page.
     * 
     * @since 0.1.0
     * 
     * @todo Update!
     */
    function checkoutEmailEntered() {
        // Get the form element
        const createAccountForm = document.getElementById('bc-create-account');
    
        // Check if the form element exists
        if (createAccountForm) {
            const emailInput = document.querySelector('input[name="create-account-email"]');
            const sessionToken = getSessionToken();
            
            // Add an event listener to the email input field
            emailInput.addEventListener('change', function(event) {
                const enteredEmail = event.target.value;
                if (isValidEmailAddress(enteredEmail)) {
                    // AJAX request
                    jQuery.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'bc_new_user_email_entered',
                            email: enteredEmail,
                            token: sessionToken,
                        },
                        success: function(response) {
                          //  location.reload();
                        }
                    });
                }
            });
        }
    }
    // Call the function on page load
    window.addEventListener('load', checkoutEmailEntered);
    
    /**
     * Validates email address.
     * 
     * @since 0.1.0
     */
     function isValidEmailAddress(email) {
        // Regular expression for email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    /**
     * Retrieves session token.
     * 
     * @since 0.1.0
     */
     function getSessionToken() {
        const cookieName = 'session_token' + "=";
        const cookieArray = document.cookie.split(';');
        for (let i = 0; i < cookieArray.length; i++) {
            let cookie = cookieArray[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(cookieName) === 0) {
                return cookie.substring(cookieName.length, cookie.length);
            }
        }
        return ""; // Return empty string if cookie is not found
    }