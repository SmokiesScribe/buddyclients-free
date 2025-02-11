/**
 * Initializes a signature capture functionality on a canvas element.
 *
 * Allows users to draw a signature on a canvas, converts it to a
 * base64-encoded image, and stores it in a hidden input field for form submission.
 * 
 * @since 1.0.0
 */
document.addEventListener("DOMContentLoaded", function() {

    // Get signature field container
    const signatureContainer = document.querySelector( '.buddyc-signature-container' );
    
    // Exit if no signature field on page
    if ( ! signatureContainer  ) {
        return;
    }

    // Define element Ids
    const signatureIds = {
        canvas:     'buddyc-signature-canvas',
        data:       'buddyc-signature-data',
        clear:      'buddyc-signature-clear-button',
    };

    // Get elements
    const canvas = signatureContainer.querySelector( '#' + signatureIds.canvas );
    const signatureInput = signatureContainer.querySelector( '#' + signatureIds.data );
    const clearButton = signatureContainer.querySelector( '#' + signatureIds.clear );
    
    // Get constants    
    const ctx = canvas.getContext("2d");
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

    // Check if necessary elements exist
    if ( canvas && signatureInput ) {
        
        // Get the parent form and submit button
        const parentForm = canvas.closest('form');
        const submitButton = parentForm.querySelector('input[type="submit"]');

        // Event listeners to capture the signature
        canvas.addEventListener("mousedown", (e) => {
            isDrawing = true;
            [lastX, lastY] = [e.offsetX, e.offsetY];
        });
    
        canvas.addEventListener("mousemove", (e) => {
            if (!isDrawing) return;
            ctx.strokeStyle = "#000";
            ctx.lineWidth = 2;
            ctx.lineJoin = "round";
            ctx.lineCap = "round";
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            [lastX, lastY] = [e.offsetX, e.offsetY];
            ctx.lineTo(lastX, lastY);
            ctx.stroke();
        });
    
        canvas.addEventListener("mouseup", () => {
            isDrawing = false;
            signatureInput.value = canvas.toDataURL("image/png").split(',')[1];
        });
    
        canvas.addEventListener("mouseout", () => {
            isDrawing = false;
        });
    
        // Clear the canvas
        if (clearButton) {
            clearButton.addEventListener("click", () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            });
        } else {
            console.error("Clear button not found.");
        }
    
        // Form submission event listener
        if (submitButton) {
            submitButton.addEventListener("click", () => {
                if (signatureInput.value === "") {
                    alert("Please provide a signature before submitting the form.");
                    return false; // Prevent form submission
                }
            });
        } else {
            console.error("Submit button not found.");
        }
    }
});