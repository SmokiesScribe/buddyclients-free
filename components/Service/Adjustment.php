<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
        if ( get_post_type( $post_id ) !== 'buddyc_adjustment' ) {
            return;
        }
        
        // Construct ServiceComponent
        parent::__construct( $post_id );
        
        // Retrieves the adjustment options
        $this->get_options();
    }

    /**
     * Retrieves the AdjustmentOption objects and count.
     * 
     * @since 0.1.0
     */
    private function get_options() {
        $this->options          = $this->get_adjustment_options();
        $this->options_count    = $this->get_adjustment_option_count();
    }

    /**
     * Retrieves the attached AdjustmentOption objects.
     * 
     * @since 1.0.25
     */
    private function get_adjustment_options() {
        $options = [];
    
        // Get all meta keys that match 'option_X_label'
        foreach ( get_post_meta( $this->ID ) as $meta_key => $value ) {
            if ( preg_match('/^option_(\d+)_label$/', $meta_key, $matches ) ) {
                $option_number = $matches[1];
    
                // Retrieve and validate adjustment option
                $cache_key = "{$this->ID}-{$option_number}";
                if ( ( $adjustment_option = buddyc_get_service_cache( 'adjustment_option', $cache_key )) && $adjustment_option->validate() ) {
                    $options[$cache_key] = $adjustment_option;
                }
            }
        }
    
        return $options;
    }

    /**
     * Counts the attached Adjustment Option objects.
     * 
     * @since 1.0.25
     */
    private function get_adjustment_option_count() {
        return count( $this->options );
    }
}