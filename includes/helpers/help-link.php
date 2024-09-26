<?php
/**
 * Outputs a popup link.
 * 
 * @since 0.1.0
 * 
      * @param  int     $post_id        The ID of the post from which to retrieve the content.
      * @param  string  $link_text      Optional. The text to display. Defaults to ? icon.
      * @param  string  $url            Optional. The full url of the page to display.
 */
function bc_help_link( $post_id = null, $link_text = null, $url = null, $raw_content = null ) {
    return BuddyClients\Includes\Popup::link( $post_id, $link_text, $url, $raw_content );
}