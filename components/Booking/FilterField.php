<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\{
    XprofileField as XprofileField
};

/**
 * Filter field.
 *
 * Handles a single field to filter team members.
 * Retrieves field data from settings.
 * Generates arguments to build the field on the booking form.
 */
class FilterField {
    
    /**
     * The post ID of the filter field.
     *
     * @var int
     */
    public $post_id;
    
    /**
     * The ID of the Xprofile field.
     *
     * @var int
     */
    public $field_id;
    
    /**
     * The form label.
     *
     * @var string
     */
    public $form_label;
    
    /**
     * The form description.
     *
     * @var string
     */
    public $form_description;
    
    /**
     * Match type.
     * Accepts 'exact', 'include_any', 'include_all', 'exclude'.
     *
     * @var string
     */
    public $match_type;
    
    /**
     * Whether the field allows multiple options.
     *
     * @var bool
     */
    public $multiple_options;
    
    /**
     * Help post ID
     *
     * @var int
     */
    public $help_post_id;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param int $post_id  The ID of the filter field post.
     */
    public function __construct( $post_id ) {
        // Set post id
        $this->post_id = $post_id;
        
        // Get post meta
        $this->get_meta();
        
        // Get Xprofile field id
        $this->field_id = $this->get_xprofile_field_id();
    }
    
    /**
     * Gets meta from filter field post.
     * 
     * @since 0.1.0
     */
    private function get_meta() {

        // Get all meta data for the post
        $meta_data = get_post_meta($this->post_id);
        
        // Loop through the meta data array to access each meta key and its value
        foreach ($meta_data as $key => $value) {
            $this->{$key} = $value[0];
        }

    }
    
    /**
     * Retrieves the Xprofile field object.
     * 
     * @since 0.1.0
     */
    private function get_xprofile_field_id() {
        return get_post_meta($this->post_id, 'xprofile_field', true);
    }
    
    /**
     * Builds form field.
     * 
     * @since 0.1.0
     */
    public function args() {
        
        // Build help link
        $help_link = $this->help_post_id ? buddyc_help_link( $this->help_post_id ) : '';
        
        // Build arguments for the project select field
        return [
            'key' => 'team-filter-field-' . $this->field_id, // should this be post id?
            'type' => $this->multiple_options === 'yes' ? 'checkbox' : 'dropdown',
            'label' => $this->form_label,
            'description' => $this->form_description . $help_link,
            'options' => XprofileField::get_options( $this->field_id ),
            'field_classes' => 'project-filter-field create-project',
            'data_atts' => [
                'match-type' => $this->match_type,
                'filter-id' => $this->post_id
            ],
        ];
    }
}