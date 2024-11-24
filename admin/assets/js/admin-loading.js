/**
 * Creates and manages the admin loading indicator.
 * 
 * @since 1.0.20
 * 
 * @param {boolean} show - Whether to show or hide the indicator.
 * @param {string} [message] - Optional message to display in the indicator.
 */
function bcAdminLoadingIndicator( show, message ) {
    // Check if the indicator already exists
    var loadingIndicator = document.getElementById('buddyc-loading-indicator-container');
    
    if ( show ) {
        if ( ! loadingIndicator ) {

            // Create the loading indicator element if it does not exist
            loadingIndicator = document.createElement('div');
            loadingIndicator.id = 'buddyc-admin-loading';
            loadingIndicator.style.opacity = '1';
            loadingIndicator.style.display = 'block';
            loadingIndicator.style.visibility = 'visible';
            
            // Build spinner
            loadingIndicator.innerHTML = '<i class="fa-solid fa-circle-notch" id="buddyc-admin-spinner"></i>';

            // Add message
            if ( message ) {
                loadingIndicator.innerHTML += '<div id="buddyc-admin-loading-message">' + message + '</div>';
            }

            // Insert the loading indicator into the body
            document.body.appendChild(loadingIndicator);
        }

        // Show the loading indicator
        loadingIndicator.style.display = 'block';
        loadingIndicator.style.opacity = '1';
        loadingIndicator.style.visibility = 'visible'; // Initially hidden
    } else {
        if ( loadingIndicator ) {
            // Hide the loading indicator
            loadingIndicator.style.opacity = '0';
            setTimeout(function() {
                loadingIndicator.style.display = 'none';
                // Optionally, remove the element from the DOM
                loadingIndicator.remove();
            }, 300); // Match the duration if there are transitions
        }
    }
}

/**
 * Triggers admin loading indicator on clicks to elements
 * with the class 'buddyc-admin-loader-click'.
 * 
 * Accesses the attribute 'data-loader-message' to generate
 * the text below the loading indicator.
 * 
 * @since 1.0.20
 */
document.addEventListener('DOMContentLoaded', function() {
    // Select all buttons with the class 'buddyc-loader-on-click'
    const buttons = document.querySelectorAll('.buddyc-admin-loader-click');

    // Attach a click event listener to each button
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // Get message from html data
            const message = button.dataset.loaderMessage;
            // Call the bcLoadingIndicator function
            bcAdminLoadingIndicator( true, message );
        });
    });
});