/**
 * Primary function to update line items table.
 * 
 * @since 0.1.0
 */
function updateLineItemsTable(updatedLineItems) {
    if (!document.getElementById('bc-booking-form')) return;
    
    var lineItemsTableBody = jQuery('.checkout-table tbody');
    lineItemsTableBody.empty();
    
    // No line items
    if ( ! updatedLineItems ) {
        calculateTotalFee([0]);
        return;
    }
    
    // Update hidden field
    const lineItemsField = document.getElementById( 'hidden-line-items' );
    lineItemsField.value = JSON.stringify( updatedLineItems );
    
    // Initialize
    let serviceFees = [];
    
    updatedLineItems.forEach( function ( lineItem ) {
        
        // Check if the lineItem is an object
        if ( typeof lineItem === 'object' ) {

            // Append a row to the table
            lineItemsTableBody.append('<tr><td>' + lineItem.service_name + '<br><span class="checkout-unit-label">' + lineItem.unit_label + lineItem.adjustment_label + '</span></td><td>' + '$' + lineItem.service_fee + '</td></tr>');

            // Add service fee to array
            serviceFees.push(lineItem.service_fee);
        }
    });

    // Calculate and append the initial total fee
    calculateTotalFee(serviceFees);
}

/**
 * Update project name.
 * 
 * @since 0.1.0
 */
jQuery(document).ready(function($) {
    if (!document.getElementById('bc-booking-form')) return;
    
    const projectInput = $('#project_title');
    const projectDisplay = $('#bc-checkout-project');
    const projectSelect = $('#bc_projects');

    function updateProjectName() {
        const projectName = projectInput.val();
        if (projectName) {
            projectDisplay.text(projectName);
        } else {
            projectDisplay.text('');
        }
    }
    
    // Listen for changes to project title
    projectInput.change(function() {
        updateProjectName();
    });
    
    // Listen for changes to project select
    projectSelect.change(function() {
        updateProjectName();
    });
    
    // Call the function initially
    updateProjectName();

});


/**
 * Calculate the total fee based on the line items.
 * 
 * @since 0.1.0
 */
function calculateTotalFee(serviceFees) {
    if (!document.getElementById('bc-booking-form')) return;

    // Calculate the total fee by summing up the service fees in the array
    var totalFee = serviceFees.reduce(function(sum, fee) {

        // Remove comma
        var formattedFeeValue = String(fee).replace(/,/g, '');
        
        // Convert fee to a number, or 0 if it's not a valid number
        var feeValue = parseFloat(formattedFeeValue) || 0;
        
        // Add the fee value to the sum
        return sum + feeValue;
    }, 0);
    
    // Set value of tabel total
    const totalDisplays = document.querySelectorAll('.bc-checkout-total-fee');
    totalDisplays.forEach(totalDisplay => {
        totalDisplay.textContent = '$' + totalFee.toFixed(2);
    });
    
    // Append a row to the table with the total fee
    var lineItemsTableBody = jQuery('#line-items-table tbody');
    lineItemsTableBody.append('<tr><td><b>Total Fee</b></td><td></td><td>' + '$' + totalFee.toFixed(2) + '</td></tr>');

    // Update the value of the "Total Fee" input field
    jQuery('#total-fee').val(totalFee.toFixed(2)); // Set the value as a formatted string
}

/**
 * Update service values.
 * 
 * @since 0.1.0
 * @updated 0.2.3
 */
function updateServiceValues() {
    const form = document.getElementById('bc-booking-form');
    if (!form) return;

    const submitButton = document.getElementById('booking-submit');
    if (!submitButton) return;

    // Cache the original value of the submit button
    if (!submitButton.dataset.originalValue) {
        submitButton.dataset.originalValue = submitButton.value;
    }

    // Disable the submit button and change its text
    submitButton.disabled = true;
    submitButton.value = 'Updating...'; // Change the text of the submit button

    var selectedServices = []; // Initialize an array to store selected service IDs

    // Get the parent element of all service options
    const serviceOptionsParent = form;

    // Initialize variable for fee multiplication
    let feeNum = false;

    // Get all service options within the parent element
    const serviceOptions = serviceOptionsParent.querySelectorAll('.service-option');

    // Loop through each element
    serviceOptions.forEach(option => {
        // Make sure the option is not disabled
        if (!option.classList.contains('service-disabled')) {
            // Check if the option is a checkbox
            if (option.type === 'checkbox' && option.checked) {
                selectedServices.push(option.value); // Push the value to the array
            } else if (option.tagName === 'OPTION' && option.selected) {
                selectedServices.push(option.value); // Push the value to the array
            }
        }
    });

    // Initialize an array to store rateData for all selected services
    let lineItems = [];

    // No service
    if (selectedServices.length === 0) {
        // Clear field if no service is selected
        jQuery('#hidden-line-items').val('');
        // Update the table
        updateLineItemsTable();

        // Re-enable the submit button and reset its text
        submitButton.disabled = false;
        submitButton.value = submitButton.dataset.originalValue;

        return;
    }

    // Valid and selected services exist
    if (selectedServices.length > 0) {
        // Create an array to hold the AJAX promises
        let ajaxRequests = [];

        // Loop through selected services
        serviceOptions.forEach(option => {
            // Double check that the option is selected and not disabled
            if (!option.classList.contains('service-disabled') && (option.checked || option.selected)) {
                // Get service rate type id
                const rateType = option.getAttribute('data-rate-type');
                // Get matching rate type field
                const rateTypeField = document.getElementById("fee-number-" + rateType);

                if (rateTypeField) {
                    // Check whether to attach to project or service
                    const attach = rateTypeField.getAttribute('data-attach');
                    
                    if (attach === 'service') {
                        const serviceId = option.value;
                        const serviceRateField = document.getElementById('fee-number-' + rateType + '-' + serviceId);
                        if (serviceRateField) {
                            // Get service fee num value
                            feeNum = serviceRateField.value;
                        }
                    } else {
                        // Get project fee num value
                        feeNum = rateTypeField.value;
                    }
                }

                // Initialize
                var selectedAdjustments = [];

                // Get option adjustment ids
                const adjustments = option.getAttribute('data-adjustments');
                if (adjustments) {
                    const adjustmentArray = adjustments.split(',');
                    adjustmentArray.forEach(adjustmentId => {
                        const adjustmentFieldOptions = document.querySelectorAll('.adjustment-option-' + adjustmentId);
                        if (adjustmentFieldOptions) {
                            adjustmentFieldOptions.forEach(adjustmentOption => {
                                if (adjustmentOption.selected || adjustmentOption.checked) {
                                    const adjustmentOperator = adjustmentOption.getAttribute('data-operator');
                                    const adjustmentValue = adjustmentOption.getAttribute('data-value');
                                    const adjustmentName = adjustmentOption.getAttribute('data-name');
                                    
                                    selectedAdjustments.push(adjustmentOption.value);
                                }
                            });
                        }
                    });
                }

                // Get team member
                let teamID = '';
                const assignedTeamID = option.getAttribute('data-assigned-team-member');
                const roleID = option.getAttribute('data-role-id');

                if (assignedTeamID) {
                    teamID = assignedTeamID;
                } else {
                    const roleField = document.getElementById("role-" + roleID);
                    teamID = roleField.value;
                }

                // Create AJAX request and add it to the promises array
                let request = jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'bc_create_line_item',
                        service_id: option.value,
                        fee_num: feeNum,
                        adjustments: selectedAdjustments,
                        team_id: teamID,
                        team_member_role: roleID,
                    },
                    success: function(response) {
                        var lineItem = JSON.parse(response);
                        
                        if (lineItem) {
                            lineItems.push(lineItem);
                            updateLineItemsTable(lineItems);
                        } else {
                            console.error('Received invalid rateData:', lineItems);
                        }
                    }
                });

                ajaxRequests.push(request);
            }
        });

        // After all AJAX requests complete
        jQuery.when.apply(jQuery, ajaxRequests).done(function() {
            submitButton.disabled = false;
            submitButton.value = submitButton.dataset.originalValue; // Restore the original value
        });
    }
}


jQuery(document).ready(function($) {
    if (!document.getElementById('bc-booking-form')) return;
    
    // Get project select
    const projectSelect = document.getElementById('bc_projects');
    
    // Use event delegation to handle change events on dynamically added fields
    jQuery('#bc-booking-form').on('change', 'input, select', function() {
        // Exclude the projectSelect element from handling
        if (this !== projectSelect) {
            updateServiceValues();
        }
    });
    
    // Call the function initially to populate the readonly field
    updateServiceValues();
});


