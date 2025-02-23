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
                'title' => __( 'reCAPTCHA Integration', 'buddyclients-free' ),
                'description' => sprintf(
                    '%1$s <a href="%2$s" target="_blank">%3$s</a>',
                    __( 'Protect your website against spam with Google reCAPTCHA.', 'buddyclients-free' ),
                    'https://cloud.google.com/recaptcha/docs/create-key-website',
                    __( 'Learn how to create reCAPTCHA keys.', 'buddyclients-free' )
                ),
                'fields' => [
                    'enable_recaptcha' => [
                        'label' => __( 'Enable reCAPTCHA', 'buddyclients-free' ),
                        'type' => 'dropdown',
                        'options' => [
                            'disable' => __( 'Disable', 'buddyclients-free' ),
                            'enable'    => __( 'Enable', 'buddyclients-free' ),
                        ],
                        'description' => __( 'Enable reCAPTCHA to protect your forms from spam.', 'buddyclients-free' ),
                    ],
                    'recaptcha_site_key' => [
                        'label' => __( 'Site Key', 'buddyclients-free' ),
                        'type' => 'text',
                        'description' => __( 'Enter your site key.', 'buddyclients-free' ),
                    ],
                    'recaptcha_secret_key' => [
                        'label' => __( 'Secret Key', 'buddyclients-free' ),
                        'type' => 'text',
                        'description' => __( 'Enter your secret key.', 'buddyclients-free' ),
                    ],
                    'recaptcha_threshold' => [
                        'label' => __( 'reCAPTCHA Threshold', 'buddyclients-free' ),
                        'type' => 'dropdown',
                        'options' => [
                            '0.9'     => sprintf( '0.9 - %s',
                                                __( 'Most sensitive', 'buddyclients-free' )
                                            ),
                            '0.8'     => '0.8',
                            '0.7'     => '0.7',
                            '0.6'     => '0.6',
                            '0.5'     => sprintf( '0.5 - %s',
                                                __( 'Default', 'buddyclients-free' )
                                            ),
                            '0.4'     => '0.4',
                            '0.3'     => '0.3',
                            '0.2'     => '0.2',
                            '0.1'     => sprintf( '0.1 - %s',
                                                __( 'Least sensitive', 'buddyclients-free' )
                                            ),
                        ],
                        'description' => __( 'Determine how sensitive the spam filter should be. Thresholds above 0.5 may block valid submissions.', 'buddyclients-free' ),
                    ],
                ],
            ],
            //'meta' => [
            //    'title' => __( 'Meta Ads Integration', 'buddyclients-free' ),
            //    'description' => __( 'Set up the API integration to send conversion events to Meta (Facebook).', 'buddyclients-free' ),
            //    'fields' => [
            //        'meta_access_token' => [
            //            'label' => __( 'Access Token', 'buddyclients-free' ),
            //            'type' => 'text',
            //            'description' => __( 'Enter your access token.', 'buddyclients-free' ),
            //        ],
            //        'meta_pixel_id' => [
            //            'label' => __( 'Pixel ID', 'buddyclients-free' ),
            //            'type' => 'text',
            //            'description' => __( 'Enter your pixel ID.', 'buddyclients-free' ),
            //        ],
            //    ],
            //],
        ];
    }
}