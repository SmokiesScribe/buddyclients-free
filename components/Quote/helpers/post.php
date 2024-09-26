<?php
use BuddyClients\Components\Quote\Quote;
/**
 * Validates custom quote on post save.
 * 
 * @since 0.1.0
 * 
 * @param   int     $post_id    The ID of the Custom Quote post.
 */
function bc_validate_quote( $post_id ) {
    if ( get_post_type( $post_id ) === 'bc_quote' ) {
        new Quote( $post_id );
    }
}
add_action( 'save_post_bc_quote', 'bc_validate_quote', 10, 1 );

/**
 * Handles a new custom quote.
 * 
 * @since 0.1.0
 * 
 * @param   string  $new_status     The new status of the post.
 * @param   string  $old_status     The old status of the post.
 * @param   string  $post           The post object.
 */
function bc_custom_quote_published( $new_status, $old_status, $post ) {
    
    // Make sure it's a quote transitioning to published
    if ( get_post_type( $post ) === 'bc_quote' && $new_status === 'publish' && $old_status === 'draft' ) {
        
        // New quote object
        $quote = new Quote( $post->ID );
        
        /**
         * Fires on the creation of a new custom quote.
         * 
         * @since 0.1.0
         * 
         * @param   object  $quote  The Quote object.
         */
        do_action( 'bc_new_quote', $quote );
    }
}
add_action('transition_post_status', 'bc_custom_quote_published', 10, 3 );