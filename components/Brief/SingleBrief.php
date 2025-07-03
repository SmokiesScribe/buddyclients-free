<?php
namespace BuddyClients\Components\Brief;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Brief\Brief;

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
     * The Brief object.
     * 
     * @var Brief
     */
    public $brief;

    /**
     * The title of the brief.
     * 
     * @var string
     */
    public $title;
    
    /**
     * The brief type ID.
     * 
     * @var int
     */
    public $brief_type;
    
    /**
     * Brief fields data.
     * 
     * @var array
     */
    public $fields;

    /**
     * The view for the page.
     * 'form' or 'completed'.
     * 
     * @var string
     */
    public $view;

    /**
     * Whether the current user is a group admin.
     * 
     * @var bool
     */
    public $is_admin;

    /**
     * Whether the current user is a group member.
     * 
     * @var bool
     */
    public $is_member;

    /**
     * Whether to show the form.
     * 
     * @var bool
     */
    public $show_form;

    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $brief_id = null ) {
        $this->brief_id = $this->get_brief_id( $brief_id );
        $this->get_var();
        $this->get_user_status();
        $this->get_brief_state();
    }

    /**
     * Retrieves properties.
     * 
     * @since 1.0.21
     */
    private function get_var() {
        $this->brief = new Brief( $this->brief_id );
        $this->title = get_the_title( $this->brief_id );
        $this->brief_type = $this->brief->brief_type_id;
        $this->fields = self::get_fields( $this->brief_type );
    }

    /**
     * Retrieves status-based properties.
     * 
     * @since 1.0.21
     */
    private function get_brief_state() {
        $this->view = buddyc_get_param( 'brief-view' );
        $this->show_form = $this->show_form();
    }

    /**
     * Retrieves the current user's group status.
     * 
     * @since 1.0.21
     */
    private function get_user_status() {
        $curr_user = get_current_user_id();
        $this->is_admin = groups_is_user_admin( $curr_user, $this->brief->project_id ) || buddyc_is_admin();
        $this->is_member = groups_is_user_member( $curr_user, $this->brief->project_id );
    }

    /**
     * Defines the brief ID.
     * 
     * @since 1.0.21
     * 
     * @param   ?int    $brief_id   Optional. The brief ID.
     */
    private function get_brief_id( $brief_id = null ) {
        if ( empty( $brief_id ) ) {
            global $post;
            $brief_id = $post->ID;
        }
        return $brief_id;     
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
        $brief_fields = buddyc_post_query( 'buddyc_brief_field' );
        
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
        
        // Initialize with title
        $content = '<h1>' . esc_html( $this->title ) . '</h1>';
        
        // Display brief content based on user status and brief view
        $content .= $this->content_by_status();
        
        return $content;
    }
    
    /**
     * Check if the brief form should be shown.
     * 
     * @since 0.1.0
     */
    private function show_form( ) {
        // Hide from non group admins - or site admins
        if ( ! $this->is_admin ) {
            return false;
        }
        
        // Brief completed and brief view is not form
        if ( $this->brief->updated_date && $this->view !== 'form' ) {
            return false;
        }
        
        // Brief view is 'completed'
        if ( $this->view === 'completed' ) {
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
    private function toggle_button() {
        // Initialize url
        $updated_url = null;
        
        // Initialize param manager
        $param_manager = buddyc_param_manager();

        // Is admin viewing completed brief
        if ( ! $this->show_form && $this->is_admin ) {
            $updated_url = $param_manager->add_param( 'brief-view', 'form' );
            $label = __( 'Edit Your Brief Submission', 'buddyclients-lite' );
            
            
        // Is admin viewing form and the brief has been completed
        } else if ( $this->show_form && $this->is_admin && $this->brief->updated_date ) {
            $updated_url = $param_manager->add_param( 'brief-view', 'completed' );
            $label = __( 'View Your Brief Submission', 'buddyclients-lite' );
            
        // Default
        } else {
            // Remove 'brief-view' parameter if set
            if ( isset( $query_params['brief-view'] ) ) {
                unset( $query_params['brief-view'] );
            }
        }

        // Generate the button HTML
        if ( $updated_url ) {

            $btn_args = [
                'text'  => $label,
                'link'  => esc_url( $updated_url ),
                'type'  => 'outline',
                'size'  => 'medium'
            ];
            return buddyc_btn( $btn_args );
        }
    }
    
    /**
     * Generate the content based on user status and brief view.
     * 
     * @since 0.1.0
     */
    private function content_by_status() {
        $content = '';
        
        if ( ! $this->is_member && ! $this->is_admin ) {
            $content .= $this->not_member_content();
        } else {
            $content .= $this->member_content();
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
                __('Please <a href="%s">log in</a> to access this brief.', 'buddyclients-lite'),
                esc_url(wp_login_url(esc_html($current_url)))
            ) . '</p>';
            $content .= '</div>';
        } else {
            $content .= '<p>' . __('You do not have permission to view this brief.', 'buddyclients-lite') . '</p>';
        }
        
        return $content;
    }
    
    /**
     * Generate the content for members or admins.
     * 
     * @since 0.1.0
     */
    private function member_content() {
        $content = '';
        
        // Last updated date
        if ( $this->brief->updated_date ) {
            $content .= '<p>';
            $content .= sprintf(
                /* translators: %s: the date and time the brief was last updated */
                __( 'Last Updated: %s', 'buddyclients-lite' ),
                esc_html( gmdate( 'F j, Y, h:i A', strtotime( $this->brief->updated_date ) ) )
            );
            $content .= '</p>';
        }
        
        // Toggle button
        $content .= $this->toggle_button();

        // Build content by status
        $content .= ! $this->show_form ? $this->completed_brief() : $this->brief_form();
        
        return $content;
    }

    /**
     * Outputs the completed brief content.
     * 
     * @since 1.0.21
     */
    private function completed_brief() {
        $completed_brief = new CompletedBrief;
        return $completed_brief->display();

    }

    /**
     * Outputs the brief form.
     * 
     * @since 1.0.21
     */
    private function brief_form() {
        $form = new BriefForm;
        return $form->form();
    }
}
