<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Config\AssetManager;


/**
 * Admin-specific functionality of the plugin.
 *
 * This class handles admin-specific functionality such as enqueueing styles and scripts.
 *
 * @since 0.1.0
 */
class Admin {
    
	/**
	 * Instance of the class.
	 *
	 * @var Admin The single instance of the class
	 * @since 0.1.0
	 */
	protected static $instance = null;
    	
	/**
	 * BuddyClients Admin Instance.
	 *
	 * @since 0.1.0
	 * @static
	 * @return Admin instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    /**
     * Constructor.
     *
     * Initializes the Admin class.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->includes();
    }

    /**
     * Include necessary files and register hooks and filters.
     *
     * @since 1.0.0
     */
    private function includes() {
        
        // Define hooks
        $this->define_hooks();
        
        // Require helpers
        $this->require_helpers();
        
        // Initialize
        Nav::run();
        RepairButtonManager::run();
    }

    /**
     * Define hooks and filters.
     *
     * @since 1.0.0
     */
    private function define_hooks() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'color_picker']);
        add_action('admin_enqueue_scripts', [$this, 'chart_script']);
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_menu', [$this, 'admin_pages']);
        add_action('init', [$this, 'page_manager']);
        add_action('init', [$this, 'initialize_post_types']);
    }
    
    /**
     * Loads color picker.
     * 
     * @since 0.1.0
     */
    public function color_picker() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('iris');
        wp_enqueue_script('wp-color-picker');
    }
    
    /**
     * Loads chart js.
     * 
     * @since 1.0.2
     */
    public function chart_script() {
        wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], '4.4.4', true );
    }
    
    /**
     * Initializes XprofileManager.
     * 
     * @since 0.1.0
     */
    public function initialize_post_types() {
        ( PostTypeManager::instance() )->run();
    }
    
    /**
     * Initializes PageManager.
     * 
     * @since 0.1.0
     */
    public function page_manager() {
        new PageManager;
    }
    
    /**
     * Adds top-level menu.
     * 
     * @since 0.1.0
     */
    public function menu() {
        if ( ! function_exists( 'add_menu_page' ) ) {
            return;
        }

        // Add primary menu
        add_menu_page(
            'BuddyClients',
            'BuddyClients',
            'manage_options',
            'buddyc-dashboard',
            'buddyc_dashboard_content',
            'dashicons-buddyclients',
            5
        );
        add_submenu_page(
            'buddyc-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'buddyc-dashboard',
            'buddyc_dashboard_content',
            0
        );

        // Add hidden menu
        add_menu_page(
            'Hidden Menu',
            'Hidden Menu',
            'manage_options',
            'buddyc-hidden-menu',
            '' // no callback needed
        );

        // Remove the hidden menu item so it doesn't appear in the admin menu
        remove_menu_page('buddyc-hidden-menu');
    }
    
    /**
     * Adds settings pages.
     * 
     * @since 0.1.0
     */
    public function admin_pages() {
        ( MenuManager::instance() )->run();
    }

    /**
     * Enqueue admin-specific stylesheets.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        $this->enqueue_assets('assets/css');
    }

    /**
     * Enqueue admin-specific JavaScript files.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        $this->enqueue_asset( 'assets/js', 'loading.js' );
        $this->enqueue_assets( 'assets/js' );
    }
    
    /**
     * Require helper functions.
     *
     * @since 1.0.0
     */
    public function require_helpers() {
        $this->enqueue_assets('helpers');
        $this->enqueue_assets('partials');
    }

    /**
     * Enqueues all assets.
     *
     * @since 1.0.0
     *
     * @param string $dir The directory path where assets are located.
     */
    private function enqueue_assets($dir) {
        $asset_manager = new AssetManager( __FILE__, $dir );
        $asset_manager->run();
    }
    
    /**
     * Enqueues a single asset.
     *
     * @since 1.0.0
     *
     * @param   string  $dir        The directory path in which the asset is located.
     * @param   string  $file_name  The file name of the single asset to load.
     */
    private function enqueue_asset( $dir, $file_name ) {
        $asset_manager = new AssetManager( BUDDYC_PLUGIN_FILE, $dir, $file_name );
        $asset_manager->run();
    }
}
