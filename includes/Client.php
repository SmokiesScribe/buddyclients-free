<?php
namespace BuddyClients\Includes;

use BuddyClients\Components\Stripe\StripeKeys as StripeKeys;

/**
 * Data for a single client.
 * 
 * Retrieves and updates data for a client.
 *
 * @since 0.1.0
 */
class Client {
    
    /**
     * The client ID.
     * 
     * @var int
     */
    public $ID;
    
    /**
     * Display name.
     * 
     * @var string
     */
    public $name;
    
    /**
     * Email address.
     * 
     * @var string
     */
    public $email;
    
    /**
     * Nicename.
     * 
     * @var string
     */
    public $handle;
    
    /**
     * User meta.
     * 
     * @var array
     */
    public $meta;
    
    /**
     * The referring affiliate ID.
     * 
     * @var int
     */
    public $affiliate_id;
    
    /**
     * The client's projects data.
     * 
     * False if no user groups.
     * 
     * @var array|bool
     */
    public $projects;
    
    /**
     * The client's Booking Intent IDs.
     * 
     * @var array
     */
    public $booking_intent_ids;
    
    /**
     * Group IDs.
     * 
     * The IDs of all groups for which the user is admin. False if none.
     * 
     * @var array|bool
     */
    public $group_ids;
    
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param array $args
     */
    public function __construct( $user_id ) {
        
        // Assign user id as object ID
        $this->ID = $user_id;
        
        // Get customer ID
        $this->stripe_keys = bc_stripe_keys();
        $this->get_customer_id();
        
        // Get user details
        $this->user_details();

        // Get project data
        $this->get_group_ids();
        $this->get_group_data();
    }
    
    /**
     * Retrieves the client's Booking Intents.
     * 
     * @since 0.1.0
     */
    public function get_booking_intent_ids() {
        $database = new Database( 'booking_intent' );
        $this->booking_intent_ids = $database->get_all_records_by_column( 'client_id', $this->ID, true );
    }
    
    /**
     * Retrieves Stripe customer ID.
     * 
     * @since 0.1.0
     * 
     * @return int|null Customer ID or null.
     */
    public function get_customer_id() {
        // No Stripe keys
        if ( ! $this->stripe_keys ) {
            return null;
        }
        // Get current Stripe mode
        $mode = $this->stripe_keys->mode;
        
        // Retrieve customer id
        $customer_id = get_user_meta( $this->ID, $mode . '_stripe_customer_id', true );
        
        // Assign variable if it exists
        if ( $customer_id ) {
            $this->customer_id = $customer_id;
            return $customer_id;
        } else {
            return null;
        }
    }
    
    /**
     * Updates Stripe customer ID.
     * 
     * @since 0.1.0
     * 
     * @param   int     $customer_id    New customer ID.
     */
    public function update_customer_id( $customer_id ) {
        // Get current Stripe mode
        $mode = $this->stripe_keys->mode;
        
        // Update user meta with new customer id
        update_user_meta( $this->ID, $mode . '_stripe_customer_id', $customer_id );
        
        // Assign variable
        $this->customer_id = $customer_id;
    }
    
    /**
     * Deletes Stripe customer ID.
     * 
     * @since 0.1.0
     */
    public function delete_customer_id() {
        // Get current Stripe mode
        $mode = $this->stripe_keys->mode;
        
        // Delete user meta
        delete_user_meta( $this->ID, $mode . '_stripe_customer_id');

        // Assign variable
        $this->customer_id = null;
    }
    
    /**
     * Retrieves user details.
     * 
     * @since 0.1.0
     */
    private function user_details() {
        $this->name     = bp_core_get_user_displayname( $this->ID );
        $this->email    = bp_core_get_user_email( $this->ID );
        $this->handle   = bp_core_get_username( $this->ID );
        $this->meta     = get_user_meta( $this->ID );
    }
    
    /**
     * Fetches user groups.
     * 
     * @since 0.1.0
     */
     private function get_group_ids() {
        // Define args for bp_get_user_groups
        $args = array(
            'is_admin' => null,
            'is_mod' => null,
        );
        
        if (function_exists('bp_get_user_groups')) {
        
            // Get the user's groups using bp_get_user_groups
            $groups = bp_get_user_groups($this->ID, $args);
            
            // Add IDs to array
            $group_ids = [];
            foreach ($groups as $group) {
                $group_ids[] = $group->group_id;
            }
            
            // Newest groups first
            $group_ids = array_reverse($group_ids);
            
            $this->group_ids = $group_ids;
        }
     }
    
    /**
     * Retrieves data for user groups.
     * 
     * @since 0.1.0
     */
     private function get_group_data() {
         
         // If the user is admin of groups
         if ( $this->group_ids ) {
             
             // Initialize
             $groups_data = [];
             $projects = [];
            
            // Loop through group ids
             foreach ($this->group_ids as $group_id) {
                 $group = groups_get_group($group_id);
                 $group_type = bp_groups_get_group_type($group_id);
                 
                 // Skip groups that are not projects
                 if ($group_type !== 'project') {
                     continue;
                 }
                 
                 $projects[] = new Project( $group_id );
                 
                // @todo Get previously booked services
                
                // @todo Get team members
                // $team_members_array = get_project_team_members($group_id);
                // $team_members_string = json_encode($team_members_array);
                
                // Add to array
                $groups_data[$group_id] = [
                    'name' => bp_get_group_name($group),
                 //   'services' => '',
                 //   'team' => '',
                ];
             }
             $this->projects = $projects;
         }
     }
}