/**
 * Injects a Get Started button in the header.
 * Only works for BuddyBoss Theme.
 * 
 * @since 1.0.25
 */
document.addEventListener('DOMContentLoaded', function() {
    var ctaText = headerButtonData.btnText;
    var ctaUrl = headerButtonData.btnUrl;

    if ( ! ctaText || ! ctaUrl ) {
        return;
    }

    // Get primary header
    var header = document.querySelector('header:not(.panel-head), .site-header:not(.panel-head)');

    if ( header ) {
        
        // Get signup buttons container
        btnsContainer = header.querySelector('.bb-header-buttons');

        if ( btnsContainer ) {

            // Define text
            var btnText = (headerButtonData.btnText && headerButtonData.btnText.trim() !== '') ? headerButtonData.btnText : 'Get Started';

            // Create the custom button element
            var button = document.createElement('a');
            button.href = headerButtonData.btnUrl;  // Link the button to the desired URL
            button.classList.add('button', 'small', 'signup');  // Add a class for styling
            button.innerText = btnText;  // Set the button text

            // Append the container to the header
            btnsContainer.appendChild( button );
        }
    }
});