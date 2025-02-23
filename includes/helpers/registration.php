<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Retrieves the CTA button text from settings.
 * 
 * @since 1.0.25
 */
function buddyc_cta_btn_text() {
    return buddyc_get_setting( 'general', 'register_button_text' );
}

/**
 * Retrieves the CTA button url from settings.
 * 
 * @since 1.0.25
 */
function buddyc_cta_btn_url() {
    return buddyc_get_setting( 'general', 'register_button_url' );
}

/**
 * Checks if the CTA button is enabledin settings.
 * 
 * @since 1.0.25
 */
function buddyc_enable_cta_btn() {
    $setting = buddyc_get_setting( 'general', 'enable_cta' );
    return $setting === 'enable';
}

/**
 * Builds the array of info for localizing the header button script.
 * 
 * @since 1.0.25
 */
function buddyc_header_btn_info() {
    // Initialize
    $info = [];

    // Check if WP registration is enabled
    $reg_enabled = get_option( 'users_can_register' );

    // Check if CTA button is enabled
    $cta_enabled = buddyc_enable_cta_btn();

    if ( ! $reg_enabled && $cta_enabled ) {
        // Define text and url
        $btn_text = buddyc_cta_btn_text();
        $btn_url = buddyc_cta_btn_url();

        // Make sure they exist
        if ( ! empty( $btn_url ) && $btn_url !== '#' && ! empty( $btn_text ) ) {

            // Build array
            $info = [
                'btnText'       => $btn_text,
                'btnUrl'        => esc_url( $btn_url ),
            ];
        }
    }

    return $info;
}