<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Adds plugin links to the plugin page.
 * 
 * @since 0.2.1
 */
class PluginLinks {
    
    /**
     * The html-formatted links to add.
     * 
     * @var array
     */
    public $links;
    
    /**
     * Constructor method.
     * 
     * @since 0.2.1
     */
    public function __construct() {

        // Build the array of links
        $this->links = $this->build_links();

        // Hook into the filter
        add_filter( 'plugin_action_links_' . plugin_basename( BUDDYC_PLUGIN_FILE ), [$this, 'filter_action_links'] );
    }

    /**
     * Retrieves the license object.
     * 
     * @since 1.0.25
     */
    private function get_license() {
        if ( function_exists( 'buddyc_get_license' ) ) {
            return buddyc_get_license();
        }
    }

    /**
     * Defines the data to build the regular links.
     * 
     * @since 1.0.25
     */
    private function link_data() {
        $link_data = [
            'settings'  => [
                'url'       => 'admin.php?page=buddyc-general-settings',
                'text'      => __( 'Settings', 'buddyclients-free' ),
                'target'    => null
            ],
            'help'  => [
                'url'       => trailingslashit( BUDDYC_URL ) . 'help',
                'text'      => __( 'User Guides', 'buddyclients-free' ),
                'target'    => '_blank'
            ],
            'license'  => [
                'url'       => trailingslashit( BUDDYC_URL ) . 'license',
                'text'      => __( 'Account', 'buddyclients-free' ),
                'target'    => '_blank'
            ]
        ];

        // Add upgrade link and return
        return $this->add_upgrade_link( $link_data );
    }

    /**
     * Adds the upgrade link to the array of link data.
     * 
     * @since 1.0.25
     */
    private function add_upgrade_link( $link_data ) {

        // Get license
        $license = $this->get_license();

        // Default to free
        $curr_product = $license?->product ?? 'buddyc_free';

        // Define upgrade links
        $upgrade_links = [
            'buddyc_basic'  => 'license',
            'buddyc_free'   => 'pricing'
        ];
        
        // Loop through upgrade link options
        foreach ( $upgrade_links as $product => $slug ) {
            // Check if the product matches the license
            if ( strpos( $curr_product, $product ) !== false ) {
                // Add to array
                $link_data['upgrade'] = [
                    'url'       => trailingslashit( BUDDYC_URL ) . $slug,
                    'text'      => __( 'Upgrade', 'buddyclients-free' ),
                    'target'    => '_blank'
                ];
            }
        }
        return $link_data;
    }

    /**
     * Builds the array of links.
     * 
     * @since 1.0.25
     * 
     * @return   array  The array of action links to add.
     */
    private function build_links() {

        // Get data to build links
        $link_data = $this->link_data();

        // Build the html links from the array
        $links = self::build_links_from_data( $link_data );

        return $links;
    }

    /**
     * Builds the html formatted links from an array of data.
     * 
     * @since 1.0.25
     * 
     * @param   array   $link_data
     *     The array of link data.
     * 
     *     @type    string  $url    The url for the link.
     *     @type    string  $text   The text for the link.
     *     @type    string  $target The target attribute.
     */
    private static function build_links_from_data( $link_data ) {
        $links = [];
        foreach ( $link_data as $key => $data ) {
            $target = $data['target'] ?? '';
            $links[$key] = sprintf(
                '<a href="%1$s" target="%2$s">%3$s</a>',
                esc_url( $data['url'] ),
                esc_attr( $target ),
                esc_html( $data['text'])
            );
        }
        return $links;
    }
    
    /**
     * Adds the action links.
     * 
     * @since 0.2.1
     * 
     * @param   array   $links  The original links to filter.
     * @return  array   The new array of links.
     */
    public function filter_action_links( $links ) {
        // Make sure links exist
        if( ! empty( $this->links ) ) {
            // Add links to the beginning
            $links = array_merge( $this->links, $links );
        }
        return $links;
    }
    
}