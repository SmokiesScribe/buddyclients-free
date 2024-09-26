/**
 * Unchecks the terms or confirmation checkbox.
 * 
 * @since 0.1.0
 */
(function() {
    document.addEventListener('DOMContentLoaded', function () {

        // Get confirmation checkbox
        const confirmCheckboxes = document.querySelectorAll('.confirmation-checkbox');
        
        if ( ! confirmCheckboxes ) {
            return;
        }
        
        function uncheckConfirmationCheckbox() {
            confirmCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        
       // Listen for changes
        jQuery('.bc-form input:not(.confirmation-checkbox)').on('change', function() {
            uncheckConfirmationCheckbox();
        });
    });
})();