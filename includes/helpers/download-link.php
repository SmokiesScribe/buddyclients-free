<?php
/**
 * File handling functions.
 * 
 * @since 0.1.0
 */
use BuddyClients\Includes\{
    FileHandler as FileHandler,
    File        as File
};

    /**
     * Generates file download links.
     * 
     * @since 0.1.0
     * 
     * @param   array   $file_ids           An array of file IDs.
     * @param   bool    $show_file_name     Optional. Whether to display the file name.
     *                                      Defaults to false.
     */
    function bc_download_links( $file_ids, $show_file_name = false ) {
        if ( ! empty( $file_ids ) ) {
            return FileHandler::download_links( $file_ids, $show_file_name );
        }
    }
    
    /**
     * Generates list of file names.
     * 
     * @since 0.1.0
     * 
     * @param   array   $file_ids           An array of file IDs.
     * @param   bool    $comma_separated    Optional. Whether to display the file names as a comma separated list or with line breaks.
     *                                      Defaults to false.
     */
    function bc_file_names( $file_ids, $comma_separated = false ) {
        if ( empty( $file_ids ) ) {
            return '';
        }
        
        // Initialize
        $file_names = [];
        
        // Loop through file ids
        foreach ( (array) $file_ids as $file_id ) {
            $icon = $comma_separated ? '' : bc_icon( 'paperclip' ) . ' ';
            $file_names[] = $icon . File::get_file_name( $file_id );
        }
        
        // Define separator
        $separator = $comma_separated ? ', ' : '<br>';
        
        // Implode array
        return implode( $separator, $file_names );
    }