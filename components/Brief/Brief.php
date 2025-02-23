<?php
namespace BuddyClients\Components\Brief;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\Project;

/**
 * A single brief.
 *
 * @since 0.1.0
 */
class Brief {
    
    /**
     * The ID of the Brief post.
     * 
     * @var int
     */
    public $ID;
    
    /**
     * The ID of the project group.
     * 
     * @var int
     */
    public $project_id;
    
    /**
     * The name of the project group.
     * 
     * @var int
     */
    public $project_name;

    /**
     * The link to the project group.
     * 
     * @var string
     */
    public $project_link;

    /**
     * The Project object.
     * 
     * @var Project
     */
    public $project;
    
    /**
     * The date the brief was last updated.
     * 
     * @var string
     */
    public $updated_date = null;
    
    /**
     * The applicable brief types.
     * 
     * @var array
     */
    public $brief_types;

    /**
     * The brief type ID.
     * 
     * @var int
     */
    public $brief_type_id;
    
    /**
     * A string of brief type names.
     * 
     * @var string
     */
    public $brief_type_names;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param int   $ID     Optional. The ID of the Brief.
     */
    public function __construct( $ID = null ) {
        if ( $ID ) {
            $this->ID = $ID;
            $this->get_brief_info( $ID );
        }
    }
    
    /**
     * Retrieves the data for an existing brief.
     * 
     * @since 0.4.0
     */
    private function get_brief_info( $ID ) {
        $this->project_id = get_post_meta( $ID, 'project_id', true );
        $this->project = new Project( $this->project_id );
        $this->project_name = $this->project->name;
        $this->project_link = $this->project->permalink;
        $this->updated_date = get_post_meta( $ID, 'updated_date', true );
        $this->brief_types = wp_get_post_terms( $ID, 'brief_type', array( 'fields' => 'names' ) );
        $this->brief_type_id = $this->get_brief_type_id( $ID );
        $this->brief_type_names = ucfirst( implode( ', ', $this->brief_types ) );
    }

    /**
     * Retrieves the first brief type id.
     * 
     * @since 1.0.21
     * 
     * @param   int     $ID     The ID of the brief.
     */
    private function get_brief_type_id( $ID ) {
        $brief_type_ids = wp_get_post_terms( $ID, 'brief_type', array( 'fields' => 'ids' ) );
        return isset( $brief_type_ids[0] ) ? $brief_type_ids[0] : null;
    }
    
    /**
     * Creates brief post.
     * 
     * @since 0.1.0
     * 
     * @param   int     $project_id     The ID of the project group.
     * @param   int     $service_id     The ID of the service post.
     */
    public function create( $project_id, $service_id ) {
        
        $this->project_id = $project_id;
        $this->project_name = bp_get_group_name( groups_get_group( $project_id ) );
        
        // Get the brief type for the service
        $brief_types = get_post_meta($service_id, 'brief_type', true);
        
        // Exit if no brief type defined
        if ( ! $brief_types ) {
            return;
        }
        
        // Check if there are multiple brief types
        if ( is_array ( $brief_types ) ) {
            foreach ( $brief_types as $brief_type ) {
                $this->publish( $brief_type, $service_id );
            }
        } else {
            $this->publish( $brief_types, $service_id );
        }
    }
    
    /**
     * Publishes a single brief post.
     * 
     * @since 0.1.0
     * 
     * @param   int     $brief_type     The ID of the brief type term.
     */
    private function publish( $brief_type, $service_id ) {
        
        // Check if a post exists 
        if ( $this->brief_exists( $brief_type, $this->project_id ) ) {
            return;
        }
        
        // Get tax term
        $term = get_term_by('term_id', $brief_type, 'brief_type');

        // Define the new brief args
        $args = array(
            'post_title' => $this->project_name . ' ' . $term->name . ' ' . __( 'Brief', 'buddyclients-free' ),
            'post_status'   => 'publish',
            'post_type'     => 'buddyc_brief',
        );

        // Insert the post into the database
        $new_brief_id = wp_insert_post($args);
        
        // Check if the post was created
        if ($new_brief_id) {
            // Set the post's brief type by ID
            wp_set_post_terms($new_brief_id, array($brief_type), 'brief_type');

            // Update the post meta
            update_post_meta($new_brief_id, 'project_id', $this->project_id);
        }
    }
    
    /**
     * Checks for existing brief.
     * 
     * @since 0.1.0
     * 
     * @param   int     $brief_type     The ID of the brief type term.
     * @param   int     $project_id     The ID of the project group.
     */
    private function brief_exists( $brief_type, $project_id ) {
        
        // Check if the brief already exists for the project
        $existing_brief = get_posts(array(
            'post_type' => 'buddyc_brief',
            'posts_per_page' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'brief_type',
                    'field' => 'term_id',
                    'terms' => $brief_type,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => 'project_id',
                    'value' => $this->project_id,
                    'compare' => '=',
                ),
            ),
        ));
        
        // Check if posts were found
        return empty( $existing_brief ) ? false : true;
    }
}