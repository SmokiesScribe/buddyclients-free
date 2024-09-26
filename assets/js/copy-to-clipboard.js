/**
 * Handles copy to clipboard clicks.
 * 
 * @since 0.1.0
 */
function copyToClipboard(elementId) {

    // Select the element containing the link
    var linkElement = document.getElementById(elementId);
    
    // Create a temporary input element
    var tempInput = document.createElement('input');

    // Set the value of the temporary input element to the link text
    tempInput.value = linkElement.innerText;

    // Append the temporary input element to the body
    document.body.appendChild(tempInput);

    // Select the text inside the input element
    tempInput.select();

    // Copy the selected text to the clipboard
    document.execCommand('copy');

    // Remove the temporary input element from the body
    document.body.removeChild(tempInput);
    
    // Get all success containers
    var allSuccessDivs = document.querySelectorAll('.bc-copy-success');
    
    // Clear all success divs
    allSuccessDivs.forEach(function(div) {
        div.textContent = '';
    });
    
    // Get the parent container
    var parent = linkElement.closest('div');
    
    // Get the success message container
    var successDiv = parent.querySelector('.bc-copy-success');
    
    // Set the success message
    successDiv.textContent = 'Copied to clipboard!';
}