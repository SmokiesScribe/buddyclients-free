<?php
use BuddyClients\Includes\XprofileManager;
use BuddyClients\Includes\XprofileField;
use BuddyClients\Components\Booking\FilterField;

/**
 * Retrieves all xprofile fields.
 * 
 * @since 0.2.9
 */
function bc_all_xprofile() {
    return XprofileManager::all_xprofile();
}

/**
 * Initializes FilterField class on post updates.
 * 
 * @since 0.1.0
 * 
 * @param int $post_id The ID of the post being saved.
 */
function bc_update_filter_field( $post_id ) {
    new FilterField( $post_id );
}
add_action('save_post_bc_filter', 'bc_update_filter_field', 10, 1);

/**
 * Retrieves Roles field ID.
 * 
 * Creates or updates roles field if necessary.
 * 
 * @since 0.1.0
 */
function bc_roles_field_id() {

    // Define field args
    $args = [
        'field_type'    => 'checkbox',
        'field_name'    => 'Team Member Roles',
        'field_options' => bc_roles_options( 'bc_role' ),
        'member_types'  => 'team'
    ];
    
    $xprofile = new XprofileField( 'roles', $args );

    return $xprofile->field_id;
}

/**
 * Builds array of options for roles xprofile field.
 * 
 * @since 0.1.3
 * 
 * @string  $post_type  The roles post type.
 */
function bc_roles_options( $post_type ) {
    $options = array();
    
    // Get posts
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
    );
    
    $posts = get_posts($args);
    
    // Loop through posts
    foreach ($posts as $post) {
        // Add to options
        $options[$post->ID] = $post->post_title;
    }
    return $options;
}

/**
 * Initializes XprofileManager.
 * 
 * @since 0.1.0
 */
function bc_xprofile_manager() {
    new XprofileManager;
}
bc_xprofile_manager();

/**
 * Allows team to self-select roles.
 * 
 * Hides or shows edit option based on setting.
 * 
 * @since 0.1.0
 * 
 * @param   array   $css_variables  The associative array of css names and variables.
 */
function bc_show_role_xprofile() {
    // Get self select setting
    $self_select_role = bc_get_setting( 'general', 'self_select_role' );
    
    // Get or create xprofile field id
    $field_id = bc_roles_field_id();

    // Define role field class
    $class = '.editfield.field_' . $field_id;

    // Check if hiding
    if ( $self_select_role !== 'yes' ) {
        
        // Build inline css
        $inline_css = "{$class} { display: none !important; }";

        // Add inline css
        buddyclients_inline_style( $inline_css );
    }
}
add_action( 'init', 'bc_show_role_xprofile' );

/**
 * Disallows manual updates to the roles field.
 * 
 * @since 0.1.0
 */
function bc_no_roles_updates() {

    $roles_field_id = bc_roles_field_id();

    if ( ! $roles_field_id ) {
        return;
    }

    // Init param manager
    $param_manager = bc_param_manager();
    
    // Define url params to check
    $params = [
        'page'      => 'bp-profile-setup',
        'mode'      => 'edit_field',
        'field_id'  => $roles_field_id
    ];
    
    // Check all params
    foreach ( $params as $param => $value ) {
        if ( $param_manager->get( $param ) !== $value ) {
            // Exit if not a match
            return;
        }
    }
    
    // Define css
    $css = '#publishing-action .button-primary {
                pointer-events: none;
                opacity: 0.5;
            }';
            buddyclients_inline_style( $css, $admin = true );
    
    // Add an admin notice
    $args = [
        'message'           => 'This field is managed by BuddyClients.',
        'repair_link'       => '/edit.php?post_type=bc_role',
        'repair_link_text'   => 'Edit team member roles.',
        'color'             => 'blue',
        'dismissable'       => true
    ];
    bc_admin_notice( $args );
}
add_action('admin_init', 'bc_no_roles_updates');