/**
 * Handles new account creation.
 * 
 * Generates an alert and returns an error on failure.
 * 
 * @since 0.4.3
 * @param {Event} e - The event object from the form submission.
 * @param {string} ajaxurl - The URL to send the AJAX request to.
 * @param {submitButton} stripe - The submit button.
 * @returns {Promise<boolean>} - Resolves to true if account creation is successful, false otherwise.
 */
export function handleCreateAccount(e, ajaxurl, submitButton) {
  e.preventDefault();

  // Start account creation process first
  const createAccountForm = document.getElementById("bc-create-account-form");

  // Disable the submit button to prevent multiple submissions
  submitButton.disabled = true;

  // Check if the account creation form is present
  if (!createAccountForm) {
    // No account creation needed; assume positive result for logged-in users
    return true;
  }
  
  // Return a Promise for async handling
  return new Promise((resolve) => {

    // AJAX request for account creation
    jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      data: {
        action: "buddyc_checkout_create_account",
        username: document.querySelector('input[name="create-account-name"]').value,
        email: document.querySelector('input[name="create-account-email"]').value,
        password: document.querySelector('input[name="create-account-password"]').value,
        booking_intent_id: document.querySelector('input[name="booking-intent-id"]').value,
        registration_intent_id: document.querySelector('input[name="registration-intent-id"]').value,
        sponsor_intent_id: document.querySelector('input[name="sponsor-intent-id"]').value,
        nonce: createAccountData.nonce,
        nonceAction: createAccountData.nonceAction,
      },
      success: function (response) {

        if (response.success) {
          // Account created successfully
          resolve(true);
        } else {
          const errorMessage = response.data;
          console.error("Error response data:", errorMessage);

          // Display appropriate error messages
          if (errorMessage.includes("Sorry, that email address is already used!")) {
            alert("That email address is already in use. Please log in to your account.");
          } else if (errorMessage.includes("Cannot create a user with an empty login name")) {
            alert("Please provide a name to create an account.");
          } else if ( errorMessage && typeof errorMessage === 'string' ) {
            alert( errorMessage );
          } else {
              alert("An error occurred while creating the user. Please try again later.");
          }

          // Re-enable the submit button
          submitButton.disabled = false;
          resolve(false);
        }
      },
      error: function (xhr, status, error) {
        // Handle AJAX error
        console.error("AJAX request failed. Status:", status, "Error:", error);
        alert("An error occurred while creating the account. Please try again later.");

        // Re-enable the submit button
        submitButton.disabled = false;
        resolve(false);
      },
    });
  });
}
