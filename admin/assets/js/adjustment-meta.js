/**
 * Handles additions to the adjustment option meta fields.
 * 
 * @since 0.1.0 
 */
document.addEventListener('DOMContentLoaded', function() {

    // Get button to add option tables
    const addButton = document.getElementById( 'buddyc_adjustment_create_option' );

    // Get all option tables
    const optionTables = document.querySelectorAll( '.buddyc-adjustment-options' );

    // Exit if elements are missing
    if ( ! addButton || ! optionTables ) return;

    // Show option tables on page load
    buddycShowOptionTables();

    // Listen for button clicks
    addButton.addEventListener('click', buddycAddNewOptionTable);

    /**
     * Shows option tables with a value.
     * 
     * @since 1.0.25
     */
    function buddycShowOptionTables() {
        // Loop through option tables
        optionTables.forEach((optionTable, index) => {
            // Increment index
            var tableIndex = index + 1;

            // Get label field
            var labelField = optionTable.querySelector( '#option_' + tableIndex + '_label' );

            if ( labelField ) {
                // Check if label field is empty
                if ( labelField && labelField.value.trim() === '' ) {
                    // Hide the empty table
                    optionTable.classList.add('buddyc-hidden');
                }
            }
        });
    }

    /**
     * Adds a new option table on button click.
     * 
     * @since 0.1.0 
     */
    function buddycAddNewOptionTable() {
        // Ignore clicks if button is disabled after 10
        if ( addButton.classList.contains( 'disabled' ) ) return;

        // Loop through option tables
        for (let index = 0; index < optionTables.length; index++) {
            const optionTable = optionTables[index];
            
            // Increment index (though it's handled by the loop itself)
            var tableIndex = index + 1;

            // Show the first hidden table
            if ( optionTable.classList.contains( 'buddyc-hidden' ) ) {
                optionTable.classList.remove('buddyc-hidden' );
                
                // Break the loop
                break;
            }
        }

        // Check if we showed the 10th option table
        if ( tableIndex === 10 ) {
            // Disable add button
            addButton.classList.add( 'disabled' );
        }
    }
});