/**
 * Validates forms to allow Stripe payment submission.
 * 
 * @since 0.1.0
 */
function handleFormValidation() {
    
    // Get Stripe submit button
    const stripeSubmitButton = document.getElementById('stripe-submit');
    
    // Get free checkout button
    const freeCheckoutButton = document.getElementById('free-checkout-submit');
    
    // Define submit button
    const submitButton = stripeSubmitButton ? stripeSubmitButton : freeCheckoutButton;
    
    // Make sure Stripe submit exists
    if ( ! submitButton ) {
        return;
    }
    
    // Get the new account form
    const createAccountForm = document.getElementById('bc-create-account-form');
    
    // Site terms checkbox
    const siteTermsCheckbox = document.getElementById('site-agree-checkbox');
    
    // Get the service terms checkbox
    const serviceTermsCheckbox = document.getElementById('checkout-agree-terms-checkbox');

    // Function to validate the form and enable/disable the button accordingly
    function validateNewAccountForm() {
        // Initialize variable inside the function
        let enableButton = true;
        
        if (createAccountForm) {
            const nameInput = document.querySelector('input[name="create-account-name"]');
            const emailInput = document.querySelector('input[name="create-account-email"]');
            const passwordInput = document.querySelector('input[name="create-account-password"]');

            // Check if all required fields are filled out and email is valid
            // use isValidEmailAddress in checkout-email.js
            if (nameInput.value && passwordInput.value && emailInput.value && isValidEmailAddress(emailInput.value)) {
                // enabled
            } else {
                enableButton = false;
            }
        }
        
        if (siteTermsCheckbox) {
            // Check if all required fields are filled out and email is valid
            if (siteTermsCheckbox.checked) {
                // enabled
            } else {
                enableButton = false;
            }
        }
        
        if (serviceTermsCheckbox) {
            // Check if all required fields are filled out and email is valid
            if (serviceTermsCheckbox.checked) {
                // enabled
            } else {
                enableButton = false;
            }
        }
        
        if (enableButton) {
            // Enable the button if all required fields are filled out and email is valid
            submitButton.disabled = false;
            document.getElementById('button-text').classList.remove('hidden');
            document.getElementById('spinner').classList.add('hidden');
        } else {
            // Disable the button if any required field is not filled out or email is invalid
            submitButton.disabled = true;
            document.getElementById('button-text').classList.add('hidden');
            document.getElementById('spinner').classList.add('hidden');
        }
    }
    
    // Check if the form element exists
    if (createAccountForm) {
        // Add event listener for input changes in required fields
        createAccountForm.addEventListener('input', validateNewAccountForm);
        // Validate the form initially on page load
        validateNewAccountForm();
    }
    
    // Check if the terms checkbox exists
    if (siteTermsCheckbox) {
        // Add event listener for terms checkbox
        siteTermsCheckbox.addEventListener('change', validateNewAccountForm);
        // Validate the form initially on page load
        validateNewAccountForm();
    }
    
    // Check if the terms checkbox exists
    if (serviceTermsCheckbox) {
        // Add event listener for terms checkbox
        serviceTermsCheckbox.addEventListener('change', validateNewAccountForm);
        // Validate the form initially on page load
        validateNewAccountForm();
    }
}

// Call the function on page load
window.addEventListener('load', handleFormValidation);