/**
 * Listens for create new page button clicks and retrieves args from data atts.
 * 
 * @since 1.0.27
 */
document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll( '.buddyc-create-page-btn' );
    if ( buttons.length === 0 ) return;

    // Add listener to each button
    buttons.forEach( button => {
        button.addEventListener( "click", function () {

            /**
             * Arguments extracted from the data attributes
             * 
             * @type {Object}
             * @property {string} page_key - Unique identifier for the page.
             * @property {string} settings_key - Key related to plugin settings.
             * @property {string} post_title - Title of the new page.
             * @property {string} post_content - Content of the new page.
             * @property {string} post_type - Post type of the new page (default: "page").
             * @property {string} post_status - Status of the post (e.g., "publish", "draft").
             * @property {string} [redirect] - Optional URL to redirect after page creation.
             */
            const args = {
                page_key: this.dataset.pageKey,
                settings_key: this.dataset.settingsKey,
                post_title: this.dataset.postTitle,
                post_content: this.dataset.postContent,
                post_type: this.dataset.postType,
                post_status: this.dataset.postStatus,
                redirect: this.dataset.redirect,
            };

            // Create page
            buddycCreateNewPage( args );
        });
    });
});


/**
 * Creates a plugin page from the admin area.
 * 
 * @since 0.1.0
 * 
 * @param {Object} args - Parameters for creating the page.
 * @param {string} args.page_key - Unique identifier for the page.
 * @param {string} args.settings_key - Key related to plugin settings.
 * @param {string} args.post_title - Title of the new page.
 * @param {string} args.post_content - Content of the new page.
 * @param {string} args.post_type - Post type of the new page (default: "page").
 * @param {string} args.post_status - Status of the post (e.g., "publish", "draft").
 * @param {string} [args.redirect] - Optional URL to redirect after page creation.
 */
function buddycCreateNewPage( args ) {
    // Show loading indicator
    buddycLoadingIndicator( true, 'Creating page...' );
    
    // Build ajax request
    jQuery.post( ajaxurl, {
        action: 'buddyc_admin_create_new_page',
        args: args,
        nonce: createPageData.nonce,
    })
    .done( function( response ) {
        // Parse the JSON response
        var responseData = JSON.parse( response );

        // Check if the response indicates success
        if ( responseData.success ) {

            // Redirect to specified url or reload page
            if ( responseData.redirect_url ) {
                window.location.href = responseData.redirect_url;
            } else {
                location.reload();
            }

        } else {
            // Handle failure
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