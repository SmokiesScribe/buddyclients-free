<?php
namespace BuddyClients\Components\Brief;

use BuddyClients\Includes\PostQuery;

/**
 * Single brief content.
 * 
 * Builds content for a single brief.
 * Retrieves fields for the brief type.
 * Displays the brief form or the completed brief based on
 * user permissions and the status of the manual toggle.
 * 
 * @since 0.1.0
 */
class SingleBrief {
    
    /**
     * The brief post ID.
     * 
     * @var int
     */
    public $brief_id;
    
    /**
     * The brief type ID.
     * 
     * @var int
     */
    public $brief_type;
    
    /**
     * The ID of the associated project.
     * 
     * @var int
     */
    public $project_id;
    
    /**
     * Brief fields data.
     * 
     * @var array
     */
    public $fields;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $brief_id = null ) {
        
        // Define brief ID
        if ( $brief_id ) {
            $this->brief_id = $brief_id;
        } else {
            global $post;
            $this->brief_id = $post->ID;
        }
        
        // Get brief type
        $this->brief_type = self::get_brief_type( $this->brief_id );
        
        // Get proejct ID
        $this->project_id = get_post_meta( $this->brief_id, 'project_id', true );
        
        // Get fields for brief type
        $this->fields = self::get_fields( $this->brief_type );
    }
    
    /**
     * Retrieves the brief type of a single brief post.
     * 
     * Returns the first brief type found.
     * 
     * @since 0.1.0
     * 
     * @param   int     $brief_id       The ID of the brief post.
     */
    private static function get_brief_type( $brief_id ) {
        $brief_types = wp_get_post_terms( $brief_id, 'brief_type', array('fields' => 'ids') );
        if ( $brief_types ) {
            return $brief_types[0];
        }
    }
    
    /**
     * Retrieves fields for a brief type.
     * 
     * @since 0.1.0
     * 
     * @param   int     $brief_type     The post ID of the brief type.
     */
    protected static function get_fields( $brief_type ) {
        
        // Initialize
        $fields = [];
        
        // Get all brief fields
        $brief_fields = ( new PostQuery( 'buddyc_brief_field' ) )->posts;
        
        // Loop through fields
        foreach ( $brief_fields as $field ) {
            
            // Get brief types for each field
            $field_brief_types = get_post_meta( $field->ID, 'brief_types', true );

            if ( ! is_array( $field_brief_types ) ) {
                continue;
            }
            
            // Check if the provided brief type is in the array
            if ( in_array( $brief_type, $field_brief_types ) ) {
                
                // Add field id to array
                $fields[$field->ID] = [
                    'type'          => get_post_meta( $field->ID, 'field_type', true ),
                    'label'         => get_the_title( $field->ID ),
                    'description'   => get_post_meta( $field->ID, 'field_description', true ),
                    'options'       => get_post_meta( $field->ID, 'field_options', true ),
                    'file_types'    => get_post_meta( $field->ID, 'file_types', true ),
                    'multiple_files'=> get_post_meta( $field->ID, 'multiple_files', true ),
                    'help_post_id'  => get_post_meta( $field->ID, 'help_post_id', true ),
                ];
            }
        }
        return $fields;
    }
    
    /**
     * Display single brief content.
     * 
     * @since 0.1.0
     */
    public function display() {
        
        // Initialize
        $content = '';
        
        // Check brief view
        $brief_view = buddyc_get_param( 'brief-view' );
        
        // Get brief type
        $brief_type_name = implode(', ', wp_get_post_terms($this->brief_id, 'brief_type', array('fields' => 'names')));
        
        // Get post meta
        $updated_date = get_post_meta($this->brief_id, 'updated_date', true);
        $project_id = get_post_meta($this->brief_id, 'project_id', true);
        
        // Check user status
        $current_user_id = get_current_user_id();
        $is_admin = groups_is_user_admin( $current_user_id, $project_id ) || buddyc_is_admin();
        $is_member = groups_is_user_member( $current_user_id, $project_id );
        
        // Get project details
        $group_obj = groups_get_group($project_id);
        $group_permalink = bp_get_group_permalink($group_obj);
        $group_name = bp_get_group_name($group_obj);
        
        // Check form vs completed brief criteria
        $show_form = $this->show_form( $is_admin, $updated_date, $brief_view );
        
        // Define toggle button
        $toggle_button = $this->toggle_button( $show_form, $is_admin, $updated_date );
        
        // Display brief content based on user status and brief view
        $content = $this->content_by_status( $show_form, $is_member, $is_admin, $group_permalink, $group_name, $toggle_button, $updated_date );
        
        return $content;
    }
    
    /**
     * Check if the brief form should be shown.
     * 
     * @since 0.1.0
     */
    private function show_form( $is_admin, $updated_date, $brief_view ) {
        // Hide from non group admins - or site admins
        if ( ! $is_admin ) {
            return false;
        }
        
        // Brief completed and brief view is not form
        if ( $updated_date && $brief_view !== 'form' ) {
            return false;
        }
        
        // Brief view is 'completed'
        if ( $brief_view === 'completed' ) {
            return false;
        }
        
        // Otherwise show brief form
        return true;
    }
    
    /**
     * Generate the toggle button for showing/hiding the brief form.
     * 
     * @since 0.1.0
     */
private function toggle_button( $show_form, $is_admin, $updated_date ) {
    $toggle_button = '';
    $btn_type = null;
    $updated_url = null;
    
    // Initialize param manager
    $param_manager = buddyc_param_manager();

    // Is admin viewing completed brief
    if ( ! $show_form && $is_admin ) {
        $updated_url = $param_manager->add_param( 'brief-view', 'form' );
        $btn_type = 'edit';
        
        
    // Is admin viewing form and the brief has been completed
    } else if ( $show_form && $is_admin && $updated_date ) {
        $updated_url = $param_manager->add_param( 'brief-view', 'completed' );
        $btn_type = 'view';
        
    // Default
    } else {
        // Remove 'brief-view' parameter if set
        if ( isset( $query_params['brief-view'] ) ) {
            unset( $query_params['brief-view'] );
        }
    }
    
    // Define labels
    $labels = [
        'edit'      => __( 'Edit', 'buddyclients' ),
        'view'      => __( 'View', 'buddyclients' ),
    ];

    // Generate the button HTML
    if ( $updated_url ) {
        $toggle_button = '<a class="show-hide-brief-btn ' . $btn_type . '" href="' . esc_url( $updated_url ) . '">' . ( $labels[$btn_type] ?? '' ) . ' Your Brief Submission</a>';
    }
    
    return $toggle_button;
}

    
    /**
     * Generate the content based on user status and brief view.
     * 
     * @since 0.1.0
     */
    private function content_by_status( $show_form, $is_member, $is_admin, $group_permalink, $group_name, $toggle_button, $updated_date ) {
        $content = '';
        
        if ( ! $is_member && ! $is_admin ) {
            $content .= $this->not_member_content();
        } else {
            $content .= $this->member_content( $show_form, $group_permalink, $group_name, $toggle_button, $updated_date );
        }
        
        return $content;
    }
    
    /**
     * Generate the content for non-members.
     * 
     * @since 0.1.0
     */
    private function not_member_content() {
        $is_logged_in = is_user_logged_in();
        $current_url = get_permalink();
        $content = '';
        
        if ( ! $is_logged_in ) {
            $content .= '<div class="login-options">';
            $content .= '<p>' . sprintf(
                /* translators: %s: the login url */
                __('Please <a href="%s">log in</a> to access this brief.', 'buddyclients-free'),
                esc_url(wp_login_url(esc_html($current_url)))
            ) . '</p>';
            $content .= '</div>';
        } else {
            $content .= '<p>' . __('You do not have permission to view this brief.', 'buddyclients-free') . '</p>';
        }
        
        return $content;
    }
    
    /**
     * Generate the content for members or admins.
     * 
     * @since 0.1.0
     */
    private function member_content( $show_form, $group_permalink, $group_name, $toggle_button, $updated_date ) {
        $content = '';
        
        // Breadcrumbs
        $sep = '<i class="fa-solid fa-angle-right" style="margin: 0 8px; font-size: 12px;"></i>';
        $projects_link = '<a href="' . bp_get_loggedin_user_link() . '/groups">' . __('Projects', 'buddyclients-free') . '</a>';
        $group_link = '<a href="' . $group_permalink . '">' . esc_html($group_name) . '</a>';
        $briefs_link = '<a href="' . $group_permalink . '/brief/">' . __('Briefs', 'buddyclients-free') . '</a>';
        
        $content .= '<p class="buddyc-single-brief-breadcrumbs">' . $projects_link . $sep . $group_link . $sep . $briefs_link . $sep . get_the_title() . '</p>';
        
        // Title
        $content .= '<h1>' . get_the_title() . '</h1>';
        
        // Last updated
        $content .= '<p></p>';
        
        // Open links container
        $content .= '<div class="buddyc-brief-links">';
        
        // Display last updated date
        if ($updated_date) {
            $content .= '<p>' . __('Last Updated:', 'buddyclients-free') . ' ' . gmdate('F j, Y,  h:i A', strtotime($updated_date)) . '</p>';
        }
        
        // Display toggle button
        $content .= $toggle_button;
        
        // Close links container
        $content .= '</div>';
        
        // Completed Brief Content
        if ( ! $show_form ) {
            
            // Show submitted brief content
            $content .= ( new CompletedBrief )->display( $content );
    
        } else {
            
            // Brief Form
            $content .= ( new BriefForm )->form();
        }
        
        return $content;
    }
}
