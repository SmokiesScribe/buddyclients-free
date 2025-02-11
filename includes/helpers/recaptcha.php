<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Enqueues the Google reCAPTCHA script.
 * 
 * @since 1.0.25
 */
function buddyc_enqueue_recaptcha_script() {

    // Make sure reCAPTCHA is enabled
    $enabled = buddyc_recaptcha_enabled();
    if ( ! $enabled ) {
        return;
    }

    // Get the site key
    $site_key = buddyc_recaptcha_site_key();

    // Enqueue the script
    wp_enqueue_script(
        'google-recaptcha',
        sprintf(
            'https://www.google.com/recaptcha/api.js?render="%s"',
            esc_attr( $site_key )
        ),
        [],  // No dependencies
        null, // No version number needed
        true  // Load the script in the footer
    );
}
add_action( 'wp_enqueue_scripts', 'buddyc_enqueue_recaptcha_script' );

/**
 * Fetches the reCAPTCHA site key.
 * 
 * @since 1.0.25
 */
function buddyc_recaptcha_site_key() {
    return buddyc_get_setting( 'integrations', 'recaptcha_site_key' );
}

/**
 * Fetches the reCAPTCHA secret key.
 * 
 * @since 1.0.25
 */
function buddyc_recaptcha_secret_key() {
    return buddyc_get_setting( 'integrations', 'recaptcha_secret_key' );
}

/**
 * Checks that the reCAPTCHA keys exist and reCAPTCHA is enabled.
 * 
 * @since 1.0.25
 */
function buddyc_recaptcha_enabled() {
    $enabled = buddyc_get_setting( 'integrations', 'enable_recaptcha' );
    if ( $enabled !== 'enable' ) {
        return false;
    }
    $site_key = buddyc_recaptcha_site_key();
    $secret_key = buddyc_recaptcha_secret_key();
    return ! empty( $site_key ) && ! empty( $secret_key );
}

/**
 * Fetches the reCAPTCHA threhold setting.
 * Defaults to 0.5 (0 most suspicous, 1.0 least suspicious).
 * 
 * @since 1.0.25
 */
function buddyc_recaptcha_threshold() {
    $threshold = buddyc_get_setting( 'integrations', 'recaptcha_threshold' );
    return (float) $threshold;
}