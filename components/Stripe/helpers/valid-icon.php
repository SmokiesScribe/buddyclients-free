<?php
/**
 * Generates the icon for Stripe validation.
 * 
 * @since 0.1.0
 */
function bc_stripe_valid_icon( $key, $value ) {
    $valid = BuddyClients\Components\Stripe\StripeKeys::validate( $key, $value );
    $icon = $valid ? 'check' : 'x';
    
    return bc_admin_icon( $icon );
}

/**
 * Generates the icon for the full Stripe mode validation.
 * 
 * @since 0.1.0
 */
function bc_stripe_mode_valid_icon() {
    $stripe_keys = new BuddyClients\Components\Stripe\StripeKeys;
    $valid = $stripe_keys->validate_mode();
    $icon = $valid ? 'check' : 'x';
    
    return bc_admin_icon( $icon );
}