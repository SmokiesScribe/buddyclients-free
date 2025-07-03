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
                'title' => __( 'reCAPTCHA Integration', 'buddyclients-lite' ),
                'description' => sprintf(
                    '%1$s <a href="%2$s" target="_blank">%3$s</a>',
                    __( 'Protect your website against spam with Google reCAPTCHA.', 'buddyclients-lite' ),
                    'https://cloud.google.com/recaptcha/docs/create-key-website',
                    __( 'Learn how to create reCAPTCHA keys.', 'buddyclients-lite' )
                ),
                'fields' => [
                    'enable_recaptcha' => [
                        'label' => __( 'Enable reCAPTCHA', 'buddyclients-lite' ),
                        'type' => 'dropdown',
                        'options' => [
                            'disable' => __( 'Disable', 'buddyclients-lite' ),
                            'enable'    => __( 'Enable', 'buddyclients-lite' ),
                        ],
                        'description' => __( 'Enable reCAPTCHA to protect your forms from spam.', 'buddyclients-lite' ),
                    ],
                    'recaptcha_site_key' => [
                        'label' => __( 'Site Key', 'buddyclients-lite' ),
                        'type' => 'text',
                        'description' => __( 'Enter your site key.', 'buddyclients-lite' ),
                    ],
                    'recaptcha_secret_key' => [
                        'label' => __( 'Secret Key', 'buddyclients-lite' ),
                        'type' => 'text',
                        'description' => __( 'Enter your secret key.', 'buddyclients-lite' ),
                    ],
                    'recaptcha_threshold' => [
                        'label' => __( 'reCAPTCHA Threshold', 'buddyclients-lite' ),
                        'type' => 'dropdown',
                        'options' => [
                            '0.9'     => sprintf( '0.9 - %s',
                                                __( 'Most sensitive', 'buddyclients-lite' )
                                            ),
                            '0.8'     => '0.8',
                            '0.7'     => '0.7',
                            '0.6'     => '0.6',
                            '0.5'     => sprintf( '0.5 - %s',
                                                __( 'Default', 'buddyclients-lite' )
                                            ),
                            '0.4'     => '0.4',
                            '0.3'     => '0.3',
                            '0.2'     => '0.2',
                            '0.1'     => sprintf( '0.1 - %s',
                                                __( 'Least sensitive', 'buddyclients-lite' )
                                            ),
                        ],
                        'description' => __( 'Determine how sensitive the spam filter should be. Thresholds above 0.5 may block valid submissions.', 'buddyclients-lite' ),
                    ],
                ],
            ],
            //'meta' => [
            //    'title' => __( 'Meta Ads Integration', 'buddyclients-lite' ),
            //    'description' => __( 'Set up the API integration to send conversion events to Meta (Facebook).', 'buddyclients-lite' ),
            //    'fields' => [
            //        'meta_access_token' => [
            //            'label' => __( 'Access Token', 'buddyclients-lite' ),
            //            'type' => 'text',
            //            'description' => __( 'Enter your access token.', 'buddyclients-lite' ),
            //        ],
            //        'meta_pixel_id' => [
            //            'label' => __( 'Pixel ID', 'buddyclients-lite' ),
            //            'type' => 'text',
            //            'description' => __( 'Enter your pixel ID.', 'buddyclients-lite' ),
            //        ],
            //    ],
            //],
        ];
    }
}