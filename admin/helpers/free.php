<?php
/**
 * Mofifies the admin pages for the free version.
 * 
 * @since 1.0.0
 */
function bc_free_admin_pages( $pages ) {
    // Remove license page
    unset( $pages['license'] );

    // Add upgrade page
    $pages['free_upgrade'] = [
        'key' => 'free-upgrade',
        'settings' => false,
        'title' => __('Upgrade', 'buddyclients'),
        'parent_slug' => 'bc-dashboard',
        'bc_menu_order' => 30,
        'group' => 'settings',
        'callable' => 'bc_free_upgrade_content'
    ];

    return $pages;
}
add_action( 'bc_admin_pages', 'bc_free_admin_pages', 1, 10 );

/**
 * Mofifies the admin nav tabs for the free version.
 * 
 * @since 1.0.0
 */
function bc_free_nav_tabs( $tabs ) {
    // Remove license page
    unset( $tabs['license'] );

    // Add upgrade tab
    $tabs['free_upgrade'] = [__( 'Upgrade', 'buddyclients' ) => ['page'  => 'bc-free-upgrade']];

    return $tabs;
}
add_action( 'bc_nav_tabs', 'bc_free_nav_tabs', 1, 10 );

/**
 * Outputs the ugprade admin page content.
 * 
 * @since 1.0.0
 * @ignore
 */
function bc_free_upgrade_content() {
    ob_start();
    
    // Open container
    echo '<div class="bcf-upgrade-info">';
    
    // Upgrade heading
    echo __( '<h1>Upgrade BuddyClients to accept payments, manage projects, and <strong>grow your business</strong>.</h1>', 'buddyclients' );
    
    // Open options container
    echo '<div class="bcf-upgrade-options">';
    
    // Define options
    $options = [
        'essential' => [
            'name'          => 'BuddyClients ' . __( 'Essential', 'buddyclients' ),
            'description'   => __( 'Sell services and manage clients\' projects in one place.', 'buddyclients' ),
            'features'      => [
                __( 'Everything in Free', 'buddyclients' ),
                __( 'Flexible pricing structures', 'buddyclients' ),
                __( 'Adjust prices dynamically', 'buddyclients' ),
                __( 'Filter team by preferences', 'buddyclients' ),
                __( 'Manage team payments', 'buddyclients' ),
            ],
        ],
        'business' => [
            'name'          => 'BuddyClients ' . __( 'Business', 'buddyclients' ),
            'description'   => __( 'Grow your service-based business with premium tools.', 'buddyclients' ),
            'features'      => [
                __( 'Everything in Essential', 'buddyclients' ),
                __( 'Affiliate program', 'buddyclients' ),
                __( 'Client testimonials', 'buddyclients' ),
                __( 'Custom quotes', 'buddyclients' ),
                __( 'Live search help docs', 'buddyclients' ),
                __( 'Contact form', 'buddyclients' ),
                __( 'Legal agreements', 'buddyclients' ),
                __( 'Team availability', 'buddyclients' ),
                __( 'Sales team commission', 'buddyclients' ),
                __( 'Manual and assisted bookings', 'buddyclients' ),
            ],
        ]
    ];
    
    // BuddyClients Options
    foreach ( $options as $key => $data ) {
    
        echo '<div class="bcf-upgrade-option">';
        echo '<div>';
        echo '<h3>BuddyClients ' . $data['name'] . '</h3>';
        echo '<p>' . $data['description'] . '</p>';
        
        // Features List
        echo '<ul>';
        foreach ( $data['features'] as $feature ) {
            echo '<li><li><i class="feature-check bb-icon-check bb-icon-rf"></i>' . $feature . '</li>';
        }
        echo '</ul>';
        echo '</div>';

        echo '<a href="' . bc_upgrade_url() . '" class="buy-now" target="_blank">' . __( 'Learn More', 'buddyclients' ) . '</a>';
        echo '</div>';
        
    }
    
    // Close options container
    echo '</div>';
    
    // Close container
    echo '</div>';
    
    echo ob_get_clean();
}