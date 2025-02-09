<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Dismisses the welcome notice for BuddyClients Free permanently.
 * 
 * @since 1.0.13
 */
function buddyc_free_dismiss_welcome_message() {
    update_option( 'buddyc_free_welcome_message_dismissed', true ); // Store permanently
    wp_redirect( wp_get_referer() ); // Redirect back to the referring page
    exit;
}
add_action( 'admin_post_buddyc_free_dismiss_welcome_message', 'buddyc_free_dismiss_welcome_message' );

/**
 * Generates welcome message for BuddyClients Free.
 * 
 * @since 1.0.13
 */
function buddyc_free_welcome_message() {
    // Check for upgrade from free to regular
    $dismissed = get_option( 'buddyc_free_welcome_message_dismissed' );

    if ( ! $dismissed ) {
        // Check if the message should be displayed
        if ( get_option( 'buddyc_free_welcome_message_dismissed', false ) ) {
            return; // Exit if the message is dismissed permanently
        }

        $message = '<div class="buddyc-welcome-message">';
        $message .= '<h2 class="buddyc-bot-margin-small">🎉 ' . __( 'Welcome to BuddyClients!', 'buddyclients-free' ) . '</h2>';

        $message .= '<ul class="buddyc-no-list">';
        $message .= '<li class="buddyc-bot-margin-small">';
        $message .= '<i class="fa-solid fa-circle-info"></i> ' . sprintf(
            /* translators: %s: the url to user guides */
            __( '<strong><a href="%s" target="_blank">Explore user guides</a></strong> to get the most out of BuddyClients.', 'buddyclients-free' ),
            esc_url( trailingslashit( BUDDYC_URL ) . 'help' )
        );
        $message .= '</li>';
        $message .= '<li class="buddyc-bot-margin-small">';
        $message .= '<i class="fa-solid fa-key"></i> ' . sprintf(
            /* translators: %s: the url to upgrade the plugin */
            __( '<strong><a href="%s" target="_blank">Upgrade to access</a> premium features and accept payments.', 'buddyclients-free' ),
            esc_url( trailingslashit( BUDDYC_URL ) . 'pricing' )
        );
        $message .= '</li>';
        $message .= '</ul>';

        // Add dismiss button
        $message .= '<p class="buddyc-top-margin-med">';
        $message .= '<a class="buddyc-dismiss-welcome-btn" href="' . esc_url( admin_url( 'admin-post.php?action=buddyc_free_dismiss_welcome_message' ) ) . '">';
        $message .= esc_html__( 'Hide this message', 'buddyclients-free' ) . '</a>';
        $message .= '</p>';

        $links = [
            '<a href="admin.php?page=buddyc-general-settings">' . __( 'Settings', 'buddyclients-free' ) . '</a>',
            '<a href="' . trailingslashit( BUDDYC_URL ) . 'help" target="_blank">' . __( 'User Guides', 'buddyclients-free' ) . '</a>',
            '<a href="' . trailingslashit( BUDDYC_URL ) . 'license" target="_blank">' . __( 'Account', 'buddyclients-free' ) . '</a>',
            '<a href="' . trailingslashit( BUDDYC_URL ) . 'support" target="_blank">' . __( 'Support', 'buddyclients-free' ) . '</a>',
        ];

        $message .= '<p>Quick Links: ' . implode(' | ', $links) . '</p>';
        $message .= '</div>';

        // Output the admin notice
        $args = [
            'message'     => $message,
            'dismissable' => 'true',
            'color'       => 'green'
        ];
        buddyc_admin_notice( $args );
    }
}
add_action( 'admin_init', 'buddyc_free_welcome_message' );
