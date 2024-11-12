<?php
namespace BuddyClients\Config;

use BuddyClients\Config\AssetManager;

/**
 * Autoloads class assets.
 * 
 * Retrieves and loads items in the assets folder for an activated class.
 * 
 * @since 0.1.0
 */
class AssetAutoloader {
    
    /**
     * Loaded assets cache.
     * 
     * @var array
     */
    private static $loaded_assets = [];
    
    /**
     * Path to class file.
     * 
     * @var string
     */
    private $path;
    
    /**
     * Handle from class name.
     * 
     * @var string
     */
    private $handle;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param string $path Path to class file.
     */
    public function __construct( $path ) {
        $this->path = $path;
        
        // Do not autoload admin assets
        if ( strpos( $this->path, 'admin' ) !== false ) {
            return;
        }
        
        // Exit if assets already loaded
        if ( ! $this->check_cache() ) {
            return;
        }
        
        // Define all hooks
        $this->define_hooks();
        
        // Require helpers
        $this->enqueue_assets('helpers');
    }
    
    /**
     * Checks previously loaded assets.
     * 
     * @since 0.1.0
     */
    private function check_cache() {
        // Get directory
        $dir = plugin_dir_path( $this->path );
        
        if ( ! isset( self::$loaded_assets[ $dir ] ) ) {
            // Not loaded previously
            self::$loaded_assets[ $dir ] = true;
            return true;
        } else {
            // Loaded previously
            return false;
        }
    }
    
    /**
     * Defines hooks.
     * 
     * @since 0.1.0
     */
    public function define_hooks() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    /**
     * Enqueue scripts and styles.
     *
     * @since 0.1.0
     */
    public function enqueue_scripts() {
        $this->enqueue_assets('assets/css');
        $this->enqueue_assets('assets/js');
    }

    /**
     * Enqueue all assets.
     *
     * @since 0.1.0
     *
     * @param string $dir The directory path where assets are located.
     */
    public function enqueue_assets( $dir ) {
        $asset_manager = new AssetManager( $this->path, $dir );
        $asset_manager->run();
    }
}