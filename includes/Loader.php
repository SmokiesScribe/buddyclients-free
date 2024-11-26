<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Registers all actions, filters, and shortcodes.
 *
 * @since 0.1.0
 * 
 * @deprecated 0.2.0 This class is deprecated and will be removed in future versions.
 * Add hooks and filters directly instead.
 */
class Loader {

    /**
     * The array of actions registered with WordPress.
     * 
     * @var array
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @var array
     */
    protected $filters;

    /**
     * The array of shortcodes registered with WordPress.
     *
     * @var array
     */
    protected $shortcodes;

    /**
     * Initialize the collections used to maintain the actions, filters, and shortcodes.
     *
     * @since 0.1.0
     */
    public function __construct() {
        $this->actions = array();
        $this->filters = array();
        $this->shortcodes = array();
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @since 0.1.0
     * 
     * @param    string               $hook             The name of the WordPress action that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the action is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since 0.1.0
     * 
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
     */
    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add a new shortcode to the collection to be registered with WordPress.
     *
     * @since 0.1.0
     * 
     * @param    string               $tag              The shortcode tag to be registered.
     * @param    object               $component        A reference to the instance of the object on which the shortcode is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     */
    public function add_shortcode( $tag, $component, $callback ) {
        $this->shortcodes[] = array(
            'tag'       => $tag,
            'component' => $component,
            'callback'  => $callback,
        );
    }

    /**
     * A utility function that is used to register the actions, filters, and shortcodes into a single collection.
     *
     * @since 0.1.0
     * 
     * @access   private
     * @param    array                $hooks            The collection of hooks that is being registered (actions, filters, or shortcodes).
     * @param    string               $hook             The name of the WordPress hook that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the hook is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         The priority at which the function should be fired.
     * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
     * @return   array                                  The collection of hooks registered with WordPress.
     */
    private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );
        return $hooks;
    }

    /**
     * Register the filters, actions, and shortcodes with WordPress.
     *
     * @since 0.1.0
     */
    public function run() {
        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        foreach ( $this->shortcodes as $shortcode ) {
            add_shortcode( $shortcode['tag'], array( $shortcode['component'], $shortcode['callback'] ) );
        }
    }
}