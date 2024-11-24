<?php
/**
 * Mofifies the admin pages for the free version.
 * 
 * @since 1.0.0
 */
function buddyc_free_admin_pages( $pages ) {
    // Remove license page
    unset( $pages['license'] );

    // Add upgrade page
    $pages['free_upgrade'] = [
        'key' => 'free-upgrade',
        'settings' => false,
        'title' => __('Upgrade', 'buddyclients'),
        'parent_slug' => 'bc-dashboard',
        'buddyc_menu_order' => 30,
        'group' => 'settings',
        'callable' => 'buddyc_free_upgrade_content'
    ];

    return $pages;
}
add_action( 'buddyc_admin_pages', 'buddyc_free_admin_pages', 1, 10 );

/**
 * Mofifies the admin nav tabs for the free version.
 * 
 * @since 1.0.0
 */
function buddyc_free_nav_tabs( $tabs ) {
    // Remove license page
    unset( $tabs['license'] );

    // Add upgrade tab
    $tabs['free_upgrade'] = [__( 'Upgrade', 'buddyclients-free' ) => ['page'  => 'bc-free-upgrade']];

    return $tabs;
}
add_action( 'buddyc_nav_tabs', 'buddyc_free_nav_tabs', 1, 10 );

/**
 * Outputs the ugprade admin page content.
 * 
 * @since 1.0.0
 * @ignore
 */
function buddyc_free_upgrade_content() {
    ob_start();
    
    // Open container
    echo '<div class="bcf-upgrade-info">';
    
    // Upgrade heading
    echo __( '<h1>Upgrade BuddyClients to accept payments, manage projects, and <strong>grow your business</strong>.</h1>', 'buddyclients-free' );
    
    // Open options container
    echo '<div class="bcf-upgrade-options">';
    
    // Define options
    $options = [
        'essential' => [
            'name'          => 'BuddyClients ' . __( 'Essential', 'buddyclients-free' ),
            'description'   => __( 'Sell services and manage clients\' projects in one place.', 'buddyclients-free' ),
            'features'      => [
                __( 'Everything in Free', 'buddyclients-free' ),
                __( 'Flexible pricing structures', 'buddyclients-free' ),
                __( 'Adjust prices dynamically', 'buddyclients-free' ),
                __( 'Filter team by preferences', 'buddyclients-free' ),
                __( 'Manage team payments', 'buddyclients-free' ),
            ],
        ],
        'business' => [
            'name'          => 'BuddyClients ' . __( 'Business', 'buddyclients-free' ),
            'description'   => __( 'Grow your service-based business with premium tools.', 'buddyclients-free' ),
            'features'      => [
                __( 'Everything in Essential', 'buddyclients-free' ),
                __( 'Affiliate program', 'buddyclients-free' ),
                __( 'Client testimonials', 'buddyclients-free' ),
                __( 'Custom quotes', 'buddyclients-free' ),
                __( 'Live search help docs', 'buddyclients-free' ),
                __( 'Contact form', 'buddyclients-free' ),
                __( 'Legal agreements', 'buddyclients-free' ),
                __( 'Team availability', 'buddyclients-free' ),
                __( 'Sales team commission', 'buddyclients-free' ),
                __( 'Manual and assisted bookings', 'buddyclients-free' ),
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

        echo '<a href="' . buddyc_upgrade_url() . '" class="buy-now" target="_blank">' . __( 'Learn More', 'buddyclients-free' ) . '</a>';
        echo '</div>';
        
    }
    
    // Close options container
    echo '</div>';
    
    // Close container
    echo '</div>';
    
    echo ob_get_clean();
}