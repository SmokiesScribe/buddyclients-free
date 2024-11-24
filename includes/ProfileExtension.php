<?php
namespace BuddyClients\Includes;

/**
 * Profile or accoutn settings extension.
 * 
 * Creates a tab in the user profile page or account settings subnav.
 * 
 * @since 0.1.0
 */
class ProfileExtension {
    
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
     *     @type    string      $type               Accepts 'profile' or 'settings'.
     *     @type    callable    $content_callback   The callback method to display the content.
     *     @type    string      $slug               The nav or subnav slug.
     *     @type    string      $name               The nav or subnav tab name.
     *     @type    string      $title              Optional. The title to display. Defaults to name.
     *     @type    int         $position           Optional. The nav or subnav position. Defaults to 30.
     *     @type    bool        $private            Optional. Whether to display the tab only for the profile owner. Defaults to false.
     *     @type    string      $member_type        Optional. Type of user to restrict to. Accepts 'team', 'client', and 'sales'. Defaults to null.
     *     @type    string      $icon               Optional. The icon for account settings extensions.
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
        
        // Exit if member type not a match
        if ( isset( $this->args['member_type'] ) && ! self::is_member_type( $this->args['member_type'] ) ) {
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
     * Checks whether the member type matches that of the profile owner.
     * 
     * @since 0.1.0
     * 
     * @param   string  $member_type    The member type to restrict to. Accepts 'team', 'client', and 'sales'.
     */
    private static function is_member_type( $member_type ) {
        
        // Initialize
        $match = false;
        
        // Get profile owner id
        $profile_user = bp_displayed_user_id();
        
        // Check member type
        switch ( $member_type ) {
            case 'team':
                $match = buddyc_is_team( $profile_user );
                break;
            case 'client':
                $match = buddyc_is_client( $profile_user );
                break;
            case 'sales':
                $match = buddyc_is_sales( $profile_user );
                break;
        }
        
        return $match;
    }
    
    /**
     * Generates a profile nav item.
     * 
     * @since 0.1.0
     */
     private function profile_nav() {
        global $bp;
         
        bp_core_new_nav_item( array(
            'name'            =>  $this->args['name'],
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
        echo esc_html( $this->args['title'] ?? $this->args['name'] );
    }
    
    /**
     * Builds a user link to the profile screen.
     * 
     * @since 0.1.0
     * 
     * @param   string     $slug        The profile tab slug.
     * @param   int         $user_id    Optional. The ID of the user.
     *                                  Defaults to current user.
     */
    public static function link( $slug, $user_id = null ) {
        $user_id = $user_id ?? get_current_user_id();
        $profile_link = bp_core_get_userlink( $user_id, false, true );
        return trailingslashit( $profile_link ) . $slug;
    }
}