<?php
namespace BuddyClients\Admin;

/**
 * Admin page.
 *
 * Creates single admin page.
 */
class AdminPage {
    
    /**
     * Data.
     * 
     * @var array Associative array of data to register submenu.
     */
     public $args;
     
    /**
     * Key used to build slug.
     * 
     * @var string
     */
     public $key;
     
    /**
     * The callback to display the content.
     * 
     * @var callable
     */
     public $callback;
     
    /**
     * An array of data to pass with the callable.
     * 
     * @var array
     */
     public $callback_args;
     
    /**
     * The callback to display the content.
     * 
     * @var callable
     */
     public $callable;
     
    /**
     * The component required for the admin page.
     * 
     * @var string
     */
     private $required_component;

    /**
     * Admin page constructor.
     *
     * Sets up the admin page using the provided data.
     *
     * @since 0.1.0
     * 
     * @param   string  $key    The key for the admin page.
     * @param   array   $args   An associative array of data to configure the admin page.
     *
     *     @type string         $key            The key used to build the slug.
     *     @type string         $parent_slug    The parent menu slug.
     *     @type string         $title          The title of the admin page.
     *     @type callable       $callback       The method or function used to render the page content.
     *                                          Defaults to a method in the SettingsPage class.
     *     @type string         $cap            The capability required to access the page. Default 'manage_options'.
     *     @type string         $menu_order     Optional. The order in which the menu item should appear.
     *                                          Default null.
     *     @type array          $callback_args  An array of data to pass with the callback.
     */
    public function __construct( $key, $args ) {
        
        // Check for required component
        $this->check_component( $args['required_component'] ?? null );
        
        // Assign callback if component enabled
        if ( ! $this->callback ) {
            $this->callable = $args['callable'] ?? null;
            $this->callback_args = $args['callback_args'] ?? null;
            $this->callback = $this->build_callback( $args );
        }
        
        // Handle post type
        // Make sure the component is enabled
        if ( ! $this->required_component || bc_component_enabled( $this->required_component ) ) {
            // Make sure it's a post type
            if ( isset( $args['singular_name'] ) && isset( $args['bc_menu_order'] ) && $args['bc_menu_order'] ) {
                // Extract the data
                $args = $this->post_type_data( $key, $args );
                // Overwrite callback with redirect function
                $this->callback = function() use ( $key ) {
                    // Echo a script to redirect the user
                    echo '<script type="text/javascript">
                            window.location.href = "' . esc_url( admin_url( 'edit.php?post_type=' . $key ) ) . '";
                          </script>';
                };
            }
        }

        // Add submenu page
        $this->add_submenu( $key, $args );
    }
    
    /**
     * Builds the page data for a post type.
     * 
     * @since 0.4.0
     *
     * @param   string  $key    The key for the page.
     * @param   array   $args   The array of data.
     * 
     * @return  array   The data for the post type page.
     */
    private function post_type_data( $key, $args ) {
        return [
            'key' => $key,
            'settings' => false,
            'title' => $args['menu_name'] ?? $args['plural_name'],
            'parent_slug' => 'bc-dashboard',
            'bc_menu_order' => $args['bc_menu_order'] ?? null,
            'group' => $args['group'] ?? null,
            'menu_slug' => 'edit.php?post_type=' . $key
        ];
    }
    
    /**
     * Handles unavailable components.
     * 
     * @since 0.1.0
     * 
     * @param   string  $component  The component to check.
     */
    private function check_component( $component ) {
        
        // Make sure a required component exists
        if ( ! $component ) {
            return;
        }

        // Assign variable
        $this->required_component = $component;
        
        // Check if the component exists
        if ( ! bc_component_exists( $component ) ) {
            $this->callback = [self::class, 'upgrade_link'];
            
        // Check if the component is disabled
        } else if ( ! bc_component_enabled( $component ) ) {
            $this->callback = [$this, 'enable_link'];
        }
    }
    
    /**
     * Generates not available message.
     * 
     * @since 0.1.0
     */
    public static function upgrade_link() {
        echo bc_upgrade_link(true);
    }
    
    /**
     * Generates a link to enable the component.
     * 
     * @since 0.1.0
     */
    public function enable_link() {
        echo bc_enable_component_link( $this->required_component, true );
    }
    
    /**
     * Builds the callback.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args   The arguments to configure the callback.
     * 
     * @return  callable  The callback function.
     */
    private function build_callback( $args ) {
        
        // Check if we're building a settings page
        if ( isset( $args['settings'] ) && $args['settings'] ) {
            
            // Return settings callback
            return [ new SettingsPage( $args ), 'render_page' ];
            
        // Not a settings page
        } else {
            // Return function from callable
            if ( $this->callable ) {
                return function () {
                    // Check if the callback function exists
                    if ( is_callable( $this->callable ) ) {
                        // Call the callback function with arguments
                        call_user_func( $this->callable, $this->callback_args );
                    }
                };
            }
        }
    }
    
    /**
     * Builds slug.
     * 
     * @since 0.1.0
     * 
     * @param   string  $key    The key for the slug.
     * @param   array   $args   The arguments to configure the slug.
     * 
     * @return  string  The generated slug.
     */
    private static function build_slug( $key, $args ) {
        $suffix = isset( $args['settings'] ) && $args['settings'] ? '-settings' : '';
        $key = str_replace( '_', '-', $args['key'] ?? $key );
        return 'bc-' . $key . $suffix;
    }

    /**
     * Adds submenu page.
     * 
     * @since 0.1.0
     * 
     * @param   string  $key    The key for the submenu page.
     * @param   array   $args   The arguments for the submenu page.
     */
    public function add_submenu( $key, $args ) {
        add_submenu_page(
            $args['parent_slug'] ?? '',
            $args['title'] ?? '', // page title
            $args['title'] ?? '', // menu title
            $args['cap'] ?? 'manage_options',
            $args['menu_slug'] ?? self::build_slug( $key, $args ),
            $this->callback,
            $args['menu_order'] ?? null
        );
    }
}
