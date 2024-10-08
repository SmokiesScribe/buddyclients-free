<?php
namespace BuddyClients\Includes;

use BuddyClients\Includes\Alert;
use BuddyClients\Components\Legal\Legal;
use BuddyClients\Components\Availability\Availability;

/**
 * Manages alerts.
 * 
 * @since 1.0.4
 */
class AlertManager {
    
    /**
     * Constructor method.
     * 
     * @since 1.0.4
     */
    public function __construct() {
        add_action( 'init', [$this, 'init_alerts'] );
    }
    
    /**
     * Initializes alerts.
     * 
     * @since 1.0.4
     */
    public function init_alerts() {
        $this->team_legal_alert();
        $this->availability_alert();
        $this->affiliate_alert();
    }
    
    /**
     * Displays the alert.
     * 
     * @since 1.0.4
     */
    private function alert( $content = null, $priority = 50 ) {
        if ( $content ) {
            new Alert( $content, $priority );
        }
    }
    
    /**
     * Outputs the availability alert.
     * 
     * @since 1.0.4
     */
    public function availability_alert() {
        if ( ! bc_component_enabled( 'Availability' ) || ! bc_is_team() ) {
            return;
        }
        
        // Initialize
        $content = null;
        
        // Get avaialbility
        $availability = Availability::get_availability( get_current_user_id() );
        
        // Get profile link
        $link = bc_profile_ext_link( 'availability' );
        
        // Check if the user has no availability set
        if ( ! $availability ) {
            $content = sprintf(
                '<a href="%s">%s</a>',
                esc_url( $link ),
                __( 'Add your availability.', 'buddyclients' )
            );
        
        // Check if the availability is expired
        } else if ( Availability::expired( $availability ) ) {
            $content = sprintf(
                /* translators: %s: url to update availability */
                __( 'Your availability has expired. <a href="%s">Update your availability.</a>', 'buddyclients' ),
                esc_url( $link ),
            );
        }
            
        // Output alert
        $this->alert( $content, 20 );
    }
    
    /**
     * Outputs the team agreement alert.
     * 
     * @since 1.0.4
     */
    private function team_legal_alert() {
        if ( bc_is_team() ) {
            self::legal_alert( 'team' );
        }
    }
    
    /**
     * Outputs the affiliate alert.
     * 
     * @since 1.0.4
     */
     public function affiliate_alert() {
        if ( bc_component_enabled( 'Affiliate' ) && bc_was_affiliate() ) {
            self::legal_alert( 'affiliate' );
        }
     }
     
    /**
     * Outputs a legal alert.
     * 
     * @since 1.0.4
     * 
     * @param   string  $type   The type of legal alert to output.
     *                          Accepts 'team' and 'affiliate'.
     */
    private function legal_alert( $type ) {
        if ( ! class_exists( Legal::class ) ) {
            return;
        }
        
        // Initialize
        $content = null;
        
        // Get legal data
        $legal = new Legal( $type );
        $user_data = $legal->get_user_data();
        
        // No agreement available
        if ( $legal->status === 'none' ) {
            return;
        }
        
        // Get profile link
        $link = bc_profile_ext_link( $type );
        
        // Transitioning and not current
        if ( $legal->status === 'transition' && $user_data['status'] !== 'current' ) {
            $content = sprintf(
                /* translators: %1$s: url to complete agreement; %2$s: deadline for the agreement */
                __( 'Complete your <a href="%1$s">new team member agreement</a> by %2$s.', 'buddyclients' ),
                esc_url( $link ),
                esc_html( $legal->deadline )
            );            
            
        // Stable and not current
        } else if ( $user_data['status'] !== 'current' ) {
            $content = sprintf(
                /* translators: %1$s: url to complete agreement */
                __( 'Complete your <a href="%s">team member agreement</a>.', 'buddyclients' ),
                esc_url( $link )
            );
        }
        
        // Output alert
        $this->alert( $content );
    }
}