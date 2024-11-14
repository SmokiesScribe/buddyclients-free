<?php
namespace BuddyClients\Includes;

use DOMDocument;
use DOMXPath;

/**
 * Generates the popup structure.
 * 
 * Creates a popup where content will be inserted for help links.
 * 
 * @since 0.1.0
 */
class Popup {
    
    /**
     * The single instance of the class.
     * 
     * @var Popup|null
     */
    private static $instance = null;
    
    /**
     * The popup content.
     * 
     * @var string
     */
    public $content;
    
    /**
     * The initial popup visibility.
     * 
     * @var string
     */
    private $visible;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        
        // Initialize variables
        $this->visible = false;
        $this->content = '';
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Returns the single instance of the class.
     * 
     * @return  Popup               The single instance of the class.
     */
    public static function get_instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
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
     * Manually outputs a popup.
     * 
     * @since 1.0.20
     */
    public function output( $content ) {
        $this->content = $content;
        $this->visible = true;
        $this->build();
    }
    
    /**
     * Outputs popup.
     * 
     * @since 0.1.0
     */
    public function build() {
        
        // Define display class
        $display_class = $this->visible ? 'bc-popup-visible' : 'bc-popup-hidden';
    
        // Build popup
        $popup = '';
        $popup .= '<div class="bc-popup ' . $display_class . '" id="bc-popup">';
        $popup .= '<a id="bc-close-btn" href=""><i class="fa-solid fa-x"></i></a>';
        $popup .= '<div id="bc-popup-content">';
        $popup .= $this->content;
        $popup .= '</div>';
        $popup .= '</div>';
        
        echo wp_kses_post( $popup );
        
        // Reset visibility
        $this->visible = false;
    }
     
     /**
      * Formats content for popup.
      * 
      * @since 0.1.0
      * 
      * @param  int     $post_id    The ID of the post.
      * @param  string  $url        The url of the post or page whose content to fetch.
      * @param  string  $raw_content Raw content to be used directly.
      */
    public static function format_content( $post_id = null, $url = null, $raw_content = null ) {
        if ( $raw_content ) {
            return self::content_from_raw( $raw_content );
        }
        
        // Get content from post ID
        if ( $post_id ) {
            return self::content_from_post_id( $post_id );
        
        // Get content from url
        } else if ( $url ) {
            return self::content_from_url( $url );
        }
    }
    
    /**
     * Retrieves and formats raw content for popup.
     * 
     * @since 0.4.0
     * 
     * @param   string     $raw_content  The raw content to be used.
     */
    private static function content_from_raw( $raw_content ) {
        
        // Strip slashes
        $raw_content = stripslashes( $raw_content );
        
        // Convert all links to open in a new tab
        $content = preg_replace('/<a\s+(?:[^>]*?\s+)?href=([\'"])(.*?)\1/', '<a target="_blank" href=$1$2$1', $raw_content);

        // Reduce headings levels
        $content = preg_replace_callback('/<h([1-4])([^>]*)>/', function($matches) {
            return '<h' . ($matches[1] + 2) . $matches[2] . '>';
        }, $content);
        
        // Return content
        return $content;
    }
    
    /**
     * Retrieves and formats content from a post ID.
     * 
     * @since 0.4.0
     * 
     * @param   int     $post_id    The ID of the post whose content to retrieve.
     */
    private static function content_from_post_id( $post_id ) {
        // Get the content
        $title = get_the_title( $post_id );
        $content = get_post_field( 'post_content', $post_id );
    
        // Add the title
        $title = '<h3>' . $title . '</h3>';
    
        // Convert all links to open in a new tab
        $content = preg_replace('/<a\s+(?:[^>]*?\s+)?href=([\'"])(.*?)\1/', '<a target="_blank" href=$1$2$1', $content);
    
        // Reduce headings levels
        $content = preg_replace_callback('/<h([1-4])([^>]*)>/', function($matches) {
            return '<h' . ($matches[1] + 2) . $matches[2] . '>';
        }, $content);
        
        // Return content
        return $title . $content;
    }
    
    /**
     * Retrieves and formats content from a url.
     * 
     * @since 0.4.0
     * 
     * @param   string     $url    The url of the page whose content to retrieve.
     */
    private static function content_from_url( $url ) {
        // Fetch the content from the URL
        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            return ''; // Handle error appropriately
        }

        // Get the body of the response
        $body = wp_remote_retrieve_body( $response );

        // Load content into DOMDocument
        $dom = new DOMDocument();
        @$dom->loadHTML($body); // Suppress warnings caused by malformed HTML

        // Remove <aside> elements (sidebars)
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//aside') as $node) {
            $node->parentNode->removeChild($node);
        }

        // Remove <header> elements
        foreach ($xpath->query('//header') as $node) {
            $node->parentNode->removeChild($node);
        }

        // Remove <footer> elements
        foreach ($xpath->query('//footer') as $node) {
            $node->parentNode->removeChild($node);
        }

        // Remove specific footer widget area by class
        foreach ($xpath->query('//div[contains(@class, "footer-widget-area") and contains(@class, "bb-footer")]') as $node) {
            $node->parentNode->removeChild($node);
        }

        // Extract the cleaned content
        $content = $dom->saveHTML();

        // Optionally, extract the title from the body if needed
        preg_match('/<title>([^<]*)<\/title>/', $content, $matches);
        $title = isset($matches[1]) ? '<h3>' . $matches[1] . '</h3>' : '';

        // Convert all links to open in a new tab
        $content = preg_replace('/<a\s+(?:[^>]*?\s+)?href=([\'"])(.*?)\1/', '<a target="_blank" href=$1$2$1', $content);

        // Reduce headings levels
        $content = preg_replace_callback('/<h([1-4])([^>]*)>/', function($matches) {
            return '<h' . ($matches[1] + 2) . $matches[2] . '>';
        }, $content);
        
        // Return content
        return $title . $content;
    }
     
    /**
      * Outputs popup link.
      * 
      * @since 0.1.0
      * 
      * @param  int     $post_id        The ID of the post from which to retrieve the content.
      * @param  string  $link_text      Optional. The text to display. Defaults to ? icon.
      * @param  string  $url            Optional. The full url of the page to display.
      * @param  string  $raw_content    Optional. Raw content to be used directly.
      */
     public static function link( $post_id = null, $link_text = null, $url = null, $raw_content = null ) {
         
         // Initialize the class
         self::get_instance();

        // Make sure we have a post ID, url, or raw content
        if ( ! $post_id && ! $url && ! $raw_content ) {
            return '';
        }
        
        // Default to icon
        $link_text = $link_text && $link_text !== '' ? $link_text : '<i class="fas fa-question-circle bc-help-icon"></i>';
        
        // Define data
        $data = '';
        $data .= $post_id ? 'data-post-id="' . $post_id . '"' : '';
        $data .= $url ? 'data-url="' . $url . '"' : '';
        $data .= $raw_content ? 'data-raw-content="' . esc_attr( $raw_content ) . '"' : '';
        
        // Build help button with text or icon
        return ' <a class="bc-popup-link" ' . $data . ' href="">' . $link_text . '</a>';
     }
     
    /**
     * Updates the content of the popup.
     *
     * @param string $new_content The new content to be displayed in the popup.
     */
    public function update_content( $new_content ) {
        $this->content = $new_content;
        $this->visible = true;
    }

}
