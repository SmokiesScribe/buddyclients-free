<?php
namespace BuddyClients\Admin;

use BuddyClients\Includes\Loader as Loader;

/**
 * Post type.
 *
 * Creates a single custom post type.
 */
class PostType {
    
    /**
     * The completed args needed to register the post type.
     * 
     * @var array
     */
    private $post_type_args;
    
    /**
     * Labels for the post type.
     * 
     * @var array
     */
    private $labels;
    
    /**
     * The slug of the post type.
     * 
     * @var string
     */
    private $slug;
    
    /**
     * Constructor method.
     * 
     * @param   string  $slug   Slug of the post type.
     * @param   array   $args {
     *     Array of args to build the post type.
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
     *     @type    string          $required_component     The component required for this post type.
     *     @type    string          $menu_icon              The menu dashicon. 
     * }
     */
    public function __construct( $slug, $args ) {
        
        // Make sure the required component exists
        if ( isset( $args['required_component'] ) ) {
            if ( ! bc_component_enabled( $args['required_component'] ) ) {
                return;
            }
        }
        
        // Get slug
        $this->slug = $slug;
        
        // Define labels
        $this->set_labels( $args );
        
        // Set post type args
        $this->set_args( $args );
        
        // Register post type
        $this->register_post_type();
        
        // Define hooks
        //$this->define_hooks();
    }
    
    /**
     * Defines hooks.
     * 
     * @since 0.1.0
     */
    private function define_hooks() {
        add_action( 'init', [$this, 'register_post_type'] );
    }
    
    /**
     * Sets labels for the post type.
     */
    public function set_labels( $args ) {
        $singular_name = $args['singular_name'];
        $plural_name   = $args['plural_name'];
        $menu_name     = $args['menu_name'] ?? $args['plural_name'];
        
        $this->labels = array(
            'name'               => __( $plural_name, 'buddyclients' ),
            'singular_name'      => __( $singular_name, 'buddyclients' ),
            'add_new'            => __( 'Add New', 'buddyclients' ),
            'add_new_item'       => __( 'Add New ' . $singular_name, 'buddyclients' ),
            'edit_item'          => __( 'Edit ' . $singular_name, 'buddyclients' ),
            'new_item'           => __( 'New ' . $singular_name, 'buddyclients' ),
            'all_items'          => __( $menu_name, 'buddyclients' ),
            'view_item'          => __( 'View ' . $singular_name, 'buddyclients' ),
            'search_items'       => __( 'Search ' . $plural_name, 'buddyclients' ),
            'not_found'          => __( 'No ' . strtolower($plural_name) . ' found', 'buddyclients' ),
            'not_found_in_trash' => __( 'No ' . strtolower($plural_name) . ' found in trash', 'buddyclients' ),
            'parent_item_colon'  => '',
            'menu_name'          => __( $menu_name, 'buddyclients' ),
        );
    }
    
    /**
     * Sets arguments for registering the post type.
     */
    public function set_args( $args ) {
        $this->post_type_args = array(
            'labels'             => $this->labels,
            'public'             => $args['public'] ?? true,
            'show_in_menu'       => $args['show_in_menu'] ?? true,
            'publicly_queryable' => $args['publicly_queryable'] ?? true,
            'show_ui'            => $args['show_ui'] ?? true,
            'rewrite'            => $args['rewrite'] ?? false,
            'capability_type'    => $args['capability_type'] ?? 'post',
            'has_archive'        => $args['has_archive'] ?? true,
            'hierarchical'       => $args['hierarchical'] ?? false,
            'exclude_from_search'=> $args['exclude_from_search'] ?? false,
            'show_in_nav_menus'  => $args['show_in_nav_menus'] ?? false,
            'show_in_rest'       => $args['show_in_rest'] ?? true,
            'menu_position'      => $args['menu_position'] ?? null,
            'supports'           => $args['supports'] ?? array('title', 'editor'),
            'menu_icon'          => $args['menu_icon'] ?? ''
        );
    }
    
    /**
     * Registers the custom post type.
     */
    public function register_post_type() {
        register_post_type( $this->slug, $this->post_type_args );
    }
}
