/**
 * Validates the Booking Form submission.
 * 
 * @since 0.1.0
 */
(function() {
    document.addEventListener('DOMContentLoaded', function () {
        
        // Get booking form
        bookingForm = document.getElementById('buddyc-booking-form');
        
        if ( ! bookingForm ) {
            return;
        }

        // Get fields
        const lineItemsField = document.querySelector(`[name="hidden-line-items"]`);
        const totalField = bookingForm.querySelector(`[name="total-fee"]`);
        const minimumField = bookingForm.querySelector(`[name="minimum-fee"]`);
        const confirmCheckboxes = document.querySelectorAll('.confirmation-checkbox');
        
        // Initialize variable
        let valid = true;
        
        // Listen for form submission
        bookingForm.addEventListener('submit', function (event) {
            
            // Require services
            if ( ! lineItemsField.value ) {
                buddycPreventSubmission('Please select your services.');
                return;
            }
            
            // Convert values to numbers and handle validation
            var totalValue = parseFloat(totalField.value);
            var minimumValue = parseFloat(minimumField.value);
            
            // Require minimum fee
            if ( totalValue < minimumValue ) {
                buddycPreventSubmission('Please select services amounting to at least $' + minimumField.value + '.');
                return;
            }
        
            // Require confirmation checkboxes
            confirmCheckboxes.forEach(function ( checkbox ) {
                if ( ! checkbox.checked ) {
                    buddycPreventSubmission('Please check the confirmation box.');
                    return;
                }
            });
        });
        
        /**
         * Prevents form submission and displays alert.
         * 
         * @since 0.1.0
         * 
         * @param   string  alertMessage  The message to display in the alert.
         */
        function buddycPreventSubmission( alertMessage ) {
                event.preventDefault(); // Prevent form submission
                alert( alertMessage );
        }
    });
})();