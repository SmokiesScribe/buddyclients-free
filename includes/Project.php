<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Booking\BookingIntent   as BookingIntent;

/**
 * Single project.
 * 
 * Handles data for a single project group.
 *
 * @since 0.1.0
 */
class Project {
    
    /**
     * The ID of the group.
     * 
     * @var ?int
     */
    public $ID;
    
    /**
     * The BookingIntent object.
     * 
     * @var BookingIntent
     */
    public $booking_intent;
    
    /**
     * The name of the group.
     * 
     * @var string
     */
    public $name;
    
    /**
     * The permalink to the group.
     * 
     * @var string
     */
    public $permalink;
    
    /**
     * The project filter data.
     * 
     * @var array
     */
    public $filter_data;
    
    /**
     * The project team data.
     * 
     * @var array
     */
    public $team_data;
    
    /**
     * Whether the team should be locked.
     * 
     * @var bool
     */
    public $lock_team;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $ID = null ) {
        // Define project ID
        $this->ID = $ID ?? null;
        
        // Get group info
        if ( $ID ) {
            $this->get_group_info( $ID );
        }
    }
    
    /**
     * Retrieves group info.
     * 
     * @since 0.1.3
     * 
     * @int $group_id   The ID of the group.
     */
    public function get_group_info( $group_id ) {
        // Get group object
        $group = groups_get_group( $group_id );
        
        // Get group type
        $group_type = bp_groups_get_group_type( $group_id );
        
        // Exit if not a project group
        if ($group_type !== 'project') {
            return;
        }
        
        // Get group name
        $this->name = bp_get_group_name( $group );
        $this->permalink = bp_get_group_permalink( $group );
        
        // Get filter data
        $this->filter_data = self::get_project_meta( $group_id, 'filter_data', true );
        
        // Get team members
        $this->lock_team = ( buddyc_get_setting( 'booking', 'lock_team' ) === 'lock' );
        $this->team_data = self::get_project_meta( $group_id, 'team_data', true );
        
        // Get booked services
        $this->booked_services = self::get_project_meta( $group_id, 'booked_services', true );
    }
    
    /**
     * Creates new project group.
     * 
     * @since 0.1.0
     * 
     * @param   int     $booking_intent_id  The ID of the BookingIntent.
     * @param   int     $client_id          The ID of the user.
     * @return  int     $project_id         The ID of the project group.
     */
    public function create_project( $booking_intent_id, $client_id ) {
        
        // Exit if guest
        if ($client_id === 'guest') {
            return null;
        }
        
        // Get BookingIntent
        $this->booking_intent = BookingIntent::get_booking_intent( $booking_intent_id );
        
        // Check if project ID exists
        if ( $this->booking_intent->project_id != 0 ) {
            $this->ID = $this->booking_intent->project_id;
            
            // Update project and return project ID
            return $this->handle_new_project();
        }
    
        // Define new group args
        $group_args = array(
            'group_id'      => 0,
            'creator_id'    => $client_id,
            'name'          => $this->booking_intent->post['project_title'],
            'status'        => 'hidden', // Group status accepts public, private, hidden
            'parent_id'     => 0,
            'enable_forum'  => 0, // Set to 1 if you want to enable a forum for the group
        );
        
        // Create group
        $this->ID = groups_create_group( $group_args );
        
        // Handle new group and return ID
        return $this->handle_new_project();
    }
    
    /**
     * Handles new group creation.
     * 
     * @since 0.1.0
     */
    private function handle_new_project() {
        
        // Check if the group was created successfully
        if ( $this->ID !== false ) {
            
            // Set group type to project
            bp_groups_set_group_type($this->ID, 'project');
            
            // Update BookingIntent
            BookingIntent::update_project_id( $this->booking_intent->ID, $this->ID );
            
            // Update project filters
            $this->update_project_filters();

            // Update team
            $this->update_project_team( $this->booking_intent->post );
            
            // Update booked services
            $this->update_booked_services();
            
            // Return the newly created group ID
            return $this->ID;
        
        } else {
            // Handle group creation failure
            return null;
        }
    }
    
    /**
     * Updates project team members.
     * 
     * @since 0.1.0
     * 
     * @param   array   $post   Post data from the BookingIntent.
     */
    private function update_project_team( $post ) {
        
        // Get existing team data
        $team_data = groups_get_groupmeta( $this->ID, 'team_data', false );
        
        // Get line items from BookingIntent
        $line_items = unserialize( $this->booking_intent->line_items );
        
        // Loop through line items
        foreach ( $line_items as $line_item ) {
            $role = $line_item->team_member_role ?? null;
            
            // Add to array
            $team_data[$role] = $line_item->team_id;
            
            // Add team member to project group
            $this->add_to_group( $line_item->team_id );
        }
        
        // Update group meta
        self::update_project_meta( $this->ID, [ 'team_data' => $team_data ] );
    }
    
    /**
     * Adds team member to project group.
     * 
     * @since 0.1.0
     * 
     * @param int $team_id The ID of the team member.
     */
    private function add_to_group( $team_id ) {
        // Add team member to group
        groups_join_group( $this->ID, $team_id );
        
        // Check if successful
        if ( groups_is_user_member( $team_id, $this->ID ) ) {
            // Promote to mod so they can send messages
            groups_promote_member( $team_id, $this->ID, 'mod' );
        }
    }
    
    /**
     * Updates filter field data.
     * 
     * @since 0.1.0
     */
    private function update_project_filters() {
        
        // Initialize
        $filter_data = [];
        
        // Loop through post data
        foreach ( $this->booking_intent->post as $key => $value ) {
            
            // Make sure it's a filter field
            if ( strpos( $key, 'team-filter-field-' ) !== false ) {
                
                // Get filter ID
                $filter_id = str_replace( 'team-filter-field-', '', $key );
                
                // Add to data
                $filter_data[$filter_id] = $value;
            }
        }
        // Update group meta
        self::update_project_meta( $this->ID, [ 'filter_data' => $filter_data ] );
    }
    
    /**
     * Updates booked services.
     * 
     * @since 0.1.3
     */
    private function update_booked_services() {
        
        // Initialize
        $booked_services = self::get_project_meta( $this->ID, 'booked_services', true );
        
        if ( ! $booked_services ) {
            $booked_services = [];
        }
        
        // Get line items from BookingIntent
        $line_items = unserialize( $this->booking_intent->line_items );
        
        // Loop through line items
        foreach ( $line_items as $line_item ) {
            $service_id = $line_item->service_id ?? null;
            
            error_log('Service ID: ' . $service_id);
            
            // Add to array
            $booked_services[] = $service_id;
        }
        
        error_log('Booked services:');
        error_log(print_r($booked_services, true));
        
        // Update group meta
        self::update_project_meta( $this->ID, [ 'booked_services' => $booked_services ] );
    }
    
    /**
     * Updates group meta.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID     The ID of the project group.
     * @param   array   $meta   The array of meta keys and values.
     */
    public static function update_project_meta( $ID, $meta ) {
        foreach ( $meta as $key => $value ) {
            groups_update_groupmeta( $ID, $key, $value );
        }
    }
    
    /**
     * Retrieves group meta.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The ID of the project group.
     * @param   string  $meta_key   The key of the meta value to retrieve.
     * @param   bool    $single     Optional. Whether to return a single value.
     *                              Defaults to true.
     */
    public static function get_project_meta( $ID, $meta_key, $single ) {
        return groups_get_groupmeta( $ID, $meta_key, $single ?? true );
    }
    
}