<?php
namespace BuddyClients\Admin;

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
	 * Checks archive page style settings.
	 * 
	 * @since 0.1.0
	 */
	private static function has_archive( $archive ) {
	    $setting = bc_get_setting( 'style', $archive );
	    return $setting === 'bc_custom' ? false : true;
	}
    
    /**
     * Defines post types.
     * 
     * @since 0.1.0
     */
    public static function post_types() {
        $post_types = [
            'bc_service' => [
                'singular_name'         => __( 'Service', 'buddyclients' ),
                'plural_name'           => __( 'Services', 'buddyclients' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => true,
                'supports'              => array('title', 'editor', 'excerpt', 'custom-fields'),
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'show_in_nav_menus'     => true,
                'show_in_rest'          => true,
                'rewrite'               => array('slug' => 'services'),
                'bc_menu_order'         => 2
            ],
            'bc_adjustment' => [
                'singular_name'         => __( 'Rate Adjustment', 'buddyclients' ),
                'plural_name'           => __( 'Rate Adjustments', 'buddyclients' ),
                'show_in_menu'          => false,
                'has_archive'           => false,
                'public'                => true,
                'supports'              => array('title'),
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
            ],
            'bc_rate_type' => [
                'singular_name'         => __( 'Rate Type', 'buddyclients' ),
                'plural_name'           => __( 'Rate Types', 'buddyclients' ),
                'show_in_menu'          => false,
                'has_archive'           => false,
                'public'                => true,
                'supports'              => array('title'),
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
            ],
            'bc_service_type' => [
                'singular_name'         => __( 'Service Type', 'buddyclients' ),
                'plural_name'           => __( 'Service Types', 'buddyclients' ),
                'show_in_menu'          => false,
                'has_archive'           => false,
                'public'                => true,
                'supports'              => array('title'),
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
            ],
            'bc_role' => [
                'singular_name'         => __( 'Team Member Role', 'buddyclients' ),
                'plural_name'           => __( 'Team Member Roles', 'buddyclients' ),
                'show_in_menu'          => false,
                'has_archive'           => false,
                'public'                => true,
                'supports'              => array('title'),
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
            ],
            'bc_email' => [
                'singular_name'         => __( 'Email Template', 'buddyclients' ),
                'plural_name'           => __( 'Email Templates', 'buddyclients' ),
                'menu_name'             => __( 'Emails', 'buddyclients' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
                'supports'              => array('title', 'editor'),
                'bc_menu_order'         => 3
            ],
            'bc_brief' => [
                'singular_name'         => __( 'Brief', 'buddyclients' ),
                'plural_name'           => __( 'Briefs', 'buddyclients' ),
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
                'bc_menu_order'         => 4
            ],
            'bc_brief_field' => [
                'singular_name'         => __( 'Brief Field', 'buddyclients' ),
                'plural_name'           => __( 'Brief Fields', 'buddyclients' ),
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
            'bc_legal' => [
                'singular_name'         => __( 'Legal Agreement', 'buddyclients' ),
                'plural_name'           => __( 'Legal Agreements', 'buddyclients' ),
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
            'bc_legal_mod' => [
                'singular_name'         => __( 'Legal Modification', 'buddyclients' ),
                'plural_name'           => __( 'Legal Modification', 'buddyclients' ),
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
            'bc_quote' => [
                'singular_name'         => __( 'Custom Quote', 'buddyclients' ),
                'plural_name'           => __( 'Custom Quotes', 'buddyclients' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'show_in_rest'          => false,
                'supports'              => array('title'),
                'required_component'    => 'Quote',
                'bc_menu_order'         => 6
            ],
            'bc_testimonial' => [
                'required_component'    => 'Testimonial',
                'singular_name'         => __( 'Testimonial', 'buddyclients' ),
                'plural_name'           => __( 'Testimonials', 'buddyclients' ),
                'show_in_menu'          => false,
                'public'                => true,
                'has_archive'           => true,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'show_in_nav_menus'     => true,
                'show_in_rest'          => false,
                'supports'              => array('title', 'editor', 'excerpt', 'thumbnail'),
                'rewrite'               => array('slug' => 'testimonials'),
                'bc_menu_order'         => 5
            ],
            'bc_filter' => [
                'singular_name'         => __( 'Filter Field', 'buddyclients' ),
                'plural_name'           => __( 'Filter Fields', 'buddyclients' ),
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
            'bc_file_upload' => [
                'singular_name'         => __( 'File Upload Type', 'buddyclients' ),
                'plural_name'           => __( 'File Upload Types', 'buddyclients' ),
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
         $post_types = apply_filters( 'bc_post_types', $post_types );

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
