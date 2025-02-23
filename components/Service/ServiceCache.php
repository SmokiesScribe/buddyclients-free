<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Service\{
    Service,
    Adjustment,
    AdjustmentOption,
    FileUpload,
    RateType,
    Role,
    ServiceType
};

/**
 * Handles caching and retrieval for service components.
 *
 * @since 1.0.25
 */
abstract class ServiceCache {

    /**
     * Defines the keyed post types.
     * 
     * @since 1.0.25
     */
    private static function post_types() {
        return [
            'service'           => 'buddyc_service',
            'adjustment'        => 'buddyc_adjustment',
            'adjustment_option' => 'buddyc_adjustment_option',
            'file_upload'       => 'buddyc_file_upload',
            'rate_type'         => 'buddyc_rate_type',
            'role'              => 'buddyc_role',
            'service_type'      => 'buddyc_service_type',
        ];
    }

    /**
     * Defines the class names and keys.
     * 
     * @since 1.0.25
     */
    private static function class_names() {
        return [
            'service'           => Service::class,
            'adjustment'        => Adjustment::class,
            'adjustment_option' => AdjustmentOption::class,
            'file_upload'       => FileUpload::class,
            'rate_type'         => RateType::class,
            'role'              => Role::class,
            'service_type'      => ServiceType::class
        ];
    }

    /**
     * Retrieves the class name by key.
     * 
     * @since 1.0.25
     * 
     * @param   string  $class_key    The key for the class.
     */
    private static function get_class( $class_key ) {
        $classes = self::class_names();
        return $classes[$class_key] ?? null;
    }

    /**
     * Builds the cache key.
     * 
     * @since 1.0.25
     * 
     * @param   int $post_id    The ID of the Service post.
     */
    private static function cache_key( $class_key, $post_id ) {
        return 'buddyc_service_post_' . $class_key . '_' . $post_id;
    }

    /**
     * Unsets an instance from the static cache.
     * 
     * @since 1.0.25
     *
     * @param int $post_id The ID of the service post to remove.
     */
    public static function clear_instance( $class_key, $post_id ) {
        delete_transient( self::cache_key( $class_key, $post_id ) );
    }

    /**
     * Retrieves the cached object for the post ID.
     * Creates a new instance if no cached object exists.
     * 
     * @since 1.0.25
     * 
     * @param int $post_id The ID of the service post.
     */
    public static function get_instance( $class_key, $post_id ) {
        // Check for cached instance
        $cache_key = self::cache_key( $class_key, $post_id );
        $cached_instance = get_transient( self::cache_key( $class_key, $post_id ) );

        if ( $cached_instance ) {
            // Return the cached instance from the transient
            return $cached_instance;
        }

        // Otherwise call constructor
        $class_name = self::get_class( $class_key );
        $new_instance = new $class_name( $post_id );

        // Cache the instance in a transient for future requests
        set_transient( self::cache_key( $class_key, $post_id ), $new_instance, HOUR_IN_SECONDS );

        return $new_instance;
    }

    /**
     * Clears the cache for a post.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The post type.
     * @param   int     $post_id    The ID of the post.
     */
    public static function clear_cache_on_update( $post_type, $post_id ) {
        $class_key = self::class_key_from_post_type( $post_type );
        self::clear_instance( $class_key, $post_id );
    }

    /**
     * Builds the class key from the post type.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The post type.
     */
    private static function class_key_from_post_type( $post_type ) {
        return str_replace( 'buddyc_', '', $post_type );
    }
}