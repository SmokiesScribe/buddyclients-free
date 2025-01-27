<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\ObjectHandler;

/**
 * Initializes an instance of the ObjectHandler class.
 * 
 * @since 1.0.21
 * 
 * @param   string  $class  The fully qualified class name.
 */
function buddyc_object_handler( $class ) {
    return new ObjectHandler( $class );
}

/**
 * Retrieves all items of a class.
 * 
 * @since 1.0.21
 * 
 * @param   string  $class  The fully qualified class name.
 */
function buddyc_get_all_objects( $class ) {
    $object_handler = buddyc_object_handler( $class );
    return $object_handler->get_all_objects();
}