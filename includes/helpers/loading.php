<?php
/**
 * Outputs the loading indicator.
 * 
 * @since 0.1.0
 * 
 * @deprecated
 */
function bc_loading_indicator() {
        echo '<div id="bc-loading-indicator"></div>';
}
//add_action('wp_footer', 'bc_loading_indicator');