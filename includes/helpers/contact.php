<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
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
function buddyc_contact_message( $site_name = false, $lowercase = false ) {
    // Initialize
    $link = null;
    
    // Check for contact page
    $contact_page_link = buddyc_get_page_link( 'contact_form' );
    
    // Contact page exists
    if ( $contact_page_link !== '#' && buddyc_component_enabled( 'Contact' ) ) {
        $link = $contact_page_link;
        
    // No contact page
    } else {
        
        // Check for public email
        $public_email = buddyc_get_setting( 'email', 'from_email' );
        
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
    $text = $lowercase ? sprintf( __( 'contact %s', 'buddyclients' ), $name ) : sprintf( __( 'Contact %s', 'buddyclients' ), $name ) . '.';
    $message = '<a href="' . esc_url( $link ) . '">' . $text . '</a>';
    
    return $message;
}
add_action('init', 'buddyc_contact_message');