<?php
/**
 * Generates a url to the BuddyClients website.
 * 
 * @since 0.1.0
 * 
 * @ignore
 * 
 * @param   string  $path   Optional. Path to append to the url.
 */
function bc_site_url( $path = null ) {
    $url = 'https://buddyclients.com';
    
    return $path ? $url . $path : $url;
}

/**
 * Generates a url to the BuddyClients pricing page.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_upgrade_url() {
    return bc_site_url('/pricing');
}

/**
 * Generates a URL to the BuddyClients account pagbc_account_linke.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_account_url() {
    return bc_site_url('/license');
}

/**
 * Generates a url to the enable components admin page.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_enable_component_url() {
    return admin_url( '/admin.php?page=bc-components-settings' );
}

/**
 * Generates a URL to the Freelancer Mode setting.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_freelancer_mode_url() {
    return admin_url('/admin.php?page=bc-booking-settings');
}

/**
 * Generates an upgrade link.
 * 
 * @since 0.1.0
 * 
 * @ignore
 * 
 * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
 *                               Defaults to null.
 */
function bc_upgrade_link( $boxed = null ) {
    
    // Define icon
    if ( bc_buddyboss_theme() ) {
        $icon = '<i class="bb-icon-rocket bb-icon-l"></i> ';
    } else {
        $icon = '<i class="fa-solid fa-rocket"></i> ';
    }
    
    // Build container if boxed
    $open_container = $boxed ? '<div class="bc-upgrade-link-container">' : '';
    $close_container = $boxed ? '</div>' : '';
    
    // Get url
    $url = bc_upgrade_url();
    
    // Build link
    $link = sprintf(
        '<p class="bc-upgrade-link">%s ' . __( 'Upgrade to <a href="%s" target="_blank">BuddyClients Essential or BuddyClients Business</a>.', 'buddyclients' ),
        $icon,
        esc_url( $url )
    );
    
    // Build output
    $output = $open_container . $link . $close_container;
    
    return $output;
}

/**
 * Generates a BuddyClients account link.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_account_link() {
    $icon_class = bc_buddyboss_theme() ? 'bb-icon-cog bb-icon-l' : 'fa-solid fa-gear';
    $icon = '<i class="' . $icon_class . '"></i> ';
    $url = bc_account_url();
    return sprintf(
        '<p class="bc-upgrade-link">%s ' . __( 'Manage <a href="%s" target="_blank">%s</a>.', 'buddyclients' ) . '</p>',
        $icon,
        esc_url( $url ),
        __( 'your BuddyClients subscription', 'buddyclients' )
    );
}

/**
 * Generates a link to enable a component.
 * 
 * @since 0.1.0
 * 
 * @ignore
 * 
 * @param   string   $component  The component to be enabled.
 * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
 *                               Defaults to null.
 */
function bc_enable_component_link( $component, $boxed = null ) {
    
    // Build url
    $url = bc_enable_component_url();
    
    // Define icon class
    $icon_class = bc_buddyboss_theme() ? 'bb-icon-toggle-off bb-icon-l' : 'fa-solid fa-toggle-off';
    
    // Build icon
    $icon = '<i class="' . $icon_class . '"></i> ';
    
    $icon = bc_icon( 'toggle_off' );
    
    // Build container if boxed
    $open_container = $boxed ? '<div class="bc-upgrade-link-container">' : '';
    $close_container = $boxed ? '</div>' : '';
    
    // Build link
    $link = sprintf(
        '<p class="bc-upgrade-link">%s ' . __( 'The %s component is disabled. <a href="%s">Enable the component</a>.', 'buddyclients' ) . '</p>',
        $icon,
        bc_component_name( $component ),
        esc_url( $url )
    );
    
    // Build output
    $output = $open_container . $link . $close_container;
    
    return $output;
}

/**
 * Generates a link to disable Freelancer Mode.
 * 
 * @since 0.1.0
 * 
 * @ignore
 * 
 * @param   ?bool    $enable     Optional. Whether to include language about enabling Freelancer Mode.
 *                               Defaults to language about disabling Freelancer Mode.
 * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
 *                               Defaults to null.
 */
function bc_freelancer_mode_link( $enable = null, $boxed = null ) {
    
    // Build url
    $url = bc_freelancer_mode_url();
    
    // Define icon class
    $icon_class = bc_buddyboss_theme() ? 'bb-icon-toggle-off bb-icon-l' : 'fa-solid fa-toggle-off';
    
    // Build icon
    $icon = '<i class="' . $icon_class . '"></i> ';
    
    $icon = bc_icon( 'toggle_off' );
    
    // Build container if boxed
    $open_container = $boxed ? '<div class="bc-upgrade-link-container">' : '';
    $close_container = $boxed ? '</div>' : '';
    
    // Build link
    $link = '<p class="bc-upgrade-link">' . $icon . __( 'Freelancer Mode is enabled. <a href="%s">Disable Freelancer Mode</a>.', 'buddyclients' ) . '</p>';
    
    // Build output
    $output = $open_container . $link . $close_container;
    
    return $output;
}