<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Template Name: Single BuddyClients Brief
 * Description: A custom template for a single buddyc_brief post.
 */

// Get header
get_header( 'header.php' );

// Initialize
$content = '<div class="buddyc-single-post">';

// Class does not exist
if ( ! class_exists( 'BuddyClients\Components\Brief\SingleBrief' ) ) {
    $content .= '<p>' . __( 'Briefs are not enabled.', 'buddyclients-free' ) . '</p>';
} else {
    // Generate content
    $content .= ( new BuddyClients\Components\Brief\SingleBrief )->display();
}

// Close container
$content .= '</div>';

// Output content
$allowed_html = buddyc_allowed_html_form();
$allowed_html['ul'] = ['class' => []];
$allowed_html['li'] = [];
echo wp_kses( $content, $allowed_html );

// Get footer
get_footer();
