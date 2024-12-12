<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Admin icons.
 * 
 * @since 0.1.0
 * @deprecated Use buddyc_icon.
 * 
 * @param string $icon Icon key.
 * @return string HTML-formatted icon.
 */
function buddyc_admin_icon( $icon ) {
    return buddyc_icon( $icon );
}