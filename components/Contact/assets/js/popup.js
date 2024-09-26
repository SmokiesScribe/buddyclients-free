/**
 * Controls the display of the floating contact popup.
 * 
 * @since 0.1.0
 */
document.addEventListener('DOMContentLoaded', function () {
    
    var contactPopup = document.getElementById('contact-popup');
    
    // Exit if popup not on page
    if ( ! contactPopup ) {
        return;
    }
    
    // Check for the presence of the cookie
    if (document.cookie.includes('popupDisplayed=true')) {
        // Initialize the popup to visible
        contactPopup.style.visibility = 'visible';
        contactPopup.style.opacity = 1;

        // Remove the cookie
        document.cookie = 'popupDisplayed=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    }
    
    // Close Contact Popup Click Event
    document.addEventListener('click', function (event) {
        
        var contactButton = document.getElementById('floating-contact-btn');
        var contactSubmitButton = document.getElementById('contact-submit');
        var closeButton = document.getElementById('close-bc-pop-up-button');
        
        var outsideClick = !contactPopup.contains(event.target);
        var submitBtnClick = contactSubmitButton && contactSubmitButton.contains(event.target);
        var floatBtnClick = contactButton && contactButton.contains(event.target);
        var closeBtnClick = closeButton.contains(event.target);
        
        if (closeBtnClick || outsideClick && !submitBtnClick && !floatBtnClick) {
            if (closeBtnClick) {
                event.preventDefault();
            }
            // Hide Contact Popup only if it's currently visible
            if (contactPopup.style.visibility === 'visible') {
                contactPopup.style.visibility = 'hidden';
                contactPopup.style.opacity = 0;
            }
        }
    });

    // Floating Contact Button Click Event
    document.getElementById('floating-contact-btn').addEventListener('click', function () {
        // Show Contact Popup
        var contactPopup = document.getElementById('contact-popup');
        if (contactPopup.style.visibility === 'hidden' || contactPopup.style.visibility === '') {
            contactPopup.style.visibility = 'visible';
            contactPopup.style.opacity = 1;
        } else {
            contactPopup.style.visibility = 'hidden';
            contactPopup.style.opacity = 0;
        }
    });
});

/**
 * Sets cookie after form submission.
 * 
 * @since 1.1
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // Get the contact submit button
    var contactSubmitButton = document.getElementById('bc-contact-submit-button');
    
    // Make sure the button is present
    if (contactSubmitButton) {
        
        // Add event listener to the button
        contactSubmitButton.addEventListener('click', function (event) {
            // Set a cookie named 'popupDisplayed' with a value of 'true' and an expiration time of 1 minute
            var expirationTime = new Date(Date.now() + 60000).toUTCString(); // 60000 milliseconds = 1 minute
            document.cookie = 'popupDisplayed=true; expires=' + expirationTime + '; path=/';
        });
    }
});