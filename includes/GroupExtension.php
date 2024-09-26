<?php
namespace BuddyClients\Includes;

/**
 * Project group extension.
 * 
 * Creates a tab in a project group.
 * 
 * @since 0.1.0
 */
class GroupExtension extends BP_Group_Extension {
    
    /**
     * An array of args.
     * 
     * @var array
     */
    protected $args;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $args {
     *     An array of arguments to build the nav or subnav.
     * 
     *     @type    callable    $content_callback   The callback method to display the content.
     *     @type    string      $slug               The nav or subnav slug.
     *     @type    string      $name               The nav or subnav tab name.
     *     @type    string      $title              Optional. The title to display. Defaults to name.
     *     @type    int         $position           Optional. The nav or subnav position. Defaults to 30.
     *     @type    bool        $private            Optional. Whether to display the tab only for the profile owner. Defaults to false.
     * }
     */
    public function __construct( $args ) {
        $this->args = $args;
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Defines hooks.
     * 
     * @since 0.1.0
     */
    private function define_hooks() {
        add_action('init', [$this, 'create']);
    }
    
    /**
     * Creates nav item based on type.
     * 
     * @since 0.1.0
     */
    public function create() {
        
        // Exit if private and not owner
        if ( isset( $this->args['private'] ) && $this->args['private'] && ! self::is_owner() ) {
            return;
        }
        
        // Create profile nav item
        if ( $this->args['type'] === 'profile' && is_user_logged_in() ) {
            $this->profile_nav();
            
        // Create account settings subnav item
        } else if ( $this->args['type'] === 'settings' ) {
            $this->account_subnav();
        }
    }
    
    /**
     * Checks whether the current user is the profile owner.
     * 
     * @since 0.1.0
     */
    public static function is_owner() {
        // Get current user and profile user
        $curr_user = get_current_user_id();
        $profile_user = bp_displayed_user_id();
        
        // Check if they are the same
        if ($curr_user === $profile_user) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Generates a profile nav item.
     * 
     * @since 0.1.0
     */
     private function profile_nav() {
        global $bp;
         
        bp_core_new_nav_item( array(
            'name'            => __( $this->args['name'] ),
            'slug'            => $this->args['slug'],
            'parent_url'      => $bp->loggedin_user->domain . $bp->slug . '/',
            'parent_slug'     => $bp->slug,
            'screen_function' => [$this, 'screen'],
            'position'        => $this->args['position'] ?? 30
        ) );
     }
     
     /**
      * Generates an account settings subnav item.
      * 
      * @since 0.1.0
      */
     private function account_subnav() {
        global $bp;
    
        bp_core_new_subnav_item(array(
            'name' => $this->args['name'],
            'slug' => $this->args['slug'],
            'data-bp-user-scope' => 'profile',
            'position' => $this->args['position'] ?? 30,
            'screen_function' => [$this, 'screen'],
            'show_for_displayed_user' => true,
            'parent_url' => trailingslashit($bp->loggedin_user->domain . $bp->slug . "settings"),
            'parent_slug' => 'settings',
            'user_has_access' => bp_core_can_edit_settings(),
        ));
     }
     
    /**
     * Screen callback.
     * 
     * @since 0.1.0
     */
    function screen() {
        add_action( 'bp_template_title', [$this, 'title'] );
        add_action( 'bp_template_content', $this->args['content_callback'] );
        bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
    }
    
    /**
     * Title callback.
     * 
     * @since 0.1.0
     */
    function title() {
        echo esc_html__( $this->args['title'] ?? $this->args['name'] );
    }
    
    /**
     * Builds a user link to the profile screen.
     * 
     * @since 0.1.0
     * 
     * @param   string     $slug    The profile tab slug.
     */
    public static function link( $slug ) {
        $user_id = $user_id ?? get_current_user_id();
        $profile_link = bp_core_get_userlink( $user_id, false, true );
        return $profile_link . trailingslashit( $slug );
    }
}