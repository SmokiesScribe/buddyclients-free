<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Website footer alert.
 * 
 * Generates a sticky alert in the website footer.
 * 
 * @since 0.1.0
 */
class Alert {
    
    /**
     * Current alert priority.
     * 
     * @var int
     */
    private static $priority = null;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   string  $content    The content to display.
     * @param   ?int    $priority   Optional. The priority of the alert.
     */
    public function __construct( $content, $priority = null ) {
        if ( is_admin() ) return;
        if ( empty( $content ) ) return;
            
        // Define var
        $this->content = $content;
        
        // Check the priority
        if ( ! $this->greater_priority( $priority ) ) {
            return;
        }
        
        // Set priority
        self::$priority = $priority;
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Checks the priority.
     * 
     * @since 0.1.0
     */
    private function greater_priority( $new_priority ) {
        
        // Check if the new priority is greater
        if ( $new_priority > self::$priority ) {
            
            // Set the static priority to the new int
            self::$priority = $new_priority;
            return true;
            
        } else {
            return false;
        }
    }
    
    /**
     * Define hooks and filters.
     *
     * @since 1.0.0
     */
    private function define_hooks() {
        add_action('wp_footer', [$this, 'build']);
    }
    
    /**
     * Builds the alert.
     * 
     * @since 0.1.0
     */
    public function build() {
        if ( $this->content && ! self::at_link( $this->content ) ) {
            echo '<div class="alert-container"><div class="custom-alert-bar">' . wp_kses_post( $this->content ) . '</div></div>';
        }
    }
    
    /**
     * Checks if we are currently on one of the linked pages.
     * 
     * @since 0.1.0
     */
    private static function at_link( $content ) {
        // Get links
        $links = self::extract_links( $content );
        
        // No links
        if ( ! $links ) {
            return false;
        }
        
        // Get current url
        $current_url = buddyc_curr_url();

         // Loop through the links
         foreach ( $links as $link ) {
             $link = trailingslashit( $link );
            if ( $current_url === $link ) {
                return true;
            }
         }
         return false;
    }
    
    /**
     * Extracts links from the content.
     * 
     * @since 0.1.0
     */
    private static function extract_links( $content ) {
        // Define a regular expression pattern to match links
        $pattern = '/<a\s+(?:[^>]*?\s+)?href="([^"]*)"/i';
    
        // Perform the regular expression match
        preg_match_all($pattern, $content, $matches);
    
        // Extract the matched links from the regex matches
        $links = $matches[1];
    
        return $links;
    }
    
}