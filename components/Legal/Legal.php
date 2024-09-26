<?php
namespace BuddyClients\Components\Legal;

use BuddyClients\Includes\{
    FileHandler as FileHandler,
    File as File,
    PostQuery as PostQuery
};
use BuddyClients\Admin\Settings as Settings;

/**
 * Legal data for a specific type.
 * 
 * Generates data for a specific type of legal agreement.
 * Retrieves the current legal agreement info and its status.
 * Retrieves related data for a specific user.
 * 
 * @since 0.1.0
 */
class Legal {
    
    /**
     * The Legal type.
     * 
     * @var string
     */
    public $type;
    
    /**
     * The current legal agreement version post ID.
     * 
     * @var ?int
     */
    public $curr_version;
    
    /**
     * The Legal agreement title.
     * 
     * @var string
     */
    public $title;
    
    /**
     * The Legal agreement content.
     * 
     * @var string
     */
    public $content;
    
    /**
     * The Legal agreement permalink.
     * 
     * @var string
     */
    public $link;
    
    /**
     * The deadline to complete the new agreement version.
     * 
     * @var string
     */
    public $deadline;
    
    /**
     * The agreement version status.
     * Accepts 'none', 'transition', 'stable'.
     * 
     * @var string
     */
    public $status;
    
    /**
     * The legal data.
     * 
     * @var array
     */
    public $data;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * $param   string      $type       Type of Legal agreement.
     *                                  Accepts 'team', 'client', 'affiliate'.
     * @param   int         $user_id    Optional. A user ID to add user-specific content to the agreement.
     */
    public function __construct( $type, $user_id = null ) {
        $this->type = $type;
        $this->user_id = $user_id;
        
        // Get current version
        $curr_version = $this->version();
        $this->curr_version = $this->exists( $curr_version ) ? $curr_version : null;
        
        // Get data
        $this->data = $this->curr_version ? $this->get_data( $this->curr_version ) : [];
        
        // Check the status
        $this->status = $this->status();
    }
    
    /**
     * Retrieves the legal version.
     * 
     * @since 0.1.0
     * 
     * @param   string  $veresion   Optional. The version to retrieve.
     *                              Accepts 'current', 'draft', 'previous'. Defaults to 'current'.
     */
    public function version( $version = 'current' ) {
        
        switch ( $version ) {
            case 'current':
                $key = $this->type . '_legal_version';
                break;
            case 'previous':
                $key = $this->type . '_legal_version_prev';
                break;
            case 'draft':
                $key = $this->type . '_legal_version_draft';
                break;
            }
            
        $version = bc_get_setting('legal', $key);
        return $version;
    }
    
    /**
     * Makes sure the post exists.
     * 
     * @since 0.1.0
     */
    private function exists( $post_id ) {
        $post = get_post( $post_id );
        return $post ? true : false;
    }
    
    /**
     * Retrieves data for a legal version post.
     * 
     * @since 0.1.0
     * 
     * @param   int     $post_id    The ID of the legal post.
     */
    private function get_data( $post_id ) {
        $this->title    = get_the_title( $post_id );
        $this->link     = get_permalink( $post_id );
        
        // Get content and user-specific content
        $this->content = $this->modify_content( get_post_field( 'post_content', $post_id ), get_current_user_id(), $this->type );
    }
    
    /**
     * Retrieves legal mod posts.
     * 
     * @since 0.1.0
     * 
     * @param   string  $content    The content to modify.
     * @param   int     $user_id    The ID of the user.
     * @param   string  $legal_type The type of legal agreement.
     */
    private function modify_content( $content, $user_id, $legal_type ) {
        // Get matching modifications
        $mods = ( new PostQuery( 'bc_legal_mod', ['legal_type' => $legal_type] ) )->posts;
        
        // Loop through mods
        if ( $mods ) {
            foreach ( $mods as $mod ) {
                $user_ids = get_post_meta( $mod->ID, 'user_id', true );
                if ( $user_ids && is_array( $user_ids ) ) {
                    if ( in_array( $user_id, $user_ids ) ) {
                        // Append mod to the end of the content
                        return $content . get_post_field( 'post_content', $mod->ID );
                    }
                }
            }
        }
        return $content;
    }
    
    /**
     * Checks whether the Legal agreement is transitioning to a new version.
     * 
     * @since 0.1.0
     */
    public function status() {
        if ( $this->version( 'current' ) && $this->version( 'previous' ) && $this->prev_is_active() ) {
            return 'transition';
        } else if ( $this->version( 'current' ) ) {
            return 'stable';
        } else {
            return 'none';
        }
    }
    
    /**
     * Checks whether a Legal agreement is current.
     * 
     * @since 0.1.0
     * 
     * @param   int     $post_id    The ID of the legal post to check.
     */
    public function is_current( $post_id ) {
        return $this->curr_version == $post_id ? true : false;
    }
    
    /**
     * Checks whether a Legal agreement is active.
     * 
     * @since 0.1.0
     * 
     * @param   int     $post_id    The ID of the legal post to check.
     * @return  bool    True if active, false if not active.
     */
    public function is_active( $post_id ) {
        
        // Check if it's current
        $is_current = $this->is_current( $post_id );
        
        // Return true if current
        if ( $is_current ) {
            return true;
            
        // Else check if in active versions
        } else {
            return in_array( $post_id, $this->active_versions() );
        }
    }
    
    /**
     * Retrieve active versions.
     * 
     * @since 0.1.0
     * 
     * @return  array   Array of active version IDs.
     */
    private function active_versions() {
        
        // Make sure a current version exists
        if ( ! $this->curr_version ) {
            return [];
        }
        
        // Initialize with curr version
        $active_versions = [$this->curr_version];
        
        // Retrieve the previous version
        $prev = $this->version( 'previous' );
        
        // Make sure the post exists
        if ( $prev ) {
            $exists = $this->exists( $prev ) ? $prev : null;
            $is_active = $this->prev_is_active();
            
            if ( $exists && $is_active ) {
                $active_versions[] = $prev;
            }
        }
        return $active_versions;
    }
    
    /**
     * Checks whether the previous version is active.
     * 
     * @since 0.1.0
     */
    private function prev_is_active() {
        // Get deadline setting
        $deadline_setting = bc_get_setting( 'legal', 'legal_deadline' );
        
        // Get the current date and time
        $current_datetime = date('Y-m-d H:i:s');
        
        // Get current version pub date
        $publish_date = get_post_field( 'post_date', $this->curr_version );
        
        // Deadline setting is not forever
        if ( $deadline_setting !== '' ) {
            
            // Calculate deadline
            $deadline = date( 'Y-m-d H:i:s', strtotime( $publish_date . ' +' . $deadline_setting . ' days' ) );
            $this->deadline = date('F j, Y, g:i A', strtotime( $deadline ) );
            
            // Compare the deadline with the current date and time
            if ($deadline > $current_datetime) {
                // Deadline has not passed
                return true;
            } else {
                // Deadline has passed
                return false;
            }
        } else {
            // Has forever
            $this->deadline = __( 'the end of time', 'buddyclients' );
            return true;
        }
    }
    
    /**
     * Checks the status of a user's legal version.
     * 
     * @since 0.1.0
     * 
     * @param   ?int    $user_id    Optional. The ID of the user.
     *                              Defaults to current user.
     * @return  string|bool         Current, active, or false.
     */
    public function user_agreement_status( $user_id = null ) {
        
        // Initialize status
        $status = false;
        
        // Get user id
        $user_id = $user_id ?? get_current_user_id();
        
        // Get the user version
        $version = $this->legal_meta( $user_id, 'version' );
        
        // Make sure a version was returned
        if ( $version ) {
            // Check if current or active
            if ( $this->is_current( $version ) ) {
                $status = 'current';
            } else if ( $this->is_active( $version ) ) {
                $status = 'active';
            }
        }
        return $status;
    }
    
    /**
     * Retrieves a user's legal data.
     * 
     * @since 0.1.0
     * 
     * @param   ?int    $user_id    Optional. The ID of the user.
     *                              Defaults to current user.
     * @return  array
     */
    public function get_user_data( $user_id = null ) {
        
        // Get user id
        $user_id = $user_id ?? get_current_user_id();
        
        // Get signature File ID
        $signature_id = $this->legal_meta( $user_id, 'signature' );
        
        // Get signature file path
        $signature_file_path = File::get_file_path( $signature_id );
        
        // Get the user data
        $data = [
            'user_id'               => $user_id,
            'version'               => $this->legal_meta( $user_id, 'version' ),
            'date'                  => $this->legal_meta( $user_id, 'date' ),
            'status'                => $this->user_agreement_status( $user_id ),
            'name'                  => $this->legal_meta( $user_id, 'name' ),
            'email'                 => $this->legal_meta( $user_id, 'email' ),
            'payment_method'        => $this->legal_meta( $user_id, 'payment_method' ),
            'signature'             => $signature_id ?? null,
            'signature_image'       => ( FileHandler::generate_image( $signature_id ) ),
            'signature_file_path'   => $signature_file_path,
            'active'                => $this->user_is_active( $this->user_agreement_status( $user_id ) ),
            'content'               => $this->content,
            'pdf'                   => $this->legal_meta( $user_id, 'pdf' ),
        ];
        
        return $data;
    }
    
    /**
     * Checks whether the user's agreement status is active or current.
     * 
     * @since 0.2.3
     * 
     * @param   string  $status The user's agreement status.
     *                          Accepts 'current', 'active', or false;
     */
    private function user_is_active( $status ) {
        return ( $status === 'current' || $status === 'active' );
    }
    
    /**
     * Retrieves a single piece of legal meta.
     * 
     * @since 0.1.0
     * 
     * @param   int     $user_id    The ID of the user.
     * @param   string  $key        The base meta key.
     */
    private function legal_meta( $user_id, $key ) {
        // Build full meta key
        $meta_key = 'bc_legal_' . $key . '_' . $this->type;
        
        // Retrieve user meta
        $meta_value = get_user_meta( $user_id, $meta_key, true );
        
        // Validate the meta value.
        $value = $meta_value != '' ? $meta_value : null;
        
        return $value;
    }
    
    /**
     * Updates a user's legal data.
     * 
     * @since 0.1.0
     * 
     * @param   int     $user_id    The ID of the user to update.
     * @param   array   $data       The new legal data.
     */
    public function update_user_data( $user_id, $data ) {
        
        // Loop through data
        foreach ( $data as $key => $value ) {
            // Make sure the value is valid
            if ( $value && $value !== '' ) {
                // Update the user meta
                update_user_meta( $user_id, 'bc_legal_' . $key . '_' . $this->type, $value );
            }
        }
    }
}