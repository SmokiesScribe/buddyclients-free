<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\PostQuery;
use BuddyClients\Components\Quote\Quote;

/**
 * Validates services and custom quotes.
 * 
 * Initializes the validation process on post updates.
 *
 * @since 0.1.0
 */
class ServiceHandler {
    
    /**
     * Associative arrays of required meta names and
     * numbered flags defining the last time they were updated.
     * 
     * Keyed by post ID.
     * 
     * @var array
     */
    private $meta_flags = [];
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        $this->define_hooks();
    }
    
    /**
     * Defines the required meta values for service validation.
     * 
     * @since 0.4.0
     */
    private function init_meta_flags() {
        return [
            'team_member_role'  => 0,
            'rate_value'        => 0,
        ];
    }
    
    /**
     * Registers hooks and filters.
     *
     * @since 0.1.0
     */
    private function define_hooks() {
        // Validate single Service
        add_action( 'updated_post_meta', [$this, 'update_meta_flags'], 10, 4 );
        
        // Validate all Services
        add_action( 'update_option_buddyc_components_settings', [$this, 'validate_all_services'] );
        add_action( 'update_option_buddyc_booking_settings', [$this, 'validate_all_services'] );
        add_action( 'buddyc_version_switch', [$this, 'validate_all_services'] );        
        
        // Validate single Quote
        add_action( 'updated_post_meta', [$this, 'update_meta_flags'], 10, 4 );
        
        // Validate all Quotes
        add_action( 'update_option_buddyc_components_settings', [$this, 'validate_all_quotes'] );
        add_action( 'update_option_buddyc_booking_settings', [$this, 'validate_all_quotes'] );
        add_action( 'buddyc_version_switch', [$this, 'validate_all_quotes'] );        

    }
    
    /**
     * Updates a meta flag on an update to the post's meta values.
     * 
     * @since 0.4.0
     * 
     * @param  integer $meta_id    ID of the meta data field
     * @param  integer $post_id    Post ID
     * @param  string $meta_key    Name of meta field
     * @param  string $meta_value  Value of meta field
     */
    public function update_meta_flags( $meta_id, $post_id, $meta_key, $meta_value ) {
        
        // Check post type
        $post_type = get_post_type( $post_id );
        
        if ( $post_type !== 'buddyc_service' && $post_type !== 'buddyc_quote' ) {
            return;
        }
        
        // Get current meta flags
        $meta_flags = isset( $this->meta_flags[$post_id] ) ? $this->meta_flags[$post_id] : $this->init_meta_flags();
        
        // Make sure it's a required meta value
        if ( array_key_exists( $meta_key, $meta_flags ) ) {
            // Increment the flag
            $meta_flags[$meta_key] += 1;
        }
        
        // Update meta flags array
        $this->meta_flags[$post_id] = $meta_flags;
        
        // Check if all flags match
        $all_match = true; // init
        $flag_value = reset( $meta_flags ); // first number
        foreach ( $meta_flags as $key => $value ) {
            if ( $value !== $flag_value ) {
                $all_match = false;
                break;
            }
        }
        
        // Validate if all match
        if ( $all_match ) {
            $this->validate_service( $post_id );
        }
    }
    
    /**
     * Validates a Service or Quote.
     * 
     * @since 0.1.0
     */
    public function validate_service( $post_id ) {
        // Service
        if ( get_post_type( $post_id ) === 'buddyc_service' ) {
            $service = new Service( $post_id );
            $service->validate();
            
        // Quote
        } else if ( get_post_type( $post_id ) === 'buddyc_quote' ) {
            $quote = new Quote( $post_id );
            $quote->validate();
        }
    }
    
    /**
     * Validates all Services.
     * 
     * @since 0.1.0
     */
    public function validate_all_services() {
        // Get all service posts
        $query = new PostQuery( 'buddyc_service' );
        $posts = $query->posts;
        
        // Loop through service IDs and validate
        foreach ( $posts as $post ) {
            $service = new Service( $post->ID );
            $service->validate();
        }
    }
    
    /**
     * Validates a Custom Quote.
     * 
     * @since 0.1.0
     */
    public function validate_quote( $post_id ) {
        if ( class_exists( Quote::class ) ) {
            if ( get_post_type( $post_id ) === 'buddyc_quote' ) {
                $service = new Quote( $post_id );
                $service->validate();
            }
        }
    }
    
    /**
     * Validates all Custom Quotes.
     * 
     * @since 0.1.0
     */
    public function validate_all_quotes() {
        if ( class_exists( Quote::class ) ) {
        
            // Get all quote posts
            $query = new PostQuery( 'buddyc_quote' );
            $posts = $query->posts;
            
            // Loop through service IDs and validate
            foreach ( $posts as $post ) {
                $quote = new Quote( $post->ID );
                $quote->validate();
            }
        }
    }
    
}