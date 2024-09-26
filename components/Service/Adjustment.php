<?php
namespace BuddyClients\Components\Service;

use BuddyClients\Includes\PostQuery as PostQuery;

/**
 * An adjustment to a service.
 * 
 * Retrieves data from an adjustment post.
 *
 * @since 0.1.0
 * 
 * @see ServiceComponent
 */
class Adjustment extends ServiceComponent {
    
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
     * The form field type.
     * Accepts 'checkbox' and 'dropdown'.
     * 
     * @var string
     */
     public $form_field_type;
     
    /**
     * The description to display.
     * 
     * @var string
     */
     public $field_description;
     
    /**
     * The ID of the help post.
     * 
     * @var int
     */
     public $help_post_id;
     
    /**
     * An array of field options.
     * 
     * @var array {
     *     @type string     $label      The field option label.
     *     @type string     $operator   Accepts '-', '+', and 'x';
     *     @type int        $value      The value to use the operator on.
     * }
     */
     public $options;
     
    /**
     * The number of options.
     * 
     * @var int
     */
     public $options_count;
     
    /**
     * Service IDs.
     * 
     * An array of service IDs that use the rate type.
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
        
        // Make sure it's an adjustment post
        if ( get_post_type( $post_id ) !== 'bc_adjustment' ) {
            return;
        }
        
        // Construct ServiceComponent
        parent::__construct( $post_id );
        
        // Build adjustment options
        $this->get_options( $post_id );
    }
    
    /**
     * Retrieves the number of options.
     * 
     * @since 0.1.0
     * 
     * @param   int     $post_id    The ID of the adjustment post.
     */
    private function get_options( $post_id ) {
        // Initialize
        $options_count = 0;
        $options = [];
        
        // Get all meta
        $all_meta = get_post_meta( $post_id );
        
        // Iterate through all meta data
        foreach ( $all_meta as $meta_key => $value ) {
            
            // Check if the meta key matches the pattern 'option_X_label'
            if ( preg_match('/^option_\d+_label$/', $meta_key ) ) {
                
                // Get the option number
                $parts = explode( '_', $meta_key );
                $option_number = $parts[1];
                
                // Build the object
                $adjustment_option = new AdjustmentOption( $post_id . '-' . $option_number );
                
                // Validate the option
                if ( $adjustment_option->validate() ) {
                    // Add to array
                    $options[$post_id . '-' . $option_number] = $adjustment_option;
                    // Increment counter
                    $options_count += 1;
                }
            }
        }
        $this->options = $options;
        $this->options_count = $options_count;
    }
}