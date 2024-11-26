<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Single service type.
 * 
 * Retrieves data for a service type.
 *
 * @since 0.1.0
 * 
 * @see ServiceComponent
 */
class ServiceType extends ServiceComponent {
    
    /**
     * The post ID.
     * 
     * @var int
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
     * The post name.
     * 
     * @var string
     */
     public $slug;
     
    /**
     * Form field type.
     * 
     * The type of field to display on the booking form.
     * 
     * @var string
     */
     public $form_field_type;
     
    /**
     * Whether the service type is hidden on the booking form.
     * 
     * @var bool
     */
     public $hide;
     
    /**
     * The ID of the help post.
     * 
     * @var int
     */
     public $help_post_id;
    
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
    }
}