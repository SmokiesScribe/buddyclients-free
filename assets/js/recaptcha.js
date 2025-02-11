/**
 * Triggers the reCAPTCHA validation on form submission.
 * 
 * @since 1.0.25
 */
function buddycRecaptchaSubmit( event, manual = false ) {

    // Check which form was submitted
    var form = event.target.closest( 'form' );

    // Exit reCAPTCHA if it's the booking form
    if ( form.id === 'buddyc-booking-form' && event.type === 'click' && ! manual ) {
        return;
    }

    // Prevent the default form submission
    event.preventDefault();

    // Trigger native form validation (HTML5 built-in)
    if (form.checkValidity()) {
        // If the form is valid, proceed with reCAPTCHA validation
        buddycValidateRecaptcha(event).then(function(valid) {
            if (valid) {
                // Submit the form if reCAPTCHA is valid
                form.submit();
            }
        });
    } else {
        // If form validation fails (e.g., required fields), show error
        form.reportValidity();  // This will show the browser's built-in validation messages
    }
}

/**
 * Validates the reCAPTCHA submission.
 * 
 * @since 1.0.25
 */
function buddycValidateRecaptcha( event ) {
    // Initialize a promise that will resolve if reCAPTCHA validation passes
    return new Promise(function(resolve, reject) {
        // Ensure reCAPTCHA is ready before executing
        grecaptcha.ready(function() {
            grecaptcha.execute(recaptchaData.siteKey, {action: 'submit'}).then(function(token) {
                // Add the token to a hidden input field in the form
                var form = event.target.closest('form'); // Get the form element
                var recaptchaResponse = form.querySelector('input[name="recaptcha_response"]');
                
                if (recaptchaResponse) {
                    recaptchaResponse.value = token; // Set the token to the hidden field
                }

                // Resolve the promise with 'true' to indicate the reCAPTCHA passed
                resolve(true);
            }).catch(function() {
                // If something goes wrong with the reCAPTCHA execution, reject the promise
                resolve(false);
            });
        });
    });
}

// Add the onSubmit function to the submit button's click event
document.querySelectorAll('.g-recaptcha').forEach(function( button ) {
    button.addEventListener('click', buddycRecaptchaSubmit);
});
