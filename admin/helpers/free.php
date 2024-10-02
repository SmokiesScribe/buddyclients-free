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
       // 'callback' => 'bc_free_upgrade_content',
       // 'callback_args' => [true],
        //'callable' => ['bc_upgrade_link', [true]] 
    ];

    return $pages;
}
add_action( 'bc_admin_pages', 'bc_free_admin_pages', 1, 10 );

/**
 * Outputs the ugprade admin page content.
 * 
 * @since 1.0.0
 */
function bc_free_upgrade_content() {
    echo 'Test content';
    return 'Test content returned';
}