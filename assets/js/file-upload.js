document.addEventListener('DOMContentLoaded', () => {
    /**
     * Check if the "Add Files" button and file input elements exist.
     * If they exist, attach an event listener to the "Add Files" button to trigger a click on the file input element.
     */
    const uploadContainers = document.querySelectorAll('.buddyc-file-upload');

    uploadContainers.forEach(uploadContainer => {
        const addButton = uploadContainer.querySelector('.dz-button');
        const fileInput = uploadContainer.querySelector('input[type="file"]');
        const fileNameDisplay = uploadContainer.querySelector('#selected-file-name');

        if (addButton && fileInput) {
            addButton.addEventListener('click', () => {
                fileInput.click();
            });
        }

        /**
         * Attach an event listener to the file input element to listen for changes in the selected file.
         * When a file is selected, call the buddycDisplayFileName function to update the UI.
         */
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                buddycDisplayFileName(fileInput, fileNameDisplay);
            });
        }

        /**
         * Handle the file drop event when files are dragged and dropped onto the upload box.
         * When files are dropped, update the file input element with the dropped files and trigger a change event.
         * Prevent the default behavior for drag-and-drop to allow file uploading.
         */
        const uploadBox = uploadContainer.querySelector('.dz-clickable');

        if (uploadBox) {
            uploadBox.addEventListener('drop', (e) => {
                e.preventDefault();
                const files = e.dataTransfer.files;
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            });

            uploadBox.addEventListener('dragover', (e) => {
                e.preventDefault();
            });
        }
    });

    /**
     * Display the name of the selected file in the UI.
     * If no file is selected, reset the UI.
     */
    function buddycDisplayFileName(fileInput, fileNameDisplay) {
        if (fileInput.files.length > 0) {
            let fileNames = '';
            for (let i = 0; i < fileInput.files.length; i++) {
                fileNames += '<i class="fa-solid fa-paperclip"></i> ' + fileInput.files[i].name + '<br>'; // Concatenate file names with line breaks
            }
            fileNameDisplay.innerHTML = fileNames; // Use innerHTML to render HTML content
        } else {
            fileNameDisplay.textContent = ''; // Clear the content if no files are selected
        }
    }
});
