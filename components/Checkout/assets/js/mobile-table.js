/**
 * Displays checkout table on mobile.
 * 
 * Modifies visibility on button click.
 * 
 * @since 0.3.0
 */
document.addEventListener("DOMContentLoaded", function() {
    
    const mobileButton = document.getElementById('checkout-mobile-button');
    const tableContainer = document.querySelector('.checkout-details-container');
    
    function updateTableVisibility() {
        const createAccountForm = document.getElementById('buddyc-create-account-form');
        
        // Toggle active class to trigger CSS transition
        tableContainer.classList.toggle('active');
        
        // Update button text based on visibility
        const isVisible = tableContainer.classList.contains('active');
        const buttonText = isVisible ? 'Close Summary' : 'View Summary';
        mobileButton.innerHTML = buttonText;
    }
    
    // Add event listener
    if (mobileButton) {
        mobileButton.addEventListener("click", updateTableVisibility);
    }
});