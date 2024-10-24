<?php
/**
 * Template Name: Single BC Brief
 * Description: A custom template for a single bc_brief post.
 */

// Get header
get_header();

// Initialize
$content = '<div class="bc-single-post">';

// Class does not exist
if ( ! class_exists( 'BuddyClients\Components\Brief\SingleBrief' ) ) {
    $content .= '<p>' . __( 'Briefs are not enabled.', 'buddyclients' ) . '</p>';
} else {
    // Generate content
    $content .= ( new BuddyClients\Components\Brief\SingleBrief )->display();
}

// Close container
$content .= '</div>';

// Output content
echo wp_kses( $content, bc_allowed_html_form() );

// Get footer
get_footer();
?>
