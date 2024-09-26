/**
 * Populates sales email field with selected client email.
 * 
 * @since 0.1.0
 */
jQuery(document).ready(function($) {
    if (!document.getElementById('bc-sales-form')) return;
    
    // Get form
    const form = document.getElementById('bc-sales-form');
    
    // Get client select
    const clientSelect = jQuery('#sales_client_id');
    
    // Get client email input
    const emailInput = jQuery('#sales_client_email');

    function populateClientEmail() {
        // Get selected client email
        const selectedClient = clientSelect.find('option:selected');
        const clientEmail = selectedClient.attr('data-email');
        
        // Populate email input
        emailInput.val(clientEmail);
    }
    
    // Listen for changes to client select
    clientSelect.on('change', function() {
        populateClientEmail();
    });
});
