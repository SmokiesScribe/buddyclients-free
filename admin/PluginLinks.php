<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin page links.
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
     * 
     * @param   array   $links  The array of action links to add.
     */
    public function __construct( $links ) {
        $this->links = $links;
        add_filter( 'plugin_action_links_' . plugin_basename( BUDDYC_PLUGIN_FILE ), [$this, 'filter_action_links'] );
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
        if( $this->links ) {
            // Add links to the beginning
            $links = array_merge( $this->links, $links );
        }
        return $links;
    }
    
}