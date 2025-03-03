<?php
/**
 * Plugin Name: BuddyClients Free
 * Plugin URI:  https://buddyclients.com
 * Description: BuddyClients is a flexible and comprehensive platform for any service-based business. Compatible with BuddyPress and BuddyBoss.
 * Author:      Victoria Griffin
 * Author URI:  https://victoriagriffin.com/
 * Version:     1.0.28
 * Text Domain: buddyclients-free
 * Domain Path: /languages/
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define constants
if ( ! defined( 'BUDDYC_PLUGIN_VERSION' ) ) {
	define( 'BUDDYC_PLUGIN_VERSION', '1.0.28' );
}

if ( ! defined( 'BUDDYC_PLUGIN_FILE' ) ) {
	define( 'BUDDYC_PLUGIN_FILE', __FILE__ );
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
    if ( function_exists( 'buddypress' ) && ! function_exists( 'buddyclients-free' ) && class_exists( 'BuddyClientsFree' ) ) {
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
function buddyc_installed_notice() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    // Disable BuddyClients Free message.
    if ( function_exists( 'buddyclients' ) ) {
        $plugins_url = is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
		$allowed_html = ['a' => ['href' => [], 'target' => []]];
        ?>

        <div id="message" class="error notice">
            <p><strong><?php esc_html_e( 'BuddyClients Error', 'buddyclients-free' ); ?></strong></p>
            <p><?php esc_html_e( 'Multiple versions of the BuddyClients Platform are installed.', 'buddyclients-free' ); ?></p>
			<?php /* translators: %s: link to deactivate BuddyClients Free */ ?>
			<p><?php 
				printf( 
					wp_kses(
						/* translators: %s: link to deactivate BuddyClients Free */
						sprintf( __( 'Please %s to continue.', 'buddyclients-free' ), 
							sprintf( '<a href="%s">deactivate BuddyClients Free</a>', esc_url( $plugins_url ) )
						),
						$allowed_html 
					) 
				); 
			?></p>
		</div>

        <?php
    }
}

/**
 * Only one version of BuddyClients can be installed at a time.
 */
if ( function_exists( 'buddyclients-free' ) ) {
	add_action( 'admin_notices', 'buddyc_installed_notice' );
	return;
}

/**
 * Displays an admin notice when BuddyPress is missing.
 *
 * @since 0.1.0
 */
function buddyc_missing_bp_notice_free() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	
	// Prevent notice from showing on the plugin install page.
	$screen = get_current_screen();
	if ( $screen && 'plugin-install' === $screen->id ) {
		return;
	}

	// Disable BuddyPress message.
	if ( ! function_exists( 'buddypress' ) ) {
		$bp_install     = admin_url( '/plugin-install.php?s=buddypress&tab=search&type=term' );
		$bb_install		= 'https://www.buddyboss.com/website-platform/';
		$allowed_html	= ['a' => ['href' => [], 'target' => []]];	
		
		?>

		<div id="message" class="error notice">
			<p><strong><?php esc_html_e( 'BuddyPress is missing.', 'buddyclients-free' ); ?></strong></p>
			<p><?php esc_html_e( 'The BuddyClients Platform can\'t work without BuddyPress.', 'buddyclients-free' ); ?></p>
			<?php /* translators: %1$s: link to install BuddyPress; %2$s: link to install BuddyBoss */ ?>
			<p><?php 
				printf(
					wp_kses(
						/* translators: %s: link to BuddyPress installation */
						__( 'Install %1$s or %2$s.', 'buddyclients-free' ),
						$allowed_html
					),
					sprintf( '<a href="%s">BuddyPress</a>', esc_url( $bp_install ) ),
					sprintf( '<a href="%s" target="_blank">BuddyBoss</a>', esc_url( $bb_install ) )
				); 
			?></p>
		</div>

		<?php
	}
}

/**
 * You can't have BuddyClients without BuddyPress!
 */
add_action( 'admin_notices', 'buddyc_missing_bp_notice_free' );

/**
 * Displays an admin notice when groups are not enabled.
 *
 * @since 0.4.3
 */
function buddyc_groups_disabled_notice_free() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	// Groups disabled message.
	if ( function_exists( 'buddypress' ) && ! bp_is_active( 'groups' ) ) {
	    $enable_link = admin_url( 'admin.php?page=bp-components' );
	    $bp_install     = admin_url( '/plugin-install.php?s=buddypress&tab=search&type=term' );
		$allowed_html = ['a' => ['href' => [], 'target' => []]];
		?>

		<div id="message" class="error notice">
			<p><strong><?php esc_html_e( 'Social groups are disabled.', 'buddyclients-free' ); ?></strong></p>
			<p><?php esc_html_e( 'Groups must be enabled for the BuddyClients Platform to function properly.', 'buddyclients-free' ); ?></p>
			<?php /* translators: %s: link to enable social groups */ ?>
			<p><?php printf( wp_kses( __( '<a href="%s">Enable social groups.</a>', 'buddyclients-free' ), $allowed_html ), esc_url( $enable_link ) ); ?></p>
		</div>

		<?php
	}
}
add_action( 'admin_notices', 'buddyc_groups_disabled_notice_free' );