<?php
/**
 * Loads Stripe library.
 * 
 * @since 0.1.0
 */
function bc_stripe_library() {
    require_once ABSPATH . 'wp-content/plugins/buddyclients/vendor/stripe/init.php';
}