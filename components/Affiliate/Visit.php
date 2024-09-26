<?php
namespace BuddyClients\Components\Affiliate;

/**
 * Single site visit.
 * 
 * Checks for an affiliate link on each site visit.
 * Updates the client and the affiliate's click data.
 * 
 * @since 0.1.0
 */
class Visit {
    
    /**
     * The ID of the affiliate.
     * 
     * @var int
     */
    private $affiliate_id;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        @session_start();
        
        // Check for session flag
        if ( isset( $_SESSION['bc_affiliate'] ) ) {
            return;
        }
        
        // Get existing user affiliate id
        $this->affiliate_id = $this->user_affiliate() ?? $this->get_cookie();

        // Exit if no affiliate ID
        if ( ! $this->affiliate_id ) {
            return;
        }
        
        // Exit if affiliate id is same as current user
        if ( $this->affiliate_id == get_current_user_id() ) {
            return;
        }
        
        // Update client meta
        $this->update_client();
        
        // Update click count
        $this->add_click();
        
        // Add session flag
        $_SESSION['bc_affiliate'] = $this->affiliate_id;
    }
    
    /**
     * Retrieves existing affiliate ID from user meta.
     * 
     * @since 0.1.0
     */
    private function user_affiliate() {
        
        // Initialize
        $existing_affiliate_id = null;
        
        // Get current user id
        $user_id = get_current_user_id();
        
        // Make sure the user is logged in
        if ($user_id) {
            // Check for an existing affiliate ID
            $user_affiliate_id = get_user_meta($user_id, 'bc_affiliate', true);
            
            // Check if $user_affiliate_id is an integer
            if (is_int($user_affiliate_id)) {
                $existing_affiliate_id = $user_affiliate_id;
            }
        }
        
        return $existing_affiliate_id;
    }

    
    /**
     * Adds to the affiliate click count.
     * 
     * @since 0.1.0
     */
    private function add_click() {
        
        // Get current date
        $current_date = date( 'Y-m-d' );
        
        // Initialize with current meta
        $click_data = get_user_meta( intval( $this->affiliate_id ), 'bc_affiliate_clicks', true );
        
        if ( ! is_array( $click_data ) ) {
            $click_data = [];
        }
    
        // Increment click count for current date
        if ( isset( $click_data[$current_date] ) ) {
            $click_data[$current_date]++;
        } else {
            $click_data[$current_date] = 1;
        }
        
        // Update meta
        update_user_meta( $this->affiliate_id, 'bc_affiliate_clicks', $click_data );
    }
    
    /**
     * Updates users referring affiliate ID.
     * 
     * @since 0.1.0
     */
    private function update_client() {
        
        // Get current user id
        $user_id = get_current_user_id();
        
        // Make sure the user is logged in
        if ( $user_id ) {
            
            // Check for an existing affiliate ID
            $existing_affiliate_id = get_user_meta( $user_id, 'bc_affiliate', true );
            
            // Add the affiliate ID if one doesn't exist
            if ( ! $existing_affiliate_id ) {
                update_user_meta( $user_id, 'bc_affiliate', $this->affiliate_id );
            }
        }
    }
    
    /**
     * Retrieves the oldest affiliate value from the cookie.
     *
     * @since 0.1.0
     *
     * @return string|null The oldest affiliate value from the 'affiliate' cookie, or null if not found.
     */
    private function get_cookie() {
        
        // Check if cookie is set
        if ( isset( $_COOKIE['affiliate'] ) ) {
            
            // Get affiliate cookie
            $cookie_value = $_COOKIE['affiliate'];
            
            // Loop through affiliate values
            foreach ( explode(',', $cookie_value ) as $affiliate_id ) {
                
                // Make sure the user exists
                if ( get_user_by( 'id', $affiliate_id ) ) {
                    
                    // Return oldest integer
                    return $affiliate_id;
                }
            }
        }
        // No affiliate cookie found
        return null;
    }
}