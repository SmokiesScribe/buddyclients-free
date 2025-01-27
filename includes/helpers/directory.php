<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Directory;

/**
 * Initializes a new Directory from a path.
 * 
 * @since 1.0.21
 * 
 * @param string $path  The subpath of the directory to be created.
 *                      Default to '0' if no path defined.
 */
function buddyc_directory( $path = '0' ) {
    return new Directory( $path );
}

/**
 * Retrieves the full path for a Directory.
 * 
 * @since 1.0.21
 * 
 * @param string $path  The subpath of the directory to be created.
 *                      Default to '0' if no path defined.
 */
function buddyc_directory_path( $path = '0' ) {
    $directory = buddyc_directory( $path );
    return $directory->full_path();
}

 /**
 * Retrieves the full url for a Directory.
 * 
 * @since 1.0.21
 * 
 * @param string $path  The subpath of the directory to be created.
 *                      Default to '0' if no path defined.
 */
function buddyc_directory_url( $path = '0' ) {
    $directory = buddyc_directory( $path );
    return $directory->full_url();
}