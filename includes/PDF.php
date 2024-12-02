<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\FileHandler;
use BuddyClients\Includes\ObjectHandler;

use GriffinVendor\TCPDF;

use DOMDocument;

/**
 * Handles the generation and retrieval of PDFs.
 * 
 * Uses TCPDF.
 * 
 * @since 0.4.0
 */
class PDF {
    
    /**
     * ObjectHandler instance.
     *
     * @var ObjectHandler|null
     */
    private static $object_handler = null;
    
    /**
     * The PDF URL.
     * 
     * @var string
     */
    public $file_url;
    
    /**
     * The full file path to the PDF.
     * 
     * @var string
     */
    public $file_path;
    
    /**
     * The ID of the user to whom the PDF belongs.
     * Defaults to 0.
     * 
     * @var int
     */
    public $user_id = 0;
    
    /**
     * The file name.
     * 
     * @var string
     */
    public $file_name;
    
    /**
     * The primary content of the document.
     * 
     * @var string
     */
    public $content;
    
    /**
     * The title of the document.
     * 
     * @var string
     */
    public $title;
    
    /**
     * Strings to append to the document content.
     * 
     * @var array
     */
    public $items;
    
    /**
     * The path to the signature image.
     * 
     * @var string
     */
    public $image_path;
    
    /**
     * Initializes ObjectHandler.
     * 
     * @since 0.4.0
     */
    private static function init_object_handler() {
        if ( ! self::$object_handler ) {
            self::$object_handler = new ObjectHandler( __CLASS__ );
        }
    }
    
    /**
     * Constructor method.
     * 
     * @since 0.4.0
     * 
     * @param   int     $ID     Optional. The ID of an existing PDF.
     */
    public function __construct( $ID = null ) {
        $this->ID = $ID ?? null;
        
        // Load the TCPDF library
        require_once BUDDYC_VENDOR_DIR . '/tecnickcom/tcpdf/tcpdf.php';
        
        // Initialize object handler
        self::init_object_handler();
    }
    
    /**
     * Creates a new PDF from arguments.
     * 
     * @since 0.4.0
     * 
     * @param   array   $args {
     *     An array of arguments for generating the PDF content.
     * 
     *     @int      $user_id       Optional. The user to whom the PDF belongs.
     *     @string   $type          The type of PDF document.
     *     @string   $title         The title of the PDF document.
     *     @string   $content       The primary content to include in the PDF.
     *     @array    $items         Optional. An array of strings to append to the document.
     *     @string   $image_path    Optional. The file path to an image to include.
     * }
     */
    public function create_pdf( $args ) {
        if ( ! $args ) {
            return;
        }
        
        // Set properties
        $this->set_properties( $args );
        
        // Generate the PDF
        $created = $this->generate_pdf();
        
        if ( $created ) {
            // Add object to database
            $this->ID = self::$object_handler->new_object( $this );
            
            // Return PDF object
            return $this;
        }
    }
    
    /**
     * Sets properties from arguments.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args {
     *     An array of arguments for generating the PDF content.
     * 
     *     @int      $user_id       Optional. The user to whom the PDF belongs.
     *     @string   $type          The type of PDF document.
     *     @string   $title         The title of the PDF document.
     *     @string   $content       The primary content to include in the PDF.
     *     @array    $items         Optional. An array of strings to append to the document.
     *     @string   $image_path    Optional. The file path to an image to include.
     * }
     */
    private function set_properties( $args ) {
        // Extract values
        foreach ( $args as $key => $value ) {
            $this->{$key} = $value;
        }
        // Build file name
        $this->file_name = $this->generate_file_name();
        
        // Get directory path
        $this->dir_path = ( new Directory( 'pdfs/' . $this->user_id ) )->full_path();
        
        // Build the full path and url
        $this->file_path = trailingslashit( $this->dir_path ) . $this->file_name . '.pdf';
        $this->file_url = str_replace( ABSPATH, trailingslashit( site_url() ), $this->file_path );
    }
    
    /**
     * Generates a PDF.
     * 
     * @since 0.4.0
     * 
     * @return  string  The URL of the newly created PDF.
     */
    private function generate_pdf() {
        
        // Create a new instance of TCPDF class
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor( get_bloginfo( 'name' ) );
        $pdf->SetTitle( $this->title );
        $pdf->SetSubject( $this->title );
        $pdf->SetKeywords( 'User, Legal, PDF' );
        
        // Set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        // Add a page
        $pdf->AddPage();
        
        // Set title font
        $pdf->SetFont('helvetica', 'B', 18);
        
        // Add title
        $pdf->Ln(10); // Add space
        $pdf->Cell(0, 10, $this->title, 0, 1, 'C');
        $pdf->Ln(10); // Add space
        
        // Set content font
        $pdf->SetFont('helvetica', '', 12);
        
        // Convert HTML to formatted text with headings
        $pdf = $this->format_content( $pdf, $this->content );
        
        // Add space
        $pdf->Ln(20);
        
        // Add image (such as signature)
        if ( $this->image_path && $this->image_path !== '' ) {
            $pdf->Image( $this->image_path, 10, $pdf->GetY() + 10, 80 );
            $pdf->Ln(40); // Add space after the signature image
        }
        
        // Add additional items
        if ( $this->items && is_array( $this->items ) ) {
            foreach ( $this->items as $string ) {
                $pdf = $this->format_content( $pdf, $string );
            }
        }
        
        // Save the PDF to a file on the server
        $pdf->Output( $this->file_path, 'F' );
        
        // Check if the file was successfully created
        if ( file_exists( $this->file_path ) && is_readable( $this->file_path ) ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Converts HTML content to formatted text with headings and lists.
     *
     * @param TCPDF $pdf TCPDF object for PDF generation.
     * @param string $content HTML content to convert.
     * @return void
     */
    function format_content( $pdf, $content ) {
        // Split content into an array of paragraphs
        $paragraphs = self::split_paragraphs( $content );
        
        // Make sure it's an array
        $paragraphs = is_array( $paragraphs ) ? $paragraphs : [$paragraphs];
        
        // Loop through paragraphs
        foreach ( $paragraphs as $paragraph ) {
            
            // Decode HTML entities to display special characters correctly
            $decoded_paragraph = html_entity_decode( $paragraph );
            
            // Check if it's a header (H1, H2, H3)
            if ( strpos( $paragraph, '<h' ) !== false ) {
                $pdf->SetFont('helvetica', 'B', 14);
            } else {
                $pdf->SetFont('helvetica', '', 12);
            }
            
            // Replace list tags with asterisk for PDF formatting
            $stripped_paragraph = strip_tags( $decoded_paragraph, '<ul><ol><li>' );
            $stripped_paragraph = preg_replace('/<ul[^>]*>/i', '', $stripped_paragraph);
            $stripped_paragraph = preg_replace('/<\/ul>/i', '', $stripped_paragraph);
            $stripped_paragraph = preg_replace('/<ol[^>]*>/i', '', $stripped_paragraph);
            $stripped_paragraph = preg_replace('/<\/ol>/i', '', $stripped_paragraph);
            $stripped_paragraph = preg_replace('/<li[^>]*>/i', ' • ', $stripped_paragraph);
            $stripped_paragraph = preg_replace('/<\/li>/i', '', $stripped_paragraph);
            
            // Add to pdf content
            $pdf->MultiCell(0, 10, $stripped_paragraph, 0, 'L');
            
            // Add space
            $pdf->Ln(8); // Add space
        }
        return $pdf;
    }

    /**
     * Splits content into an array of paragraphs and elements.
     * 
     * @since 0.2.6
     */
    private static function split_paragraphs( $content ) {
        // Create a DOMDocument object
        $dom = new DOMDocument();

        // Load HTML content, suppressing errors for malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_use_internal_errors(false);

        // Initialize an array to hold paragraphs
        $paragraphs = [];

        // Get all elements inside the body
        $body = $dom->getElementsByTagName('body')->item(0);
        foreach ($body->childNodes as $node) {
            self::process_node($node, $paragraphs);
        }

        // Remove empty paragraphs and normalize whitespace
        $paragraphs = array_filter($paragraphs);
        $paragraphs = array_map('trim', $paragraphs);

        return $paragraphs;
    }

    /**
     * Processes a single node.
     * 
     * @since 0.2.6
     */
    private static function process_node( $node, &$paragraphs ) {
        // Handle text nodes
        if ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) !== '') {
            $paragraphs[] = '<p>' . trim($node->nodeValue) . '</p>';
        } elseif ($node->nodeType === XML_ELEMENT_NODE) {
            $nodeName = strtolower($node->nodeName);

            // Check if it's a block-level element or specific tags like <ul>, <ol>, <li>
            if (in_array($nodeName, ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li'])) {
                $paragraphs[] = $node->ownerDocument->saveHTML($node);
            } elseif ($node->childNodes) {
                // Recursively process child nodes
                foreach ($node->childNodes as $childNode) {
                    self::process_node($childNode, $paragraphs);
                }
            }
        }
    }
    
    /**
     * Generates a the PDF file name.
     * 
     * @since 0.1.0
     */
    public function generate_file_name() {
        return time() . $this->ID;
    }
    
    /**
     * Retrieves the PDF object by ID.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID     The ID of the PDF.
     */
    public static function get_pdf( $ID ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Get the object
        return self::$object_handler->get_object( $ID );
    }
    
    /**
     * Generates an HTML-formatted download link.
     * 
     * @since 0.1.0
     * 
     * @param   string  $ID     The ID of the PDF.
     * @param   string  $type   Optional. A word to add to the download link text.
     */
    public static function download_link( $ID, $type = null ) {
        $pdf = self::get_pdf( $ID );
        if ( isset( $pdf->file_url ) && isset( $pdf->file_path ) ) {
            $pdf->file_url = str_replace( ABSPATH, trailingslashit( site_url() ), $pdf->file_path );
            if ( $pdf->file_url && $pdf->file_url !== '' ) {
                $link_text = $type ? __( 'Download ', 'buddyclients' ) . ucfirst( $type ) . __( ' PDF', 'buddyclients' ) : __( 'Download PDF', 'buddyclients' );
                return '<a href="' . esc_url( $pdf->file_url ) . '" ' . __( 'download', 'buddyclients' ) . '><i class="fa-solid fa-download"></i> ' . $link_text . '</a>';
            }
        }
    }
    
}