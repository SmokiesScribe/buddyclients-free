<?php
namespace BuddyClients\Includes;

use BuddyClients\Admin\Settings;

/**
 * Freelancer data for Freelancer Mode.
 * 
 * Retrieves user data for the specified freelancer.
 *
 * @since 0.1.0
 */
class Freelancer {
    
    /**
     * The freelancer ID.
     * 
     * False if disabled.
     * 
     * @var int|bool
     */
     public $ID;
     
    /**
     * Whether Freelancer Mode is enabled.
     * 
     * @var bool
     */
     public $enabled;
     
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param int $post_id
     */
    public function __construct() {
        
        // Check Freelancer Mode setting
        $freelancer_id = bc_get_setting( 'booking', 'freelancer_id' );
        
        // Is enabled
        $this->enabled = ( $freelancer_id && $freelancer_id !== '' ) ? true : false;
        
        // Get Freelancer ID
        $this->ID = $this->enabled ? $freelancer_id : null;
    }
}