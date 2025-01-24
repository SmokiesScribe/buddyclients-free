<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A single service.
 * 
 * Retrieves data for a single service, including adjustments, roles,
 * rate types, file uploads, briefs, dependencies, and other information.
 *
 * @since 0.1.0
 */
class Service {
    
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
     * An array of associated file upload post IDs.
     * 
     * @var array
     */
    public $file_uploads;
    
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
    public $manuscript_required;
    
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
     * Formatted string of dependency IDs.
     * 
     * @var string
     */
    public $dependencies_string;

    /**
     * Whether the service is visible.
     * 'Visible' if visible.
     * 
     * @var string
     */
    public $visible;

     
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param   int    $post_id The ID of the service post.
     */
    public function __construct( $post_id ) {
        
        // Get post
        $this->ID = $post_id;

        // All post vars
        $this->get_var( $post_id );

    }
    
    /**
     * Retrieves post variables.
     *
     * @since 0.1.0
     * 
     * @param   int     $post_id    The ID of the post.
     */
     private function get_var( $post_id ) {
         
        // Get the post object
        $post = get_post( $post_id );
        
        // Make sure the post exists
        if ( $post ) {
            
            // Get post info
            $this->title = $post->post_title;
            $this->slug = $post->name;
            $this->get_meta( $post_id );
        }
    }
    
    /**
     * Retrieves post meta values.
     * 
     * @since 0.1.0
     * 
     * @param   int     $post_id    The ID of the post.
     */
     private function get_meta( $post_id ) {
        
        // Get all meta data
        $meta_data = get_post_meta( $post_id );
        
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
     * Validates the service.
     * 
     * @since 0.1.0
     */
     public function validate() {
         
        // Initialize
        $error = array();
        $valid = true;
        $visible = true;
        
        // Check for team member role
        if ( ! buddyc_freelancer_mode() ) {
            if ( ! $this->team_member_role || $this->team_member_role === '' ) {
                $error[] = __( 'Team Member Role is required.', 'buddyclients-free' );
                $valid = false;
            }
        }
        
        // Check for service type
        if ( ! $this->service_type || $this->service_type === '' ) {
            $error[] = __( 'Service Type is required.', 'buddyclients-free' );
            $valid = false;
        }
        
        // Generate error string
        $error_string = implode('<br>', $error);
        $error_message = buddyc_admin_icon('error') . ' ' . $error_string;
        
        // Check if service or service type is hidden
        if ( $this->hide || ( new ServiceType( $this->service_type ) )->hide ) {
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