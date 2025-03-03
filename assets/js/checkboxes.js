/**
 * Selects all checkboxes in a field.
 * 
 * @since 1.0.27
 */
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".buddyc-select-all").forEach(button => {
        button.addEventListener("click", function() {
            let targetName = this.getAttribute("data-target");
            let checkboxes = document.querySelectorAll(`input[name='${targetName}']`);
            let allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            
            checkboxes.forEach(checkbox => checkbox.checked = !allChecked);
            this.textContent = allChecked ? "Select All" : "Deselect All";
        });
    });
});