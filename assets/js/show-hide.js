/**
 * Toggles the visibility of an element with the specified ID.
 *
 * This function checks if the element is visible or hidden and toggles its display 
 * style accordingly. It will either show the element (by setting display to 'block') 
 * or hide it (by setting display to 'none').
 *
 * @param {string} formContainerId - The ID of the element to toggle visibility for.
 */
function buddycShowElement(formContainerId) {
    // Get the element by the ID passed
    var element = document.getElementById(formContainerId);

    // Check if the element exists
    if (element) {
        // Toggle the visibility
        if (element.style.display === 'none' || element.style.display === '') {
            element.style.display = 'block'; // Show the element
        } else {
            element.style.display = 'none'; // Hide the element
        }
    } else {
        console.error('Element with ID "' + formContainerId + '" not found.');
    }
}