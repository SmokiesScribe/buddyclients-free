<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\PostQuery as PostQuery;

/**
 * Team member role.
 * 
 * Retrieves and formats data for a single team member role.
 *
 * @since 0.1.0
 * 
 * @see ServiceComponent
 */
class Role extends ServiceComponent {
    
    /**
     * The post ID or 'flat'.
     * 
     * @var int|string
     */
     public $ID;
     
    /**
     * The post object.
     * 
     * Null if no post exists for ID.
     * 
     * @var object|null
     */
     private $post;
     
    /**
     * The post title.
     * 
     * @var string
     */
     public $title;
     
    /**
     * The singular label.
     * 
     * @var string
     */
     public $singular;
     
    /**
     * The plural label.
     * 
     * @var string
     */
     public $plural;
     
    /**
     * The description for the booking form.
     * 
     * @var string
     */
     public $form_description;
     
    /**
     * The post name.
     * 
     * @var string
     */
     public $slug;
    
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param int $post_id
     */
    public function __construct( $post_id ) {
        
        // Construct ServiceComponent
        parent::__construct( $post_id );
        
        // Define labels
        $this->define_labels();
    }
    
    /**
     * Defines the role labels.
     * 
     * @since 0.1.0
     */
    private function define_labels() {
        // Default to title
        $this->singular = $this->singular ?? $this->title;
        // Default to title plus 's'
        $this->plural = $this->plural ?? $this->title . 's';
    }
}