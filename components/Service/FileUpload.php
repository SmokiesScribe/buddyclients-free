<?php
namespace BuddyClients\Components\Service;

use BuddyClients\Includes\PostQuery as PostQuery;

/**
 * File upload type.
 * 
 * Retrieves and formats data from an upload type post.
 *
 * @since 0.1.0
 * 
 * @see ServiceComponent
 */
class FileUpload extends ServiceComponent {
    
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
     * Accepted file types.
     * 
     * @var array
     */
     public $file_types;
     
    /**
     * Whether multiple files are allowed.
     * 
     * @var bool
     */
     public $multiple_files;
     
    /**
     * The description to display.
     * 
     * @var string
     */
     public $form_description;
     
    /**
     * The ID of the help post.
     * 
     * @var int
     */
     public $help_post_id;
     
    /**
     * The upload field label.
     * 
     * @var string
     */
     public $field_label;
     
    /**
     * Service IDs.
     * 
     * An array of service IDs that use the file upload type.
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
        
        // Construct ServiceComponent
        parent::__construct( $post_id );
        
        // Get data
        $this->build_label()->format_file_types();
    }
    
    /**
     * Builds the upload field label.
     * 
     * @since 0.1.0
     */
    private function build_label() {

        // Check if multiple files are allowed
        $item = $this->multiple_files == 'true' ? $this->plural : $this->singular;
        
        // Build label
        $this->field_label = sprintf(
            /* translators: %s: the plural or singular file type name (e.g. File or Files) */
            __( 'Upload Your %s', 'buddyclients-free' ),
            esc_html( $item )
        );
        
        // Return object
        return $this;
    }
    
    /**
     * Formats file types.
     * 
     * @since 0.1.0
     */
    private function format_file_types() {
        $this->file_types = implode(',', $this->file_types);
        return $this;
    }
}