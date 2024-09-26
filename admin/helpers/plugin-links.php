<?php
/**
 * Initializes the plugin links.
 * 
 * @since 0.2.1
 */
function bc_plugin_page_links() {
    
    // Build array
    $links = [
        '<a href="admin.php?page=bc-general-settings">' . __( 'Settings', 'buddyclients' ) . '</a>',
        '<a href="' . trailingslashit( BC_URL ) . 'help" target="_blank">' . __( 'User Guides', 'buddyclients' ) . '</a>',
        '<a href="' . trailingslashit( BC_URL ) . 'license" target="_blank">' . __( 'Account', 'buddyclients' ) . '</a>',
    ];
    
    // Get current license status
    $license = get_option( 'bc_license' );
    
    if ( ! $license ) {
        return;
    }
    
    // Define upgrade links
    $upgrade_links = [
        'bc_basic'  => 'license',
        'bc_free'   => 'pricing'
    ];
    
    // Add upgrade link
    foreach ( $upgrade_links as $product => $link ) {
        if ( strpos( $license->product, $product ) !== false ) {
            $links[] = '<a href="' . trailingslashit( BC_URL ) . $link . '" target="_blank" style="color: green; font-weight: bold">' . __( 'Upgrade', 'buddyclients' ) . '</a>';
        }
    }
    
    // Initialize plugin links
    new BuddyClients\Admin\PluginLinks( $links );
}
add_action('init', 'bc_plugin_page_links');
