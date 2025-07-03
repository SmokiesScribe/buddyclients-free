<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Admin\MetaRouter;

/**
 * Manages meta fields for all post types.
 *
 * @since 0.1.0
 */
class MetaManager {
    
    /**
     * Post type.
     * 
     * The slug of the post type.
     * 
     * @var string
     */
    public $post_type;
    
    /**
     * The array of meta fields data for the post type.
     * 
     * @var array
     */
    public $meta;

    /**
     * An associative array of meta names and types.
     * Used when saving meta values.
     * 
     * @var array
     */
    public $meta_types;

    /**
     * Stores instances of MetaManager per post type.
     * 
     * @var array
     */
    private static $instances = [];

    /**
     * Private constructor to enforce singleton.
     * 
     * @param   string  $post_type  The post type slug.
     */
    private function __construct( $post_type ) {
        $this->post_type = $post_type;
        $this->get_meta( $post_type );
    }

    /**
     * Retrieves the single instance of MetaManager for a given post type.
     * 
     * @param   string  $post_type  The post type slug.
     * @return  MetaManager  The singleton instance.
     */
    public static function get_instance( $post_type ) {
        if ( ! isset( self::$instances[ $post_type ] ) ) {
            self::$instances[ $post_type ] = new self( $post_type );
        }
        return self::$instances[ $post_type ];
    }

    /**
     * Prevents cloning.
     * 
     * @since 1.0.25
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning this class is not allowed.', 'buddyclients-lite' ), esc_html( (string) BUDDYC_PLUGIN_VERSION ) );
    }

    /**
     * Prevents unserialization.
     * 
     * @since 1.0.25
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is not allowed.', 'buddyclients-lite' ), esc_html( (string) BUDDYC_PLUGIN_VERSION ) );
    }

    /**
     * Generates an array of callables that define meta info.
     * 
     * @since 0.3.4
     */
    private static function meta_callbacks() {
        
        // Get class methods
        $methods = get_class_methods( static::class );
        
        // Convert method names to callables
        $callables = array_map( function( $method ) {
            return [static::class, $method];
        }, $methods );
        
        /**
         * Filters the callbacks defining meta fields.
         *
         * @since 0.3.4
         *
         * @param array  $callables   An array of callables defining meta fields.
         */
         $callables = apply_filters( 'buddyc_meta_methods', $callables );
         
         // Return modified methods array
         return $callables;
    }

    /**
     * Builds the meta arrays.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The slug of the post type.
     */
    private function get_meta( $post_type ) {

        // Check cache
        $cache_key = $this->cache_key( $post_type );
        $cached_meta = get_option( $cache_key );

        // Check if cached data is valid
        if ( $this->validate_cache( $cached_meta ) ) {
            // Return cached
            $this->meta = $cached_meta['meta'];
            $this->meta_types = $cached_meta['meta_types'];
            return;
        }

        // Update cache if necessary
        $data = $this->update_cache( $post_type );
        $this->meta = $data['meta'] ?? [];
        $this->meta_types = $data['meta_types'] ?? [];
    }

    /**
     * Validates the cached data.
     * 
     * @since 1.0.25
     * 
     * @param   array   $cached_meta    The array of cached data to validate.
     * 
     * @return  bool    True if valid, false if not.
     */
    private function validate_cache( $cached_meta ) {
        // Empty value
        if ( empty( $cached_meta ) ) {
            return false;
        }

        // Meta or meta types not set
        if ( ! isset( $cached_meta['meta'] ) || ! isset( $cached_meta['meta_types'] ) ) {
            return false;
        }

        // Meta or meta types empty
        if ( empty( $cached_meta['meta'] ) || empty( $cached_meta['meta_types'] ) ) {
            return false;
        }

        // Meta or meta types not array
        if ( ! is_array( $cached_meta['meta'] ) || ! is_array( $cached_meta['meta_types'] ) ) {
            return false;
        }
        
        // Five by five
        return true;

    }

    /**
     * Updates the cached meta.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The slug of the post type.
     */
    private function update_cache( $post_type ) {
        // Get versions
        $version_cache = buddyc_version_cache();

        // Build data
        $meta = $this->post_type_meta( $post_type );
        $meta_types = self::build_meta_types( $post_type, $meta );

        // Build array
        $option_data = [
            'meta'          => $meta,
            'meta_types'    => $meta_types
        ];

        $cache_key = $this->cache_key( $post_type, $version_cache->curr_version );

        // Update cache
        update_option( $this->cache_key( $post_type, $version_cache->curr_version ), $option_data );

        // Delete previous cache
        delete_option( $this->cache_key( $post_type, $version_cache->prev_version ) );

        // Return data
        return $option_data;
    }

    /**
     * Builds the cache key for the option where the meta arrays are stored.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The post type slug.
     * @param   string  $version    Optional. The plugin version.
     *                              Defaults to the current version.
     */
    private function cache_key( $post_type, $version = null ) {
        $version = $version ?? BUDDYC_PLUGIN_VERSION;
        $formatted_version = str_replace( '.', '_', $version );
        return 'buddyc_meta_cache_' . $post_type . '_' . $formatted_version;
    }
    
    /**
     * Retrieves meta array by post type.
     * 
     * @since 0.1.0
     * @updated 0.3.4
     * 
     * @param   string  $post_type  The slug of the post type.
     */
    public function post_type_meta( $post_type ) {
        return MetaRouter::get_meta( $post_type );
    }

    /**
     * Generates an associative array of meta names and types.
     * Used when saving meta values.
     * 
     * @since 1.0.25
     * 
     * @param   string  $post_type  The slug of the post type.
     * @param   array   $meta       The full array of meta data.
     */
    private static function build_meta_types( $post_type, $meta ) {
        // Initialize
        $meta_types = [];

        // Make sure meta exists
        if ( ! empty( $meta ) ) {
            // Loop through post type meta
            foreach ( $meta as $category => $category_data ) {
                // Loop through tables
                foreach ( $category_data['tables'] as $table => $table_data ) {
                    // Loop through meta items
                    foreach ( $table_data['meta'] as $meta_key => $field_data ) {
                        // Add meta key and type to array
                        $meta_types[$meta_key] = $field_data['type'] ?? '';
                    }
                }
            }
        }
        return $meta_types;
    }
}