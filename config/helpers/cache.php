<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Config\Cache;

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
function buddyc_cache_get( $key, $group = 'default', $type = 'wp_cache' ) {
    return Cache::get( $key, $group, $type );
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
function buddyc_cache_set( $key, $value, $expires = HOUR_IN_SECONDS, $group = 'default', $type = 'wp_cache' ) {
    return buddyc_cache_set( $key, $value, $expires, $group, $type );
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
function buddyc_cache_delete( $key, $group = 'default', $type = 'wp_cache' ) {
    return buddyc_cache_delete( $key, $group, $type );
}

/**
 * Clears all cached values belonging to this plugin (transients, options, and wp_cache).
 * 
 * @since 1.0.27
 */
function buddyc_cache_clear_all() {
    return Cache::clear_all();
}