<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\AdminTableManager;

/**
 * Outputs an admin table. 
 * 
 * @since 1.0.28
 * 
 * @param   string  $key    The table key.
 */
function buddyc_admin_table( $key ) {
    $table_manager = new AdminTableManager( $key );
    return $table_manager->build_table();
}