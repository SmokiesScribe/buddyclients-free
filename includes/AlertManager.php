<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
        if ( ! buddyc_component_enabled( 'Availability' ) || ! buddyc_is_team() ) {
            return;
        }
        
        // Initialize
        $content = null;
        
        // Get avaialbility
        $availability = Availability::get_availability( get_current_user_id() );
        
        // Get profile link
        $link = buddyc_profile_ext_link( 'availability' );
        
        // Check if the user has no availability set
        if ( ! $availability ) {
            $content = sprintf(
                '<a href="%s">%s</a>',
                esc_url( $link ),
                __( 'Add your availability.', 'buddyclients-free' )
            );
        
        // Check if the availability is expired
        } else if ( Availability::expired( $availability ) ) {
            $content = sprintf(
                /* translators: %s: url to update availability */
                __( 'Your availability has expired. <a href="%s">Update your availability.</a>', 'buddyclients-free' ),
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
        if ( buddyc_is_team() ) {
            self::legal_alert( 'team' );
        }
    }
    
    /**
     * Outputs the affiliate alert.
     * 
     * @since 1.0.4
     */
     public function affiliate_alert() {
        if ( buddyc_component_enabled( 'Affiliate' ) && buddyc_was_affiliate() ) {
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
        $user_id = get_current_user_id();
        $status = buddyc_user_agreement_status( $user_id, $type );
        
        // Get profile link
        $link = buddyc_profile_ext_link( $type );
        
        // Transitioning
        if ( $status === 'active' ) {
            $content = sprintf(
                /* translators: %s: url to complete agreement */
                __( 'Complete your <a href="%s">new team member agreement</a>.', 'buddyclients-free' ),
                esc_url( $link )
            );            
            
        // Stable and not current
        } else if ( $status !== 'current' ) {
            $content = sprintf(
                /* translators: %s: url to complete agreement */
                __( 'Complete your <a href="%s">team member agreement</a>.', 'buddyclients-free' ),
                esc_url( $link )
            );
        }
        
        // Output alert
        $this->alert( $content );
    }
}