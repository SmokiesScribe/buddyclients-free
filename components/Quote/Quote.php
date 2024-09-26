<?php
namespace BuddyClients\Components\Quote;

use BuddyClients\Components\Service\ServiceType as ServiceType;

/**
 * Custom quote data.
 * 
 * Handles a single custom quote.
 * Retrieves post data and validates the quote.
 *
 * @since 0.1.0
 */
class Quote {
    
    /**
     * The post ID.
     * 
     * @var int
     */
     public $ID;
     
    /**
     * The post object.
     * 
     * @var object
     */
     private $post;
     
    /**
     * The post title.
     * 
     * @var string
     */
     public $title;
     
    /**
     * The post name.
     * 
     * @var string
     */
     public $slug;
     
    /**
     * Service type.
     * 
     * @var string
     */
    public $service_type;
    
    /**
     * The post ID of the associated team member role.
     * 
     * @var int
     */
    public $team_member_role;
    
    /**
     * The value of the service fee.
     * 
     * @var float
     */
    public $rate_value;
    
    /**
     * The post ID of the associated rate type.
     * 
     * @var int
     */
    public $rate_type;
    
    /**
     * The team member percentage in whole numbers.
     * 
     * @var int
     */
    public $team_member_percentage;
    
    /**
     * An array of associated adjustment post IDs.
     * 
     * @var array
     */
    public $adjustments;
    
    /**
     * The post IDs for all brief types required with this service.
     * 
     * @var array
     */
    public $brief_type;
    
    /**
     * Dependencies.
     * 
     * The post IDs of services that should be booked before this service.
     * 
     * @var array
     */
    public $dependency;
    
    /**
     * Whether a file upload is required.
     * 
     * @todo work on this
     * 
     * @var bool
     */
    public $file_uploads;
    
    /**
     * Assigned team member.
     * 
     * Optional. The permanently assigned team member.
     * 
     * @var int
     */
    public $assigned_team_member;
    
    /**
     * The order to display the service.
     * 
     * Optional. Higher numbers are shown first.
     * 
     * @var int
     */
    public $order;
    
    /**
     * Whether a file upload is required.
     * 
     * @var bool
     */
    public $file_required;
    
    /**
     * Whether to hide the service from the booking form.
     * 
     * @var bool
     */
    public $hide;
    
    /**
     * Whether the service is valid.
     * 
     * 'valid' on success, false on failure.
     * 
     * @var string|bool
     */
    public $valid;
    
    /**
     * Formatted string of dependency IDs.
     * 
     * @var string
     */
    public $dependencies_string;
    
    /**
     * The ID of the client.
     * 
     * @var int
     */
    public $client_id;
    
    /**
     * The ID of the project.
     * 
     * @var int
     */
    public $project_id;
     
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param int    $post_id The ID of the service post.
     */
    public function __construct( $post_id ) {
        
        // Get post from param
        $this->ID = $post_id;
        $this->post = get_post( $post_id );
        
        // All post vars
        $this->get_var();
        
        // Validate the quote
        $this->validate();
    }
    
    /**
     * Retrieves post variables.
     *
     * @since 0.1.0
     */
     private function get_var() {
        
        if ($this->post) {
            $this->title = $this->post->post_title;
            $this->slug = $this->post->name;
            $this->get_meta();
        }
    }
    
    /**
     * Retrieves post meta values.
     * 
     * @since 0.1.0
     */
     private function get_meta() {
        
        // Get all meta data
        $meta_data = get_post_meta($this->ID);
        
        if ($meta_data) {
            // Loop through the meta data
            foreach ($meta_data as $key => $value) {
                // Check if the value is serialized
                $unserialized_value = maybe_unserialize($value[0]);
        
                // Assign the unserialized value to the object property
                $this->{$key} = is_array($unserialized_value) ? $unserialized_value : $value[0];
            }
        }
        return $this;
     }
    
    /**
     * Validates the custom quote.
     * 
     * @since 0.1.0
     */
     private function validate() {
         
        // Initialize
        $error = array();
        $valid = true;
        $visible = true;
        
        // Check for client id
        if ( ! $this->client_id || $this->client_id === '' ) {
            $error[] = __('You must select a client.', 'buddyclients' );
            $valid = false;
        }
        
        // Check for team member role
        if ( ! bc_freelancer_mode() ) {
            if ( ! $this->team_member_role || $this->team_member_role === '' ) {
                $error[] = __( 'Team Member Role is required.', 'buddyclients' );
                $valid = false;
            }
        }
        
        // Check for paid service without stripe
        if ( $this->rate_value > 0 ) {
            if ( ! bc_component_exists( 'Stripe' ) ) {
                $error[] = sprintf(
                    __( '<a href="%s" target="_blank">Upgrade BuddyClients</a> to offer paid services.', 'buddyclients' ),
                    esc_url( bc_upgrade_url() )
                );
                $valid = false;
            } else if ( ! bc_component_enabled( 'Stripe' ) ) {
                $error[] = sprintf(
                    __( '<a href="%s">Enable the Stripe component</a> to offer paid services.', 'buddyclients' ),
                    esc_url( bc_enable_component_url() )
                );
                $valid = false;
            }
        }
        
        // Generate error string
        $error_string = implode('<br>', $error);
        $error_message = bc_admin_icon('error') . ' ' . $error_string;
        
        // Check if service or service type is hidden
        if ($this->hide || (new ServiceType($this->service_type))->hide) {
            $visible = false;
        }
        
        // Define values
        $valid_value = $valid ? 'valid' : $error_message;
        $visible_value = $valid && $visible ? 'visible' : false;

        // Set meta
        update_post_meta( $this->ID, 'valid', $valid_value );
        update_post_meta( $this->ID, 'visible', $visible_value );
     }
}