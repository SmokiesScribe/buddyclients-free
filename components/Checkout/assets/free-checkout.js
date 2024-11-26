import { buddycHandleCreateAccount } from './create-account.js';

/**
 * Handles a free checkout.
 * 
 * @since 0.4.3
 */
document.addEventListener("DOMContentLoaded", function () {

    // Get the form and button elements
    const freeCheckoutForm = document.getElementById('buddyc-free-checkout-form');
    const submitButton = document.getElementById('free-checkout-submit');
    
    // Ensure the form exists before proceeding
    if (!freeCheckoutForm) {
        return;
    }
    
    // Get booking intent id from form
    const bookingIntentId = freeCheckoutForm.querySelector('#booking_intent_id').value;

    // Add click event listener to the submit button
    submitButton.addEventListener("click", async (e) => {
        // Prevent the default form submission behavior
        e.preventDefault();
        
        // Disable the button to prevent multiple clicks
        submitButton.disabled = true;
        
        try {
            // Assuming ajaxurl is defined elsewhere in your script or passed as a parameter
            const isSuccess = await buddycHandleCreateAccount(e, ajaxurl, submitButton);
            
            // Proceed with the next steps only if account creation is successful
            if (isSuccess) {
                
                // Allow default submission of form
                freeCheckoutForm.submit();
            } else {
                console.log("Account creation failed. Submission halted.");
            }
        } catch (error) {
            console.error("An error occurred during free checkout:", error);
        } finally {
            // Re-enable the button after processing
            submitButton.disabled = false;
        }
    });
});