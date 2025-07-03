<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
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
            self::$object_handler = buddyc_object_handler( __CLASS__ );
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

        // Set paths and urls
        $this->set_dir_properties();
    }

    /**
     * Sets the directory paths and urls.
     * 
     * @since 1.0.21
     */
    private function set_dir_properties() {
        // Init Directory
        $directory = buddyc_directory( 'pdfs/' . $this->user_id );

        // Get dir path and url
        $dir_path = $directory->full_path();
        $dir_url = $directory->full_url();
        
        // Build the full path and url
        $this->file_path = trailingslashit( $dir_path ) . $this->file_name . '.pdf';
        $this->file_url = trailingslashit( $dir_url ) . $this->file_name . '.pdf';
    }
    
    /**
     * Generates a PDF.
     * 
     * @since 0.4.0
     * 
     * @return  string  The URL of the newly created PDF.
     */
    private function generate_pdf() {

        // Initialize the PDF
        $pdf = $this->init_pdf();

        // Add a page
        $this->add_page( $pdf );

        // Add the title
        $this->add_title( $pdf );

        // Add content
        $this->add_content( $pdf );

        // Add image
        $this->add_image( $pdf );

        // Additional content
        $this->additional_content( $pdf );

        // Save to server
        $success = $this->save_to_server( $pdf );

        // Return bool
        return $success;
    }

    /**
     * Initializes the PDF and its info.
     * 
     * @since 1.0.21
     */
    private function init_pdf() {
        // Create a new instance of TCPDF class
        $pdf = new TCPDF();
        
        // Set PDF info
        $pdf->SetCreator(PDF_CREATOR);

        // Set author
        $pdf->SetAuthor( get_bloginfo( 'name' ) );

        // Set title
        $pdf->SetTitle( $this->title );

        // Set subject
        $pdf->SetSubject( $this->title );

        // Set keywords
        $pdf->SetKeywords( 'User, Legal, PDF' );

        // Set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Return pdf
        return $pdf;
    }

    /**
     * Saves the PDF to the server.
     * 
     * @since 1.0.21
     * 
     * @param   TCPDF   $pdf    The PDF object.
     * @return  bool            True if the file was saved successfully, false otherwise.
     */
    private function save_to_server( $pdf ) {
        try {
            // Save the PDF to a file on the server
            $pdf->Output( $this->file_path, 'F' );

            // Check if the file exists
            if ( $this->exists() ) {
                return true;
            } else {
                // File does not exist
                $error_message = sprintf(
                    /* translators: %s: the file path */
                    __( 'PDF file was not created at %s', 'buddyclients-lite' ),
                    $this->file_path
                );
                return false;
            }
        } catch ( Exception $e ) {
            // TCPDF error
            $error_message = sprintf(
                /* translators: %s: the error message */
                __( 'PDF generation failed: %s', 'buddyclients-lite' ),
                $e->getMessage()
            );
            return false;
        }
    }

    /**
     * Check whether the PDF exists and is readable.
     * 
     * @since 1.0.21
     * 
     * @return  bool    True if the file exists and is readable, false if not.
     */
    private function exists() {
        return file_exists( $this->file_path ) && is_readable( $this->file_path );
    }

    /**
     * Adds a PDF page.
     * 
     * @since 1.0.21
     * 
     * @param   TCPDF   $pdf    The PDF object.
     */
    private function add_page( $pdf ) {
        // Add a page
        $pdf->AddPage();
    }

    /**
     * Adds the document title.
     * 
     * @since 1.0.21
     * 
     * @param   TCPDF   $pdf    The PDF object.
     */
    private function add_title( $pdf ) {
        // Set the font size
        $this->set_font( $pdf, 18, 'B' );
        
        // Add space
        $pdf->Ln(10);

        // Add title cell
        $pdf->Cell( 0, 10, $this->title, 0, 1, 'C' );

        // Add space
        $pdf->Ln(10);
    }

    /**
     * Adds the PDF content.
     * 
     * @since 1.0.21
     * 
     * @param   TCPDF   $pdf    The PDF object.
     */
    private function add_content( $pdf ) {
        // Set content font
        $this->set_font( $pdf, 12, '' );

        // Convert HTML to formatted text with headings
        $pdf = $this->format_content( $pdf, $this->content );
        
        // Add space
        $pdf->Ln(20);
    }

    /**
     * Adds an image, such as a signature.
     * 
     * @since 1.0.21
     * 
     * @param   TCPDF   $pdf    The PDF object.
     */
    private function add_image( $pdf ) {
        if ( ! empty( $this->image_path ) ) {
            $pdf->Image( $this->image_path, 10, $pdf->GetY() + 10, 80 );
            $pdf->Ln(40); // Add space after the signature image
        }
    }

    /**
     * Adds additional content from the array.
     * 
     * @since 1.0.21
     * 
     * @param   TCPDF   $pdf    The PDF object.
     */
    private function additional_content( $pdf ) {
        if ( ! empty( $this->items ) && is_array( $this->items ) ) {
            foreach ( $this->items as $string ) {
                $pdf = $this->format_content( $pdf, $string );
            }
        }
    }

    /**
     * Sets the font size and style.
     * 
     * @since 1.0.21
     * 
     * @param   TCPDF   $pdf    The PDF object.
     * @param   int     $size   The font size.
     * @param   string  $style  Optional. The font style (e.g. 'B').   
     */
    private function set_font( $pdf, $size, $style = '' ) {
        $pdf->SetFont( 'helvetica', $style, $size );
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
        // Get PDF object
        $pdf = self::get_pdf( $ID );

        // Make sure the file url exists
        if ( isset( $pdf->file_url ) && ! empty( $pdf->file_url ) ) {
            // Build the link text
            if ( ! empty( $type ) ) {
                $link_text = sprintf(
                    /* translators: %s: the PDF type (e.g. 'Agreement') */
                    __( 'Download %s PDF', 'buddyclients-lite' ),
                    ucfirst( esc_html( $type ) )
                );
            } else {
                // No PDF type
                $link_text = __( 'Download PDF', 'buddyclients-lite' );
            }

            // Build the html link
            return sprintf(
                '<a href="%1$s" download><i class="fa-solid fa-download"></i> %2$s</a>',
                esc_url( $pdf->file_url ),
                esc_html( $link_text )
            );
        }
    }

    /**
     * Generates an HTML-formatted download link with an icon only.
     * 
     * @since 1.0.21
     * 
     * @param   string  $ID     The ID of the PDF.
     */
    public static function download_icon( $ID ) {
        $pdf = self::get_pdf( $ID );
        if ( isset( $pdf->file_url ) && ! empty( $pdf->file_url ) ) {
            $title = __( 'Download PDF', 'buddyclients-lite' );
            return '<a title="' . $title . '" href="' . esc_url( $pdf->file_url ) . '" ' . __( 'download', 'buddyclients-lite' ) . '><i class="fa-solid fa-download"></i></a>';
        }
    }
}