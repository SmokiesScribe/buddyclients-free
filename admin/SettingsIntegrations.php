<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the general settings.
 *
 * @since 1.0.25
 */
class SettingsIntegrations {

    /**
     * Defines default Integrations settings.
     * 
     * @since 1.0.25
     */
    public static function defaults() {
        return [
            'enable_recaptcha'      => 'disable',
            'recaptcha_threshold'   => '0.5',
        ];
    }
    
    /**
     * Defines integrations settings.
     * 
     * @since 1.0.25
     */
    public static function settings() {
        return [
            'recaptcha' => [
                'title' => __( 'reCAPTCHA Integration', 'buddyclients' ),
                'description' => sprintf(
                    '%1$s <a href="%2$s" target="_blank">%3$s</a>',
                    __( 'Protect your website against spam with Google reCAPTCHA.', 'buddyclients' ),
                    'https://cloud.google.com/recaptcha/docs/create-key-website',
                    __( 'Learn how to create reCAPTCHA keys.', 'buddyclients' )
                ),
                'fields' => [
                    'enable_recaptcha' => [
                        'label' => __( 'Enable reCAPTCHA', 'buddyclients' ),
                        'type' => 'dropdown',
                        'options' => [
                            'disable' => __( 'Disable', 'buddyclients' ),
                            'enable'    => __( 'Enable', 'buddyclients' ),
                        ],
                        'description' => __( 'Enable reCAPTCHA to protect your forms from spam.', 'buddyclients' ),
                    ],
                    'recaptcha_site_key' => [
                        'label' => __( 'Site Key', 'buddyclients' ),
                        'type' => 'text',
                        'description' => __( 'Enter your site key.', 'buddyclients' ),
                    ],
                    'recaptcha_secret_key' => [
                        'label' => __( 'Secret Key', 'buddyclients' ),
                        'type' => 'text',
                        'description' => __( 'Enter your secret key.', 'buddyclients' ),
                    ],
                    'recaptcha_threshold' => [
                        'label' => __( 'reCAPTCHA Threshold', 'buddyclients' ),
                        'type' => 'dropdown',
                        'options' => [
                            '0.9'     => __( '0.9 - Most sensitive', 'buddyclients' ),
                            '0.8'     => __( '0.8', 'buddyclients' ),
                            '0.7'     => __( '0.7', 'buddyclients' ),
                            '0.6'     => __( '0.6', 'buddyclients' ),
                            '0.5'     => __( '0.5 - Default', 'buddyclients' ),
                            '0.4'     => __( '0.4', 'buddyclients' ),
                            '0.3'     => __( '0.3', 'buddyclients' ),
                            '0.2'     => __( '0.2', 'buddyclients' ),
                            '0.1'     => __( '0.1 - Least sensitive', 'buddyclients' ),
                        ],
                        'description' => __( 'Determine how sensitive the spam filter should be. Thresholds above 0.5 may block valid submissions.', 'buddyclients' ),
                    ],
                ],
            ],
            //'meta' => [
            //    'title' => __( 'Meta Ads Integration', 'buddyclients' ),
            //    'description' => __( 'Set up the API integration to send conversion events to Meta (Facebook).', 'buddyclients' ),
            //    'fields' => [
            //        'meta_access_token' => [
            //            'label' => __( 'Access Token', 'buddyclients' ),
            //            'type' => 'text',
            //            'description' => __( 'Enter your access token.', 'buddyclients' ),
            //        ],
            //        'meta_pixel_id' => [
            //            'label' => __( 'Pixel ID', 'buddyclients' ),
            //            'type' => 'text',
            //            'description' => __( 'Enter your pixel ID.', 'buddyclients' ),
            //        ],
            //    ],
            //],
        ];
    }
}