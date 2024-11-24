<?php
namespace BuddyClients\Config;

use BuddyClients\Includes\DatabaseManager;
use BuddyClients\Admin\Settings;
use BuddyClients\Admin\AdminNotice;

/**
 * Handles plugin components.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
class ComponentsHandler {
    
    /**
     * DatabaseManager instance.
     * 
     * @var DatabaseManager
     */
    private static $database = null;
    
    /**
     * Components data.
     * 
     * @var array
     */
    private $components;
    
    /**
     * Initializes the DatabaseManager.
     * 
     * @since 0.1.0
     */
    private static function init_database() {
        if ( ! self::$database ) {
            self::$database = new DatabaseManager( 'components' );
        }
    }
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        // Initialize DatabaseManager
        self::init_database();
    }
    
    /**
     * Retrieves components.
     * 
     * @since 0.1.0
     */
    public static function get_components() {
        return [
            // Required
            'Booking',
            'Checkout',
            'Service',
            // Core
            'Email',
            'Brief',
        ];
    }
    
    /**
     * Defines required components.
     * 
     * @since 0.1.0
     */
    public static function required_components() {
        return [
            'Booking',
            'Checkout',
            'Service',
        ];
    }
    
    /**
     * Defines dependent components.
     * 
     * @since 0.1.0
     */
    public static function dependent_components() {
        return [];
    }
    
    /**
     * Checks whether an item is in the components.
     * 
     * @since 0.1.0
     * 
     * @param   string  $component  The component to check.
     * @return  bool
     */
    public static function in_components( $component ) {
        
        // Retrieve components
        $components = self::get_components();
        
        // Check if the component is in the array
        if ( ! in_array( $component, $components ) ) {
            return false;
        }
        
        // Make sure the component is not disabled
        $enabled_components = Settings::get_value( 'components', 'components' );
        if ( ! in_array( $component, $enabled_components ) ) {
            return false;
        }
        
        // Get dependent components
        $dependent_components = self::dependent_components();
        
        // Check if the component is dependent
        if ( isset( $dependent_components[$component] ) ) {
            $necessary_component = $dependent_components[$component];
            if ( ! in_array( $necessary_component, $enabled_components ) ) {
                $notice_args = [
                    'repair_link'   => 'admin.php?page=buddyc-components-settings',
                    'message'       => 'The ' . $component . ' component requires the ' . $necessary_component . ' component to be enabled.',
                    'color'         => 'orange'
                ];
                new AdminNotice( $notice_args );
                return false;
            }
        }
        
        // Checks passed
        return true;
    }
    
    /**
     * Checks if the component exists.
     * 
     * @since 0.1.0
     * 
     * @param   string  $component  The component to check.
     * @return  bool
     */
    public static function component_exists( $component ) {
        
        // Retrieve components
        $components = self::get_components();
        
        // Check if the component is in the array
        if ( ! in_array( $component, $components ) ) {
            return false;
        } else {
            return true;
        }        
    }
}