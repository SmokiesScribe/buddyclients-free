/**
 * Attaches copy-to-clipboard event listeners to elements with the class "buddyc-copy-to-clipboard".
 *
 * @since 1.0.0
 */
document.addEventListener( "DOMContentLoaded", function () {

    // Get all elements with the copy-to-clipboard functionality
    const copyContainers = document.querySelectorAll( ".buddyc-copy-to-clipboard" );

    if ( copyContainers.length === 0 ) return;

    // Loop through elements and add event listeners
    copyContainers.forEach( copyContainer => {

        const button  = copyContainer.querySelector( ".copy-to-clipboard-icon" );
        const content = copyContainer.querySelector( ".buddyc-copy-content" );

        if ( ! button || ! content ) return;

        // Determine the text to copy (input value or inner text)
        const value = content.value !== undefined ? content.value : content.innerText;

        copyContainer.addEventListener( "click", function ( event ) {
            buddycCopyToClipboard( event, value, copyContainer );
        });

    });

});

/**
 * Handles copy-to-clipboard clicks.
 * 
 * @since 0.1.0
 * 
 * @param {MouseEvent} event       The click event.
 * @param {string}     value       The text to copy.
 * @param {HTMLElement} linkElement The element that triggered the copy action.
 */
function buddycCopyToClipboard( event, value, linkElement ) {

    if ( ! value ) return;

    var textToCopy = value.trim();

    if ( navigator.clipboard && navigator.clipboard.writeText ) {

        // Modern Clipboard API
        navigator.clipboard.writeText( textToCopy ).then( () => {
            buddycShowCopySuccess( event );
        }).catch( err => {
            console.error( "Clipboard API failed:", err );
        });

    } else {
        // Fallback for older browsers
        buddycFallbackCopyText( event, textToCopy, linkElement );
    }

}

/**
 * Fallback method for copying text (uses document.execCommand).
 * 
 * @since 0.1.0
 * 
 * @param {MouseEvent} event   The click event.
 * @param {string}     text    The text to copy.
 * @param {HTMLElement} element The element that triggered the copy action.
 */
function buddycFallbackCopyText( event, text, element ) {

    var tempInput = document.createElement( "textarea" );
    tempInput.value = text;
    document.body.appendChild( tempInput );
    tempInput.select();

    try {

        document.execCommand( "copy" );
        buddycShowCopySuccess( event );

    } catch ( err ) {
        console.error( "Fallback copy failed:", err );
    }

    document.body.removeChild( tempInput );

}

/**
 * Displays "Copied" message at the exact click position.
 * 
 * @since 0.1.0
 * 
 * @param {MouseEvent} event The click event.
 */
function buddycShowCopySuccess( event ) {

    // Clear existing success messages
    document.querySelectorAll( ".buddyc-copy-success" ).forEach( div => div.remove() );

    // Create the success message element
    var successDiv = document.createElement( "div" );
    successDiv.className = "buddyc-copy-success show";
    successDiv.textContent = "Copied!";

    // Append to the body so we can position it absolutely
    document.body.appendChild( successDiv );

    // Position the success message at the exact click location
    successDiv.style.top  = `${event.pageY - 50}px`; // Slightly above the click
    successDiv.style.left = `${event.pageX}px`;      // At the exact click position

    // Hide feedback after 2 seconds
    setTimeout( () => {

        successDiv.classList.replace( "show", "hide" );

        setTimeout( () => successDiv.remove(), 500 ); // Remove from DOM after fade-out

    }, 2000 );

}
