<?php
namespace BuddyClients\Config;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\DatabaseManager;

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

        // Check if available components have changed
        $prev_components = get_option( '_buddyc_available_components', [] );

        // Retrieve components
        $components = self::get_components();

        if ( $prev_components !== $components ) {

            // Update option
            set_option( '_buddyc_available_components', $components );

            /**
             * Fires when available components are changed.
             * 
             * @since 1.0.26
             */
            do_action( 'buddyc_available_components_updated' );
        }
        
        // Check if the component is in the array
        if ( ! in_array( $component, $components ) ) {
            return false;
        }
        
        // Make sure the component is not disabled
        $enabled_components = buddyc_get_setting( 'components', 'components' );
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
                buddyc_admin_notice( $notice_args );
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