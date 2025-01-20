/**
 * Primary function to update line items table.
 * 
 * @since 0.1.0
 */
function buddycUpdateLineItemsTable( updatedLineItems ) {
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

// Initialize request map
const buddycRequestMap = new Map();

/**
 * Update service values.
 * 
 * @since 0.1.0
 * @updated 0.2.3
 */
function buddycUpdateServiceValues() {
    const formId = 'buddyc-booking-form';
    const form = document.getElementById( formId );
    if ( ! form ) return;

    // Abort any ongoing request for this form
    if ( buddycRequestMap.has( formId )) {
        const ongoingRequest = buddycRequestMap.get( formId );
        if ( ongoingRequest.readyState !== 4 ) {
            ongoingRequest.abort();
        }
    }

    // Disable submit button
    buddycBookingFormUpdating( false );

    // Initialize an array to store selected service IDs
    var selectedServices = [];

    // Get the parent element of all service options
    const serviceOptionsParent = form;

    // Initialize variable for fee multiplication
    let feeNum = false;

    // Get all service options within the parent element
    const serviceOptions = serviceOptionsParent.querySelectorAll('.service-option');

    // Loop through each element
    serviceOptions.forEach(option => {
        // Make sure the option is not disabled
        if ( ! option.classList.contains( 'service-disabled' ) ) {
            // Check if the option is a checkbox
            if (option.type === 'checkbox' && option.checked) {
                selectedServices.push(option.value); // Push the value to the array
            } else if (option.tagName === 'OPTION' && option.selected) {
                selectedServices.push(option.value); // Push the value to the array
            }
        }
    });

    // Initialize array to store all line items for ajax request
    let lineItemsData = [];

    // No service
    if ( selectedServices.length === 0 ) {
        // Clear field if no service is selected
        jQuery('#hidden-line-items').val('');
        // Update the table
        buddycUpdateLineItemsTable();

        // Re-enable the submit button and reset its text
        buddycBookingFormUpdating( true );
        return;
    }

    // Valid and selected services exist
    if ( selectedServices.length > 0 ) {

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

                // Build line item data array
                let lineItemData = {
                    action: 'buddyc_create_line_item',
                    service_id: option.value,
                    fee_num: feeNum,
                    adjustments: selectedAdjustments,
                    team_id: teamID,
                    team_member_role: roleID,
                    nonce: lineItemsTableData.nonce,
                    nonceAction: lineItemsTableData.nonceAction,
                };

                // Push line item to array
                lineItemsData.push(lineItemData);
            }
        });

        // Create a new AJAX request
        const ajaxRequest = jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'buddyc_create_line_item',
                nonce: lineItemsTableData.nonce,
                nonceAction: lineItemsTableData.nonceAction,
                lineItems: lineItemsData,
            },
            success: function(response) {
                try {
                    const lineItems = JSON.parse(response);
                    if (lineItems) {
                        buddycUpdateLineItemsTable( lineItems );
                    }
                } catch (error) {
                    console.error('Error parsing response JSON:', error);
                }
            },
            error: function(xhr, status, error) {
                // Log error unless request was aborted intentionally
                if ( status !== 'abort' ) {
                    console.error('AJAX request failed:', status, error);
                }
            },            
            complete: function() {
                // Enable submit button
                buddycBookingFormUpdating(true);
                // Remove the request from the map when complete
                buddycRequestMap.delete(formId);
            },
        });

        // Store the request in the map
        buddycRequestMap.set( formId, ajaxRequest );
    }
}

let buddycWorkingMessageInterval = null;

/**
 * Helper function to manage periodic "working" messages.
 * 
 * @param {boolean} start Whether to start or stop the message updates.
 * @param {HTMLCollection} detailsContainers Collection of details containers.
 */
function manageWorkingMessages(start, detailsContainers) {
    // No containers found
    if ( ! detailsContainers || detailsContainers.length === 0 ) {
        return;
    }
    
    // Loop thorugh containers
    for (let i = 0; i < detailsContainers.length; i++) {
        const container = detailsContainers[i];

        // Get working message div
        const workingMessageContainer = container.querySelector('.buddyc-working-message');

        if ( ! workingMessageContainer ) {
            return;
        }

        // Show the workling messages
        if ( start ) {

            // Show container
            workingMessageContainer.style.display = 'block';

            // Define messages
            const messages = [
                'Still working...',
                'Hang tight, almost done!',
                'Processing your request...',
                'Thanks for your patience!'
            ];
            let messageIndex = 0;

            // Clear any existing interval to prevent duplicates
            if ( buddycWorkingMessageInterval ) {
                clearInterval( buddycWorkingMessageInterval );
            }

            // Start updating messages
            buddycWorkingMessageInterval = setInterval(() => {
                workingMessageContainer.textContent = messages[messageIndex];
                messageIndex = (messageIndex + 1) % messages.length; // Loop through messages
            }, 3000); // Update every 3 seconds
        } else {
            // Stop message updates
            clearInterval( buddycWorkingMessageInterval );
            buddycWorkingMessageInterval = null;
            if ( workingMessageContainer ) {
                workingMessageContainer.textContent = '';
            }
        }
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
function buddycBookingFormUpdating(complete = false) {

    const form = document.getElementById('buddyc-booking-form');
    if (!form) {
        return;
    }

    const submitButton = document.getElementById('booking-submit');
    if (!submitButton) {
        return;
    }

    // Define variables
    const submitDisabled = !complete;

    // Cache the original value of the submit button
    if (!submitButton.dataset.originalValue) {
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
                childElements[j].style.display = complete ? 'none' : 'block';
            } else {
                childElements[j].style.display = complete ? 'block' : 'none';
            }
        }
    }

    // Start or stop the working messages
    manageWorkingMessages(!complete, detailsContainers);
}


/**
 * Calls function to update values on changes to the form.
 */
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


