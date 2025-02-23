<?php
namespace BuddyClients\Components\Service;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\PostQuery as PostQuery;

/**
 * Custom rate type.
 * 
 * Retrieves and formats data for a rate type.
 *
 * @since 0.1.0
 * 
 * @see ServiceComponent
 */
class RateType extends ServiceComponent {
    
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
     * The post name.
     * 
     * @var string
     */
     public $slug;
     
    /**
     * Attach to project or service.
     * 
     * @var string
     */
     public $attach;
     
    /**
     * Minimum number of units required to book.
     * 
     * @var int
     */
     public $minimum;
     
    /**
     * Singular unit.
     * 
     * @var string
     */
     public $singular;
     
    /**
     * Plural unit.
     * 
     * @var string
     */
     public $plural;
     
    /**
     * Form description.
     * 
     * @var string
     */
     public $form_description;
     
    /**
     * Service IDs.
     * 
     * An array of service IDs that use the rate type.
     * 
     * @var array
     */
     public $service_ids;
    
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param int $post_id
     */
    public function __construct( $post_id ) {
        
        // Exit if it's a flat rate
        if ($post_id !== 'flat') {
            // Construct ServiceComponent
            parent::__construct( $post_id );
        }
        
        // Build unit label
        $this->unit_label( $post_id );
    }
    
    /**
     * Builds unit label.
     * 
     * @since 0.1.0
     */
    private function unit_label( $post_id ) {
        $this->unit_label = ($post_id === 'flat') 
            ? __('Flat', 'buddyclients') 
            : sprintf(
                /* translators: %s: the singular unit name (e.g. word) */
                __('Per %s', 'buddyclients'),
                strtolower( $this->singular )
            );
    }
}