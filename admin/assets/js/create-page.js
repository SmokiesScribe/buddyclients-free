/**
 * Creates plugin pages from admin area.
 * 
 * @since 0.1.0
 */
function buddycbuddycbuddycbuddycCreateNewPage(args) {
    // Show loading indicator
    buddycLoadingIndicator( true, 'Creating page...' );
    
    jQuery.post(ajaxurl, {
        action: 'buddyc_admin_create_new_page',
        args: args,
        nonce: createPageData.nonce,
        nonceAction: createPageData.nonceAction,
    })
    .done(function(response) {
        // Parse the JSON response
        var responseData = JSON.parse(response);
        // Check if the response indicates success
        if (responseData.success) {
            // If successful, check if an edit post URL is provided
            if (responseData.edit_post_url) {
                // Redirect to the edit post URL
                window.location.href = responseData.edit_post_url;
            } else {
                location.reload();
            }
        } else {
            // Handle the case where the server indicates failure
            alert('Failed to create page: ' + responseData.error_message);
            buddycLoadingIndicator( false );
        }
    })
    
    .fail(function(xhr, status, error) {
        // Handle failure response
        console.error(xhr.responseText);
        alert('Error creating page: ' + error);
        buddycLoadingIndicator( false );
    });
}