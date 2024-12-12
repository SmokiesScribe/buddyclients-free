/**
 * Validates forms to allow Stripe payment submission.
 * 
 * @since 0.1.0
 */
function buddycHandleFormValidation() {
    let submitButton = null;
    
    // Get Stripe submit button
    const stripeSubmitButton = document.getElementById('stripe-submit');
    
    // Get free checkout button
    const freeCheckoutButton = document.getElementById('free-checkout-submit');

    // Get skip checkout button
    const skipCheckoutButton = document.getElementById('skip-payment-checkout-submit');
    
    // Define submit button
    if ( stripeSubmitButton ) {
        submitButton = stripeSubmitButton;
    } else if ( freeCheckoutButton ) {
        submitButton = freeCheckoutButton;
    } else if ( skipCheckoutButton ) {
        submitButton = skipCheckoutButton;
    }
    
    // Make sure Stripe submit exists
    if ( ! submitButton ) {
        return;
    }
    
    // Get the new account form
    const createAccountForm = document.getElementById('buddyc-create-account-form');
    
    // Site terms checkbox
    const siteTermsCheckbox = document.getElementById('site-agree-checkbox');
    
    // Get the service terms checkbox
    const serviceTermsCheckbox = document.getElementById('checkout-agree-terms-checkbox');

    // Generate password link
    const generatePasswordLinks = document.querySelectorAll('.buddyc-generate-password-link');

    // Function to validate the form and enable/disable the button accordingly
    function buddycValidateNewAccountForm() {
        // Initialize variable inside the function
        let enableButton = true;
        
        if (createAccountForm) {
            const nameInput = document.querySelector('input[name="create-account-name"]');
            const emailInput = document.querySelector('input[name="create-account-email"]');
            const passwordInput = document.querySelector('input[name="create-account-password"]');

            // Check if all required fields are filled out and email is valid
            // use buddycIsValidEmailAddress in checkout-email.js
            if (nameInput.value && passwordInput.value && emailInput.value && buddycIsValidEmailAddress(emailInput.value)) {
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

        const buttonTextElement = document.getElementById('button-text');
        const spinnerElement = document.getElementById('spinner');
        
        if (enableButton) {
            // Enable the button if all required fields are filled out and email is valid
            submitButton.disabled = false;
            
            if (buttonTextElement) {
              buttonTextElement.classList.remove('hidden');
            }
            
            if (spinnerElement) {
              spinnerElement.classList.add('hidden');
            }
            
        } else {
            // Disable the button if any required field is not filled out or email is invalid
            submitButton.disabled = true;

            if (buttonTextElement) {
                buttonTextElement.classList.add('hidden');
              }
              
              if (spinnerElement) {
                spinnerElement.classList.add('hidden');
              }
        }
    }
    
    // Check if the form element exists
    if (createAccountForm) {
        // Add event listener for input changes in required fields
        createAccountForm.addEventListener('input', buddycValidateNewAccountForm);

        // START HERE - FIX THIS

        // Check if generate password links exist
        generatePasswordLinks.forEach((linkElement) => {
            linkElement.addEventListener('click', buddycValidateNewAccountForm);
        });

        // Validate the form initially on page load
        buddycValidateNewAccountForm();
    }
    
    // Check if the terms checkbox exists
    if (siteTermsCheckbox) {
        // Add event listener for terms checkbox
        siteTermsCheckbox.addEventListener('change', buddycValidateNewAccountForm);
        // Validate the form initially on page load
        buddycValidateNewAccountForm();
    }
    
    // Check if the terms checkbox exists
    if (serviceTermsCheckbox) {
        // Add event listener for terms checkbox
        serviceTermsCheckbox.addEventListener('change', buddycValidateNewAccountForm);
        // Validate the form initially on page load
        buddycValidateNewAccountForm();
    }
}

// Call the function on page load
window.addEventListener('load', buddycHandleFormValidation);