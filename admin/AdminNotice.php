<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Admin notice.
 * 
 * @since 0.1.0
 */
class AdminNotice {

    /**
     * The notice message.
     * 
     * @var string
     */
    private $message;

    /**
     * The notice color.
     * 
     * @var string
     */
    private $color;

    /**
     * The repair link or links.
     * 
     * @var array
     */
    private $repair_link;

    /**
     * Whether the notice is dismissable.
     * 
     * @var bool
     */
    private $dismissable;
    
    /**
     * The text for the repair link or links.
     * 
     * @var array
     */
    private $repair_link_text;

    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args {
     *     An array of arguments for building the admin notice.
     * 
     *     @type    string  $repair_link        The link to the repair page.
     *     @type    string  $repair_link_text   Optional. The link text.
     *                                          Defaults to 'Repair'.
     *     @type    string  $message            The message to display in the notice.
     *     @type    bool    $dismissable        Optional. Whether the notice should be dismissable.
     *                                          Defaults to false.
     *     @type    string  $color              Optional. The color of the notice.
     *                                          Accepts 'green', 'blue', 'orange', 'red'.
     *                                          Defaults to blue.
     * }
     */
    public function __construct( $args ) {
        $this->message = $args['message'] ?? null;

        // Exit if no message
        if ( ! $this->message ) {
            return;
        }

        // Extract args
        $this->extract_args( $args );

        // Cache key based on the message
        $cache_key = 'buddyc_admin_notice_' . md5( $this->message );

        // Check if the notice has already been shown
        if ( ! get_transient( $cache_key ) ) {
            // Build notice
            $this->define_hooks();

            // Set transient for 1 second
            set_transient( $cache_key, true, 1 );
        }
    }

    /**
     * Extracts args.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args   See constructor.
     */
    private function extract_args( $args ) {
        $this->repair_link = $this->extract_repair_links( $args );
        $this->repair_link_text = isset( $args['repair_link_text'] ) ? (array) $args['repair_link_text'] : ['Repair'];
        $this->dismissable = $args['dismissable'] ?? false;
        $this->color = $args['color'] ?? 'blue';
    }
    
    /**
     * Retrieves repair link or links.
     * 
     * @since 1.0.3
     * 
     * @param   array   $args   See constructor.
     */
    private function extract_repair_links( $args ) {
        // Initialize
        $repair_link = null;
        
        // Check if link is set
        if ( isset( $args['repair_link'] ) ) {
            
            // Normalize to an array
            $repair_links = (array) $args['repair_link'];
            
            // Apply admin_url to each item only if it's not a full URL
            $repair_link = array_map( function( $link ) {
                // Trim any spaces from the link to avoid issues
                $link = trim( $link );
                
                // Check if it's a valid full URL (starts with http:// or https://)
                if ( ! filter_var( $link, FILTER_VALIDATE_URL ) ) {
                    // If it's not a full URL, apply admin_url
                    return admin_url( $link );
                }
                
                // Return the link as-is if it's already a complete URL
                return $link;
            }, $repair_links );
        }
        
        return $repair_link;
    }

    /**
     * Hooks the notice to the admin notices.
     * 
     * @since 0.1.0
     */
    private function define_hooks() {
        add_action('admin_notices', [$this, 'build']);
    }

    /**
     * Builds the notice.
     * 
     * @since 0.1.0
     */
    public function build() {
        // Define the notice class
        $class = $this->notice_class( $this->color );

        // Build the repair link html
        $repair_link = $this->build_repair_link();

        // Define the dismissable class
        $dismissable_class = $this->dismissable ? ' is-dismissible' : '';

        // Build the notice
        $notice = '<div class="notice notice-' . $class . $dismissable_class . '"><p>' . $this->message . ' ' . $repair_link . '</p></div>';

        // Escape and output notice
        $allowed_html = self::allowed_html();
        echo wp_kses( $notice, $allowed_html );
    }

    /**
     * Defines the allowed html.
     * 
     * @since 1.0.21
     */
    private static function allowed_html() {
        return [
            'div' => ['class' => true],
            'p' => [],
            'a' => ['href' => true, 'class' => true, 'target' => []],
        ];
    }

    /**
     * Builds the repair link html.
     * 
     * @since 0.1.0
     */
    private function build_repair_link() {
        $formatted_links = [];
        
        // Get current site's hostname
        $current_site = wp_parse_url( home_url(), PHP_URL_HOST );
        
        // Make sure the repair link exists and we are not on the repair page
        if ( $this->repair_link && ! $this->on_repair_page() ) {
            
            // Loop through repair links
            foreach ( $this->repair_link as $index => $repair_link ) {
                // Ensure there is corresponding text for the link (fallback to 'Repair' if text is missing)
                $repair_link_text = isset( $this->repair_link_text[ $index ] ) ? $this->repair_link_text[ $index ] : 'Repair';
                
                // Parse the repair link to get its hostname
                $parsed_link = wp_parse_url( $repair_link );
                $link_host = isset( $parsed_link['host'] ) ? $parsed_link['host'] : '';
                
                // Check if the link is external
                $is_external = $link_host && $link_host !== $current_site;
                
                // Build the anchor tag
                $formatted_links[] = '<a href="' . esc_url( $repair_link ) . '"' . ($is_external ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' . esc_html( $repair_link_text ) . '</a>';
            }
        }
        
        return implode( ' | ', $formatted_links );
    }

    /**
     * Checks if we're on the repair page.
     * 
     * @since 0.1.0
     */
    private function on_repair_page() {
        // Exit if no repair link
        if ( ! $this->repair_link ) {
            return false;
        }
        $current_url = buddyc_curr_url();
        foreach ( $this->repair_link as $repair_link ) {
            if ( basename( $current_url ) === basename( $repair_link ) ) {
                return true;
            }
        }
    }

    /**
     * Retrieves the notice class by color.
     * 
     * @since 0.1.0
     * 
     * @param   string  $color  Optional. The color for the class.
     *                          Defaults to 'info'.
     */
    private function notice_class( $color = null ) {
        $classes = [
            'red'       => 'error',
            'orange'    => 'warning',
            'blue'      => 'info',
            'green'     => 'success'
        ];
        return $classes[$color] ?? 'info';
    }
}
