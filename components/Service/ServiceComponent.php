<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\PostQuery;

/**
 * Abstract class representing a service component.
 * 
 * Provides a template to generate service components such as adjustments,
 * rate types, file upload types, etc.
 *
 * @since 0.1.0
 */
abstract class ServiceComponent {
    
    /**
     * The post ID.
     * 
     * @var int
     */
     public $ID;
     
    /**
     * The post object.
     * 
     * Null if no post exists for ID.
     * 
     * @var object|null
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
     * Service IDs.
     * 
     * An array of service IDs that use the component.
     * 
     * @var array
     */
     public $service_ids;
    
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param int $post_id
     */
    public function __construct( $post_id ) {
        // Set post ID
        $this->ID = $post_id;
        
        // Get data
        $this->get_post_data()->get_meta()->get_service_ids();
    }
    
    /**
     * Retrieves post variables.
     *
     * @since 0.1.0
     */
     private function get_post_data() {
         
        // Get post
        $post = get_post( $this->ID );
        
        // Make sure post exists
        if ($post) {
            $this->title = $post->post_title;
            $this->slug = $post->name;
        }
        return $this;
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
     * Retrieves service IDs.
     * 
     * @since 0.1.0
     */
    private function get_service_ids() {
        
        // Initialize
        $service_ids = [];
        
        // Get service posts with matching rate type
        $services = new PostQuery( 'buddyc_service', ['adjustments' => strval($this->ID)], 'LIKE' );
        
        // Loop through posts and add to ids array
        foreach ($services->posts as $service) {
            $service_ids[] = $service->ID;
        }
        
        $this->service_ids = $service_ids;
        
        return $this;
    }
}