<?php
/**
 * BuddyClients favicon.
 * 
 * @since 0.1.0
 */
function bc_favicon() {
  echo '
    <style>
    .dashicons-buddyclients {
        background-image: url("' . plugins_url('buddyclients/admin/assets/media/buddyclients-icon-white.png') . '");
        background-repeat: no-repeat;
        background-position: center;
        background-size: 20px;
    }
    .dashicons-buddyclients-dark {
        background-image: url("' . plugins_url('buddyclients/admin/assets/media/buddyclients-icon-color.png') . '");
        background-repeat: no-repeat;
        background-position: center;
        background-size: 20px;
    }
    </style>
'; }
add_action('admin_head', 'bc_favicon');