<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\Form\Form;

/**
 * Form to filter items in post archive.
 * 
 * @since 1.0.21
 */
class ArchiveFilterForm {

    /**
     * The post type for the archive.
     * 
     * @var string
     */
    public $post_type;
    
    /**
     * An array of items to filter by.
     * 
     * @var array
     */
    public $filter_items;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   string  $post_type  The post type for the filter.
     */
    public function __construct( $post_type = null ) {
        $this->post_type = $post_type;
        $this->filter_items = $this->get_filter_items( $post_type );
    }

    /**
     * Retrieves the filter items for the post type.
     * 
     * @since 1.0.21
     */
    private function get_filter_items( $post_type ) {
        $filters = [
            'buddyc_service'    => $this->get_service_types()
        ];
        return $filters[$post_type] ?? null;
    }

    /**
     * Retrieves all service types.
     * 
     * @since 1.0.21
     */
    private function get_service_types() {
        // Init
        $types_items = [];

        // Define args
        $args = array(
            'post_type'      => 'buddyc_service_type',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'order',
                    'compare' => 'EXISTS', // Check if the meta key exists
                ),
                array(
                    'key'     => 'order',
                    'compare' => 'NOT EXISTS', // Check if the meta key does not exist
                ),
            ),
            'orderby'    => 'meta_value_num', // Order by numeric meta value
            'order'      => 'DESC', // Order in descending order
        );
        
        // Get the service types
        $types = get_posts( $args );

        // Make sure types were found
        if ( ! empty( $types ) ) {
            foreach ( $types as $type ) {
                $types_items[$type->ID] = get_the_title( $type->ID );
            }
        }

        return $types_items;
    }
    
    /**
     * Builds the form.
     * 
     * @since 1.0.21
     */
    public function build() {
        // Make sure the filter items exist
        if ( empty( $this->filter_items ) ) {
            return;
        }
        
        // Define form args
        $form_args = [
            'key'                   => 'archive-filter',
            'fields_callback'       => [$this, 'form_fields'],
            'submission_class'      => null,
            'no_submit'             => true
        ];
        
        $form = new Form( $form_args );
        return $form->build();
    }
    
    /**
     * Generates the form fields.
     * 
     * @since 1.0.21
     */
    public function form_fields() {
        // Initialize
        $options = [];
        
        if ( ! empty( $this->filter_items ) ) {
            // Add each group to the options array
            foreach ( $this->filter_items as $item_id => $item_label ) {
                // Add option
                $options[$item_id] = [
                    'label'         => $item_label,
                    'value'         => $item_id,
                    'selected'      => true
                ];
            }
        }
        
        // Build arguments for the checkbox field
        return [[
            'key'           => 'archive_filter',
            'type'          => 'checkbox',
            'label'         => '',
            'description'   => '',
            'options'       => $options,
            'classes'       => 'horizontal'
        ]];
    }
}