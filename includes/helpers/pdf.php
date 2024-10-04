<?php
use BuddyClients\Includes\PDF;
/**
 * Generates a new PDF instance.
 * 
 * @since 0.4.0
 * 
 * @param   int     $ID     Optional. The ID of the PDF.
 */
function bc_pdf( $ID = null ) {
    return new PDF( $ID );
}

/**
 * Generates a new PDF.
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
 * 
 * @return  int     The ID of the newly created PDF.
 */
function bc_create_pdf( $args ) {
    // New PDF instance
    $pdf = bc_pdf();
    
    // Create PDF
    $pdf = $pdf->create_pdf( $args );
    
    // Return PDF ID
    return $pdf->ID;
}

/**
 * Includes the TCPDF library.
 * 
 * @since 0.2.6
 */
function bc_tcpdf_library() {
    require_once ABSPATH . 'wp-content/plugins/buddyclients/vendor/tcpdf/tcpdf.php';
}

/**
 * Generates a PDF from service agreement ID.
 * 
 * @since 0.2.6
 * 
 * @param   BookingIntent   $booking_intent    The BookingIntent object.
 */
function generate_service_agreement_pdf( $booking_intent ) {
    if ( ! $booking_intent ) {
        return;
    }
    
    // Get terms version
    $post_id = $booking_intent->terms_version;
    
    if ( ! $post_id ) {
        return;
    }
    
    // Require TCPDF library
    bc_tcpdf_library(); // Assuming this function includes TCPDF library
    
    // Get legal agreement title
    $title = get_the_title( $post_id );
    
    // Create a new instance of TCPDF class
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor( get_bloginfo( 'name' ) );
    $pdf->SetTitle( $title );
    $pdf->SetSubject( $title );
    $pdf->SetKeywords( 'User, Legal, PDF' );
    
    // Set default font subsetting mode
    $pdf->setFontSubsetting(true);
    
    // Add a page
    $pdf->AddPage();
    
    // Set title font
    $pdf->SetFont('helvetica', 'B', 18);
    
    // Add title
    $pdf->Ln(10); // Add space
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->Ln(10); // Add space
    
    // Set content font
    $pdf->SetFont('helvetica', '', 12);
    
    // Get post object
    $post = get_post( $post_id );
    
    if ( ! $post ) {
        return;
    }
    
    // Add content from post
    $content = $post->post_content;
    
    if ( ! $content ) {
        return;
    }
    
    // Convert HTML to formatted text with headings
    bc_include_pdf_content( $pdf, $content );
    
    // Add space
    $pdf->Ln(20);
    
    // Add name
    $client_id = $booking_intent->client_id;
    $client_name = bp_core_get_user_displayname( $client_id );
    $client_name_string = $client_name . ' (' . bp_core_get_username( $client_id ) . ') - ' . bp_core_get_user_email( $client_id );
    $pdf->MultiCell(0, 10, $client_name_string, 0, 'L');
    
    // Add date
    $date = strtotime( $booking_intent->created_at );
    $date_human_readable = gmdate( 'F d, Y', $date );
    $message = __('%s accepted this agreement by selecting a checkbox on %s at the website %s.', 'buddyclients');
    $date_string = sprintf( $message, $client_name, $date_human_readable, site_url() );
    $pdf->MultiCell( 0, 10, $date_string, 0, 'L' );
    
    // Save the PDF to a file on the server
    $location = 'wp-content/uploads/service_agreement_' . $client_id . '_' . $date . '.pdf';
    $file_path = ABSPATH . $location;
    $pdf->Output($file_path, 'F');
    
    // Return the file URL for the download link
    $file_url = site_url('/' . $location);
    return $file_url;

}

/**
 * Generates a PDF from data.
 * 
 * @since 0.2.6
 * 
 * @param   array   $data   The user's Legal data.
 * @param   string  $type   The type of agreement.
 */
function generate_legal_pdf_from_user_data( $data, $type ) {
    if ( ! isset( $data['version'] ) ) {
        return;
    }
    
    // Require TCPDF library
    bc_tcpdf_library(); // Assuming this function includes TCPDF library
    
    // Get legal agreement title
    $title = get_the_title( $data['version'] );
    
    // Create a new instance of TCPDF class
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor( get_bloginfo( 'name' ) );
    $pdf->SetTitle( $title );
    $pdf->SetSubject( $title );
    $pdf->SetKeywords( 'User, Legal, PDF' );
    
    // Set default font subsetting mode
    $pdf->setFontSubsetting(true);
    
    // Add a page
    $pdf->AddPage();
    
    // Set title font
    $pdf->SetFont('helvetica', 'B', 18);
    
    // Add title
    $pdf->Ln(10); // Add space
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->Ln(10); // Add space
    
    // Set status font
    $pdf->SetFont('helvetica', 'I', 12);
    
    // Add status string
    $curr_date = gmdate('F j, Y');
    $status = $data['status'] ?? __( 'inactive', 'buddyclients' );
    $uc_status = strtoupper( $status );
    $message = __('This agreement is %s as of %s.', 'buddyclients');
    $status_string = sprintf( $message, $uc_status, $curr_date );
    $pdf->MultiCell(0, 10, $status_string, 0, 'L');
    
    // Set content font
    $pdf->SetFont('helvetica', '', 12);
    
    // Add content from legal user data
    $content = $data['content'];
    
    // Convert HTML to formatted text with headings
    bc_include_pdf_content( $pdf, $content );
    
    // Add signature image (if exists in data)
    if (isset($data['signature_file_path']) && !empty($data['signature_file_path'])) {
        $pdf->Image($data['signature_file_path'], 10, $pdf->GetY() + 10, 80);
        $pdf->Ln(40); // Add space after the signature image
    }
    
    // Add name
    $pdf->MultiCell(0, 10, $data['name'], 0, 'L');
    
    // Add date
    $date_human_readable = gmdate( 'F d, Y', $data['date'] );
    $pdf->MultiCell( 0, 10, $date_human_readable, 0, 'L' );
    
    // Save the PDF to a file on the server
    $location = 'wp-content/uploads/' . $type . '_agreement_' . $data['user_id'] . '_' . $data['date'] . '.pdf';
    $file_path = ABSPATH . $location;
    $pdf->Output($file_path, 'F');
    
    // Return the file URL for the download link
    $file_url = site_url('/' . $location);
    return $file_url;

}

/**
 * Converts HTML content to formatted text with headings and lists.
 *
 * @param TCPDF $pdf TCPDF object for PDF generation.
 * @param string $content HTML content to convert.
 * @return void
 */
function bc_include_pdf_content($pdf, $content) {
    // Split $content into an array of paragraphs
    $paragraphs = bc_split_paragraphs($content);

    // Now $paragraphs is an array where each element is a paragraph of text
    $firstParagraph = true;
    foreach ($paragraphs as $paragraph) {
        // Add space between paragraphs (except the first one)
        if (!$firstParagraph) {
            $pdf->Ln(10); // 10 units of space between paragraphs
        }
        $firstParagraph = false;

        // Decode HTML entities to display special characters correctly
        $decoded_paragraph = html_entity_decode($paragraph);

        // Check if it's a header (H1, H2, H3)
        if (strpos($paragraph, '<h') !== false) {
            $pdf->SetFont('helvetica', 'B', 14);
        } else {
            $pdf->SetFont('helvetica', '', 12);
        }

        // Replace list tags with asterisk for PDF formatting
        $stripped_paragraph = strip_tags($decoded_paragraph, '<ul><ol><li>');
        $stripped_paragraph = preg_replace('/<ul[^>]*>/i', '', $stripped_paragraph);
        $stripped_paragraph = preg_replace('/<\/ul>/i', '', $stripped_paragraph);
        $stripped_paragraph = preg_replace('/<ol[^>]*>/i', '', $stripped_paragraph);
        $stripped_paragraph = preg_replace('/<\/ol>/i', '', $stripped_paragraph);
        $stripped_paragraph = preg_replace('/<li[^>]*>/i', ' • ', $stripped_paragraph);
        $stripped_paragraph = preg_replace('/<\/li>/i', '', $stripped_paragraph);

        // Add to PDF
        $pdf->MultiCell(0, 10, $stripped_paragraph, 0, 'L');
    }
}

/**
 * Splits content into an array of paragraphs and elements.
 * 
 * @since 0.2.6
 */
function bc_split_paragraphs($content) {
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
        bc_process_node($node, $paragraphs);
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
function bc_process_node($node, &$paragraphs) {
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
                bc_process_node($childNode, $paragraphs);
            }
        }
    }
}