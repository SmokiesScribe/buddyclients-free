<?php
namespace BuddyClients\Components\Sales;

use BuddyClients\Components\Booking\BookedService\BookedServiceList as BookedServiceList;

use BuddyClients\Includes\{
    CommissionList as CommissionList
};

/**
 * Sales commission profile content.
 * 
 * Generates a list of commission payments for sales team members.
 * 
 * @since 0.1.0
 * 
 * @see ComissionList
 */
class SalesProfile {
    
    /**
     * User ID.
     * 
     * @var int
     */
    public $user_id;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $user_id = null ) {
        $this->user_id = $user_id ?? get_current_user_id();
    }
    
    /**
     * Builds the content.
     * 
     * @since 0.1.0
     */
    public function build() {
        
        // Initialize
        $content = '';
        
        $content .= ( new CommissionList( 'sales' ) )->build( $this->user_id );
        
        // Echo content
        echo $content;
    }
}