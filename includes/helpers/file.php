<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\File;
use BuddyClients\Includes\FileHandler;

/**
 * Retrieves the upload ID of a File.
 * 
 * @since 1.0.21
 * 
 * @param   int     $file_id    The ID of the File.
 */
function buddyc_get_file_upload_id( $file_id ) {
    return File::get_file_upload_id( $file_id );
}

/**
 * Generates a new FileHandler instance.
 * 
 * @since 1.0.21
 * 
 * @param   array   $file   Superglobal $_FILES data.
 * @param   array   $args {
 *     Array of arguments for File creation.
 *     
 *     @type    bool    $temporary      Whether the files are temporary.
 *     @type    ?int    $user_id        File owner ID.
 *     @type    ?int    $project_id     Associated project ID.
 * }
 */
function buddyc_file_handler( $files, $args ) {
    return new FileHandler( $files, $args );
}

/**
 * Initializes the File ObjectHandler.
 * 
 * @since 1.0.21
 */
function buddyc_file_object_handler() {
    return buddyc_object_handler( File::class );
}