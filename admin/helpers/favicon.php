<?php
/**
 * BuddyClients favicon.
 * 
 * @since 0.1.0
 */
function bc_favicon() {
    $icon_white_url = esc_url(plugins_url('buddyclients/admin/assets/media/buddyclients-icon-white.png'));
    $icon_color_url = esc_url(plugins_url('buddyclients/admin/assets/media/buddyclients-icon-color.png'));

    $style = '
        <style>
        .dashicons-buddyclients {
            background-image: url("' . $icon_white_url . '");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 20px;
        }
        .dashicons-buddyclients-dark {
            background-image: url("' . $icon_color_url . '");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 20px;
        }
        </style>
    ';

    echo wp_kses($style, [
        'style' => [],
        'div' => [],
        'span' => [],
        'p' => [],
        'a' => [],
        'img' => [],
    ]);
}

add_action('admin_head', 'bc_favicon');