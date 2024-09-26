/**
 * Handles help doc search requests.
 * 
 * @since 0.1.0
 */
jQuery(document).ready(function(){
    
    // Define variable to store the XMLHttpRequest object
    var xhr;
    
    // Function to perform AJAX request
    function performAjaxRequest(query) {
        // Abort any ongoing AJAX request
        if(xhr && xhr.readyState !== 4){
            xhr.abort();
        }
        
        xhr = jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'bc_get_help_docs',
                query: query, // Pass the query
            },
            success: function(data){
                jQuery('#search-results').html(data);
            }
        });
    }

    // Event handler for input change
    jQuery('#ajax-docs-search').on('input', function(){
        var query = jQuery(this).val();
        if (query !== '') {
            performAjaxRequest(query);
        } else {
            jQuery('#search-results').html('');
        }
    });

    // Event handler for clicking floating-contact-btn
    jQuery('#floating-contact-btn').on('click', function() {
        var query = jQuery('#ajax-docs-search').val();
        performAjaxRequest(query);
    });
});
