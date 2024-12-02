<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\PDF;

/**
 * Generates a new PDF instance.
 * 
 * @since 0.4.0
 * 
 * @param   int     $ID     Optional. The ID of the PDF.
 */
function buddyc_pdf( $ID = null ) {
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
function buddyc_create_pdf( $args ) {
    // New PDF instance
    $pdf = buddyc_pdf();
    
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
function buddyc_tcpdf_library() {
    require_once BUDDYC_VENDOR_DIR . '/tcpdf/tcpdf.php';
}

/**
 * Generates a PDF download link.
 * 
 * @since 1.0.17
 * 
 * @param   int     $pdf_id     The ID of the PDF file.
 */
function buddyc_pdf_download_link ( $pdf_id ) {
    return PDF::download_link( $pdf_id );
}