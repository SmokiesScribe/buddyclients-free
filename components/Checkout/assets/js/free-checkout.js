/**
 * Handles a free checkout.
 * 
 * @since 0.4.3
 */
document.addEventListener("DOMContentLoaded", function () {
    let submitButton = null;

    // Get the form and button elements
    const freeCheckoutForm = document.getElementById('buddyc-free-checkout-form');
    const freeSubmitButton = document.getElementById('free-checkout-submit');

    const skipCheckoutForm = document.getElementById('buddyc-skip-payment-checkout-form');
    const skipSubmitButton = document.getElementById('skip-payment-checkout-submit');
    
    // Ensure the form exists before proceeding
    if (!freeCheckoutForm && !skipCheckoutForm) {
        return;
    }

    // Define form
    const form = freeCheckoutForm ? freeCheckoutForm : skipCheckoutForm;

    // Define submit button by which form exists
    if ( freeCheckoutForm ) {
        submitButton = freeSubmitButton;
    } else if ( skipCheckoutForm ) {
        submitButton = skipSubmitButton;
    }
    
    // Get booking intent id from form
    const bookingIntentId = form.querySelector('#booking_intent_id').value;

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
                form.submit();
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