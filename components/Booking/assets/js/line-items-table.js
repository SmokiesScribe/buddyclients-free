/**
 * Primary function to update line items table.
 * 
 * @since 0.1.0
 */
function buddycUpdateLineItemsTable(updatedLineItems) {
    if (!document.getElementById('buddyc-booking-form')) return;
    
    var lineItemsTableBody = jQuery('.checkout-table tbody');
    lineItemsTableBody.empty();
    
    // No line items
    if ( ! updatedLineItems ) {
        buddycCalculateTotalFee([0]);
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
    buddycCalculateTotalFee(serviceFees);
}

/**
 * Update project name.
 * 
 * @since 0.1.0
 */
jQuery(document).ready(function($) {
    if (!document.getElementById('buddyc-booking-form')) return;
    
    const projectInput = $('#project_title');
    const projectDisplay = $('#buddyc-checkout-project');
    const projectSelect = $('#buddyc_projects');

    function buddycUpdateProjectName() {
        const projectName = projectInput.val();
        if (projectName) {
            projectDisplay.text(projectName);
        } else {
            projectDisplay.text('');
        }
    }
    
    // Listen for changes to project title
    projectInput.change(function() {
        buddycUpdateProjectName();
    });
    
    // Listen for changes to project select
    projectSelect.change(function() {
        buddycUpdateProjectName();
    });
    
    // Call the function initially
    buddycUpdateProjectName();

});


/**
 * Calculate the total fee based on the line items.
 * 
 * @since 0.1.0
 */
function buddycCalculateTotalFee(serviceFees) {
    if (!document.getElementById('buddyc-booking-form')) return;

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
    const totalDisplays = document.querySelectorAll('.buddyc-checkout-total-fee');
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
function buddycUpdateServiceValues() {
    const form = document.getElementById('buddyc-booking-form');
    if (!form) return;

    // Disable submit button
    buddycBookingFormUpdating( false );

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
        buddycUpdateLineItemsTable();

        // Re-enable the submit button and reset its text
        buddycBookingFormUpdating( true );
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
                console.time('AJAX Request');

                // Create AJAX request
                let request = jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'buddyc_create_line_item',
                        service_id: option.value,
                        fee_num: feeNum,
                        adjustments: selectedAdjustments,
                        team_id: teamID,
                        team_member_role: roleID,
                        nonce: lineItemsTableData.nonce,
                        nonceAction: lineItemsTableData.nonceAction,
                    },
                    success: function(response) {
                        console.timeEnd('AJAX Request'); // Log time taken for the AJAX request
                        var lineItem = JSON.parse(response);
                        
                        if (lineItem) {
                            lineItems.push(lineItem);
                            buddycUpdateLineItemsTable(lineItems);
                        } else {
                            console.error('Received invalid lineData:', lineItems);
                        }
                    },
                });
                

                ajaxRequests.push(request);
            }
        });

        // After all AJAX requests complete
        jQuery.when.apply(jQuery, ajaxRequests).done(function() {
            buddycBookingFormUpdating( true );  
        });
    }
}

/**
 * Updates text of submit button and visibility of details container content.
 * 
 * @since 1.0.17
 * 
 * @param   bool    complete    Optional. Whether the update is complete.
 *                              Defaults to false.
 */
function buddycBookingFormUpdating( complete = false ) {
    const form = document.getElementById('buddyc-booking-form');
    if ( ! form ) return;

    const submitButton = document.getElementById('booking-submit');
    if ( ! submitButton ) return;

    // Define variables
    var submitDisabled = ! complete;

    // Cache the original value of the submit button
    if ( ! submitButton.dataset.originalValue ) {
        submitButton.dataset.originalValue = submitButton.value;
    }

    // Disable the submit button and change its text
    submitButton.disabled = submitDisabled;
    submitButton.value = complete ? submitButton.dataset.originalValue : 'Updating...';

    // Get details containers
    const detailsContainers = document.getElementsByClassName('checkout-details-container');

    // Loop through containers and modify content visibility
    for (let i = 0; i < detailsContainers.length; i++) {
        const childElements = detailsContainers[i].children; // Get all child elements
    
        // Show loading indicator and hide existing content except for loading indicators
        for (let j = 0; j < childElements.length; j++) {
            if (childElements[j].classList.contains('checkout-loading-indicator')) {
                // Show the loading indicator
                childElements[j].style.display = complete ? 'none' : 'block';
            } else {
                // Hide other child elements
                childElements[j].style.display = complete ? 'block' : 'none';
            }
        }
    }
}


jQuery(document).ready(function($) {
    if (!document.getElementById('buddyc-booking-form')) return;
    
    // Get project select
    const projectSelect = document.getElementById('buddyc_projects');
    
    // Use event delegation to handle change events on dynamically added fields
    jQuery('#buddyc-booking-form').on('change', 'input, select', function() {
        // Exclude the projectSelect element from handling
        if (this !== projectSelect) {
            buddycUpdateServiceValues();
        }
    });
    
    // Call the function initially to populate the readonly field
    buddycUpdateServiceValues();
});


