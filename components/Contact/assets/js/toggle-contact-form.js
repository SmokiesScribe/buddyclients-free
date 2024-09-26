/**
 * Toggles the visibility of the contact form and search box in popup.
 * 
 * @since 0.1.0
 */
jQuery(document).ready(function(jQuery) {
    const contactForm = document.getElementById( 'bc-contact-form-container' );
    const contactPopup = document.getElementById( 'contact-popup' );
    
    // Exit if popup not on page
    if ( ! contactPopup ) {
        return;
    }

    // Click event handler for the "Get in touch" button
    jQuery(document).on('click', '#bc-get-in-touch', function(event) {
        event.preventDefault(); // Prevent default link behavior
        
        // Hide the #ajax-docs-search-container element
        jQuery('#contact-popup #ajax-docs-search-container').hide();
        
        // Show the #blue-pen-contact-form-container element
        contactForm.style.display = "block";
    });
});
