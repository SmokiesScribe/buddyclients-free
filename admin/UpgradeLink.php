<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Generates the content for an upgrade link.
 *
 * Used as a placeholder in the admin area when a component is not available.
 *
 * @since 1.0.30
 * @ignore
 */
class UpgradeLink {

    /**
     * The available product keys.
     *
     * @var array
     */
    private static $product_keys = ['free', 'basic', 'premium'];

    /**
     * The current product.
     * 
     * @var string
     */
    public $curr_product;

    /**
     * Retrieves the product name for a product key.
     * 
     * @since 1.0.30
     * 
     * @param   string  $product_key    The product key.
     * @return  string  The product name for the key.
     */
    private static function product_name( $product_key ) {
        return match ( $product_key ) {
            'free'      => __( 'BuddyClients Free', 'buddyclients-free' ),
            'basic'     => __( 'BuddyClients Essentials', 'buddyclients-free' ),
            'premium'   => __( 'BuddyClients Business', 'buddyclients-free' ),
        };
    }

    /**
     * Retrieves the keys of higher products.
     * 
     * @since 1.0.30
     * 
     * @param   string  $product_key    The product key.
     * @return  array   An array of product keys higher than the provided key.
     */
    private static function get_higher( $product_key ) {
        $index = array_search( $product_key, self::$product_keys, true );
        $index = $index ?? 0;
        return array_slice( self::$product_keys, $index + 1 );
    }
      

    /**
     * Formats an array of product names to a string.
     * 
     * @since 1.0.30
     * 
     * @param   array   $names  The array of product names.
     * @return  string  The formatted string of product names.
     */
    private static function format_names( $names ) {
        $names = (array) $names;
        if ( empty( $names ) ) return '';
    
        // Only one name provided
        if ( count( $names ) === 1 ) {
            return $names[0];
        }
    
        // Remove last element
        $last_name = array_pop( $names );

        // Implode and append last name with or
        return implode( ', ', $names ) . ' or ' . $last_name;
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
    public static function build( $boxed = null ) {

        // Get current product
        $product_key = buddyc_get_product();
        $higher_products = self::get_higher( $product_key );

        // No higher products available to upgrade
        if ( empty( $higher_products ) ) {
            return '';
        }

        // Build names
        $names = [];
        foreach ( $higher_products as $higher_product ) {
            $names[] = self::product_name( $higher_product );
        }

        // Build names string
        $names_string = self::format_names( $names );

        // Build link
        $link = sprintf(
            '<p class="buddyc-upgrade-link">%1$s %2$s %3$s</p>',
            buddyc_icon( 'rocket' ),
            __( 'Upgrade to', 'buddyclients-free' ),
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>.',
                esc_url( self::upgrade_url() ),
                esc_html( $names_string )
            )            
        );

        return sprintf(
            '%1$s%2$s%3$s',
            $boxed ? '<div class="buddyc-upgrade-link-container">' : '',
            $link,
            $boxed ? '</div>' : ''
        );
    }

    /**
     * Echoes an upgrade link.
     *
     * @since 1.0.21
     *
     * @ignore
     *
     * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
     *                               Defaults to null.
     */
    public static function echo( $boxed = null ) {
        $allowed_html = [
            'div' => [ 'class' => [] ],
            'p' => [ 'class' => [] ],
            'a' => [ 'href' => [], 'target' => [] ],
            'i' => [ 'class' => [] ],
        ];
        $content = self::build( $boxed );
        echo wp_kses( $content, $allowed_html );
    }

   /**
     * Builds the BuddyClients site url with an optional path.
     *
     * @since 1.0.27
     *
     * @param   string  $path   Optional. The path to append to the url.
     */
    public static function buddyc_url( $path = '' ) {
        return trailingslashit( BUDDYC_URL ) . $path;
    }

    /**
     * Generates a url to the BuddyClients pricing page.
     *
     * @since 1.0.27
     */
    public static function upgrade_url() {
        return self::buddyc_url( 'pricing' );
    }

    /**
     * Generates a URL to the BuddyClients account pagbuddyc_account_linke.
     *
     * @since 1.0.27
     */
    public static function account_url() {
        return self::buddyc_url(' license' );
    }

    /**
     * Generates a url to the enable components admin page.
     *
     * @since 1.0.27
     */
    public static function enable_component_url() {
        return admin_url( 'admin.php?page=buddyc-components-settings' );
    }

    /**
     * Generates a URL to the Freelancer Mode setting.
     *
     * @since 1.0.27
     */
    public static function freelancer_mode_url() {
        return admin_url( 'admin.php?page=buddyc-booking-settings' );
    }

    /**
     * Generates a BuddyClients account link.
     *
     * @since 1.0.30
     */
    public static function account_link() {
        return sprintf(
            '<p class="buddyc-upgrade-link">%1$s %2$s %3$s</p>',
            buddyc_icon( 'gear' ),
            __( 'Manage', 'buddyclients-free' ),
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>.',
                esc_url( self::account_url() ),
                __( 'your BuddyClients subscription', 'buddyclients-free' )
            )            
        );
    }

    /**
     * Generates a link to enable a component.
     *
     * @since 1.0.30
     *
     * @ignore
     *
     * @param   string   $component  The component to be enabled.
     * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
     *                               Defaults to null.
     */
    public static function enable_component_link( $component, $boxed = null ) {

        // Build link
        $link = sprintf(
            '<p class="buddyc-upgrade-link">%1$s %2$s %3$s</p>',
            buddyc_icon( 'toggle_off' ),
            sprintf(
                /* translators: %s: the name of the component */
                __( 'The %s component is disabled.', 'buddyclients-free' ),
                buddyc_component_name( $component )
            ),
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>.',
                esc_url( self::enable_component_url() ),
                __( 'Enable the component', 'buddyclients-free' )
            )            
        );

        return sprintf(
            '%1$s%2$s%3$s',
            $boxed ? '<div class="buddyc-upgrade-link-container">' : '',
            $link,
            $boxed ? '</div>' : ''
        );
    }

    /**
     * Echoes a link to enable a component.
     *
     * @since 1.0.30
     *
     * @param   string   $component  The component to be enabled.
     * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
     *                               Defaults to null.
     */
    public static function echo_enable_component_link( $component, $boxed = null ) {
        $allowed_html = [
            'p' => ['class' => []], // Allow class attribute on <p>
            'i' => ['class' => []], // Allow class attribute on <i> (for icons)
            'a' => ['href' => [], 'class' => []], // Allow href and class attributes on <a>
        ];
        $content = self::enable_component_link( $component, $boxed );
        echo wp_kses( $content, $allowed_html );
    }

    /**
     * Generates a link to disable Freelancer Mode.
     *
     * @since 1.0.30
     *
     * @param   ?bool    $enable     Optional. Whether to include language about enabling Freelancer Mode.
     *                               Defaults to language about disabling Freelancer Mode.
     * @param   ?bool    $boxed      Optional. Whether to style the message in a container.
     *                               Defaults to null.
     */
    public static function freelancer_mode_link( $enable = null, $boxed = null ) {

        // Build link
        $link = sprintf(
            '<p class="buddyc-upgrade-link">%1$s %2$s %3$s</p>',
            buddyc_icon( 'toggle_off' ),
            __( 'Freelancer Mode is enabled.', 'buddyclients-free' ),
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>.',
                esc_url( self::freelancer_mode_url() ),
                __( 'Disable Freelancer Mode', 'buddyclients-free' )
            )            
        );

        return sprintf(
            '%1$s%2$s%3$s',
            $boxed ? '<div class="buddyc-upgrade-link-container">' : '',
            $link,
            $boxed ? '</div>' : ''
        );
    }
}