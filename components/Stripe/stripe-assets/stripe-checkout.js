import { handleCreateAccount } from '../../Checkout/assets/create-account.js';

/**
 * Handles Stripe checkout.
 * 
 * @since 0.1.0
 * @updated 0.4.3
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM fully loaded and parsed.");

    const scriptElement = document.getElementById("bc-stripe-stripe-checkout-params");
    const submitButton = document.getElementById('stripe-submit');
    
    if (scriptElement && submitButton) {
        console.log("Script element and submit button found.");
        
        const params = scriptElement.textContent || '{}';
        const checkoutParams = JSON.parse(params);
        
      // Access the params passed from PHP
      if (checkoutParams) {
        const pubKey = checkoutParams.pubKey;
        const createIntentUrl = checkoutParams.createIntentUrl;
        const confirmationUrl = checkoutParams.confirmationUrl;
        const bookingIntent = checkoutParams.bookingIntent;
        const logInUrl = checkoutParams.logInUrl;
        
        console.log(logInUrl);
        
        // Get success message div
        const successDiv = document.getElementById("bc-create-account-success");
        
        /**
        if ( successDiv ) {
            successDiv.innerHTML = 'Account created! Please <a href="' + logInUrl + '">log in here</a>.';
        }
        */

    
        // Use publishable API key
        const stripe = Stripe(pubKey);
    
        let elements;
    
        initialize();
        checkStatus();
        
        submitButton.addEventListener("click", async (e) => {
            console.log("Submit button clicked.");
            
            // Prevent the default form submission behavior
            e.preventDefault();
            
            // Disable the button to prevent multiple clicks
            submitButton.disabled = true;
            
            // @TODO Handle the case where account creation is successful
            // but Stripe payment is not
            
            try {
                console.log("Attempting to create account...");
                const isSuccess = await handleCreateAccount(e, ajaxurl, submitButton);
                console.log('Create account result: ' + isSuccess);
                
                if ( isSuccess ) {
                    console.log("Account creation successful. Proceeding with payment submission.");
                    
                    // Handle payment submission
                    const paymentSucceeded = await handleSubmit();
                    
                    // Account created but payment failed
                    if ( ! paymentSucceeded && successDiv ) {
                        // Output message
                        successDiv.innerHTML = '<p>Your account has been created, but payment was not successful. Please <a href="' + logInUrl + '">log in</a> to continue.</p>';
                        
                        // Hide stripe form
                        jQuery("#payment-form").hide();
                    }
                    
                } else {
                    console.log("Account creation failed. Submission halted.");
                }
            } catch (error) {
                console.error("An error occurred during checkout:", error);
            } finally {
                // Re-enable the button after processing
                submitButton.disabled = false;
                console.log("Submit button re-enabled.");
            }
        });
    
        // Initialize Stripe Elements
        async function initialize() {
          const { clientSecret } = await fetch(createIntentUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ bookingIntent }),
          }).then((r) => r.json());
    
          elements = stripe.elements({ clientSecret });
    
          const paymentElementOptions = {
            layout: "tabs",
          };
    
          const paymentElement = elements.create("payment", paymentElementOptions);
          paymentElement.mount("#payment-element");
        }
    
        // Handles the Stripe payment submission
        async function handleSubmit() {
          setLoading(true);
          let success = false;
    
          const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
              return_url: confirmationUrl,
            },
          });
    
          // Check if Stripe payment confirmation is successful
          if ( ! error ) {
            showMessage("Payment succeeded!");
            success = true;
          } else {
            // Handle error
            if (error.type === "card_error" || error.type === "validation_error") {
              showMessage(error.message);
            } else {
              showMessage("An unexpected error occurred.");
            }
          }
          
          console.log('Success: ' + success);
    
          setLoading(false);
          return success;
        }
    
        // Fetches the payment intent status after payment submission
        async function checkStatus() {
          const clientSecret = new URLSearchParams(window.location.search).get(
            "payment_intent_client_secret"
          );
    
          if (!clientSecret) {
            return;
          }
    
          const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
    
          switch (paymentIntent.status) {
            case "succeeded":
              showMessage("Payment succeeded!");
              break;
            case "processing":
              showMessage("Your payment is processing.");
              break;
            case "requires_payment_method":
              showMessage("Your payment was not successful, please try again.");
              break;
            default:
              showMessage("Something went wrong.");
              break;
          }
        }
    
        // ------- UI helpers -------
    
        function showMessage(messageText) {
          const messageContainer = document.querySelector("#payment-message");
    
          messageContainer.classList.remove("hidden");
          messageContainer.textContent = messageText;
    
          setTimeout(function () {
            messageContainer.classList.add("hidden");
            messageContainer.textContent = "";
          }, 4000);
        }
    
        // Show a spinner on payment submission
        function setLoading(isLoading) {
          if (isLoading) {
            // Disable the button and show a spinner
            document.querySelector("#stripe-submit").disabled = true;
            document.querySelector("#spinner").classList.remove("hidden");
            document.querySelector("#button-text").classList.add("hidden");
          } else {
            document.querySelector("#stripe-submit").disabled = false;
            document.querySelector("#spinner").classList.add("hidden");
            document.querySelector("#button-text").classList.remove("hidden");
          }
        }
      }
    }
});
