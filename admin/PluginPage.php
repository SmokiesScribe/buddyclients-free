<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin page.
 */
class PluginPage extends PageManager {
    
    /**
     * Page key.
     * 
     * @var string
     * @see PageManager
     */
    public $page_key;
    
    /**
     * Post ID.
     * 
     * @var int|null
     */
    public $post_id;
    
    /**
     * The settings key.
     * 
     * @var string
     */
    public $settings_key;
    
    /**
     * Post title.
     * 
     * @var string
     */
    public $post_title;
    
    /**
     * Post content.
     * 
     * @var string
     */
    public $post_content;
    
    /**
     * Post status.
     * 
     * @var string Defaults to 'publish'.
     */
    public $post_status;
    
    /**
     * Post content.
     * 
     * @var string Defaults to 'page'.
     */
    public $post_type;
    
    /**
     * Edit URL.
     * 
     * @var string
     */
    public $edit_post_url;
    
    /**
     * Page permalink.
     * 
     * @var string
     */
    public $permalink;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   string  $page_key   The page key from the PageManager.
     */
    public function __construct( $page_key ) {
        $this->page_key = $page_key;
    }
    
    /**
     * Get plugin page.
     * 
     * @since 0.1.0
     * 
     * @param   string      $key    The page key.
     * @return  int|bool    The page ID on success, false on failure.
     */
    public static function get_page( $key ) {
        // Get the page ID from the setting
        $curr_setting = buddyc_get_setting( 'pages', $key );
        
        // Check whether the post is published
        if ( get_post_status( $curr_setting ) !== 'publish' ) {
            return false;
        } else {
            return $curr_setting;
        }
    }
    
    /**
     * Creates plugin page.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args {
     *     An array of args to create the page.
     * 
     *     @type    ?int    $post_id        Optional. The ID of the existing post.
     *     @type    string  $settings_key   Optional. The settings key.
     *                                      Defaults to 'pages'.
     *     @type    string  $post_title     The title of the new page.
     *     @type    string  $post_content   The content for the new page.
     *     @type    string  $post_status    Optional. The status of the new page.
     *                                      Defaults to 'publish'.
     *     @type    string  $post_type      Optional. The post type for the new page.
     *                                      Defaults to 'page'.
     * }
     */
    public function create_page( $args ) {
        
        // Extract args
        $this->post_id          = $args['post_id'] ?? null;
        $this->settings_key     = $args['settings_key'] ?? 'pages';
        $this->post_title       = $args['post_title'] ?? '';
        $this->post_content     = $args['post_content'] ?? '';
        $this->post_status      = $args['post_status'] ?? 'publish';
        $this->post_type        = $args['post_type'] ?? 'page';
        
        // Make sure a title was provided
        if ( ! $this->post_title ) {
            return;
        }
        
        // Check if page exists
        if ( self::get_page( $this->page_key ) ) {
            return;
        }
        
        // Define new page args
        $new_page_args = array(
            'post_title'    => $this->post_title,
            'post_content'  => $this->post_content,
            'post_status'   => $this->post_status,
            'post_type'     => $this->post_type,
        );
    
        // Insert the new page into the database
        $this->post_id = wp_insert_post( $new_page_args );
    
        // Check if the page was created successfully
        if ( $this->post_id ) {
            
            // Update settings
            $field_key = $this->post_status === 'publish' ? $this->page_key : $this->page_key . '_draft';
            buddyc_update_setting( $this->settings_key, $field_key, $this->post_id );
            
            // Assign vars
            $this->edit_post_url = admin_url( 'post.php?post=' . $this->post_id . '&action=edit' );
            $this->permalink = get_the_permalink( $this->post_id );
            
            // Add setting key to post meta
            update_post_meta( $this->post_id, 'buddyc_page_key', $this->page_key );
            
        }
        return $this;
    }
}