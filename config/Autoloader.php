<?php
namespace BuddyClients\Config;

defined( 'ABSPATH' ) || exit;

use BuddyClients\Config\ComponentsHandler;
use BuddyClients\Config\AssetAutoloader;

/**
 * Autoloads classes.
 * 
 * @since 0.4.3
 */
class Autoloader {
    
    /**
     * Initializes the class.
     * 
     * @since 0.4.3
     */
    public static function init() {
        spl_autoload_register( [ __CLASS__, 'autoload' ] );
    }
    
    /**
     * Autoloads classes.
     * 
     * @since 0.4.3
     */
    private static function autoload( $class ) {    
 

        // Not a BuddyClients class
        if ( strpos( $class, 'BuddyClients' ) === false ) {
            return;
        }
        
        // Format path
        $path = self::get_path( $class );

        // Make sure the file exists
        if ( ! $path || ! file_exists( $path ) ) {  
            return;
        }
        
        // Make sure the component is enabled
        if ( ! self::component_is_enabled( $class ) ) {
            return;
        }
        
        // Require path
        require $path;
        
        // Autoload assets
        self::autoload_assets( $class );
    }
    
    /**
     * Autoloads assets.
     * 
     * @since 1.0.4
     */
    public static function autoload_assets( $class ) {
        if ( class_exists( $class ) ) {
            // Format path
            $path = self::get_path( $class );
            
            // Autoload assets
            new AssetAutoloader( $path );
        }
    }
    
    /**
     * Formats the class name.
     * 
     * @since 1.0.4
     */
    private static function get_path( $class ) {
        if ( ! $class ) {
            return '';
        }

        // Remove primary namespace
        $stripped_class = str_replace('BuddyClients', '', $class);

        $formatted_class = str_replace( '\\', '/', $stripped_class);
        
        // Lowercase dir name
        $parts = explode('/', $formatted_class);
        if (isset($parts[1])) {
            $dir = $parts[1];
            $parts[1] = strtolower($dir);
        }
        
        // Add dir back to class
        $formatted_class = implode('/', $parts);
        
        // Define path
        $path = BC_PLUGIN_DIR . str_replace('/', DIRECTORY_SEPARATOR, $formatted_class) . '.php';

        // Replace duplicate slashes
        $path = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path );
        
        return $path;
    }
    
    /**
     * Checks whether a component is enabled.
     * 
     * @since 0.1.0
     * 
     * @param   string  $class  The fully qualified class name.
     */
    private static function component_is_enabled( $class ) {
        // Make sure it's a component class
        if ( strpos( $class, '\\Components\\' ) === false ) {
            return true;
        } else {
            // Split the string by the "Components" delimiter
            $parts = explode("\\Components\\", $class );
            
            // Extract the first part after "Components"
            $component = explode( "\\", $parts[1] )[0];
            
            // Check if $component is in the array of enabled components
            return ComponentsHandler::in_components( $component );
        }
    }
}
