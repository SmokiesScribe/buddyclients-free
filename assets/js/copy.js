/**
 * Handles copy to clipboard clicks.
 * 
 * @since 0.1.0
 */
function buddycCopyToClipboard(elementId) {
    var linkElement = document.getElementById(elementId);
    if (!linkElement) {
        console.error('Element not found:', elementId);
        return;
    }

    var textToCopy = linkElement.innerText.trim();

    if (navigator.clipboard && navigator.clipboard.writeText) {
        // Modern Clipboard API
        navigator.clipboard.writeText(textToCopy).then(() => {
            buddycShowCopySuccess(linkElement);
        }).catch(err => {
            console.error('Clipboard API failed:', err);
        });
    } else {
        // Fallback for older browsers
        buddycFallbackCopyText(textToCopy, linkElement);
    }
}

/**
 * Fallback method for copying text (uses document.execCommand).
 */
function buddycFallbackCopyText(text, element) {
    var tempInput = document.createElement('textarea');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();

    try {
        document.execCommand('copy');
        buddycShowCopySuccess(element);
    } catch (err) {
        console.error('Fallback copy failed:', err);
    }

    document.body.removeChild(tempInput);
}

/**
 * Displays "Copied" message.
 */
function buddycShowCopySuccess(element) {
    document.querySelectorAll('.buddyc-copy-success').forEach(div => div.textContent = '');
    
    var parent = element.closest('div');
    var successDiv = parent.querySelector('.buddyc-copy-success');

    // Show the feedback
    successDiv.classList.add('show');

    // Hide feedback after 2 seconds
    setTimeout(function() {
        successDiv.classList.remove('show');
    }, 2000);

    if ( successDiv.classList.contains('check') ) {
        var message = '✔️';
    } else {
        var message = 'Copied!';
    }
    

    if (successDiv) {
        successDiv.textContent = message;
    }
}