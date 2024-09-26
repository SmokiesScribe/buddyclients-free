<?php
/**
 * Retrieves Stripe keys.
 * 
 * @since 0.1.0
 */
function bc_stripe_keys() {
    if ( class_exists( 'BuddyClients\Components\Stripe\StripeKeys' ) ) {
        return new BuddyClients\Components\Stripe\StripeKeys;
    }
}