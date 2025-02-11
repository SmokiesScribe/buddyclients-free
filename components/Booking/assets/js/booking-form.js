/**
 * Anonymously handles line items functions.
 * 
 * Constants (fields and class names) are defined at the top
 * of the anonymous function to avoid repetition and for easy
 * updates.
 * 
 * @since 1.0.21
 */
jQuery(document).ready(function() {

    /**
     * *******************************
     * Variables and constants
     * *******************************
     */

    // Private selectors and constants
    const bookingForm = document.getElementById( 'buddyc-booking-form' );

    // Exit if Booking Form unavailable
    if ( ! bookingForm ) return;

    // Booking Form fields
    const fields = {
        project: document.getElementById( 'buddyc_projects' ),
        items: bookingForm.querySelector( '#hidden-line-items' ),
        services: bookingForm.querySelectorAll('.service-option'),
        submit: bookingForm.querySelector('[data-action="submit"]'),
        confirm: document.querySelectorAll('.confirmation-checkbox'),
        hiddenTotal: bookingForm.querySelector( '#total-fee' ),
        create: document.querySelectorAll('.create-project'),
        projectName: bookingForm.querySelector( '#project_title' ),
        bookedServices: document.getElementById('project-booked-services'),
        team: bookingForm.querySelectorAll('.team-select-field'),
        filter: bookingForm.querySelectorAll('.project-filter-field'),
        uploads: bookingForm.querySelectorAll('.buddyc-file-upload'),
        feeNum: bookingForm.querySelectorAll('.fee-num-field'),
        minimum: bookingForm.querySelector(`[name="minimum-fee"]`), // used
        total: bookingForm.querySelector(`[name="total-fee"]`), // used
    };

    // Booking Form prefixes
    const prefixes = {
        rateType: 'fee-number-',
        adjustment: 'adjustment-option-',
        role: 'role-',
        service: 'service-',
        adjustment: 'adjustment-'
    };

    // Booking Form data att names
    const attNames = {
        attach: 'data-attach',
        rateType: 'data-rate-type',
        adjustments: 'data-adjustments',
        assignedTeam: 'data-assigned-team-member',
        role: 'data-role-id',
        projectName: 'data-project-name',
        dependency: 'data-dependency',
        file: 'data-file-upload',
        filter: 'data-filter-id',
        upload: 'data-upload-id',
        fileRequired: 'data-file_required'
    };

    // Checkout classes
    const classNames = {
        detailsContainer: 'checkout-details-container',
        loadingIndicator: 'checkout-loading-indicator',
        table: 'checkout-table',
        totalFee: 'buddyc-checkout-total-fee',
        formGroup: 'buddyc-form-group-container',
        projectDisplay: 'buddyc-checkout-project',
        uploadContainer: 'buddyc-file-upload-container',
        feeNum: 'fee-num-field',
    };

    // Initialize the request map
    const buddycRequestMap = new Map();

    // Initialize the working interval
    let buddycWorkingMessageInterval = null;

    /**
     * *******************************
     * Triggers and initialization
     * *******************************
     */

    /**
     * Calls function to update values on changes to the form.
     * 
     * @since 1.0.0
     */
    jQuery(document).ready(function($) {
        // Use event delegation to handle change events on dynamically added fields
        jQuery(bookingForm).on('change', 'input, select', function() {
            
            // Exclude project select and confirmation checkboxes
            if ( this !== fields.project && ![...fields.confirm].includes(this) ) {
                buddycUpdateServiceValues();
            }
        });
        
        // Call the function initially to populate the readonly field
        buddycUpdateServiceValues();
    });

    /**
     * Calls function to validate form submissions.
     * 
     * @since 1.0.0
     */
        if ( bookingForm ) {   
            // Listen for form submission
            bookingForm.addEventListener('submit', function( event ) {
                // Call your custom validation function
                buddycValidateBookingSubmission(event);
            });
        }
    

    /**
     * Calls function to update the visibility of project fields.
     * 
     * @since 1.0.21
     */
        // Project selected updated
        jQuery(fields.project).on('change', function() {
            buddycToggleProjectFields();
        });

        // Project name updated
        jQuery(fields.projectName).on('input', function() {
            buddycToggleProjectFields();
        });
        
        // Call the function initially
        buddycToggleProjectFields();

    /**
     * Calls function to populate project fields.
     * 
     * @since 1.0.0
     */
        // Project select option changed
        fields.project.addEventListener( "change", function() {
            // Get the selected option
            var selectedOption = fields.project.options[fields.project.selectedIndex];

            // Extract the group ID from the selected option's value attribute
            var groupId = selectedOption.value;

            // Update fields based on the selected option
            buddycPopulateProjectFields(selectedOption);
        });

        // Run on page load
        const initialSelectedOption = fields.project.options[fields.project.selectedIndex];
        buddycPopulateProjectFields(initialSelectedOption);
        
        // Listen for changes to all form inputs and selects
        bookingForm.addEventListener('change', function(event) {
            if (event.target.matches('input, select')) {
                buddycSelectTeamMembers();
                buddycEnableDependentServices();
            }
        });

    /**
     * Calls function to update the visibility of
     * dependent fields based on the selected servics.
     * 
     * @since 1.0.21
     */
        bookingForm.addEventListener('change', function(event) {
            if (event.target.matches('input, select')) {
                buddycShowServiceComponentFields();
            }
        });

        // Initial call
        buddycShowServiceComponentFields();

    /**
     * *******************************
     * Primary update function
     * *******************************
     */

    /**
     * Updates the line items table.
     * 
     * Initiates the entire update process on changes
     * to the Booking Form.
     * 
     * @since 0.1.0
     */
    function buddycUpdateLineItemsTable( updatedLineItems ) {

        // Get the table body
        const lineItemsTableBody = jQuery(`.${classNames.table} tbody`);

        // Exit if no table found
        if ( ! lineItemsTableBody ) {
            return;
        }

        // Empty the table
        lineItemsTableBody.empty();
        
        // No line items
        if ( ! updatedLineItems ) {
            buddycCalculateTotalFee([0]);
            return;
        }
        
        // Update hidden field
        fields.items.value = JSON.stringify( updatedLineItems );
        
        // Initialize item fees array
        let serviceFees = [];

        // Loop through line items
        updatedLineItems.forEach( function ( lineItem ) {
            
            // Check if the lineItem is an object
            if ( typeof lineItem === 'object' ) {

                // Build the table row html for the item
                let itemHtml = buddycServiceRowHtml( lineItem );

                // Append a row to the table
                lineItemsTableBody.append( itemHtml );

                // Add service fee to array
                serviceFees.push(lineItem.service_fee);
            }
        });

        // Calculate and append the initial total fee
        buddycCalculateTotalFee( serviceFees );
    }

    /**
     * *******************************
     * Total helper functions
     * *******************************
     */

    /**
     * Builds the html for a single row.
     * 
     * @since 1.0.21
     * 
     * @param   {object}    lineItem     The line item object for the single service.
     * @return  {string}    The html string for the row.
     */
    function buddycServiceRowHtml(lineItem) {
        // Using template literals for better readability
        const html = `
            <tr>
                <td>
                    ${lineItem.service_name}<br>
                    <span class="checkout-unit-label">
                        ${lineItem.unit_label}${lineItem.adjustment_label}
                    </span>
                </td>
                <td>$${lineItem.service_fee}</td>
            </tr>
        `;
        return html;
    }

    /**
     * Calculates the total fee based on the line items.
     * 
     * @since 0.1.0
     * 
     * @param   {array} serviceFees     An array of fees for all services.
     */
    function buddycCalculateTotalFee( serviceFees ) {

        // Calculate the total fee by summing up the service fees in the array
        var totalFee = serviceFees.reduce(function(sum, fee) {
            // Remove comma
            var formattedFeeValue = String(fee).replace(/,/g, '');
            
            // Convert fee to a number, or 0 if it's not a valid number
            var feeValue = parseFloat( formattedFeeValue ) || 0;
            
            // Add the fee value to the sum
            return sum + feeValue;
        }, 0);
        
        // Get all total elements
        const totalDisplays = document.querySelectorAll(`.${classNames.totalFee}`);

        // Loop through total elements
        totalDisplays.forEach(totalDisplay => {
            // Set the value of the element to the formatted total
            totalDisplay.textContent = '$' + totalFee.toFixed(2);
        });

        // Update hidden field
        fields.hiddenTotal.value = parseFloat( totalFee );
    }

    /**
     * Update service values.
     * 
     * @since 0.1.0
     * @updated 0.2.3
     */
    function buddycUpdateServiceValues() {

        // Abort any ongoing request for this form
        if ( buddycRequestMap.has( bookingForm.id )) {
            const ongoingRequest = buddycRequestMap.get( bookingForm.id );
            if ( ongoingRequest.readyState !== 4 ) {
                ongoingRequest.abort();
            }
        }

        // Uncheck checkbox
        buddycUncheckConfirmationCheckbox();

        // Disable submit button
        buddycBookingFormUpdating( false );

        // Get the selected services
        let selectedServices = buddycGetSelectedServices();

        // No services selected
        if ( selectedServices.length === 0 ) {
            // Clear hidden line items field
            fields.items.value = '';

            // Update the table
            buddycUpdateLineItemsTable();

            // Re-enable the submit button and reset its text
            buddycBookingFormUpdating( true );
            return;
        }

        // Valid and selected services exist
        if ( selectedServices.length > 0 ) {            

            // Fetch line items data
            const lineItemsData = buddycGetLineItemsData( selectedServices );

            // Create a new AJAX request
            const ajaxRequest = jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'buddyc_create_line_item',
                    nonce: bookingFormData.nonce,
                    nonceAction: bookingFormData.nonceAction,
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
                    buddycRequestMap.delete(bookingForm.id);
                },
            });

            // Store the request in the map
            buddycRequestMap.set( bookingForm.id, ajaxRequest );
        }
    }

    /**
     * Retrieves the services selected on the form.
     * 
     * @since 1.0.21
     * 
     * @return  {array} An array of selected service IDs.
     */
    function buddycGetSelectedServices() {
        // Initialize an array to store selected service IDs
        var selectedServices = [];

        // Loop through each element
        fields.services.forEach(option => {
            // Make sure the option is not disabled
            if ( ! option.classList.contains( 'service-disabled' ) ) {
                // Check if the option is a checkbox
                if (option.type === 'checkbox' && option.checked) {
                    // Push the value to the array
                    selectedServices.push(option.value);
                } else if (option.tagName === 'OPTION' && option.selected) {
                    // Push the value to the array
                    selectedServices.push(option.value);
                }
            }
        });
        return selectedServices;
    }

    /**
     * Fetches line items data based on selected services.
     * 
     * @since 1.0.21
     * 
     * @param   {array} selectedServices    An array of IDs of the selected services.
     */
    function buddycGetLineItemsData( selectedServices ) {
        // Initialize array to store all line items for ajax request
        let lineItemsData = [];

        // Loop through selected services
        fields.services.forEach(option => {

            // Double check that the option is selected and not disabled
            if ( buddycOptionIsActive( option ) ) {

                // Build the data for the option
                let lineItemData = buddycGetServiceOptionData( option );

                // Push line item to array
                lineItemsData.push(lineItemData);
            }
        });

        return lineItemsData;
    }

    /**
     * *******************************
     * Single option functions
     * *******************************
     */

    /**
     * Checks whether the option is active.
     * 
     * @since 1.0.21
     * 
     * @param   {string}  option  The service option element.
     */
    function buddycOptionIsActive( option ) {
        if ( ! option ) {
            return false;
        }

        const disabled = option.classList.contains( 'service-disabled' );
        const selected = option.checked || option.selected;
        
        return ! disabled && selected;
    }

    /**
     * Builds the data for a single service option.
     * 
     * @since 1.0.21
     * 
     * @param   {string}  option  The service option element.
     */
    function buddycGetServiceOptionData( option ) { 

        // Get rate type value
        const feeNum = buddycGetFeeNum( option );

        // Get adjustment data
        const adjustmentsData = buddycGetAdjustments( option );

        // Get role id for the service
        const roleId = option.getAttribute( attNames.role );

        // Get the team id
        const teamId = buddycGetTeamId( option, roleId );

        // Build the array for the single line item
        let lineItemData = {
            action: 'buddyc_create_line_item',
            service_id: option.value,
            fee_num: feeNum,
            adjustments: adjustmentsData,
            team_id: teamId,
            team_member_role: roleId,

            nonce: bookingFormData.nonce,
            nonceAction: bookingFormData.nonceAction,
        };

        return lineItemData;
    }

    /**
     * Retrieves the fee number for a single option.
     * 
     * @since 1.0.21
     * 
     * @param {string} option The option element.
     */
    function buddycGetFeeNum( option ) {
        // Initialize
        let feeNum = false;

        // Get service rate type id
        const rateType = option.getAttribute( attNames.rateType );

        // Get matching rate type field
        const rateTypeField = document.getElementById( prefixes.rateType + rateType);

        // If a matching rate type field exists
        if ( rateTypeField ) {

            // Check whether to attach to project or service
            const attach = rateTypeField.getAttribute( attNames.attach );
            
            // Attach rate type to service
            if ( attach === 'service' ) {

                // Get service id from option value
                const serviceId = option.value;

                // Build service rate field id
                const serviceRateId = prefixes.rateType + rateType + '-' + serviceId;
                const serviceRateField = document.getElementById( serviceRateId );
                
                // Make sure the field exists
                if ( serviceRateField ) {
                    // Get service fee num value
                    feeNum = serviceRateField.value;
                }

            // Attach rate type to project
            } else {
                // Get project fee num value
                feeNum = rateTypeField.value;
            }
        }
        return feeNum;
    }

    /**
     * Retrieves the adjustment data for a single option.
     * 
     * @since 1.0.21
     * 
     * @param {string} option The option element.
     */
    function buddycGetAdjustments( option ) {
       // Initialize
       var selectedAdjustments = [];

       // Get option adjustment ids
       const adjustments = option.getAttribute( attNames.adjustments );

       // Adjustments exist for the service
       if ( adjustments ) {

            // Split ids to array
            const adjustmentArray = adjustments.split(',');

            // Loop through adjustment ids
            adjustmentArray.forEach( adjustmentId => {

                // Get all adjustment fields for the adjustment id
                const adjustmentFieldOptions = document.querySelectorAll( prefixes.adjustment + adjustmentId );

                // Make sure fields were found
                if ( adjustmentFieldOptions ) {

                    // Loop through matching fields
                    adjustmentFieldOptions.forEach(adjustmentOption => {

                        // Make sure the field is selected
                        if (adjustmentOption.selected || adjustmentOption.checked) {

                            // Push the adjustment to the array
                            selectedAdjustments.push(adjustmentOption.value);
                        }
                    });
                }
            });
        }
       return selectedAdjustments;
    }

    /**
     * Retrieves the team ID for a single service option.
     * 
     * @since 1.0.21
     * 
     * @param {string} option The option element.
     * @param {int} roleId The role ID for the service.
     */
    function buddycGetTeamId( option, roleId ) {
        // Get team member
        let teamId = '';

        // Get permanently assigned team id
        const assignedTeamID = option.getAttribute( attNames.assignedTeam );

        // Team member is permanently assigned
        if ( assignedTeamID ) {
            teamId = assignedTeamID;

        // Otherwise get role id
        } else {

            // Ge the role field
            const roleField = document.getElementById( prefixes.role + roleId );

            // Make sure the role field exists
            if ( roleField ) {

                // Get the selected team member
                teamId = roleField.value;

            } else {

                // Invalid role id
                return;
            }
        }
        return teamId;
    }

    /**
     * *******************************
     * HTML helpers
     * *******************************
     */

    /**
     * Manages the periodic "working" messages.
     * 
     * Displays messages below the spinner when the processing
     * takes longer than a few seconds.
     * 
     * @param {boolean} start Whether to start or stop the message updates.
     * @param {HTMLCollection} detailsContainers Collection of details containers.
     */
    function manageWorkingMessages( start, detailsContainers ) {
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
                    'Thanks for your patience!',
                    'Please stay on the page...'
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
    function buddycBookingFormUpdating( complete = false ) {

        // Define variable
        const submitDisabled = ! complete;

        // Make sure submit button exists
        if ( fields.submit ) {

            // Cache the original value of the submit button
            if ( ! fields.submit.dataset.originalValue ) {
                fields.submit.dataset.originalValue = fields.submit.value;
            }

            // Disable the submit button and change its text
            fields.submit.disabled = submitDisabled;
            fields.submit.value = complete ? fields.submit.dataset.originalValue : 'Updating...';
        }

        // Get details containers
        const detailsContainers = document.getElementsByClassName( classNames.detailsContainer );

        // Loop through containers and modify content visibility
        for (let i = 0; i < detailsContainers.length; i++) {
            const childElements = detailsContainers[i].children; // Get all child elements

            // Show loading indicator and hide existing content except for loading indicators
            for (let j = 0; j < childElements.length; j++) {
                if (childElements[j].classList.contains( classNames.loadingIndicator )) {
                    childElements[j].style.display = complete ? 'none' : 'block';
                } else {
                    childElements[j].style.display = complete ? 'block' : 'none';
                }
            }
        }
        // Start or stop the working messages
        manageWorkingMessages( ! complete, detailsContainers );
    }

    /**
     * *******************************
     * Uncheck
     * *******************************
     */

    /**
     * Unchecks the confirmation checkbox.
     * 
     * @since 1.0.0
     */
    function buddycUncheckConfirmationCheckbox() {
        fields.confirm.forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    /**
     * *******************************
     * Project helper functions
     * *******************************
     */

    /**
     * Toggles the visibility of the project fields.
     * 
     * @since 1.0.0
     */
    function buddycToggleProjectFields() {
        // Initialize
        let display = 'none';
        fields.projectName.required = false;
        let projectName = '';
        
        // Check project select value
        if ( fields.project.value === '0' ) {
            display = 'block';
            fields.projectName.required = true;
            projectName = fields.projectName.value;
        } else {
            // Get selected project name
            var selectedOption = fields.project.options[fields.project.selectedIndex];
            if ( selectedOption ) {
                projectName = selectedOption.getAttribute( attNames.projectName );
            }
        }
        
        // Loop through create project fields
        fields.create.forEach(field => {
            // Get field container   
            const fieldDiv = field.closest( '.' + classNames.formGroup );
            
            // Set visibility
            fieldDiv.style.display = display;
        });

        // Update project name
        buddycDisplayProjectName( projectName );
    }

    /**
     * Displays the project name in the checkout table.
     * 
     * @since 1.0.0
     */
    function buddycDisplayProjectName( projectName ) {
        const projectDisplay = document.getElementById( classNames.projectDisplay );
        if ( projectDisplay ) {
            projectDisplay.innerText = projectName;
        }
    }

    /**
     * Populates the project fields.
     * 
     * Updates fields with metadata from selected project.
     * 
     * @since 0.1.0
     */        
    function buddycPopulateProjectFields( selectedOption ) {

        // Creating a new project
        if ( selectedOption.value === '0' ) {

            // Clear all fields
            buddycClearProjectFields();

            // Stop processing
            return;
        }
        
        // Get data attributes for all meta fields
        const projectName = selectedOption.getAttribute( attNames.projectName );
        const projectId = selectedOption.value;
        
        // Update the title field with the group name
        fields.projectName.value = projectName;
        
        // Initialize
        var filterData = false;
        var bookedServices = false;
        var teamData = false;
        
        // Retrieve the project object
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'buddyc_get_project',
                project_id: projectId,
                nonce: bookingFormData.nonce,
                nonceAction: bookingFormData.nonceAction,
            },
            success: function(response) {
                // Parse the JSON response
                var project = JSON.parse( response );
                
                if ( project ) {
                    // Access properties
                    var filterData = project.filter_data;
                    var bookedServices = project.booked_services;
                    var teamData = project.team_data;
                    var lockTeam = project.lock_team;
                    
                    // Update booked services input
                    fields.bookedServices.value = JSON.stringify( bookedServices );
                } else {
                    fields.bookedServices.value = '';
                }
                    
                // Update filter fields
                buddycUpdateFilterFields( filterData );
                
                // Enable dependent services
                buddycEnableDependentServices( bookedServices );
                
                // Select team members
                buddycSelectTeamMembers( teamData, lockTeam );
            }
        });
    }

    /**
     * Clears all the project fields.
     * 
     * @since 1.0.21
     */
    function buddycClearProjectFields() {
        // Clear project title
        fields.projectName.value = '';
        // Clear filter fields
        buddycUpdateFilterFields( false );
        // Update dependent services
        buddycEnableDependentServices( false );
        // Clear booked services input
        fields.bookedServices.value = '';
    }
        
    /**
     * Enables dependent services.
     * 
     * @since 0.2.0
     * 
     * @var array   bookedServices    The previously booked services.
     */
    function buddycEnableDependentServices( bookedServices = null ) {
        
        // Default to hidden field value
        if ( ! bookedServices ) {

            // Check if booked services exist
            if ( fields.bookedServices && fields.bookedServices.value !== '' && fields.bookedServices.value !== 'undefined' ) {

                // Initialize
                let parsedData;

                // Try to parse the booked services
                try {
                    parsedData = JSON.parse(fields.bookedServices.value);
                    bookedServices = parsedData;
                    
                } catch (error) {
                    console.error('Failed to parse JSON:', error);
                    return; // Stop processing
                }
            } else {
                // Default to empty array
                bookedServices = [];
            }
        }

        // Ensure service options exist
        if ( ! fields.services.length ) return;
        
        // Loop through service options
        fields.services.forEach(option => {

            // Get option info
            const serviceId = option.value;
            const dependencies = option.getAttribute( attNames.dependency );
            const isSelected = option.checked || option.selected;
            
            if ( ! dependencies ) {
                return;
            }
            
            // Split dependencies to array
            const dependenciesArray = dependencies.split(',');
            
            // Get dependency fields
            dependenciesArray.forEach(dependencyId => {

                // Construct the name attribute value
                const nameValue = prefixes.service + dependencyId;
                
                // Use querySelector to find the element by name
                const dependencyOption = document.getElementById( nameValue );
                
                if ( dependencyOption ) {
                    // Check if selected
                    let dependencySelected = dependencyOption.checked || dependencyOption.selected;
                    
                    // Don't enable on selection if they're in the same dropdown
                    if ( option.closest('select') ) {
                            // If option is in a dropdown, check if the dependencyOption is also part of the same dropdown
                            const optionParentSelect = option.closest('select');
                            const dependencOptionParentSelect = dependencyOption.closest('select');
                            
                            if ( optionParentSelect === dependencOptionParentSelect ) {
                                dependencySelected = false;
                            }
                    }
                    
                    // Check previously booked services
                    const bookedServicesObj = JSON.stringify( bookedServices );
                            
                    // Check if dependency was previously booked
                    const dependencyBooked = bookedServicesObj.includes( dependencyId );
                    
                    if ( dependencyBooked ) {
                        dependencySelected = true;
                    }
                    
                    // Disable option if dependency is not selected
                    option.disabled = ! dependencySelected;
                }
            });
        });
    }
    
    /**
     * Selects existing team members.
     * 
     * @since 0.2.0
     * 
     * @var array   teamData    The project team data.
     * @var bool    lockTeam    Whether the project team members should be locked.
     */
    function buddycSelectTeamMembers( teamData, lockTeam ) {
        
        // Exit if team members should not be locked
        if ( ! lockTeam ) {
            return;
        }
        
        // Exit if team data not an object
        if ( typeof teamData !== 'object' || teamData === null ) {
            return;
        }
        
        // Loop through team member fields
        fields.team.forEach(field => {

            // Get role data att
            const fieldRoleId = field.getAttribute( attNames.role );
        
            // Check if the role is in the existing team
            if ( fieldRoleId in teamData ) {
                // Access the value associated with the key
                const value = teamData[fieldRoleId];
        
                // Set the value of the field
                field.value = value;
        
                // Disable all options except the selected one
                Array.from(field.options).forEach(option => {
                    option.disabled = option.value != value;
                });
            }
        });
    }
    
    /**
     * Updates filter fields.
     * 
     * @since 0.2.0
     * 
     * @var array   filterData The project filter data.
     */
    function buddycUpdateFilterFields( filterData ) {

        // Make sure filter fields exist
        if ( ! fields.filter ) {
            return;
        }
        
        // Loop through filter fields
        fields.filter.forEach(fieldContainer => {
            
            // Extract the numeric part from the id, assuming it is in the format 'team-filter-field-1234'
            // @todo Hardcoded 'team-filter-field'
            const idMatch = fieldContainer.id.match(/team-filter-field-(\d+)/);
            
            if ( idMatch ) {
                const numericId = idMatch[1]; // Extracted numeric ID as a string
                
                if (filterData && filterData.hasOwnProperty(numericId)) {
                    // If the numeric ID is a key in filterData, update the field with its value
                    const fieldValue = filterData[numericId];

                    // Check the type of field and populate it accordingly
                    if (fieldContainer.classList.contains('checkbox-options')) {
                        
                        // Process checkboxes inside this container
                        const checkboxes = fieldContainer.querySelectorAll('input[type="checkbox"]');

                        // Loop through checkboxes
                        checkboxes.forEach(checkbox => {

                            // Clear all checkboxes initially
                            checkbox.checked = false;
                            
                            if (Array.isArray(fieldValue)) {

                                // Multiple values scenario
                                if (fieldValue.includes(checkbox.value)) {
                                    checkbox.checked = true;
                                }

                            } else if (fieldValue === checkbox.value) {
                                // Single value scenario
                                checkbox.checked = true;
                            }
                        });
                    } else {
                        // Process individual form fields inside this container
                        const field = fieldContainer.querySelector(`#${fieldContainer.id}`);
                        if (field) {
                            switch (field.type) {
                                case 'checkbox':
                                    field.checked = Boolean(fieldValue);
                                    break;
                                case 'radio':
                                    const radios = document.getElementsByName(field.name);
                                    radios.forEach(radio => {
                                        radio.checked = (radio.value === fieldValue);
                                    });
                                    break;
                                case 'select-one':
                                case 'select-multiple':
                                    if (field.multiple && Array.isArray(fieldValue)) {
                                        Array.from(field.options).forEach(option => {
                                            option.selected = fieldValue.includes(option.value);
                                        });
                                    } else {
                                        field.value = fieldValue;
                                    }
                                    break;
                                default:
                                    field.value = fieldValue;
                                    break;
                            }
                        }
                    }
                } else {
                    // If the numeric ID is not a key in filterData, clear the field
                    // Handle clearing the field based on its type
                    if (fieldContainer.classList.contains('checkbox-options')) {
                        // Clear all checkboxes inside this container
                        const checkboxes = fieldContainer.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                    } else {
                        // Clear individual form fields
                        const field = fieldContainer.querySelector(`#${fieldContainer.id}`);
                        if (field) {
                            switch (field.type) {
                                case 'checkbox':
                                    field.checked = false;
                                    break;
                                case 'radio':
                                    const radios = document.getElementsByName(field.name);
                                    radios.forEach(radio => {
                                        radio.checked = false;
                                    });
                                    break;
                                case 'select-one':
                                case 'select-multiple':
                                    if (field.multiple) {
                                        Array.from(field.options).forEach(option => {
                                            option.selected = false;
                                        });
                                    } else {
                                        field.value = ''; // Clear single select or text input
                                    }
                                    break;
                                default:
                                    field.value = ''; // Clear text inputs, textareas, etc.
                                    break;
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * *******************************
     * Dependent fields visibility
     * *******************************
     */

    /**
     * Updates the visibility of dependent fields
     * based on the selected services.
     * 
     * @since 1.0.0
     */
    function buddycShowServiceComponentFields() {
        // Ensure services exist
        if ( ! fields.services.length ) return;

        // Display adjustment fields
        buddycDisplayAdjustments();

        // Display adjustment fields
        buddycDisplayUploads();

        // Display fee number fields
        buddycDisplayFeeNumFields();

        // Display team member fields
        buddycDisplayTeamFields();
    }

    /**
     * Retrieves the ids of all active rate types.
     * 
     * @since 1.0.21
     * 
     * @return  {array} An array of selected rate type IDs.
     */
    function buddycGetSelectedRateTypes() {
        // Initialize an array
        var rateTypes = [];
        // Loop through each element
        fields.services.forEach(option => {
            if ( buddycOptionIsActive( option ) ) {
                // Push rate type id to array
                const rateType = option.getAttribute( attNames.rateType );
                rateTypes.push( rateType );
            }
        });
        return rateTypes;
    }

    /**
     * Displays or hides all adjustment fields
     * based on selected services.
     * 
     * @since 1.0.21
     */
    function buddycDisplayAdjustments() {
        if ( ! fields.services ) {
            return;
        }

       // Loop through service options
       fields.services.forEach(option => {

            // Check if the option is selected
            isSelected = buddycOptionIsActive( option );

            // Get applicable adjustments
            const adjustments = option.getAttribute( attNames.adjustments );

            // Check if adjustments exist
            if ( adjustments ) {

                // Split adjustment ids to array
                const adjustmentsArray = adjustments.split(',');

                // Loop through adjustment ids
                adjustmentsArray.forEach(adjustmentID => {
                    // Define the adjustmnet field id
                    const adjustmentFieldName = prefixes.adjustment + adjustmentID;

                    // Get the adjustment field
                    const adjustmentField = document.getElementById( adjustmentFieldName );

                    // Make sure the field is found
                    if ( adjustmentField ) {
                        // Get the container
                        const adjustmentFieldDiv = adjustmentField.closest( '.' + classNames.formGroup );

                        // Display or hide
                        adjustmentFieldDiv.style.display = isSelected ? 'block' : 'none';
                    }
                });
            }
        });
    }

    /**
     * Displays or hides all upload fields
     * based on selected services.
     * 
     * @since 1.0.21
     */
    function buddycDisplayUploads() {
        // Get active upload fields
        const activeUploads = buddycActiveUploadIds();

        // Make sure upload fields exist
        if ( fields.uploads ) {

            // Loop through all upload fields
            fields.uploads.forEach(uploadField => {
                // Get upload field container
                const uploadFieldDiv = uploadField.closest( '.' + classNames.uploadContainer );
                
                // Get the upload id from the input element
                const uploadInput = uploadField.querySelector( 'input' );
                const uploadId = uploadInput.getAttribute( attNames.upload );
                
                // Upload id exists
                if ( uploadId ) {

                    // Check if the number exists in the uploadFieldsArray
                    const inArray = activeUploads.includes( uploadId );

                    // Show or hide
                    uploadFieldDiv.style.display = inArray ? 'block' : 'none';

                    // Check if the file upload should be required
                    const fileRequired = uploadInput.getAttribute( attNames.fileRequired );

                    // Required is true
                    if ( fileRequired === 'true' ) {
                        // Require if visible
                        uploadInput.required = inArray;
                    }
                }
            });
        }
    }

    /**
     * Retrieves all active upload ids based on
     * selected services.
     * 
     * @since 1.0.21
     * 
     * @return {array} An array of active upload Ids.
     */
    function buddycActiveUploadIds() {
        // Initialize array
        let activeUploads = [];
        // Loop through all service fields
        fields.services.forEach(option => {
            // Check if service option is active
            if ( buddycOptionIsActive( option ) ) {
                // Get uploads
                const uploads = option.getAttribute( attNames.file );
                // Split and push to array
                activeUploads.push(...uploads.split(','));
            }
        });
        return activeUploads;
    }

    /**
     * Retrieves all active role ids based on
     * selected services.
     * 
     * @since 1.0.21
     * 
     * @return {array} An array of active role Ids.
     */
    function buddycGetSelectedRoles() {
        // Initialize array
        let roleIds = [];
        // Loop through all service fields
        fields.services.forEach(option => {
            // Check if service option is active
            if ( buddycOptionIsActive( option ) ) {
                // Get role and assigned id for service
                const roleId = option.getAttribute( attNames.role );
                const assignedId = option.getAttribute( attNames.assignedTeam );    
                // Make sure a role exists, not permanently assigned, and no duplicates
                if ( roleId && ! assignedId && ! roleIds.includes( roleId ) ) {
                    // Push role ids to array
                    roleIds.push( roleId );
                }
            }
        });
        return roleIds;
    }

    /**
     * Displays team member fields based on selected services.
     * 
     * @since 1.0.21
     */
    function buddycDisplayTeamFields() {
        // Get selected role ids
        const roleIds = buddycGetSelectedRoles();

        // Team member fields
        fields.team.forEach(field => {
            const fieldDiv = field.parentElement;
            const fieldRoleId = field.getAttribute( attNames.role );
            const isRoleSelected = roleIds.includes( fieldRoleId );

            fieldDiv.style.display = isRoleSelected ? 'block' : 'none';
            field.required = isRoleSelected;
            
            // Filter if selected
            if ( isRoleSelected ) {
                buddycFilterTeamField( field );
            }
        });
    }

    /**
     * Displays fee number fields based on selected services.
     * 
     * @since 1.0.21
     */
    function buddycDisplayFeeNumFields() {
        // Get array of active rate type ids
        const rateIds = buddycGetSelectedRateTypes();

       // Make sure fee num fields exist
       if ( fields.feeNum ) {
            // Loop through fee num fields
            fields.feeNum.forEach(feeNumField => {

                // Get atts for each fee num field
                const rateType = feeNumField.getAttribute( attNames.rateType );
                const attach = feeNumField.getAttribute( attNames.attach );
                const feeNumFieldDiv = feeNumField.parentElement;

                // Check if attach to service
                if ( attach === 'service' ) {

                    // Loop through the field options
                    fields.services.forEach(option => {

                        // Get option atts
                        const serviceId = option.value;
                        const serviceRateType = option.getAttribute( attNames.rateType );
                        const isSelected = option.checked || option.selected;

                        const newId = prefixes.rateType + rateType + '-' + serviceId;

                        // Check if the service is selected and matches the rate type
                        const existingField = document.getElementById( newId );

                        // Make sure the option is selected and does not already exist
                        if ( isSelected && serviceRateType == rateType && ! existingField ) {

                            // Clone feeNumFieldDiv
                            const clonedDiv = feeNumFieldDiv.cloneNode( true );
                            const clonedField = clonedDiv.querySelector( '.' + classNames.feeNum );

                            // Append clonedDiv after the original feeNumFieldDiv
                            feeNumFieldDiv.parentNode.insertBefore( clonedDiv, feeNumFieldDiv.nextSibling );

                            // Display and require the clonedDiv
                            clonedDiv.style.display = 'block';
                            clonedField.required = true;

                            // Update legend and field ID/name
                            const legend = clonedDiv.querySelector('legend');
                            if ( legend ) {
                                const label = getOptionLabel(option);
                                legend.textContent += ` - ${label}`;
                            }

                            clonedField.id = newId;
                            clonedField.name = newId;

                        } else if ( ! isSelected && existingField ) {
                            // Service deselected: Hide or remove the cloned field
                            existingField.closest( '.' + classNames.formGroup ).style.display = 'none';
                            existingField.required = false;
                        }
                    });
                }

                // Check if the rate type exists in the array (for non-service attached fields)
                const inArray = rateIds.includes( rateType );

                // Display or hide the field
                feeNumFieldDiv.style.display = inArray && attach !== 'service' ? 'block' : 'none';
                feeNumField.required = inArray && attach !== 'service';
            });
        }
    }


















    /**
     * Retrieves an option label.
     * 
     * @since 0.2.4
     * 
     * @param   html    $option     The option whose label to retrieve.
     */
    function getOptionLabel( option ) {
        if ( ! option ) {
            return;
        }
        
        // Get the tag name of the element (in lowercase to standardize)
        var tagName = option.tagName.toLowerCase();
        
        // Check option type
        if ( tagName === 'option' ) {
            // It's a dropdown option
            return option.label;
            
        // It's a checkbox option
        } else if ( tagName === 'input' ) {

            // Select the label element using the `for` attribute that matches the checkbox's `id`
            var checkboxLabel = document.querySelector(`label[for="${option.id}"]`);
            
            // Find the parent element of the checkbox
            var checkboxParent = option.closest('.bp-checkbox-wrap');
            
            // Check if the parent element exists
            if ( checkboxParent ) {
                // Find the label within the parent element
                var newLabel = checkboxParent.querySelector('label');
                
                // Get the label text
                return newLabel ? newLabel.textContent.trim() : '';
            }
        }
    }
    
    /**
     * Filters team member field.
     * 
     * @since 0.2.4
     * 
     * @param   teamField   The field to filter.
     */
    function buddycFilterTeamField( teamField ) {
        
        // Create an array to store all promises for buddycCheckTeamFilters calls
        const promises = [];
        
        // Initialize check
        let teamAvailable = false;
        
        // Get container
        const teamFieldContainer = teamField.closest('div');
        
        // Loop through each team member option
        Array.from(teamField.options).forEach(teamOption => {
            // Get team id
            let teamID = teamOption.value;

            // Initialize filterValues as an object
            let filterValues = {};

            // Loop through each filter field
            fields.filter.forEach(filterField => {
                // Get filter id
                let filterId = filterField.getAttribute( attNames.filter );

                // Check the type of field
                if ( filterField.classList.contains( 'checkbox-options' ) ) {
                    // Initialize array for checkbox values
                    let checkboxValues = [];

                    // Process checkboxes inside this container
                    const checkboxes = filterField.querySelectorAll('input[type="checkbox"]');

                    // Loop through checkbox options
                    checkboxes.forEach(checkbox => {
                        if ( checkbox.checked ) {
                            const checkboxLabel = getOptionLabel( checkbox );
                            checkboxValues.push( checkboxLabel ); // Push the value to the array
                        }
                    });
                    
                    // Assign checkbox values to filterValues using filterId as key
                    filterValues[filterId] = checkboxValues;
                } else {
                    // Assign value of other field types to filterValues using filterId as key
                    filterValues[filterId] = filterField.label;
                }
            });

            // Add the promise to the array of promises
            promises.push(buddycCheckTeamFilters( teamID, filterValues )
                .then(enabled => {
                    
                    // At least one team member is available
                    if ( teamID && enabled ) {
                        teamAvailable = true;
                    }

                    // Enable or disable option based on the resolved 'enabled' status
                    teamOption.disabled = ! enabled;
                    
                    // If the option is disabled, ensure it's deselected
                    if ( teamOption.disabled ) {
                        teamOption.selected = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Handle errors here if needed
                })
            );
        });

        // After processing all team options, append message if no team members available
        promises.push(
            Promise.all( promises ).then(() => {
                if ( teamFieldContainer && ! teamAvailable ) {
                    // Check if the paragraph already exists
                    if ( ! teamFieldContainer.querySelector( '.no-team-available' ) ) {
                        // Create a new <p> element
                        const newParagraph = document.createElement( 'p' );
                        
                        // Add a unique class or ID to the new <p> element
                        newParagraph.classList.add( 'no-team-available' );
                        
                        // Set the content of the new <p> element
                        newParagraph.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> No team members available.';
                        
                        // Append the new <p> element to the closest <div> container
                        teamFieldContainer.appendChild( newParagraph );
                    }
                } else if ( teamFieldContainer ) {
                    // If team members are available, remove the message if it exists
                    const existingParagraph = teamFieldContainer.querySelector('.no-team-available');
                    if ( existingParagraph ) {
                        teamFieldContainer.removeChild( existingParagraph );
                    }
                }
            })
        );
    }
    
    /**
     * Checks whether team filters match.
     * 
     * @since 0.1.3
     * 
     * @param   int     teamID          The ID of the team member to check.
     * @param   object  filterValues    The selected values of the filter fields, keyed by filter id.
     * @returns Promise<boolean>       Resolves to true if filters match, false otherwise.
     */
    function buddycTeamFiltersMatch( teamID, filterValues ) {
        return new Promise( ( resolve, reject ) => {
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'buddyc_team_filter_match',
                    team_id: teamID,
                    filter_values: filterValues,
                    nonce: bookingFormData.nonce,
                    nonceAction: bookingFormData.nonceAction,
                },
                success: function(response) {
                    var match = JSON.parse(response);
                    resolve(match); // Resolve the promise with the match result
                },
                error: function(xhr, status, error) {
                    reject(error); // Reject the promise if there's an error
                }
            });
        });
    }

    /**
     * Asynchronously checks whether team filters match.
     * 
     * @since 0.1.3
     * 
     * @param   int     teamID          The ID of the team member to check.
     * @param   object  filterValues    The selected values of the filter fields, keyed by filter id.
     * @returns Promise<boolean>       Resolves to true if filters match, false otherwise.
     */
    async function buddycCheckTeamFilters( teamID, filterValues ) {
        try {
            const enabled = await buddycTeamFiltersMatch( teamID, filterValues );
            return enabled;
        } catch (error) {
            console.error('Error:', error);
            throw error; // Rethrow the error or handle it as needed
        }
    }

   /**
     * *******************************
     * Submission validation functions
     * *******************************
     */
    
    /**
     * Validates the Booking Form submission.
     * 
     * @since 0.1.0
     */
    function buddycValidateBookingSubmission( event ) {

        // Require services
        if ( ! fields.items.value ) {
            buddycPreventSubmission( event, 'Please select your services.' );
            return;
        }
        
        // Convert values to numbers and handle validation
        var totalValue = parseFloat( fields.total.value );
        var minimumValue = parseFloat( fields.minimum.value );
        
        // Require minimum fee
        if ( totalValue < minimumValue ) {
            buddycPreventSubmission( event, 'Please select services amounting to at least $' + minimumValue + '.' );
            return;
        }
    
        // Require confirmation checkboxes
        fields.confirm.forEach(function ( checkbox ) {
            if ( ! checkbox.checked ) {
                buddycPreventSubmission( event, 'Please check the confirmation box.' );
                return;
            }
        });

        // Validate reCAPTCHA
        buddycRecaptchaSubmit( event, true );
    }
            
    /**
     * Prevents form submission and displays alert.
     * 
     * @since 0.1.0
     * 
     * @param   string  alertMessage  The message to display in the alert.
     */
    function buddycPreventSubmission( event, alertMessage ) {
            event.preventDefault(); // Prevent form submission
            alert( alertMessage );
    }

}); // Close the anonymous function