<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Generates the content for the upgrade admin page.
 * 
 * @since 1.0.27
 * @ignore
 */
class UpgradePage {

    /**
     * The current plugin subscription level.
     * 'free', 'basic', or 'premium'
     * 
     * @var string
     */
    private $product;

    /**
     * Whether the current product is the highest available.
     * 
     * @var bool
     */
    private $is_highest;

     /**
     * The single instance of the class.
     * 
     * @since 1.0.27
     *
     * @var UpgradePage|null
     */
    private static $instance = null;

    /**
     * Retrieves the singleton instance.
     * 
     * @since 1.0.0
     *
     * @return UpgradePage
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prevents cloning.
     * 
     * @since 1.0.25
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, 'Cloning LicenseHandler is not allowed.', esc_html( (string) BUDDYC_PLUGIN_VERSION ) );
    }

    /**
     * Constructor method.
     * 
     * @since 1.0.27
     */
    private function __construct() {
        $this->define_hooks();
        $this->product = $this->get_product();
        $this->is_highest = $this->product === self::highest();
    }

    /**
     * Defines hooks.
     * 
     * @since 1.0.27
     */
    public function define_hooks() {
        add_action( 'buddyc_admin_pages', [ $this, 'modify_admin_pages'], 1, 10 );
        add_action( 'buddyc_nav_tabs', [ $this, 'modify_nav_tabs'], 1, 10 );
    }

    /**
     * Retrieves the product key.
     * 
     * @since 1.0.27
     */
    private function get_product() {
        return buddyc_get_product();
    }

    /**
     * Defines the product keys ordered by level.
     * 
     * @since 1.0.27
     */
    private static function product_keys() {
        return [
            'free',
            'basic',
            'premium'
        ];
    }

    /**
     * Retrieves the highest available subscription level.
     * 
     * @since 1.0.27
     */
    private static function highest() {
        $product_keys = self::product_keys();
        return end( $product_keys );
    }
    

    /**
     * Retrieves all subscription levels above the current product key.
     * 
     * @since 1.0.27
     * 
     * @param   string  $product    The product key.
     */
    private function higher_products( $product ) {
        $levels = self::product_keys();
        $index = array_search( $product, $levels );
    
        return ( $index !== false ) ? array_slice( $levels, $index ) : [];  
    }    

    /**
     * Mofifies the admin pages based on the current version.
     * 
     * @since 1.0.0
     * 
     * @param   array   $pages  The array of pages data to modify.
     */
    public function modify_admin_pages( $pages ) {

        // Exit if already premium
        if ( $this->is_highest ) {
            return $pages;
        }
        
        // Remove license page in free plugin
        if ( buddyc_is_free() ) {
            unset( $pages['license'] );
        }
    
        // Add upgrade page
        $pages['free_upgrade'] = [
            'key' => 'free-upgrade',
            'settings' => false,
            'title' => __('Upgrade', 'buddyclients-lite'),
            'parent_slug' => 'buddyc-dashboard',
            'buddyc_menu_order' => 30,
            'group' => 'settings',
            'callable' => [$this, 'output_content']
        ];
    
        return $pages;
    }
    
    /**
     * Mofifies the admin nav tabs for the free version.
     * 
     * @since 1.0.0
     * 
     * @param   array   $tabs   The array of tab data to modify.
     */
    function modify_nav_tabs( $tabs ) {

        // Exit if already premium
        if ( $this->is_highest ) {
            return $tabs;
        }

        // Remove license page
        unset( $tabs['license'] );
    
        // Add upgrade tab
        $tabs['free_upgrade'] = [__( 'Upgrade', 'buddyclients-lite' ) => ['page'  => 'buddyc-free-upgrade']];
    
        return $tabs;
    }

    /**
     * Defines the product info.
     * 
     * @since 1.0.27
     */
    private static function product_info() {
        return [
            'basic' => [
                'name'          => 'BuddyClients ' . __( 'Essential', 'buddyclients-lite' ),
                'description'   => __( 'Sell services and manage clients\' projects in one place.', 'buddyclients-lite' ),
                'features'      => [
                    __( 'Everything in Free', 'buddyclients-lite' ),
                    __( 'Flexible pricing structures', 'buddyclients-lite' ),
                    __( 'Adjust prices dynamically', 'buddyclients-lite' ),
                    __( 'Filter team by preferences', 'buddyclients-lite' ),
                    __( 'Manage team payments', 'buddyclients-lite' ),
                ],
            ],
            'premium' => [
                'name'          => 'BuddyClients ' . __( 'Business', 'buddyclients-lite' ),
                'description'   => __( 'Grow your service-based business with premium tools.', 'buddyclients-lite' ),
                'features'      => [
                    __( 'Everything in Essential', 'buddyclients-lite' ),
                    __( 'Affiliate program', 'buddyclients-lite' ),
                    __( 'Client testimonials', 'buddyclients-lite' ),
                    __( 'Custom quotes', 'buddyclients-lite' ),
                    __( 'Live search help docs', 'buddyclients-lite' ),
                    __( 'Contact form', 'buddyclients-lite' ),
                    __( 'Legal agreements', 'buddyclients-lite' ),
                    __( 'Team availability', 'buddyclients-lite' ),
                    __( 'Sales team commission', 'buddyclients-lite' ),
                    __( 'Manual and assisted bookings', 'buddyclients-lite' ),
                ],
            ]
        ];
    }
    
    /**
     * Outputs the ugprade admin page content.
     * 
     * @since 1.0.27
     */
    function output_content() {
        if ( $this->is_highest ) return;
        
        // Open container
        $content = '<div class="buddyc-upgrade-info">';

        // Heading
        $content .= $this->build_heading();

        // Product details
        $content .= $this->product_details();
        
        // Close container
        $content .= '</div>';
    
        echo wp_kses_post( $content );
    }

    /**
     * Builds the heading.
     * 
     * @since 1.0.27
     */
    private function build_heading() {
        return sprintf(
            '<h1>%s</h1>',
            sprintf(
                /* translators: %s: "grow your business" in bold */
                __( 'Upgrade BuddyClients to accept payments, manage projects, and %s.', 'buddyclients-lite' ),
                sprintf(
                    '<span class="buddyc-text-bold">%s</span>',
                    __( 'grow your business', 'buddyclients-lite' )
                )
            )
        );
    }

    /**
     * Builds the product details content.
     * 
     * @since 1.0.27
     */
    private function product_details() {
        // Open options container
        $content = '<div class="buddyc-upgrade-options">';

        // Get products higher than current
        $higher_products = self::higher_products( $this->product );

        // Get product info
        $product_info = self::product_info();
        
        // BuddyClients Options
        foreach ( $product_info as $key => $data ) {
            // Add content if higher than current product
            if ( in_array( $key, $higher_products ) ) {
                $content .= $this->single_product( $key, $data );
            }            
        }
        
        // Close options container
        $content .= '</div>';

        return $content;
    }

    /**
     * Builds a single product item.
     * 
     * @since 1.0.27
     * 
     * @param   string  $key    The product key.
     * @param   array   $data   The array of product data.
     */
    private function single_product( $key, $data ) {

        // Open container
        $content = '<div class="buddyc-upgrade-option">';

        // Open inner container
        $content .= '<div class="buddyc-upgrade-option-inner">';

        // Header and description
        $content .= '<div class="buddyc-upgrade-option-content">';
        $content .= sprintf(
            '<h3><span class="dashicons-buddyclients-dark buddyc-icon"></span>%s</h3>',
            $data['name'] ?? ''
        );
        $content .= sprintf(
            '<p>%s</p>',
            $data['description'] ?? ''
        );
        
        // Features List
        $content .= $this->features_list( $data );

        // Close content container
        $content .= '</div>';

        // Buttons
        $content .= $this->buttons( $key, $data );

        // Close inner container
        $content .= '</div>';

        // Close container
        $content .= '</div>';

        return $content;
    }

    /**
     * Builds the features list for a product.
     * 
     * @since 1.0.27
     * 
     * @param   array   $data   The array of product data.
     */
    private function features_list( $data ) {
        if ( ! isset( $data['features'] ) || ! is_array( $data['features'] ) ) {
            return;
        }

        // Open list
        $content = '<ul>';

        // Loop through and add features
        foreach ( $data['features'] as $feature ) {
            $content .= sprintf(
                '<li><li><i class="feature-check bb-icon-check bb-icon-rf"></i>%s</li>',
                $feature
            );
        }

        // Close list
        $content .= '</ul>';

        return $content;
    }

    /**
     * Outputs the buttons for a product.
     * 
     * @since 1.0.27
     * 
     * @param   string  $key    The product key.
     * @param   array   $data   The array of product data.
     */
    private function buttons( $key, $data ) {
        $content = '<div class="buddyc-upgrade-btns">';

        if ( $this->product === $key ) {
            $content .= sprintf(
                '<span class="buddyc-active-label">%s</span>',
                __( 'Active', 'buddyclients-lite' )
            );
        }
        
        $content .= sprintf(
            '<a href="%1$s" class="buddyc-upgrade-btn" target="_blank">%2$s<i class="fa-solid fa-arrow-up-right-from-square"></i></a>',
            esc_url( buddyc_upgrade_url() ),
            __( 'Learn More', 'buddyclients-lite' )
        );
        $content .= '</div>';
        return $content;
    }
}