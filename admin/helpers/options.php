<?php
use BuddyClients\Includes\Options as Options;
/**
 * Generates an options array.
 * 
 * @since 0.2.9
 * @param   string  $key        The key denoting which options to generate.
 *                              Accepts 'clients', 'team', 'affiliates', 'users', 'projects'
 * @param   string  $format     The format of the options array.
 *                              Accepts 'simple' and 'detail'. Defaults to 'simple'.
 * @param   array   $args       Optional. An array of args to pass to the callback.
 */
function bc_options( $key, $args = null ) {
    $options = new Options( $key, $args );
    return $options->options;
}

    /**
     * Generates associative array of users.
     * 
     * @since 0.1.0
     * 
     * @deprecated Use Options class.
     * 
     * @param string $type The key of the user type.
     */
    function bc_user_options( $type ) {
        switch ( $type ) {
            case 'team':
                $users = bc_all_team();
                break;
            case 'client':
                $users = bc_all_clients();
                break;
            case 'affiliate':
                $users = bc_all_affiliates();
                break;
            case 'faculty':
                $users = be_all_faculty();
                break;
            case 'attendee':
                $users = be_all_attendees();
                break;
            case 'all':
                $users = bp_core_get_users( ['per_page' => false] );
                break;
            }
        
        // Initialize options
        $options = array();
        
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
     * Generates list of project options.
     * 
     * @since 0.1.0
     * 
     * @deprecated Use Options class.
     */
    function bc_project_options() {
        
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
     * Generates post type options arrays.
     * 
     * @since 0.1.0
     * 
     * @deprecated Use Options class.
     * 
     * @param string|array $post_type The slug of the post type or array of slugs.
     */
    function bc_posts_options( $post_type ) {
        $options = array();
        
        // Add flat option to rate type
        if ($post_type === 'bc_rate_type') {
            $options['flat'] = 'Flat';
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
                    $expired = ' - Expired';
                }
            }
            
            // Add to options
            $options[$post->ID] = $post->post_title . $expired;
        }
        return $options;
    }
    
    /**
     * Generates associative array of taxonomy options.
     * 
     * @since 0.1.0
     * 
     * @deprecated Use Options class.
     * 
     * @param string $taxonomy The slug of the taxonomy type.
     */
    function bc_tax_options( $taxonomy ) {
        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ) );
        $options = array();
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                // Add brief type to associative array
                $options[$term->term_id] = $term->name;
            }
        }
        return $options;
    }
    
    /**
     * Generates an array of xprofile field options.
     * 
     * @since 0.1.0
     * @updated 0.2.0
     * 
     * @deprecated Use Options class.
     * 
     * @param   array   $existing   Optional. Array of key value pairs representing the post type and meta key to check.
     */
    function bc_xprofile_options( $existing = null ) {
        // Initialize array
        $options = array();
        
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