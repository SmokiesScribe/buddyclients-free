<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'BuddyClientsFree' ) ) {

	/**
	 * BuddyClientsFree Main Class.
	 * 
	 * @since 0.1.0
	 * 
	 * @internal
	 */
	#[\AllowDynamicProperties]
	final class BuddyClientsFree {
	    
		/**
		 * The single instance of the main class.
		 * 
		 * @since 0.1.0
		 */
		protected static $instance = null;
    
    	/**
    	 * @var bool Whether BuddyBoss theme is installed.
    	 */
    	public $bb_theme;
    	
    	/**
    	 * Generates the main BuddyClientsFree instance.
    	 *
    	 * Ensures only one instance of BuddyClients is loaded.
    	 *
    	 * @since 0.1.0
    	 */
    	public static function instance() {
    		if ( is_null( self::$instance ) ) {
    			self::$instance = new self();
    		}
    		return self::$instance;
    	}
    	
    	/**
    	 * Prevents cloning.
    	 *
    	 * @since 0.1.0
    	 */
    	public function __clone() {
    		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'buddyclients-free' ), '0.1.0' );
    	}
    	/**
    	 * Prevents unserializing instances of this class.
    	 *
    	 * @since 0.1.0
    	 */
    	public function __wakeup() {
    		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'buddyclients-free' ), '0.1.0' );
    	}
    
    	/**
    	 * BuddyClients constructor.
    	 */
    	public function __construct() {
    		$this->constants();
    		$this->setup_globals();
    		$this->autoload();
    		$this->includes();
    	}
    
    	/** Private Methods *******************************************************/
    
    	/**
    	 * Bootstrap constants.
    	 *
    	 * @since 0.1.0
    	 */
    	private function constants() {
    	    
    	    // Plugin name
    		if ( ! defined( 'BUDDYC_PLUGIN_NAME' ) ) {
    			define( 'BUDDYC_PLUGIN_NAME', 'BuddyClients Free' );
    		}
    		// Path and URL.
    		if ( ! defined( 'BUDDYC_PLUGIN_DIR' ) ) {
    			define( 'BUDDYC_PLUGIN_DIR', plugin_dir_path(__FILE__) );
    		}
    
    		if ( ! defined( 'BUDDYC_PLUGIN_URL' ) ) {
    			define( 'BUDDYC_PLUGIN_URL', plugin_dir_url(__FILE__) );
    		}

			// Vendor dir
			if ( ! defined( 'BUDDYC_VENDOR_DIR' ) ) {
				define( 'BUDDYC_VENDOR_DIR', BUDDYC_PLUGIN_DIR . 'vendor' );
			}
    		
    		// BuddyClients website
            if ( ! defined( 'BUDDYC_URL' ) ) {
    			define( 'BUDDYC_URL', 'https://buddyclients.com' );
    		}
		}
    
    	/**
    	 * Defines global variables.
    	 *
    	 * @since 0.1.0
    	 */
    	private function setup_globals() {
    		$this->file       = constant( 'BUDDYC_PLUGIN_FILE' );
    		$this->basename   = basename( constant( 'BUDDYC_PLUGIN_DIR' ) );
    		$this->plugin_dir = trailingslashit( constant( 'BUDDYC_PLUGIN_DIR' ) );
    		$this->plugin_url = constant( 'BUDDYC_PLUGIN_URL' );
    		$this->vendor_dir = BUDDYC_PLUGIN_DIR . '/vendor';
    		$this->bb_theme   = function_exists('buddyboss_theme_register_required_plugins') ? true : false;
    	}
    	
    	/**
    	 * Includes and initializes the autoloader.
    	 * 
    	 * @since 0.4.3
    	 */
    	private function autoload() {
    		require_once( plugin_dir_path( __FILE__ ) . 'config/Autoloader.php' );
    		BuddyClients\Config\Autoloader::init();
    	}
    
    	/**
    	 * Includes required core files.
    	 *
    	 * @since 0.1.0
    	 */
    	private function includes() {
    	    
    		// Require settings function
    		require_once( plugin_dir_path( __FILE__ ) . 'includes/helpers/settings.php' );
    		
    		// Run activator
    		add_action( 'init', [$this, 'activate'] );
    		
    		// Initialize classes
    		$this->init_classes();
    		
    		// Load component assets
    		$this->load_component_assets();
    		
    		// Require helpers
    		$this->require_helpers();
    		
    		// Initialize admin
    		$this->init_admin();
            
            // Define all hooks
            $this->define_hooks();
    	}
    	
    	/**
    	 * Initializes the Admin class.
    	 * 
    	 * @since 0.1.0
    	 */
    	public function init_admin() {
    	    BuddyClients\Admin\Admin::instance();
    	}
    	
        /**
         * Runs activation methods.
         * 
    	 * @since 0.1.0
    	 */
        public function activate() {
            BuddyClients\Config\Activator::activate();
        }
        
    	/**
    	 * Loads component assets.
    	 * 
    	 * @since 1.0.4
    	 */
    	private function load_component_assets() {
    	    $classes = [
    	        'BuddyClients\Components\Booking\BookingIntent',
    	        'BuddyClients\Components\Brief\Brief',
    	        'BuddyClients\Components\Quote\Quote',
    	        'BuddyClients\Components\Stripe\StripeKeys',
    	    ];
    	    foreach ( $classes as $class ) {
    	        BuddyClients\Config\Autoloader::autoload_assets( $class );
    	    }
    	}
    	
    	/**
    	 * Initializes components.
    	 * 
    	 * @since 0.1.0
    	 */
    	private function init_classes() {
    	    $classes = [
    	        'BuddyClients\Config\UpdateManager',
    	        'BuddyClients\Includes\ExtensionManager',
    	        'BuddyClients\Includes\AlertManager',
    	        'BuddyClients\Components\Service\ServiceHandler'
    	    ];
    	    foreach ( $classes as $class ) {
    	        if ( class_exists( $class ) ) {
    	            new $class;
    	        }
    	    }
    	}
    	
        /**
         * Registers hooks and filters.
         *
         * @since 0.1.0
         */
        private function define_hooks() {
            
            // Global scripts and styles to wp and admin
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
            
            // Shortcodes
            add_action('wp', [$this, 'register_shortcodes']);
            
            // Form submissions
            add_action('init', [$this, 'form_submission']);            
        }
    
        /**
         * Enqueue global JavaScript files.
         *
         * @since 0.1.0
         */
        public function enqueue_scripts() {
            // Loading script
            $this->enqueue_asset( 'assets/js', 'loading.js' );

			// Load Font Awesome
			$this->enqueue_font_awesome();
								
            // All CSS
            $this->enqueue_assets('assets/css');
            
            // AlL JS
            $this->enqueue_assets('assets/js');
        }

		/**
		 * Registers and enqueues the Font Awesome stylesheet.
		 * 
		 * @since 1.0.20
		 */
		private function enqueue_font_awesome() {
			// Register the FontAwesome stylesheet
			wp_register_style(
				'font-awesome-stylesheet', 
				plugins_url('vendor/fortawesome/font-awesome/css/all.min.css', __FILE__), 
				array(), 
				'6.5.1'
			);

			// Enqueue the registered stylesheet
			wp_enqueue_style('font-awesome-stylesheet');
		}
        
        /**
         * Requires helper functions.
         *
         * @since 0.1.0
         */
        public function require_helpers() {
            $this->enqueue_assets('includes/helpers');
        }
        
        /**
         * Registers shortcodes.
         *
         * @since 0.1.0
         */
        public function register_shortcodes() {
            BuddyClients\Includes\Shortcodes::run();
        }
        
        /**
         * Checks for form submissions.
         *
         * @since 0.1.0
         */
        public function form_submission() {
            new BuddyClients\Includes\FormSubmission();
        }
    
        /**
         * Enqueues all assets.
         *
         * @since 0.1.0
         *
         * @param string $dir The directory path where assets are located.
         */
        private function enqueue_assets( $dir ) {
            $asset_manager = new BuddyClients\Config\AssetManager( __FILE__, $dir );
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
            $asset_manager = new BuddyClients\Config\AssetManager( BUDDYC_PLUGIN_FILE, $dir, $file_name );
            $asset_manager->run();
        }
    }
}