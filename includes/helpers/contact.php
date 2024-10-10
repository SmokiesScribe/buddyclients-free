<?php
/**
 * Generates a contact message based on provided settings.
 * 
 * @since 0.4.3
 * 
 * @param   bool    $site_name  Whether to include the site name.
 *                              Defaults to false and uses 'us'.
 * @param   bool    $lowercase  Whether to format as lowercase.
 *                              Defaults to formatting as full sentence.
 */
function bc_contact_message( $site_name = false, $lowercase = false ) {
    // Initialize
    $link = null;
    
    // Check for contact page
    $contact_page_link = bc_get_page_link( 'contact' );
    
    // Contact page exists
    if ( $contact_page_link !== '#' ) {
        $link = $contact_page_link;
        
    // No contact page
    } else {
        
        // Check for public email
        $public_email = bc_get_setting( 'email', 'from_email' );
        
        // Public email exists
        if ( $public_email ) {
            $link = 'mailto:' . $public_email;
        }
    }
    
    // No link
    if ( ! $link ) {
        return;
    }
    
    $name = $site_name ? get_bloginfo( 'name' ) : __('us', 'buddyclients');
    /* translators: %s: the site name */
    $text = $lowercase ? sprintf( __( 'contact %s', 'buddyclients-free' ), $name ) : sprintf( __( 'Contact %s', 'buddyclients-free' ), $name ) . '.';
    $message = '<a href="' . esc_url( $link ) . '">' . $text . '</a>';
    
    return $message;
}