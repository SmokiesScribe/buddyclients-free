document.addEventListener("DOMContentLoaded", function() {
    // DOM is loaded, now execute code
    const canvas = document.getElementById("signatureCanvas");
    
    // Exit if no signature field on page
    if ( ! canvas  ) {
        return;
    }
    
    const signatureInput = document.getElementById("signature-data");
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
        const clearButton = document.getElementById("signature-clear-button");
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



// Convert the canvas data to a data URL
//const signatureData = canvas.toDataURL('image/png');

//console.log('Signature Data: ' + signatureData);

// Extract and store base64 data in the canvas dataset
//const base64Data = signatureData.split(',')[1];
//canvas.dataset.signature = base64Data;
