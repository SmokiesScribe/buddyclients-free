<?php
namespace BuddyClients\Components\Availability;

use BuddyClients\Includes\{
    ProfileExtension as ProfileExtension
};

/**
 * Availability data for a single user.
 * 
 * Retrieves, updates, and formats a team member's availability.
 * Initializes the availability profile tab.
 * Schedules a reminder to update availability.
 *
 * @since 0.1.0
 */
class Availability {
    
    /**
     * The ID of the user.
     * 
     * @var int
     */
    public $user_id;
    
    /**
     * The availability of the user.
     * 
     * @var string
     */
    public $availability;
    
    /**
     * The availability in human readable format.
     * 
     * @var string
     */
    public $human_readable;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $user_id = null ) {
        if ( ! bc_component_enabled( 'Availability' ) ) {
            return;
        }
        
        $this->user_id = $user_id ?? get_current_user_id();
        $this->availability = get_user_meta( $this->user_id, 'bc_availability', true);
        $this->human_readable = self::format( $this->availability );
    }
    
    /**
     * Retrieves a user's availability.
     * 
     * @since 0.1.0
     * 
     * @param   ?int     $user_id   The ID of the user.
     *                              Defaults to current user.
     */
    public static function get_availability( $user_id = null ) {
        // Default to current user
        $user_id = $user_id ?? get_current_user_id();
        
        // Retrieve availability
        $availability = get_user_meta( $user_id, 'bc_availability', true);
        
        return $availability;
    }
    
    /**
     * Updates a user's availability.
     * 
     * @since 0.1.0
     * 
     * @param   int      $user_id           The ID of the user to update.
     * @param   mixed    $availability      The availability value.
     */
    public static function update_availability( $user_id, $availability ) {
        
        // Get first item if array
        if ( is_array( $availability ) ) {
            $availability = $availability[0];
        }
        
        // Update user meta
        update_user_meta( $user_id, 'bc_availability', $availability );
        
        // Schedule reminder
        self::schedule_reminder( $user_id, $availability );
    }
    
    /**
     * Schedules an availability reminder.
     * 
     * @since 0.1.0
     * 
     * @param   int      $user_id           The ID of the user.
     * @param   mixed    $availability      The availability value.
     */
    private static function schedule_reminder( $user_id, $availability ) {
        // Convert the availability date to a timestamp
        $timestamp = strtotime( $availability );
    
        // Exit if the timestamp is invalid or in the past
        if ( $timestamp === false || $timestamp < time() ) {
            return;
        }
    
        // Define a unique hook name using the user ID
        $hook = 'availability_reminder';
    
        // Check if the event is already scheduled
        if ( ! wp_next_scheduled( $hook, [ 'user_id' => $user_id ] ) ) {
            // Schedule the event with the user ID as an argument
            wp_schedule_single_event( $timestamp, $hook, [ 'user_id' => $user_id ] );
        }
    }
    
     /**
      * Formats availability for display.
      * 
      * @since 0.1.0
      */
     public static function format( $availability ) {
        // Check if $availability is a valid date
        if (strtotime($availability)) {
            // If it's a valid date, format it to human-readable
            $formatted_availability = date('F j, Y', strtotime($availability));
        } else {
            // If it's not a valid date, capitalize the first letter
            $formatted_availability = ucfirst($availability);
        }
        return $formatted_availability;
     }
     
    /**
     * Checks if the availability date has expired.
     * 
     * @since 0.1.0
     * 
     * @param   string  $availability   The availability to check.
     */
    public static function expired( $availability ) {
        
        // Immediately never expires
        if ( $availability === 'immediately' ) {
            return false;
        }
        
        // Convert the date string to a timestamp
        $timestamp = strtotime( $availability );
    
        // Check if the timestamp is less than the current timestamp (meaning the date has passed)
        if ( $timestamp < time() ) {
            return true;
        } else {
            return false;
        }
        
    }
}