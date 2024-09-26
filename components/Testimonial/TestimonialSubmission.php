<?php
namespace BuddyClients\Components\Testimonial;

/**
 * Testimonial form submission.
 * 
 * Handles submission of the testimonial form.
 * Creates a draft testimonial post for admin review.
 *
 * @since 0.1.0
 * 
 * @see TestimonialForm
 */
class TestimonialSubmission {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post_data The POST data.
     * @param array|null $files_data The FILES data.
     */
    public function __construct( array $post_data, ?array $files_data = null ) {
        // Create testimonial
        $this->create_testimonial( $post_data );
    }
    
    /**
     * Creates the testimonial draft.
     * 
     * @since 0.1.0
     */
    private function create_testimonial( $post_data ) {
        // Exit if missing necessary data
        if ( ! isset( $post_data['testimonial_content'] ) ||
             ! isset( $post_data['testimonial_name'] ) ||
             ! isset( $post_data['accept_terms'] ) ) {
            return;
        }
        
        // Sanitize input data
        $client_id = intval( $post_data['client_id'] ); // Ensure it's an integer
        $testimonial_content = wp_kses_post( $post_data['testimonial_content'] ); // Sanitize content
        $testimonial_name = sanitize_text_field( $post_data['testimonial_name'] ); // Sanitize name
    
        // Create testimonial
        $new_testimonial_data = array(
            'post_title'    => $testimonial_name,
            'post_content'  => $testimonial_content,
            'post_status'   => 'pending',
            'post_type'     => 'bc_testimonial',
            'post_author'   => $client_id,
        );
        
        // Insert the new post into the database
        $new_testimonial_id = wp_insert_post( $new_testimonial_data );
        
        /**
         * Fires on new testimonial creation.
         * 
         * @param   int     $new_testimonial_id     The ID of the testimonial post.
         * @param   string  $client_name            The name of the testimonial author.
         */
        do_action( 'bc_new_testimonial', $new_testimonial_id, $testimonial_name );
    }
}
    