<?php
namespace BuddyClients\Config;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles caching and retrieval for service components.
 *
 * @since 1.0.27
 */
class Cache {

    /**
     * Cache prefix to prevent conflicts.
     *
     * @var string
     */
    private static $prefix = '_buddyc_cache_';

    /**
     * Retrieves a cached value from the preferred caching method.
     * 
     * @since 1.0.27
     *
     * @param string $key   The cache key.
     * @param string $group Optional. Cache group for wp_cache. Default is 'default'.
     * @param string $type  Optional. Cache type: 'wp_cache', 'transient', or 'option'. Default is 'wp_cache'.
     *
     * @return mixed Cached value or false if not found.
     */
    public static function get( $key, $group = 'default', $type = 'wp_cache' ) {
        $key = self::$prefix . $key;

        switch ( $type ) {
            case 'wp_cache':
                return wp_cache_get( $key, $group );
            case 'transient':
                return get_transient( $key );
            case 'option':
                return get_option( $key, false );
            default:
                return false;
        }
    }

    /**
     * Stores a value in the cache.
     * 
     * @since 1.0.27
     *
     * @param string $key       The cache key.
     * @param mixed  $value     The value to store.
     * @param int    $expires   Expiration time in seconds. Default is 1 hour.
     * @param string $group     Optional. Cache group for wp_cache.
     * @param string $type      Optional. Cache type: 'wp_cache', 'transient', or 'option'. Default is 'wp_cache'.
     *
     * @return bool True on success, false on failure.
     */
    public static function set( $key, $value, $expires = HOUR_IN_SECONDS, $group = 'default', $type = 'wp_cache' ) {
        $key = self::$prefix . $key;

        switch ( $type ) {
            case 'wp_cache':
                return wp_cache_set( $key, $value, $group, $expires );
            case 'transient':
                return set_transient( $key, $value, $expires );
            case 'option':
                return update_option( $key, $value, false );
            default:
                return false;
        }
    }

    /**
     * Deletes a cached value.
     * 
     * @since 1.0.27
     *
     * @param string $key   The cache key.
     * @param string $group Optional. Cache group for wp_cache.
     * @param string $type  Optional. Cache type: 'wp_cache', 'transient', or 'option'. Default is 'wp_cache'.
     *
     * @return bool True on success, false on failure.
     */
    public static function delete( $key, $group = 'default', $type = 'wp_cache' ) {
        $key = self::$prefix . $key;

        switch ( $type ) {
            case 'wp_cache':
                return wp_cache_delete( $key, $group );
            case 'transient':
                return delete_transient( $key );
            case 'option':
                return delete_option( $key );
            default:
                return false;
        }
    }

    /**
     * Clears all cache in a specific group (wp_cache only).
     * 
     * @since 1.0.27
     *
     * @param string $group Cache group to clear.
     */
    public static function flush( $group = 'default' ) {
        wp_cache_flush();
    }

    /**
     * Deletes all cached transients.
     * 
     * @since 1.0.27
     *
     * @param string $group Cache group to clear.
     */
    public static function clear_transients( $group = 'default' ) {
        $transients = $wpdb->get_col( 
            $wpdb->prepare( 
                "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_' . self::$prefix . '%',
                '_transient_timeout_' . self::$prefix . '%'
            ) 
        );

        foreach ( $transients as $transient ) {
            delete_transient( str_replace( '_transient_', '', $transient ) );
        }
    }

    /**
     * Deletes all cached options.
     * 
     * @since 1.0.27
     *
     * @param string $group Cache group to clear.
     */
    public static function clear_options( $group = 'default' ) {
        $options = $wpdb->get_col( 
            $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", self::$prefix . '%' ) 
        );

        foreach ( $options as $option ) {
            delete_option( $option );
        }
    }

    /**
     * Clears all cached values belonging to this plugin (transients, options, and wp_cache).
     * 
     * @since 1.0.27
     */
    public static function clear_all() {
        global $wpdb;

        // Flush object cache
        self::flush();

        // Delete only BuddyClients-related transients
        self::clear_transients();

        // Delete only BuddyClients-related options
        self::clear_options();
    }
}