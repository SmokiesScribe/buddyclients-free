/**
 * Set create project fields visibility.
 * 
 * @since 0.1.0
 * 
 */
(function() {
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('bc-booking-form');
        if ( ! form ) return;
        
        const projectSelect = document.getElementById('bc_projects');
        const fieldsToToggle = document.querySelectorAll('.create-project');
        const projectName = document.getElementById('project_title');
    
        // Function to toggle the visibility of the fields
        function toggleFieldsVisibility() {
            let display = 'none';
            projectName.required = false;
            
            // Check project select value
            if (projectSelect.value === '0') {
                display = 'block';
                projectName.required = true;
            }
            
            // Loop through create project fields
            fieldsToToggle.forEach(field => {
                // Get field container   
                const fieldDiv = field.closest('.bc-form-group-container');
                
                // Set visibility
                fieldDiv.style.display = display;
                
            });
        }

        // Initial call to set visibility based on the select value
        toggleFieldsVisibility();
    
        // Listen for changes in the select field
        projectSelect.addEventListener('change', toggleFieldsVisibility);
        
        // Listen for changes in the projectName field
        projectName.addEventListener('input', toggleFieldsVisibility);
    });
})();


/**
 * Populates create project fields
 * 
 * Updates fields with metadata from selected project.
 * 
 * @since 0.1.0
 */
(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.getElementById('bc-booking-form');
        if ( ! form ) return;
        
        const titleElement = document.getElementById('project_title');
        const projectSelect = document.getElementById('bc_projects');
        var bookedServicesInput = document.getElementById('project-booked-services');
    
        // Function to update fields based on the selected option
        function updateFieldsBasedOnSelectedOption(selectedOption) {
            if (selectedOption.value === '0') {
                // Clear project title
                titleElement.value = '';
                // Clear filter fields
                updateFilterFields( false );
                // Update dependent services
                enableDependentServices( false );
                // Clear booked services input
                bookedServicesInput.value = '';
                return;
            }
            
            // Get data attributes for all meta fields
            const projectName = selectedOption.getAttribute('data-project-name');
            const projectId = selectedOption.value;
            
            // Update the title field with the group name
            titleElement.value = projectName;
            
            // Initialize
            var filterData = false;
            var bookedServices = false;
            var teamData = false;
            
            // Retrieve the project object
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'bc_get_project',
                    project_id: projectId,
                    nonce: creatProjectFieldsData.nonce,
                    nonceAction: creatProjectFieldsData.nonceAction,
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
                        bookedServicesInput.value = JSON.stringify( bookedServices );
                    } else {
                        bookedServicesInput.value = '';
                    }
                        
                    // Update filter fields
                    updateFilterFields( filterData );
                    
                    // Enable dependent services
                    enableDependentServices( bookedServices );
                    
                    // Select team members
                    selectTeamMembers( teamData, lockTeam );
                }
            });
        }
    
        // Add an event listener to detect when an option is selected
        projectSelect.addEventListener("change", function() {
            // Get the selected option
            var selectedOption = projectSelect.options[projectSelect.selectedIndex];
    
            // Extract the group ID from the selected option's value attribute
            var groupId = selectedOption.value;
    
            // Update fields based on the selected option
            updateFieldsBasedOnSelectedOption(selectedOption);
        });
    
        // Trigger the 'change' event for the initially selected option when the page loads
        const initialSelectedOption = projectSelect.options[projectSelect.selectedIndex];
        updateFieldsBasedOnSelectedOption(initialSelectedOption);
        
        // Listen for changes to all form inputs and selects
        form.addEventListener('change', function(event) {
            if (event.target.matches('input, select')) {
                selectTeamMembers();
                enableDependentServices();
            }
        });
        
        /**
         * Enables dependent services.
         * 
         * @since 0.2.0
         * 
         * @var array   bookedServices    The previously booked services.
         */
        function enableDependentServices( bookedServices = null ) {
            
            // Default to hidden field value
            if ( ! bookedServices ) {
                const bookedServicesInput = document.getElementById('project-booked-services');
                if ( bookedServicesInput && bookedServicesInput.value !== '' && bookedServicesInput.value !== 'undefined' ) {
                    let parsedData;
                    try {
                        parsedData = JSON.parse(bookedServicesInput.value);
                        bookedServices = parsedData;
                        
                    } catch (error) {
                        console.error('Failed to parse JSON:', error);
                        return; // Stop processing
                    }
                } else {
                    bookedServices = []; // Or set to {} if you expect an object
                }
            }
            
            // Get all service options
            const serviceOptions = form.querySelectorAll('.service-option');
    
            // Ensure services exist
            if ( ! serviceOptions.length ) return;
            
            // Loop through service options
            serviceOptions.forEach(option => {
                const serviceId = option.value;
                const dependencies = option.getAttribute('data-dependency');
                const isSelected = option.checked || option.selected;
                
                if ( ! dependencies ) {
                    return;
                }
                
                // Split dependencies to array
                const dependenciesArray = dependencies.split(',');
                
                // Get dependency fields
                dependenciesArray.forEach(dependencyId => {
                    // Construct the name attribute value
                    const nameValue = `service-${dependencyId}`;
                    
                    // Use querySelector to find the element by name
                    const dependencyOption = document.getElementById(nameValue);
                    
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
        function selectTeamMembers( teamData, lockTeam ) {
            
            // Exit if team members should not be locked
            if ( ! lockTeam ) {
                return;
            }
            
            // Exit if team data not an object
            if ( typeof teamData !== 'object' || teamData === null ) {
                return;
            }
            
            // Get team member fields
            const teamFields = form.querySelectorAll('.team-select-field');
            
            // Loop through team member fields
            teamFields.forEach(field => {
                const fieldRoleId = field.getAttribute('data-role-id');
            
                // Check if the role is in the existing team
                if (fieldRoleId in teamData) {
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
        function updateFilterFields( filterData ) {
            
            // Select all elements with the class 'project-filter-field'
            const projectFilterFields = document.querySelectorAll('.project-filter-field');
            
            projectFilterFields.forEach(fieldContainer => {
                // Extract the numeric part from the id, assuming it is in the format 'team-filter-field-1234'
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
    });
})();