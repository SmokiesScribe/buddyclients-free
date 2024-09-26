<?php
namespace BuddyClients\Components\Affiliate;

use BuddyClients\Components\Legal\Legal as Legal;
use BuddyClients\Includes\{
    Alert as Alert,
    ProfileExtension as ProfileExtension
};

use BuddyClients\Components\Booking\BookingIntent;

/**
 * Affiliate data for a single user.
 * 
 * Manages affiliate-specific functionalities for a user.
 * Initializes the user's legal data, retrieves contact info,
 * and intializes an alert if a new affiliate agreement is needed.
 * 
 * @since 0.1.0
 */
class Affiliate {
    
    /**
     * The ID of the user.
     * 
     * @var int
     */
    public $ID;
    
    /**
     * Legal instance.
     * 
     * @var Legal
     */
    private $legal;
    
    /**
     * Legal user data.
     * 
     * @var array
     */
    public $user_data;
    
    /**
     * The type of commission from settings.
     * Accepts 'lifetime' or 'first_sale'.
     * 
     * @var string
     */
    public $commission_type;
    
    /**
     * The total number of referred users.
     * 
     * @var int
     */
    public $ref_users_count;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * $param   ?int     $user_id    Optional. The ID of the user.
     *                               Defaults to current user ID.
     */
    public function __construct( $user_id = null ) {
        
        // Exit if legal not enabled
        if ( ! class_exists( Legal::class ) ) {
            return;
        }
            
        // Initialize legal object
        $this->legal = new Legal( 'affiliate' );
        
        // Define user ID
        $this->ID = $user_id ?? get_current_user_id();
        
        // Get affiliate legal data
        $this->user_data = $this->legal->get_user_data( $user_id );
        
        // Check setting
        $this->commission_type = bc_get_setting( 'affiliate', 'commission_type' );
        
        // Define hooks
        $this->define_hooks( $user_id );
    }
    
    /**
     * Defines hooks.
     * 
     * @since 0.1.0
     */
    private function define_hooks( $user_id ) {
        // Make sure the defined user is logged in
        if ( $user_id === get_current_user_id() ) {
            add_action('init', [$this, 'alert']);
        }
    }
    
    /**
     * Generates affiliate link.
     * 
     * @since 0.1.0
     * 
     * $param   ?int     $user_id   Optional. The ID of the user.
     *                              Defaults to current user.
     */
    public static function affiliate_link( $user_id = null ) {
        $user_id = $user_id ?? get_current_user_id();
        return site_url( '?affiliate=' . $user_id );
    }
    
    /**
     * Retrieves affiliate email.
     * 
     * @since 0.1.0
     */
    public function affiliate_email() {
        return $this->user_data['email'];
    }
    
    /**
     * Displays alert for new affiliate agreement.
     * 
     * @TODO Combine this with team and move to Legal class
     * 
     * @since 0.1.0
     */
     public function alert() {
         // Make sure the user is logged in and a current affiliate
         if ( ! is_user_logged_in() || ! $this->user_data['status'] ) {
             return;
         }
         
         // Check if the agreement is in transition
         if ( $this->legal->status === 'transition' && $this->user_data['status'] !== 'current' ) {
             // Define profile link
             $link = ProfileExtension::link('affiliate');
             
             // Build alert content
            $content = sprintf(
                __('Complete your <a href="%s">new affiliate agreement</a> by %s.', 'buddyclients'),
                esc_url( $link ),
                esc_html( $this->legal->deadline )
            );
            
            // Output alert
            new Alert( $content, 10 );
         }
     }
     
     /**
      * Checks whether the affiliate is qualified to receive commission.
      * 
      * @since 0.1.0
      * 
      * @param  int     $client_id      The ID of the client.
      * @param  BookintIntent   $booking_intent The BookingIntent object.
      */
     public function is_qualified( $booking_intent ) {
         $client_id = $booking_intent->client_id ?? null;
         
         // Make sure the affiliate is not the client
         if ( $this->ID === $client_id ) {
             return false;
         }
         
         // Check commission type
         if ( $this->commission_type !== 'lifetime' ) {
             return $this->is_first_sale( $client_id, $booking_intent->ID );
         }
         
         // Checks passed
         return true;
     }
     
    /**
      * Checks whether it is the client's first purchase.
      * 
      * @since 0.4.3
      * 
      * @param  int     $client_id          The ID of the client.
      * @param  int     $booking_intent_id  The ID of the current BookingIntent.
      */
     public function is_first_sale( $client_id, $booking_intent_id ) {
         $booking_intents = BookingIntent::get_booking_intents_by_client( $client_id );
         if ( ! empty( $booking_intents ) ) {
             foreach ( $booking_intents as $booking_intent ) {
                 // Skip current booking intent
                 if ( $booking_intent->ID === $booking_intent_id ) {
                     continue;
                 }
                 // Check if the booking intent was successful
                 if ( $booking_intent->status === 'succeeded' ) {
                     return false;
                 }
             }
         }
         
         return true;
     }
     
     /**
      * Retrieves the total number of referred users.
      * 
      * @since 0.4.3
      */
     public function ref_users_count() {
        // Query users with the specified meta key and value.
        $users = get_users( array(
            'meta_key'   => 'bc_affiliate',
            'meta_value' => $this->ID,
            'fields'     => 'ID', // Only retrieve user IDs to optimize performance.
            'number'     => -1,   // Retrieve all matching users.
        ) );
    
        // Return the count of matching users.
        return count( $users );
     }
}