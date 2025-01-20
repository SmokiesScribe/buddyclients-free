/**
 * Handles visibility of dependent service fields.
 * 
 * @since 0.1.0
 * @updated 0.2.0
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('buddyc-booking-form');
        if ( ! form ) return;

        // Cache service options and team member fields
        const serviceOptions = form.querySelectorAll('.service-option');
        const teamFields = form.querySelectorAll('.team-select-field');
        const uploadFields = form.querySelectorAll('.buddyc-file-upload');
        const feeNumFields = form.querySelectorAll('.fee-num-field');

        // Ensure services exist
        if ( ! serviceOptions.length ) return;

        function buddycShowServiceComponentFields() {
            // Initialize role ids array
            const roleIds = [];
            
            // Initialize upload fields array
            let uploadFieldsArray = [];
            
            // Initialize fee numb fields array
            let rateIds = [];
            let selectedServiceIds = [];

            serviceOptions.forEach(option => {
                const serviceId = option.value;
                const rateType = option.getAttribute('data-rate-type');
                const adjustments = option.getAttribute('data-adjustments');
                const roleId = option.getAttribute('data-role-id');
                const uploads = option.getAttribute('data-file-upload');
                const assignedId = option.getAttribute('data-assigned-team-member');
                const isSelected = option.checked || option.selected;

                // Rate count fields
                if ( isSelected ) {
                    // Push ids to arrays
                    rateIds.push( rateType );
                    selectedServiceIds.push( serviceId );
                }

                // Adjustment fields
                if ( adjustments ) {
                    const adjustmentsArray = adjustments.split(',');
                    adjustmentsArray.forEach(adjustmentID => {
                        const adjustmentFieldName = 'adjustment-' + adjustmentID;
                        const adjustmentField = document.getElementById(adjustmentFieldName);
                        if (adjustmentField) {
                            const adjustmentFieldDiv = adjustmentField.closest('.buddyc-form-group-container');
                            adjustmentFieldDiv.style.display = isSelected ? 'block' : 'none';
                        }
                    });
                }
                
                // Create upload fields array
                if ( uploads ) {
                    if ( isSelected ) {
                        // Push service upload types to array
                        uploadFieldsArray.push(...uploads.split(','));
                    }
                }

                // Create role ids array
                if (roleId && ! assignedId && isSelected && ! roleIds.includes( roleId )) {
                    roleIds.push( roleId );
                }
            });
            
            // Upload fields
            if ( uploadFields ) {
                uploadFields.forEach(uploadField => {
                    const elementId = uploadField.id;
                    const uploadFieldDiv = uploadField.closest('.buddyc-file-upload-container');
                    
                    // Get the upload id from the input element
                    const uploadInputElement = uploadField.querySelector('input');
                    const uploadId = uploadInputElement.getAttribute( 'data-upload-id' );
                    
                    if ( uploadId ) {
                        // Check if the number exists in the uploadFieldsArray
                        const inArray = uploadFieldsArray.includes( uploadId );
                            
                        uploadFieldDiv.style.display = inArray ? 'block' : 'none';
                    }
                    
                });
            }
            
            // Fee number fields
            if (feeNumFields) {
                feeNumFields.forEach(feeNumField => {
                    const rateType = feeNumField.getAttribute('data-rate-type');
                    const attach = feeNumField.getAttribute('data-attach');
                    const feeNumFieldDiv = feeNumField.parentElement;

                    // Check if attach to service
                    if (attach === 'service') {
                        serviceOptions.forEach(option => {
                            const serviceId = option.value;
                            const serviceRateType = option.getAttribute('data-rate-type');
                            const isSelected = option.checked || option.selected;

                            const newId = 'fee-number-' + rateType + '-' + serviceId;

                            // Check if the service is selected and matches the rate type
                            const existingField = document.getElementById(newId);
                            if (isSelected && serviceRateType == rateType && !existingField) {
                                // Clone feeNumFieldDiv
                                const clonedDiv = feeNumFieldDiv.cloneNode(true);
                                const clonedField = clonedDiv.querySelector('.fee-num-field');

                                // Append clonedDiv after the original feeNumFieldDiv
                                feeNumFieldDiv.parentNode.insertBefore(clonedDiv, feeNumFieldDiv.nextSibling);

                                // Display and require the clonedDiv
                                clonedDiv.style.display = 'block';
                                clonedField.required = true;

                                // Update legend and field ID/name
                                const legend = clonedDiv.querySelector('legend');
                                if (legend) {
                                    const label = getOptionLabel(option);
                                    legend.textContent += ` - ${label}`;
                                }

                                clonedField.id = newId;
                                clonedField.name = newId;
                            } else if (!isSelected && existingField) {
                                // Service deselected: Hide or remove the cloned field
                                existingField.closest('.buddyc-form-group-container').style.display = 'none';
                                existingField.required = false;
                            }
                        });
                    }

                    // Check if the rate type exists in the array (for non-service attached fields)
                    const inArray = rateIds.includes(rateType);

                    // Display or hide the field
                    feeNumFieldDiv.style.display = inArray && attach !== 'service' ? 'block' : 'none';
                    feeNumField.required = inArray && attach !== 'service';
                });
            }

            // Team member fields
            teamFields.forEach(field => {
                const fieldDiv = field.parentElement;
                const fieldRoleId = field.getAttribute('data-role-id');
                const isRoleSelected = roleIds.includes(fieldRoleId);

                fieldDiv.style.display = isRoleSelected ? 'block' : 'none';
                field.required = isRoleSelected;
                
                // Filter if selected
                if ( isRoleSelected ) {
                    buddycFilterTeamField( field );
                }
            });
        }

        // Event delegation for form inputs and selects
        form.addEventListener('change', function(event) {
            if (event.target.matches('input, select')) {
                buddycShowServiceComponentFields();
            }
        });

        // Initial call
        buddycShowServiceComponentFields();
    });
    
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
        } else if (tagName === 'input') {

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
        
        // Select all elements with the class 'project-filter-field'
        const projectFilterFields = document.querySelectorAll('.project-filter-field');
        
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
            projectFilterFields.forEach(filterField => {
                // Get filter id
                let filterId = filterField.getAttribute('data-filter-id');

                // Check the type of field
                if (filterField.classList.contains('checkbox-options')) {
                    // Initialize array for checkbox values
                    let checkboxValues = [];

                    // Process checkboxes inside this container
                    const checkboxes = filterField.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            const checkboxLabel = getOptionLabel( checkbox );
                            checkboxValues.push(checkboxLabel); // Push the value to the array
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
            promises.push(buddycCheckTeamFilters(teamID, filterValues)
                .then(enabled => {
                    
                    // At least one team member is available
                    if ( teamID && enabled ) {
                        teamAvailable = true;
                    }

                    // Enable or disable option based on the resolved 'enabled' status
                    teamOption.disabled = !enabled;
                    
                    // If the option is disabled, ensure it's deselected
                    if (teamOption.disabled) {
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
            Promise.all(promises).then(() => {
                if (teamFieldContainer && !teamAvailable) {
                    // Check if the paragraph already exists
                    if (!teamFieldContainer.querySelector('.no-team-available')) {
                        // Create a new <p> element
                        const newParagraph = document.createElement('p');
                        
                        // Add a unique class or ID to the new <p> element
                        newParagraph.classList.add('no-team-available');
                        
                        // Set the content of the new <p> element
                        newParagraph.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> No team members available.';
                        
                        // Append the new <p> element to the closest <div> container
                        teamFieldContainer.appendChild(newParagraph);
                    }
                } else if (teamFieldContainer) {
                    // If team members are available, remove the message if it exists
                    const existingParagraph = teamFieldContainer.querySelector('.no-team-available');
                    if (existingParagraph) {
                        teamFieldContainer.removeChild(existingParagraph);
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
        function buddycTeamFiltersMatch(teamID, filterValues) {
            return new Promise((resolve, reject) => {
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'buddyc_team_filter_match',
                        team_id: teamID,
                        filter_values: filterValues,
                        nonce: serviceFieldsData.nonce,
                        nonceAction: serviceFieldsData.nonceAction,
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
        async function buddycCheckTeamFilters(teamID, filterValues) {
            try {
                const enabled = await buddycTeamFiltersMatch(teamID, filterValues);
                return enabled;
            } catch (error) {
                console.error('Error:', error);
                throw error; // Rethrow the error or handle it as needed
            }
        }
    
})();