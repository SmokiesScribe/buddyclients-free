<?php
namespace BuddyClients\Components\Email;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Defines the meta data for buddyc_email posts.
 *
 * @since 1.0.29
 */
class MetaEmail {
    
   /**
     * Defines the post meta data.
     * 
     * @since 1.0.29
     */
    public static function meta() {
        return [
            'Email Info' => [
                'tables' => [
                    'Subject' => [
                        'meta' => [
                            '_buddyc_email_subject' => [
                                'label' => __('Email Subject', 'buddyclients-free'),
                                'description' => __('Enter the subject for the email.', 'buddyclients-free'),
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}