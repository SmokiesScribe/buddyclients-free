<?php
namespace BuddyClients\Admin;

/**
 * Defines a single taxonomy.
 *
 * @since 0.1.0
 */
class Taxonomy {
    
    /**
     * The initial args passed.
     * 
     * @var array
     */
    private $args;
    
    /**
     * The completed args needed to register the taxonomy.
     * 
     * @var array
     */
    private $tax_args;
    
    /**
     * Post type.
     * 
     * The slug of the post type the taxonomy should be registered to.
     * 
     * @var string
     */
    public $post_type;
    
    /**
     * Singular name.
     * 
     * @var string
     */
    public $singular_name;
    
    /**
     * Plural name.
     * 
     * @var string
     */
    public $plural_name;
    
    /**
     * Slug.
     * 
     * @var string
     */
    public $slug;
    
    /**
     * Labels for the taxonomy.
     * 
     * @var array
     */
    public $labels;
    
    /**
     * Whether the taxonomy is public.
     * 
     * @var bool
     */
    public $public;
    
    /**
     * Publicly queryable.
     * 
     * Whether the taxonomy can be accessed by URL.
     * 
     * @var bool
     */
    public $publicly_queryable;

   /**
     * Whether the taxonomy is hierarchical (e.g., categories).
     * 
     * Defaults to true.
     * 
     * @var bool
     */
    public $hierarchical;
    
    /**
     * Whether to show the taxonomy in quick edit.
     * 
     * Defaults to true.
     * 
     * @var bool
     */
    public $show_in_quick_edit;
    
    /**
     * Whether to show the taxonomy as an admin column.
     * 
     * Defaults to true.
     * 
     * @var bool
     */
    public $show_admin_column;
    
    /**
     * An array of strings representing the default term for the taxonomy.
     * 
     * Array keys: 'name', 'slug', 'description'
     * 
     * @var array
     */
    public $default_term;
    
    /**
     * Whether the taxonomy should be available in nav menus.
     * 
     * Defaults to false.
     * 
     * @var bool
     */
    public $show_in_nav_menus;
    
    /**
     * Whether the taxonomy should be available in the Gutenberg editor.
     * 
     * Defaults to true.
     * 
     * @var bool
     */
    public $show_in_rest;
    
    /**
     * Whether the taxonomy should show in the admin menu.
     * 
     * True displays the taxonomy as a submenu of the object type.
     * 
     * @var bool
     */
    public $show_in_menu;
    
    /**
     * Array of rewrite rules.
     * 
     * Defaults to false.
     * 
     * @var array|bool
     */
    public $rewrite;
    
    /**
     * The name of the menu.
     * 
     * @var string
     */
    public $menu_name;
    
    /**
     * Taxonomy constructor.
     *
     * @param string $slug The slug of the taxonomy.
     * @param array  $args Arguments to configure the taxonomy.
     */
    public function __construct( $slug, $args ) {
        
        // Extract args
        $this->args = $args;
        
        // Get slug and names
        $this->slug = $slug;
        
        // Define labels
        $this->set_labels();
        
        // Set post type args
        $this->set_args();
        
        // Register the taxonomy
        $this->register_taxonomy();
    }
    
    /**
     * Sets labels for the taxonomy.
     */
    public function set_labels() {
        $this->singular_name = $this->args['singular_name'];
        $this->plural_name   = $this->args['plural_name'];
        $this->menu_name     = $this->args['menu_name'] ?? $this->args['plural_name'];
        
        $this->labels = [
            'name'               => $this->plural_name,
            'singular_name'      => $this->singular_name,
            'add_new'            => __( 'Add New', 'buddyclients-free' ),
            /* translators: %s: singular name of the item */
            'add_new_item'       => sprintf( __( 'Add New %s', 'buddyclients-free' ), $this->singular_name ),
            /* translators: %s: singular name of the item */
            'edit_item'          => sprintf( __( 'Edit %s', 'buddyclients-free' ), $this->singular_name ),
            /* translators: %s: singular name of the item */
            'new_item'           => sprintf( __( 'New %s', 'buddyclients-free' ), $this->singular_name ),
            /* translators: %s: menu name */
            'all_items'          => $this->menu_name,
            /* translators: %s: singular name of the item */
            'view_item'          => sprintf( __( 'View %s', 'buddyclients-free' ), $this->singular_name ),
            /* translators: %s: plural name of the item */
            'search_items'       => sprintf( __( 'Search %s', 'buddyclients-free' ), $this->plural_name ),
            /* translators: %s: plural name of the item */
            'not_found'          => sprintf( __( 'No %s found', 'buddyclients-free' ), strtolower( $this->plural_name ) ),
            /* translators: %s: plural name of the item */
            'not_found_in_trash' => sprintf( __( 'No %s found in trash', 'buddyclients-free' ), strtolower( $this->plural_name ) ),
            'parent_item_colon'  => '',
            /* translators: %s: menu name */
            'menu_name'          => $this->menu_name
        ];        
    }
    
    /**
     * Sets arguments for registering the taxonomy.
     */
    public function set_args() {
        $this->post_type = $this->args['post_type'];
        $this->tax_args = array(
            'labels'             => $this->labels,
            'public'             => $this->args['public'] ?? true,
            'show_in_menu'       => $this->args['show_in_menu'] ?? false,
            'hierarchical'       => $this->args['hierarchical'] ?? true,
            'publicly_queryable' => $this->args['publicly_queryable'] ?? true,
            'show_ui'            => $this->args['show_ui'] ?? true,
            'show_in_quick_edit' => $this->args['show_in_quick_edit'] ?? true,
            'show_admin_column'  => $this->args['show_admin_column'] ?? true,
            'default_term'       => $this->args['default_term'] ?? '',
            'rewrite'            => $this->args['rewrite'] ?? false,
            'has_archive'        => $this->args['has_archive'] ?? true,
            'show_in_nav_menus'  => $this->args['show_in_nav_menus'] ?? false,
            'show_in_rest'       => $this->args['show_in_rest'] ?? true,
        );
    }
    
    /**
     * Registers the taxonomy.
     */
    public function register_taxonomy() {
        register_taxonomy( $this->slug, $this->post_type, $this->tax_args );
    }
}
