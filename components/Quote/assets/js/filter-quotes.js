/**
 * Filters the custom quote options based on the selected client and project.
 * 
 * @since 0.1.0
 */
jQuery(document).ready(function(jQuery) {
    if (!document.getElementById('bc-booking-form')) return;
    
    // Get project and client inputs
    const projectSelect = document.getElementById('bc_projects');
    const clientInput = document.getElementById('user-id');
    
    // Get quote checkboxes wrap
    const quoteWrap = document.getElementById('service-field-quote');
    
    // Exit if the quote field is not present
    if (!quoteWrap) {
        return;
    }
    
    // Get container
    var quoteContainer = quoteWrap.closest('.bc-form-group-container');
    
    // Get all quote options
    const quoteOptions = quoteWrap.querySelectorAll('.service-option');
    
    // Exit if no quote options
    if (!quoteOptions || quoteOptions.length === 0) {
        return;
    }

    function filterQuoteOptions() {
        // Initialize a variable to track if any options are visible
        let anyVisible = false;
        
        // Loop through each element
        quoteOptions.forEach(option => {
            // Initialize
            let showOption = false;
            
            // Get the option data
            const optionClient = option.getAttribute('data-client-id');
            const optionProject = option.getAttribute('data-project-id');
            
            // Get selected data
            const selectedProject = projectSelect.value;
            const selectedClient = clientInput.value;
            
            // Compare clients
            if (optionClient && selectedClient && optionClient === selectedClient) {
                showOption = true;
            }
            
            // Compare projects
            if (optionProject && selectedProject && optionProject === selectedProject) {
                showOption = true;
            }
            
            // Show or hide option
            if (showOption) {
                option.parentNode.style.display = 'block';
                anyVisible = true; // Mark that at least one option is visible
                option.classList.remove('service-disabled'); // Remove the class when the option is shown
            } else {
                option.parentNode.style.display = 'none';
                option.classList.add('service-disabled'); // Add the class when the option is hidden
            }

        });
        
        // If no options are visible, hide the quoteWrap
        if ( ! anyVisible ) {
            quoteContainer.style.display = 'none';
        } else {
            quoteContainer.style.display = ''; // Show the quoteWrap if any options are visible
        }
        
        // Once filterQuoteOptions has completed its execution, trigger updateServiceValues
        updateServiceValues();
        
    }
    
    // Listen for changes to project select
    projectSelect.addEventListener("change", function() {
        // Call the filterQuoteOptions function
        filterQuoteOptions();
    });
    
    // Call the function initially
    filterQuoteOptions();

});
