<?php

namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\Client;
use BuddyClients\Components\Contact\ContactForm;

use BuddyClients\Components\Service\{
    Service,
    ServiceType,
    RateType,
    Role,
    Adjustment,
    FileUpload,
    ServiceCache
};

use BuddyClients\Components\Checkout\CheckoutTable;


/**
 * Booking form content.
 * 
 * Generates the form through which clients book services.
 *
 * @since 0.1.0
 */
class BookingForm {
     
    /**
     * Whether bookings are open.
     * 
     * @var bool
     */
     private $open;
    
    /**
     * Whether self-bookings are allowed.
     * 
     * @var bool
     */
     private $self_bookings;
     
    /**
     * Submit button text.
     * 
     * @var string
     */
     public $submit_text;
     
    /**
     * The ID of the client for this booking.
     * 
     * @var string
     */
     private $user_id;
     
    /**
     * Projects.
     * 
     * All user project groups.
     * 
     * @since 0.1.0
     */
     private $groups;
     
    /**
     * Formatted name to display beside the avatar.
     * 
     * @var string
     */
     private $avatar_name;
     
    /**
     * Nonce object.
     * 
     * @var Nonce
     */
     private $nonce;
     
     /**
      * Whether projects are enabled.
      * 
      * @var bool
      */
     private $projects_enabled = true;
     
    /**
     * Constructor method.
     *
     * @since 0.1.0
     */
    public function __construct() {
        $this->define_variables();
    }
    
    /**
     * Define variables for use in the booking form.
     *
     * @since 0.1.0
     */ 
    private function define_variables() {
        
        // Get client
        $this->define_client();
        
        /**
         * Filters the Booking Form submit text.
         *
         * @since 0.1.0
         *
         * @param string  $submit_text The submit text.
         */
         $this->submit_text = apply_filters( 'buddyc_booking_submit_text', __( 'Go to Checkout', 'buddyclients-free' ) );
        
        // Get user projects
        $this->projects = $this->client_id ? (new Client($this->client_id))->projects : false;
        
        // @todo Check if projects are enabled
        // $this->projects_enabled = buddyc_get_setting( 'booking', 'enable_projects' ) === 'yes';
        
    }
    
    /**
     * Defines client id.
     * 
     * @since 0.1.0
     */
    private function define_client() {
        
        // Check for url param
        $sales_client_id = buddyc_get_param( 'sales_client_id' );
        if ( $sales_client_id ) {
            $this->client_id = $sales_client_id;
            
        // Check if user is logged in
        } else if ( is_user_logged_in() ) {
            $this->client_id = get_current_user_id();
            
        // Otherwise guest
        } else {
            $this->client_id = 'guest';
        }
        
        // Client name
        $this->client_name = $this->client_id !== 'guest' ? bp_core_get_user_displayname( $this->client_id ) : 'Guest';
    }
    
    /**
     * Checks whether self-bookings are allowed.
     * 
     * @since 0.1.0
     */
    private function not_allowed() {
        if ( buddyc_is_admin() || buddyc_is_team() ) {
            return false;
        }
        $contact_form = $this->contact_form();
        $self_bookings = buddyc_get_setting( 'sales', 'self_bookings' );
        return $self_bookings === 'no' ? $contact_form : false;
    }

    /**
     * Builds the contact message when self-bookings are disabled.
     * 
     * @since 1.0.25
     */
    private function contact_form() {
        if ( class_exists( ContactForm::class  ) ) {

            $args = [
                'title'     => __( 'Read to Get Started?', 'buddyclients-free' ),
                'subtitle'  => __( 'Tell us about your project, and we\'ll be in touch soon!', 'buddyclients-free')
            ];

            $contact_form = new ContactForm( $args );
            return $contact_form->build();
        } else {
            sprintf(
                /* translators: %s: the link to email the admin */
                __( 'Please %s to book services.', 'buddyclients-free' ),
                buddyc_contact_message()
            );
        }
    }
    
    /**
     * Outputs a message when no services exist.
     * 
     * @since 0.4.0
     */
    private function no_services() {
        $message = __( 'No services are currently available.', 'buddyclients-free' );
        return $message;
    }
    
    /**
     * Outputs a message when bookings are not open.
     * 
     * @since 0.1.0
     */
    private function is_closed() {
        $message = __( 'We are not currently accepting new bookings.', 'buddyclients-free' );
        $open = buddyc_get_setting('booking', 'accept_bookings');
        return $open !== 'open' ? $message : false;
    }
    
    /**
     * Build booking form.
     *
     * This method generates HTML code for the booking form.
     *
     * @since 0.1.0
     */
    public function build_form() {
        
        // Check if self-booking is allowed.
        if ( $this->not_allowed() ) {
            return $this->not_allowed();
        }
        
        // Check if bookings are open.
        if ( $this->is_closed() ) {
            return $this->is_closed();
        }
        
        // Check if services exist.
        if ( ! buddyc_services_exist() ) {
            return $this->no_services();
        }
        
        // Define form args
        $form_args = [
            'key'                   => 'booking',
            'fields_callback'       => [$this, 'form_fields'],
            'submission_class'      => __NAMESPACE__ . '\BookingFormSubmission',
            'submit_text'           => $this->submit_text,
            'avatar'                => $this->client_id,
            'manual_recaptcha'      => true
        ];
        
        // Open container
        $content = '<div class="buddyc-booking-checkout-container">';
        
        // Open form container
        $content .= '<div class="buddyc-booking-form-column">';
        
        /**
         * Filters the Checkout content before the Booking Form is added
         *
         * @since 0.1.0
         * 
         * @param   string  $content    The content to filter.
         */
         $content = apply_filters( 'buddyc_before_booking_form', $content );
         
        // Display form
        $content .= buddyc_build_form( $form_args );
        
        /**
         * Filters the Checkout content after the Booking Form is added
         *
         * @since 0.1.0
         * 
         * @param   string  $content    The content to filter.
         */
         $content = apply_filters( 'buddyc_after_booking_form', $content );
         
        // Close form container
        $content .= '</div>';
        
        // Table
        $content .= '<div class="buddyc-checkout-fee-column">';
        $content .= $this->line_items_table();
        $content .= '</div>';
        
        // Close container
        $content .= '</div>';
        
        return $content;
    }

    /**
     * Defines field callbacks.
     * 
     * @since 0.1.0
     */
    private function field_callbacks() {

        // Project callbacks
        $project_callbacks = [
            [$this, 'hidden_fields'],
            [$this, 'project_select'],
            [$this, 'project_name'],
            [$this, 'filter_fields'],
        ];
        
        /**
         * Filters the Booking Form project field callbacks.
         * 
         * Callbacks should return an array of args to build the Field.
         *
         * @since 0.1.0
         *
         * @param array  $project_callbacks An associative array of callbacks.
         */
         $project_callbacks = apply_filters( 'buddyc_booking_project_fields', $project_callbacks );
        
        // Services callbacks
        $services_callbacks = [
            [$this, 'services_fields'],
            [$this, 'fee_num_fields'],
            [$this, 'adjustment_fields'],
            [$this, 'file_upload'],
            [$this, 'team_dropdowns'],
        ];
        
        /**
         * Filters the booking form services field callbacks.
         *
         * @since 0.1.0
         *
         * @param array  $services_callbacks An associative array of callbacks.
         */
         $services_callbacks = apply_filters( 'buddyc_booking_services_fields', $services_callbacks );
        
        // Terms callbacks
        $terms_callbacks = [
            [$this, 'terms_checkbox'],
        ];
        
        // Return all callbacks
        return array_merge( $project_callbacks, $services_callbacks, $terms_callbacks );
    }

    
    /**
     * Form fields callback.
     * 
     * @since 0.1.0
     */
    public function form_fields() {
        
        // Initialize
        $all_args = [];
        
        // Loop through callbacks and add to args
        foreach ( $this->field_callbacks() as $callback ) {
            // Check if the method exists before calling it
            if ( is_callable( $callback ) ) {
                $args = call_user_func( $callback );
                
                // Make sure it's an array
                if ( is_array( $args ) ) {
                    // Single field args
                    if ( isset( $args['type'] ) ) {
                        $all_args[] = $args;
                    } else {
                        // Multiple field args
                        foreach ( $args as $single_args ) {
                            if ( isset( $single_args['type'] ) ) {
                                $all_args[] = $single_args;
                            }
                        }
                    }
                }
            }
        }
        return $all_args;
    }
    
    /**
     * Adds a hook used for custom quote fields.
     * 
     * @since 0.1.0
     */
    private function quote_hook() {
        /**
         * Fires before the service fields inside the BookingForm.
         * 
         * @since 0.1.0
         * 
         * @param object $booked_service    The BookedService object.
         */
        do_action('buddyc_booking_after_services', $this);
    } 

    /**
     * Hidden fields.
     * 
     * @since 0.1.0
     * @updated 0.2.0
     */
     private function hidden_fields() {
         
         // Initialize
         $args = [];

         // Define values
         $sales_id = buddyc_get_param( 'sales_id' );
         $prev_paid = buddyc_get_param( 'prev_paid' );

         $sales_client_email = buddyc_get_param( 'sales_client_email' );         
         $user_email = ! empty( $sales_client_email ) ? sanitize_email( $sales_client_email ) : bp_core_get_user_email( $this->client_id );
         
         // Define fields
            $hidden_fields = [
             'user-id'                  => $this->client_id,
             'sales-id'                 => $sales_id,
             'previously-paid'          => $prev_paid,
             'user-email'               => $user_email,
             'hidden-line-items'        => '',
             'total-fee'                => '',
             'minimum-fee'              => buddyc_get_setting('booking', 'minimum_fee'),
             'project-team-members'     => '',
             'project-booked-services'  => '',
         ];
         
         // Loop through and build fields
         foreach ($hidden_fields as $key => $value) {
             $args[] = [
                'type' => 'hidden',
                'key' => $key,
                'value' => $value,
            ];
         }
         
         return $args;
     }
     
    /**
     * Project dropdown.
     * 
     * @since 0.1.0
     */
     private function project_select() {
        
        // Initialize options array with a default option to create a new project
        $project_options[0] = [
            'label' => __( 'Create a New Project', 'buddyclients-free' ),
            'value' => 0,
        ];
        
        if ( $this->projects ) {
            // Add each group to the options array
            foreach ( $this->projects as $project ) {
                
                $project_options[$project->ID] = [
                    'label' => $project->name,
                    'value' => $project->ID,
                    'data_atts' => [
                        'project-name'      => $project->name,
                        'filter-data'       => is_array( $project->filter_data ) ? wp_json_encode( $project->filter_data ) : '',
                        'booked-services'   => serialize( $project->filter_data ),
                        'team-members'      => serialize( $project->team_data ),
                    ]
                ];
            }
        }
        
        // Build arguments for the project select field
        return [
            'key' => 'buddyc_projects',
            'type' => 'dropdown',
            'label' => 'Select Your Project',
            'description' => 'Or create a new project.',
            'options' => $project_options, // Pass the generated options array
        ];
     }
     
     /**
      * Create project fields.
      * 
      * @since 0.1.0
      */
      private function project_name() {
         
        // Project title
        return [
            'key'               => 'project_title',
            'type'              => 'text',
            'label'             => __( 'Project Title', 'buddyclients-free' ),
            'field_classes'     => 'create-project',
        ];
    }
        
    /**
     * Xprofile filter fields.
     *
     * @since 0.1.0
     */
    private function filter_fields() {
         
        // Initialize
        $args = [];
        
        // Get filter field posts
        $filter_fields = buddyc_post_query( 'buddyc_filter' );
        
        // Exit if no filter fields
        if ( $filter_fields ) {
            // Loop through filter fields
            foreach ( $filter_fields as $filter_field ) {
                $args[] = ( new FilterField( $filter_field->ID ) )->args();
            }
        }
        
        return $args;
    }
    
    /**
     * Services fields.
     *
     * @since 0.1.0
     */
    private function services_fields() {
        
        // Initialize
        $args = [];
        
        // Get all service types
        $service_types = buddyc_post_query( 'buddyc_service_type' );
        
        // Exit if no service types exist
        if ( ! $service_types ) {
            return;
        }
        
        // Loop through each type
        foreach ( $service_types as $type ) {
            
            // Service type object
            $service_type = buddyc_get_service_cache( 'service_type', $type->ID );
            
            // Get services by type
            $args = ['meta' => ['service_type' => $service_type->ID]];
            $services = buddyc_post_query( 'buddyc_service', $args );
            
            // Exit if no services have the type
            if ( ! $services ) {
                return;
            }
            
            // Initialize options
            $options = [];
            if ($service_type->form_field_type === 'dropdown') {
                $options[] = [
                    'label' => sprintf(
                        /* translators: %s: the name of the service */
                        __( 'Select Your %s Service', 'buddyclients-free' ),
                        $service_type->title
                    ),
                    'value' => '',
                ];
            }
            
            // Loop through services
            foreach ( $services as $service ) {
                
                // New service object
                $service = buddyc_get_service_cache( 'service', $service->ID );
                
                // Skip if invalid or hidden
                if ( $service->validate() != 'valid' || $service->visible != 'visible' ) {
                    continue;
                }

                // Check for freelancer mode
                $freelancer = buddyc_freelancer_id();
                
                // Add to the options array
                $options['service-' . $service->ID] = [
                    'label' => $service->title,
                    'value' => $service->ID,
                    'classes' => 'service-option',
                    'data_atts' => [
                        'role-id' => $service->team_member_role,
                        'rate-type' => $service->rate_type,
                        'dependency' => is_array($service->dependency) ? implode(',', $service->dependency) : '',
                        'adjustments' => is_array($service->adjustments) ? implode(',', $service->adjustments) : '',
                        'file-upload' => is_array($service->file_uploads) ? implode(',', $service->file_uploads) : '',
                        'assigned-team-member' => $freelancer ?? ( $service->assigned_team_member ?? '' ),
                    ]
                ];
            }
            
            // Build form if service options are not empty
            if (!empty($options)) {
                
                // Build help link
                $help_link = $service_type->help_post_id ? buddyc_help_link( $service_type->help_post_id ) : '';
                
                // Build service label
                $service_label = $service_type->form_field_type === 'checkbox' ? 'services' : 'service';
                
                // Define field args
                $args[] = [
                    'key' => 'service-field-' . $service_type->ID,
                    'type' => $service_type->form_field_type,
                    'label' => $service_type->title,
                    'description' => 'Select your ' . strtolower($service_type->title) . ' ' . $service_label . '. ' . $help_link, // @todo Add help button.
                    'options' => $options,
                ];             
            }
            
        }
        return $args;
    }

    /**
     * Fee number fields.
     *
     * @since 0.1.0
     */
    private function fee_num_fields() {
        
        // @todo create separate fields for service attach
        
        // Initialize
        $args = [];
        
        // Get rate types
        $rate_types = buddyc_post_query( 'buddyc_rate_type' );
    
        if ($rate_types) {
            // Loop through rate types
            foreach ($rate_types as $rate_type_post) {
                
                // New rate type object
                $rate_type = buddyc_get_service_cache( 'rate_type', $rate_type_post->ID );
                
                // Skip flat rate services
                if ($rate_type->ID === 'flat') {
                    continue;
                }
                    
                // Build field
                $args[] = [
                    'key'           => 'fee-number-' . $rate_type->ID,
                    'field_classes' => 'fee-num-field',
                    'type'          => 'number',
                    'label'         => $rate_type->plural,
                    'description'   => $rate_type->form_description,
                    'minimum'       => $rate_type->minimum,
                    'data_atts'     => [
                        'service_ids'   => implode(',', $rate_type->service_ids),
                        'attach'        => $rate_type->attach,
                        'rate-type'     => $rate_type->ID
                    ],
                    'hide'          => true
                ];
            }
        }
        return $args;
    }
    
    /**
     * Adjustment fields.
     *
     * @since 0.1.0
     */
    private function adjustment_fields() {
        
        // Initialize
        $args = [];
        
        // Get adjustments
        $adjustments = buddyc_post_query( 'buddyc_adjustment' );
    
        if ( $adjustments ) {
            // Loop through adjustments
            foreach ($adjustments as $adjustment_post) {
                
                // New adjustment object
                $adjustment = buddyc_get_service_cache( 'adjustment', $adjustment_post->ID );
                
                // Exit if no options
                if ( ! is_array( $adjustment->options ) ) {
                    return;
                }
                
                // Initialize options
                $options = [];
                if ( $adjustment->form_field_type === 'dropdown' ) {
                    $options[] = [
                        'label' => sprintf(
                            /* translators: %s: the name of the rate adjustment */
                            __( 'Select %s', 'buddyclients-free' ),
                            $adjustment->label
                        ),
                        'value' => '',
                    ];
                }
                
                // Build options
                foreach ( $adjustment->options as $option_id => $option_object ) {
                    
                    $options[$option_id] = [
                        'label' => $option_object->label,
                        'value' => $option_id,
                        'classes' => 'adjustment-option-' . $adjustment->ID,
                        'data_atts' => [
                            'operator'          => $option_object->operator,
                            'value'             => $option_object->value,
                            'name'              => $option_object->label,
                            'class'             => $option_id,
                        ]
                    ];
                }
                
                // Build help link
                $help_link = $adjustment->help_post_id ? ' ' . buddyc_help_link( $adjustment->help_post_id ) : '';
                    
                // Build field
                $args[] = [
                    'key'           => 'adjustment-' . $adjustment->ID,
                    'type'          => $adjustment->form_field_type,
                    'label'         => $adjustment->label,
                    'field_classes' => 'adjustment-field',
                    'description'   => $adjustment->field_description . $help_link,
                    'options'       => $options,
                    'data_atts' => [
                        'service_ids' => implode(',', $adjustment->service_ids),
                    ],
                    'hide'          => true
                ];
            }
        }
        return $args;
    }
    
    /**
     * File upload field.
     *
     * @since 0.1.0
     */
    private function file_upload() {
        
        // Initialize
        $args = [];
        
        // Get upload types
        $file_uploads = buddyc_post_query( 'buddyc_file_upload' );
    
        if ($file_uploads) {
            // Loop through adjustments
            foreach ($file_uploads as $upload_post) {
                
                // New rate type object
                $upload = buddyc_get_service_cache( 'file_upload', $upload_post->ID );
                
                // Build help link
                $help_link = $upload->help_post_id ? ' ' . buddyc_help_link( $upload->help_post_id ) : '';
        
                // Build field
                $args[] = [
                    'key'           => 'booking-upload-' . $upload_post->ID,
                    'type'          => 'upload',
                    'label'         => $upload->field_label,
                    'description'   => $upload->form_description . $help_link,
                    'file_types'    => $upload->file_types,
                    'multiple_files'=> $upload->multiple_files,
                    'field_classes' => 'buddyc-upload-field',
                    'data_atts' => [
                        'service_ids'   => implode(',', $upload->service_ids),
                        'file_required' => $upload->required,
                        'upload-id'     => $upload_post->ID
                    ],
                    'hide'          => true
                ];
            }
        }
            
        return $args;
    }
    
    /**
     * Builds line items table.
     * 
     * @since 0.1.0
     */
    private function line_items_table() {
        return (new CheckoutTable())->build();
    }
    
    /**
     * Team select fields.
     *
     * @since 0.1.0
     * 
     * @todo Check for contracts, availability, and existing team. And filter fields!
     */
    private function team_dropdowns() {
        
        // Initialize
        $args = [];
        
        // Get roles
        $roles = buddyc_post_query( 'buddyc_role' );
        
        // Get xprofile field
        $xprofile_id = buddyc_roles_field_id();
        
        // Get all team
        $team_members = buddyc_all_team();
    
        if ( $roles ) {
            // Loop through roles
            foreach ( $roles as $role_post ) {
                
                // New rate type object
                $role = buddyc_get_service_cache( 'role', $role_post->ID );
                
                // Initialize options
                $team_options = [
                    '' => [
                        'label' => sprintf(
                            /* translators: %s: the singular name of the team member role (e.g. Editor) */
                            __( 'Select Your %s', 'buddyclients-free' ),
                            $role->singular
                        ),
                        'value' => '',
                    ]
                ];

                // Check whether to require legal agreement
                $require_agreement = buddyc_get_setting( 'legal', 'require_agreement' ) == 'yes';
                        
                // Loop through team members
                foreach ( $team_members['users'] as $team_member ) {
                    $add = false;
                    $user_roles = xprofile_get_field_data( $xprofile_id, $team_member->ID );
                    
                    // Check team member agreement
                    if ( function_exists( 'buddyc_user_legal' ) && $require_agreement ) {
                        
                        // Build legal object
                        $user_legal = buddyc_user_legal( $team_member->ID, 'team' );
                        
                        // Skip if team agreement is not active
                        if ( ! $user_legal->agreement_status ) {
                            continue;
                        }
                    }
                    
                    // Check if the role is in the user roles array
                    if ((is_array($user_roles) && in_array($role->singular, $user_roles))
                        || ($role->singular === $user_roles)) {
                            
                        $availability = function_exists( 'buddyc_get_availability' ) ? buddyc_get_availability( $team_member->ID ) : '';
                        $availability_message = $availability ? sprintf(
                            ' - %s',
                            __( 'Available', 'buddyclients' )
                        ) : '';
                                                    
                        $team_options[$role->ID . '-' . $team_member->ID] = [
                            'label'     => $team_member->display_name . $availability_message,
                            'value'     => $team_member->ID,
                            'classes'   => 'team-option',
                        ];
                    }
                }
                    
                // Build field
                $args[] = [
                    'key'           => 'role-' . $role->ID,
                    'field_classes' => 'team-select-field',
                    'type'          => 'dropdown',
                    'label'         => $role->plural,
                    'description'   => sprintf(
                        /* translators: %s: the singular name of the team member role (e.g. editor) */
                        __( 'Select your %s.', 'buddyclients-free' ),
                        strtolower( $role->singular )
                    ) . buddyc_team_select_help(),
                    'options'       => $team_options,
                    'data_atts'     => [
                        'role-id' => $role->ID,
                    ],
                    'hide'          => true
                ];
            }
        }
        return $args;
    }
    
    /**
     * Terms or confirmation checkbox.
     *
     * @since 0.1.0
     */
    private function terms_checkbox() {
        
        if ( buddyc_get_param( 'sales_id' ) ) {
            return;
        }
        
        // Initialize
        $service_agreement_id = '';
        $option_label = __( 'I confirm that the information above is correct.', 'buddyclients-free' );
        
        // Check for service agreement
        $service_agreement_id = buddyc_get_setting('legal', 'client_legal_version');
        
        if ( $service_agreement_id ) {
            $option_label = sprintf(
                /* translators: %s: the terms being agreed to (e.g. service terms) */
                __( 'I agree to the %s.', 'buddyclients-free' ),
                buddyc_help_link( $service_agreement_id, __( 'service terms', 'buddyclients-free' ) )
            );
        }
            
        // Build field
        return [
            'key'           => 'terms-checkbox',
            'type'          => 'checkbox',
            'label'         => '',
            'description'   => '',
            'options'       => [
                'terms-option' => [
                    'label' => $option_label,
                    'value' => $service_agreement_id,
                    'classes' => 'confirmation-checkbox'
                ]
            ],
        ];
    }
}
