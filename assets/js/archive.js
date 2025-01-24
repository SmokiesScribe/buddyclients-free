/**
 * Filters archive items based on checkbox selections.
 * 
 * @since 1.0.21
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get the filter form
    const form = document.getElementById( 'buddyc-archive-filter-form' );

    if ( ! form ) {
        return;
    }
    
    // Get all checkboxes in the filter form
    const checkboxes = document.querySelectorAll('#buddyc-archive-filter-form input[type="checkbox"]');

    // Function to update post visibility based on checked checkboxes
    function buddycArchivePostVisibility() {
        checkboxes.forEach(function(checkbox) {
            const serviceType = checkbox.value; // The service type ID
            const posts = document.querySelectorAll('.buddyc-archive-post[data-archive-filter="' + serviceType + '"]');
            
            // If the checkbox is checked, show the posts, otherwise hide them
            posts.forEach(function(post) {
                if (checkbox.checked) {
                    post.style.display = 'block'; // Show the post
                } else {
                    post.style.display = 'none'; // Hide the post
                }
            });
        });
    }

    // Attach an event listener to each checkbox to call updatePostVisibility when checked/unchecked
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', buddycArchivePostVisibility);
    });

    // Initialize the visibility when the page loads
    buddycArchivePostVisibility();
});
