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
    $transient = get_transient( 'buddyc_version_upgraded' );

    if ( $transient ) {
        // Check if the message should be displayed
        if ( get_option( 'buddyc_free_welcome_message_dismissed', false ) ) {
            return; // Exit if the message is dismissed permanently
        }

        $message = '<div class="buddyc-welcome-message">';
        $message .= '<h2 class="buddyc-bot-margin-small">🎉 Welcome to BuddyClients!</h2>';
        $message .= '<p class="buddyc-bot-margin-med">Congratulations on upgrading to <strong>BuddyClients</strong>! We\'re excited to have you using the full version of our plugin, packed with even more features to help your business thrive.</p>';

        $message .= '<h4 class="buddyc-bot-margin-small">Get Started:</h4>';
        $message .= '<ul class="buddyc-no-list">';
        $message .= '<li class="buddyc-bot-margin-small">';
        $message .= '<i class="fa-solid fa-key"></i> <strong><a href="' . esc_url( admin_url( '/admin.php?page=buddyc-license-settings' ) ) . '">Enter your license key</a></strong> to activate premium features. ';
        $message .= '</li>';
        $message .= '<li class="buddyc-bot-margin-small">';
        $message .= '<i class="fa-solid fa-globe"></i> <strong><a href="' . trailingslashit( BUDDYC_URL ) . 'license" target="_blank">Add this website</a></strong> to your BuddyClients account. ';
        $message .= '</li>';
        $message .= '<li class="buddyc-bot-margin-small">';
        $message .= '<i class="fa-solid fa-gear"></i> <strong><a href="' . esc_url( admin_url( '/admin.php?page=buddyc-license-settings' ) ) . '">Update components</a></strong> to ensure all features are enabled. ';
        $message .= '</li>';
        $message .= '<li class="buddyc-bot-margin-small">';
        $message .= '<i class="fa-solid fa-circle-info"></i> <strong><a href="' . trailingslashit( BUDDYC_URL ) . 'help" target="_blank">Explore user guides</a></strong> to get the most out of BuddyClients. ';
        $message .= '</li>';
        $message .= '</ul>';

        // Add dismiss button
        $message .= '<p class="buddyc-top-margin-med">';
        $message .= '<a class="buddyc-dismiss-welcome-btn" href="' . esc_url( admin_url( 'admin-post.php?action=buddyc_free_dismiss_welcome_message' ) ) . '">';
        $message .= esc_html__( 'Hide this message', 'buddyclients' ) . '</a>';
        $message .= '</p>';

        $links = [
            '<a href="admin.php?page=buddyc-general-settings">' . __( 'Settings', 'buddyclients' ) . '</a>',
            '<a href="' . trailingslashit( BUDDYC_URL ) . 'help" target="_blank">' . __( 'User Guides', 'buddyclients' ) . '</a>',
            '<a href="' . trailingslashit( BUDDYC_URL ) . 'license" target="_blank">' . __( 'Account', 'buddyclients' ) . '</a>',
            '<a href="' . trailingslashit( BUDDYC_URL ) . 'support" target="_blank">' . __( 'Support', 'buddyclients' ) . '</a>',
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
