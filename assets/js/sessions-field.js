/**
 * Handles session checkboxes visibility.
 * 
 * @since 0.1.09
 */
document.addEventListener('DOMContentLoaded', function () {
    // Get all elements with the class 'faculty-member-container'
    var facultyContainers = document.getElementsByClassName('faculty-member-container');

    // Define a separate function to handle the click event
    function buddycToggleCheckboxesContainer() {
        // Get the element with the class 'faculty-checkboxes-container' within the clicked 'faculty-member-container'
        var facultyCheckboxesContainer = this.parentElement.getElementsByClassName('faculty-checkboxes-container')[0];
        
        // Toggle the visibility of faculty-checkboxes-container
        if (facultyCheckboxesContainer.style.display === 'none' || facultyCheckboxesContainer.style.display === '') {
            facultyCheckboxesContainer.style.display = 'block';
        } else {
            facultyCheckboxesContainer.style.display = 'none';
        }

        // Get the parent 'faculty-member-container'
        var facultyMemberContainer = this.closest('.faculty-member-container');

        // Toggle the box shadow of faculty-member-container
        if (facultyCheckboxesContainer.style.display === 'block') {
            facultyMemberContainer.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';
        } else {
            facultyMemberContainer.style.boxShadow = 'none';
        }
    }

    // Loop through the elements and add a click event listener to each 'faculty-id-container'
    for (var i = 0; i < facultyContainers.length; i++) {
        var facultyContainer = facultyContainers[i];

        // Get the element with the class 'faculty-id-container' within each 'faculty-member-container'
        var facultyIdContainer = facultyContainer.getElementsByClassName('faculty-id-container')[0];

        // Add a click event listener to the faculty-id-container
        facultyIdContainer.addEventListener('click', buddycToggleCheckboxesContainer);
    }
});