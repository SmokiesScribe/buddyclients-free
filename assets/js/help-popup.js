/**
 * Modifies the popup content.
 * 
 * @since 0.1.0
 */
document.addEventListener('DOMContentLoaded', function () {
    
    // Get all help links
    var helpLinks = document.querySelectorAll('.bc-popup-link');
    
    // Get popup html
    var helpPopup = document.getElementById('bc-popup');
    
    // Loop through help links
    helpLinks.forEach(function(link) {
        // Add click event listener to each help link
        link.addEventListener('click', function(event) {
            // Prevent default link behavior
            event.preventDefault();
            
            // Extract post id
            var postId = link.getAttribute('data-post-id');
            var url = link.getAttribute('data-url');
            var rawContent = link.getAttribute('data-raw-content');
            
            // Show loading indicator
            bcLoadingIndicator( true );
            
            // AJAX request for post content
            var xhr = jQuery.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'buddyc_get_popup_content',
                    postId: postId,
                    url: url,
                    rawContent: rawContent,
                    nonce: helpPopupData.nonce,
                    nonceAction: helpPopupData.nonceAction,
                    fileName: helpPopupData.fileName,
                },
                success: function(data){
                    jQuery('#bc-popup-content').html(data);
                    
                    // Hide loading indicator
                    bcLoadingIndicator( false );
                    setTimeout(function() {
                        bcLoadingIndicator( false );
                    }, 300); // Match the transition duration  
                    
                    // Show the popup
                    helpPopup.style.visibility = 'visible';
                    helpPopup.style.opacity = 1;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX request failed: ', textStatus, errorThrown);
                    
                    // Show the popup
                    helpPopup.style.visibility = 'visible';
                    helpPopup.style.opacity = 1;
                    
                    // Show an error message to the user
                    jQuery('#bc-popup-content').html('<p>Sorry, there was an error loading the content. Please try again later.</p>');
                    
                    // Hide loading indicator
                    bcLoadingIndicator( false );
                    setTimeout(function() {
                        bcLoadingIndicator( false );
                    }, 300); // Match the transition duration
                }
            });
    
            // Call AJAX request function with postId
            //helpContentAjaxRequest(postId);
        });
    });
    
    // Close popup on outside click
    document.addEventListener('click', function(event) {
        var closeButton = document.getElementById('bc-close-btn');
        
        // Exit if close button does not exist
        if (! closeButton ) {
            return;
        }
        
        // Check the click location
        if (!helpPopup.contains(event.target) || closeButton.contains(event.target)) {
            // Prevent default behavior if the close button is clicked
            if (closeButton.contains(event.target)) {
                event.preventDefault();
            }
            // Hide popup if it's currently visible
            if (helpPopup.style.visibility !== 'hidden') {
                helpPopup.style.visibility = 'hidden';
                helpPopup.style.opacity = 0;
            }
        }
    });
});