<?php
namespace BuddyClients\Includes;

/**
 * Handles the submission of the initial user files deletion form.
 *
 * @since 1.0.4
 */
class UserFilesSubmission {
    
    /**
     * Constructor method.
     * 
     * @since 1.0.4
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data ) {
        $this->set_session( $post_data );
    }
    
    /**
     * Adds the post data to the session.
     * 
     * @since 1.0.4
     * 
     * @param array $post_data The POST data.
     */
    private function set_session( $post_data ) {
        $_SESSION['bc_user_files'] = $post_data;
    }
}
    