<?php
/**
 * Plugin Name: BuddyClients Free
 * Plugin URI:  https://buddyclients.com
 * Description: BuddyClients is a flexible and comprehensive platform for any service-based business. Compatible with BuddyPress and BuddyBoss.
 * Author:      Victoria Griffin
 * Author URI:  https://victoriagriffin.com/
 * Version:     1.0.2
 * Text Domain: buddyclients
 * Domain Path: /languages/
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define constants
if ( ! defined( 'BC_PLUGIN_VERSION' ) ) {
	define( 'BC_PLUGIN_VERSION', '1.0.2' );
}

if ( ! defined( 'BC_PLUGIN_FILE' ) ) {
	define( 'BC_PLUGIN_FILE', __FILE__ );
}

require_once(plugin_dir_path(__FILE__) . 'BuddyClientsFree-class.php');

/**
 * Returns the one true BuddyClients Instance.
 * 
 * @since 0.1.0
 *
 * @return BuddyClients|null The one true BuddyClients Instance.
 */
function buddyclients_free() {
    if ( function_exists( 'buddypress' ) && ! function_exists( 'buddyclients' ) && class_exists( 'BuddyClientsFree' ) ) {
	    return BuddyClientsFree::instance();
    }
}

/**
 * Initializes BuddyClients.
 * 
 * Let's go!
 * 
 * @since 0.1.0
 */
add_action( 'plugins_loaded', 'buddyclients_free' );

/**
 * Displays an admin notice if multiple versions of BuddyClients are installed.
 *
 * @since 0.1.0
 */
function bc_free_installed_notice() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    // Disable BuddyClients Free message.
    if ( function_exists( 'buddyclients' ) ) {
        $plugins_url = is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
        ?>

        <div id="message" class="error notice">
            <p><strong><?php esc_html_e( 'BuddyClients Error', 'buddyclients' ); ?></strong></p>
            <p><?php esc_html_e( 'Multiple versions of the BuddyClients Platform are installed.', 'buddyclients' ); ?></p>
            <p><?php printf( __( 'Please <a href="%s">deactivate BuddyClients Free</a> to continue.', 'buddyclients' ), $plugins_url ); ?></p>
        </div>

        <?php
    }
}

/**
 * You can't have BuddyClients without BuddyPress!
 */
add_action( 'admin_notices', 'bc_free_installed_notice' );

/**
 * Displays an admin notice when BuddyPress is missing.
 *
 * @since 0.1.0
 */
function bc_missing_bp_notice() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	// Disable BuddyPress message.
	if ( ! function_exists( 'buddypress' ) ) {
		$bp_plugins_url = is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
		$link_plugins   = sprintf( "<a href='%s'>%s</a>", $bp_plugins_url, __( 'deactivate', 'buddyclients' ) );
		$bp_install     = admin_url( '/plugin-install.php?s=buddypress&tab=search&type=term' );
		?>

		<div id="message" class="error notice">
			<p><strong><?php esc_html_e( 'BuddyPress is missing.', 'buddyclients' ); ?></strong></p>
			<p><?php printf( esc_html__( 'The BuddyClients Platform can\'t work without BuddyPress.', 'buddyclients' ), $link_plugins ); ?></p>
			<p><?php printf( __( 'Install <a href="%s">BuddyPress</a> or <a href="%s" target="_blank">BuddyBoss</a>.', 'buddyclients' ), $bp_install, 'https://www.buddyboss.com/website-platform/' ); ?></p>
		</div>

		<?php
	}
}

/**
 * You can't have BuddyClients without BuddyPress!
 */
add_action( 'admin_notices', 'bc_missing_bp_notice' );

/**
 * Displays an admin notice when groups are not enabled.
 *
 * @since 0.4.3
 */
function bc_groups_disabled_notice() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	// Groups disabled message.
	if ( function_exists( 'buddypress' ) && ! bp_is_active( 'groups' ) ) {
	    $enable_link = admin_url( 'admin.php?page=bp-components' );
	    
		$bp_plugins_url = is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
		$link_plugins   = sprintf( "<a href='%s'>%s</a>", $bp_plugins_url, __( 'deactivate', 'buddyclients' ) );
		$bp_install     = admin_url( '/plugin-install.php?s=buddypress&tab=search&type=term' );
		?>

		<div id="message" class="error notice">
			<p><strong><?php esc_html_e( 'Social groups are disabled.', 'buddyclients' ); ?></strong></p>
			<p><?php printf( esc_html__( 'Groups must be enabled for the BuddyClients Platform to function properly.', 'buddyclients' ), $link_plugins ); ?></p>
			<p><?php printf( __( '<a href="%s">Enable social groups.</a>', 'buddyclients' ), $enable_link ); ?></p>
		</div>

		<?php
	}
}
add_action( 'admin_notices', 'bc_groups_disabled_notice' );

/**
 * Displays an admin notice when the sanity check fails.
 *
 * @since 0.4.3
 */
function bc_sanity_check_notice() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	// Admin message.
	if ( defined( 'BC_SANITY_ALERT' ) ) {
		$bp_plugins_url = is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
		$link_plugins   = sprintf( "<a href='%s'>%s</a>", $bp_plugins_url, __( 'deactivate', 'buddyclients' ) );
		$bc_install     = admin_url( '/plugin-install.php?s=buddyclients&tab=search&type=term' );
		?>

		<div id="message" class="error notice">
			<p><strong><?php esc_html_e( 'BuddyClients Plugin Error', 'buddyclients' ); ?></strong></p>
			<p><?php printf( __( '%s', 'buddyclients' ), BC_SANITY_ALERT ); ?></p>
			<p><?php printf( __( 'Please <a href="%s">reinstall the BuddyClients plugin</a>.', 'buddyclients' ), $bc_install ); ?></p>
		</div>

		<?php
	}
}
add_action( 'admin_notices', 'bc_sanity_check_notice' );