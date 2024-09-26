<?php
namespace BuddyClients\Includes;

/**
 * Generates options for dropdown and checkbox fields.
 * 
 * @since 0.1.0
 */
class Options {
    
    /**
     * The format of the options array.
     * 
     * Defaults to simple.
     * 
     * @var string
     */
    private $format;
    
    /**
     * The key denoting which options to generate.
     * 
     * @var string
     */
    private $key;
    
    /**
     * The array of generated options.
     * 
     * @var array
     */
    public $options;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   string  $key        The key denoting which options to generate.
     *                              Accepts 'users', 'projects', 'posts', 'tax'
     * @param   array   $args       An array of arguments. {
     *     An array of arguments used to generate the options array.
     * 
     *     @param   string  $format     Optional. The format of the options array.
     *                                  Accepts 'simple' and 'detail'. Defaults to 'simple'.
     *     @param   mixed   $post_type  The post type or array of post types to use when generating post options.
     *     @param   mixed   $taxonomy   The taxonomy or array of taxonomies to use when generating taxonomy term options.
     *     @param   string  $user_type  The type of user to use when generating user options.
     *                                  Accepts 'client', 'team', 'affiliate'. Null retrieves all users.
     *     @param   array   $existing   Optional. Array of key value pairs representing the post type and meta key to check.
     *                                  If the xprofile field exists as a value, it will be excluded from the options.
     * }
     */
    public function __construct( $key, $args = null ) {
        $this->key = $key;
        $this->format = $args['format'] ?? 'simple';
        $this->generate_options( $args );
    }
    
    /**
     * Builds the array.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args   Optional. An array of args to pass to the callback.
     */
    public function generate_options( $args ) {
        // Call method directly
        $callable = [$this, $this->key];
        if ( is_callable( $callable ) ) {
            $this->options = call_user_func_array( $callable, [$args] );
        }
        
        // Convert to detailed if  necessary
        if ( $this->format === 'detail' ) {
            $this->options = self::convert_to_detailed( $this->options );
        }
    }
    
    /**
     * Converts simple associative array to detailed options format.
     * 
     * @since 0.2.9
     * 
     * @param   array   $options    The simple associative array to convert.
     */
    public static function convert_to_detailed( $options ) {
        
        // Exit if it's not an array
        if ( ! is_array( $options ) ) {
            return [];
        }
        
        // Initialize
        $detailed_options = [];
        
        // Loop through options
        foreach ( $options as $key => $label ) {
            $detailed_options[$key] = [
                'label' => $label,
                'value' => $key,
            ];
        }
        
        return $detailed_options;
    }
    
    /**
     * Checks whether a string is a post type slug.
     * 
     * @since 0.2.9
     * 
     * @param   string  $slug   The string to check.
     * 
     * @return  bool
     */
    private function is_post_type( $slug ) {
        return post_type_exists( $slug );
    }
    
    /**
     * Checks whether a string is a taxonomy slug.
     * 
     * @since 0.2.9
     * 
     * @param   string  $slug   The string to check.
     * 
     * @return  bool
     */
    private function is_taxonomy( $slug ) {
        return taxonomy_exists( $slug );
    }
    
    /**
     * Generates user options.
     * 
     * @since 0.2.9
     * 
     * @param   array   $args  An array of arguments passed from the constructor.
     */
    private function users( $args ) {
        switch ( $args['user_type'] ?? null ) {
            case 'team':
                $users = bc_all_team();
                break;
            case 'client':
                $users = bc_all_clients();
                break;
            case 'affiliate':
                $users = bc_all_affiliates();
                break;
            default:
                $users = bp_core_get_users( ['per_page' => false, 'type' => 'alphabetical'] );
                break;
        }
            
        // Initialize options
        $options = [];
        
        // Check if users were found
        if (isset($users['users'])) {
            
            // Make sure it's an array
            if (is_array($users['users'])) {
                
                // Loop through the users
                foreach ($users['users'] as $user) {
                    
                    // Handle an array of users
                    if (is_array($user)) {
                        $user_id = $user['ID'];
                        $user_name = $user['display_name'];
                        
                    // Handle a single user
                    } else if (is_object($user)) {
                        $user_id = $user->ID;
                        $user_name = $user->display_name;
                    }
                    
                    // Add user to options
                    $options[$user_id] = $user_name;
                }
                
            // Handle a single user
            } else if ( ! empty( $users['users'] ) ) {
                $user = $users['users'];
                if (is_array($user)) {
                    $user_id = $user['ID'];
                    $user_name = $user['display_name'];
                } else if (is_object($user)) {
                    $user_id = $user->ID;
                    $user_name = $user->display_name;
                }
                $options[$user_id] = $user_name;
            }
        }
        return $options;
    }
    
    /**
     * Generates options for all projects.
     * 
     * @since 0.2.9
     * 
     * @param   array   $args  An array of arguments passed from the constructor.
     */
    private function projects( $args ) {
        // Initialize
        $options = [];
        
        // Define args
        $args = [
            'show_hidden'   => true,
            'per_page'       => false
        ];
        
        // Get all groups
        $groups = groups_get_groups( $args );

        if ( $groups ) {
            foreach ( $groups['groups'] as $group ) {
                $options[$group->id] = $group->name;
            }
        }
        return $options;
    }
    
    /**
     * Generates options for all posts of a certain type.
     * 
     * @since 0.2.9
     * 
     * @param   array   $args  An array of arguments passed from the constructor.
     */
    private function posts( $args ) {
        $options = [];
        $post_type = $args['post_type'] ?? null;
        
        // Add flat option to rate type
        if ($post_type === 'bc_rate_type') {
            $options['flat'] = __( 'Flat', 'buddyclients' );
        }
        
        // Get posts
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
        );
        
        $posts = get_posts($args);
        
        // Loop through posts
        foreach ($posts as $post) {
            // Initialize
            $expired = '';
            
            // Check if custom quote expiration has passed
            $expiration = get_post_meta($post->ID, 'quote_expiration', true);
            if ($expiration) {
                $exp_timestamp = strtotime($expiration);
                $curr_timestamp = time();
                if ($curr_timestamp > $exp_timestamp) {
                    $expired = __( ' - Expired', 'buddyclients' );
                }
            }
            
            // Add to options
            $options[$post->ID] = $post->post_title . $expired;
        }
        return $options;
    }
    
    /**
     * Generates taxonomy options.
     * 
     * @since 0.2.9
     * 
     * @param   array   $args  An array of arguments passed from the constructor.
     */
    function taxonomy( $args ) {
        // Initialize
        $options = [];
        $taxonomy = $args['taxonomy'] ?? null;
        
        // Get terms
        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ) );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                // Add term to array
                $options[$term->term_id] = $term->name;
            }
        }
        return $options;
    }
    
    /**
     * Generates an array of xprofile field options.
     * 
     * @since 0.2.9
     * 
     * @param   array   $args  An array of arguments passed from the constructor.
     */
    function xprofile( $args ) {
        // Initialize array
        $options = [];
        $existing = $args['existing'] ?? null;
        
        // Get all exprofile fields
        $fields = bc_all_xprofile();
            
        // Loop through fields
        foreach ( $fields as $field_id => $field_data ) {
            
            // Get team types
            $team_types = get_option('bc_general_settings')['team_types'] ?? null;
            
            // Make sure field is for team
            if ($team_types && is_array($team_types)) {
                if ( ! array_intersect( $field_data['member_types'], $team_types ) ) {
                    continue;
                }
            }
            
            // Get the roles field id
            $roles_field = bc_roles_field_id();
            
            // Check if the field is already assigned to a post
            if ( $existing ) {
                // Initialize
                $assigned = false;
                
                // Loop through key value pairs
                foreach ( $existing as $post_type => $meta_key ) {
                    
                    $args = array(
                        'post_type' => $post_type,
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => $meta_key,
                                'value' => $field_id,
                            ),
                        ),
                    );
                    
                    // Get the posts based on the arguments
                    $posts = get_posts( $args );
                    
                    // Check if posts were found
                    if ( $posts ) {
                        $assigned = true;
                    }
                }
                
                // Skip if already assigned
                if ( $assigned ) {
                    continue;
                }
            }
            
            // Make sure the field is an acceptable type
            if ( $field_data['type'] !== 'checkbox' && $field_data['type'] !== 'selectbox' && $field_id !== $roles_field ) {
                continue;
            } else {
                // Add to array
                $options[$field_id] = $field_data['name'];
            }
        }
        return $options;
    }
}