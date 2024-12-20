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
        
        function buddycUncheckConfirmationCheckbox() {
            confirmCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        
       // Listen for changes
        jQuery('.buddyc-form input:not(.confirmation-checkbox)').on('change', function() {
            buddycUncheckConfirmationCheckbox();
        });
    });
})();