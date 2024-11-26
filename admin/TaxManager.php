<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles plugin taxonomies.
 *
 * @since 0.1.0
 */
class TaxManager {
    
    /**
     * Instance of the class.
     *
     * @var TaxManager
     * @since 0.1.0
     */
    protected static $instance = null;
    
    /**
     * Taxonomies to register.
     * 
     * @var array
     */
    private static $taxonomies;
        
    /**
     * BuddyClients TaxManager Instance.
     *
     * @since 0.1.0
     * @static
     * @return TaxManager instance
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Define taxonomies.
     * 
     * @since 0.1.0
     */
    private static function taxonomies() {
        $taxonomies = [
            'brief_type' => [
                'post_type'             => 'buddyc_brief',
                'singular_name'         => __( 'Brief Type', 'buddyclients-free' ),
                'plural_name'           => __( 'Brief Types', 'buddyclients-free' ),
                'show_in_menu'          => null,
                'hierarchical'          => true,
                'public'                => true,
                'show_in_rest'          => true,
            ],
        ];
        
        /**
         * Filters the plugin taxonomies.
         *
         * @since 0.1.0
         *
         * @param array $taxonomies
         *     An array of taxonomy args keyed by slug. {
         * 
         *     @type string $singular_name The singular name of the taxonomy.
         *     @type string $post_type The slug of the post type.
         *     @type string $plural_name The plural name of the post type.
         *     @type bool   $show_in_menu Whether to display the post type in the admin menu.
         *     @type bool   $public Whether the post type is intended to be publicly queryable.
         *     @type bool   $hierarchical Whether the taxonomy is hierarchical (e.g., categories).
         *     @type bool   $show_in_rest Whether the post type is available in the REST API.
         * }
         */
        $taxonomies = apply_filters( 'buddyc_taxonomies', $taxonomies );
        
        return $taxonomies;
    }
    
    /**
     * Define taxonomies.
     * 
     * @since 0.1.0
     */
    public static function run() {
        foreach ( self::taxonomies() as $slug => $args ) {
            new Taxonomy( $slug, $args );
        }
    }
}
