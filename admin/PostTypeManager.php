<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles the registration of custom post types.
 *
 * @since 0.1.0
 */
class PostTypeManager {
	
    /**
     * Post types.
     * 
     * Post types to register.
     * 
     * @var array
     */
    static private $post_types;
    
	/**
	 * Instance of the class.
	 *
	 * @var PostTypeManager The single instance of the class
	 * @since 0.4.0
	 */
	protected static $instance = null;
	
	/**
	 * PostTypeManager Instance.
	 *
	 * @since 0.4.0
	 * @static
	 * @return PostTypeManager instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    /**
     * Define post types.
     * 
     * @since 0.1.0
     */
    public function run() {
        
		// Define hooks
        self::define_hooks();
        
        // Register post types
        foreach (self::post_types() as $slug => $args) {
            new PostType( $slug, $args );
            AdminColumns::get_instance( $slug );
        }
        
        // Initialize TaxManager
        TaxManager::run();
    }
	
    /**
     * Defines hooks and filters.
     *
     * @since 1.0.0
     */
    private static function define_hooks() {
        add_action('admin_init', [self::class, 'add_metaboxes']);
    }
    
    /**
     * Defines post types.
     * 
     * @since 0.1.0
     */
    public static function post_types() {
        $post_types = [
            'buddyc_service' => [
                'singular_name'         => __( 'Service', 'buddyclients-lite' ),
                'plural_name'           => __( 'Services', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => true,
                'supports'              => array('title', 'editor', 'excerpt', 'custom-fields'),
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'show_in_nav_menus'     => true,
                'show_in_rest'          => true,
                'rewrite'               => array('slug' => 'services'),
                'buddyc_menu_order'         => 2
            ],
            'buddyc_adjustment' => [
                'singular_name'         => __( 'Rate Adjustment', 'buddyclients-lite' ),
                'plural_name'           => __( 'Rate Adjustments', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'has_archive'           => false,
                'public'                => true,
                'supports'              => array('title'),
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
            ],
            'buddyc_rate_type' => [
                'singular_name'         => __( 'Rate Type', 'buddyclients-lite' ),
                'plural_name'           => __( 'Rate Types', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'has_archive'           => false,
                'public'                => true,
                'supports'              => array('title'),
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
            ],
            'buddyc_service_type' => [
                'singular_name'         => __( 'Service Type', 'buddyclients-lite' ),
                'plural_name'           => __( 'Service Types', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'has_archive'           => false,
                'public'                => true,
                'supports'              => array('title'),
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
            ],
            'buddyc_role' => [
                'singular_name'         => __( 'Team Member Role', 'buddyclients-lite' ),
                'plural_name'           => __( 'Team Member Roles', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'has_archive'           => false,
                'public'                => true,
                'supports'              => array('title'),
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
            ],
            'buddyc_email' => [
                'singular_name'         => __( 'Email Template', 'buddyclients-lite' ),
                'plural_name'           => __( 'Email Templates', 'buddyclients-lite' ),
                'menu_name'             => __( 'Emails', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
                'supports'              => array('editor'),
                'buddyc_menu_order'         => 3
            ],
            'buddyc_brief' => [
                'singular_name'         => __( 'Brief', 'buddyclients-lite' ),
                'plural_name'           => __( 'Briefs', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => true,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
                'supports'              => array('title'),
                'rewrite'               => array('slug' => 'brief'),
                'required_component'    => 'Brief',
                'buddyc_menu_order'         => 4
            ],
            'buddyc_brief_field' => [
                'singular_name'         => __( 'Brief Field', 'buddyclients-lite' ),
                'plural_name'           => __( 'Brief Fields', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => false,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => true,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
                'supports'              => array('title'),
                'required_component'    => 'Brief'
            ],
            'buddyc_legal' => [
                'singular_name'         => __( 'Legal Agreement', 'buddyclients-lite' ),
                'plural_name'           => __( 'Legal Agreements', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => false,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => true,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => true,
                'supports'              => array('editor'),
                'required_component'    => 'Legal',
            ],
            'buddyc_legal_mod' => [
                'singular_name'         => __( 'Legal Modification', 'buddyclients-lite' ),
                'plural_name'           => __( 'Legal Modification', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => false,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => true,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => true,
                'supports'              => array('title', 'editor'),
                'required_component'    => 'Legal',
            ],
            'buddyc_quote' => [
                'singular_name'         => __( 'Custom Quote', 'buddyclients-lite' ),
                'plural_name'           => __( 'Custom Quotes', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
                'supports'              => array('title'),
                'required_component'    => 'Quote',
                'buddyc_menu_order'         => 6
            ],
            'buddyc_testimonial' => [
                'required_component'    => 'Testimonial',
                'singular_name'         => __( 'Testimonial', 'buddyclients-lite' ),
                'plural_name'           => __( 'Testimonials', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => true,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'show_in_nav_menus'     => true,
                'show_in_rest'          => false,
                'supports'              => array('title', 'editor', 'excerpt', 'thumbnail'),
                'rewrite'               => array('slug' => 'testimonials'),
                'buddyc_menu_order'         => 5
            ],
            'buddyc_filter' => [
                'singular_name'         => __( 'Filter Field', 'buddyclients-lite' ),
                'plural_name'           => __( 'Filter Fields', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
                'supports'              => array('title'),
                'required_component'    => 'Booking',
            ],
            'buddyc_file_upload' => [
                'singular_name'         => __( 'File Upload Type', 'buddyclients-lite' ),
                'plural_name'           => __( 'File Upload Types', 'buddyclients-lite' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
                'supports'              => array('title')
            ],
        ];
        
        /**
         * Filters the plugin post types.
         *
         * @since 0.1.0
         *
         * @param array  $post_types
         *     An array of post type args keyed by slug. {
         * 
         *     @type    string          $singular_name          The singular name of the post type.
         *     @type    string          $plural_name            The plural name of the post type.
         *     @type    bool            $show_in_menu           Whether to display the post type in the admin menu.
         *     @type    bool            $public                 Whether the post type is intended to be publicly queryable.
         *     @type    bool            $has_archive            Whether the post type should have an archive page.
         *     @type    array           $supports               An array of features supported by the post type.
         *     @type    bool            $exclude_from_search    Whether to exclude the post type from front-end search results.
         *     @type    bool            $publicly_queryable     Whether the post type is intended to be queried publicly (in front-end queries).
         *     @type    bool            $show_in_nav_menus      Whether to include the post type in navigation menus.
         *     @type    bool            $show_in_rest           Whether the post type is available in the REST API.
         * }
         */
         $post_types = apply_filters( 'buddyc_post_types', $post_types );

         return $post_types;
    }
    
    /**
     * Checks setting.
     * 
     * @since 0.1.0
     * 
     * @todo update testimonial archive etc.
     */
    private function check_setting() {
        $settings = SettingsManager::instance();
    }
    
    /**
     * Adds metaboxes.
     * 
     * @since 0.1.0
     */
    static public function add_metaboxes( $slug ) {
        foreach (self::post_types() as $slug => $args) {
            new Metaboxes( $slug );
        }
    }
}
