<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\Options;
/**
 * Generates an options array.
 * 
 * @since 0.2.9
 * @param   string  $key        The key denoting which options to generate.
 *                              Accepts 'clients', 'team', 'affiliates', 'users', 'projects'
 * @param   string  $format     The format of the options array.
 *                              Accepts 'simple' and 'detail'. Defaults to 'simple'.
 * @param   array   $args       Optional. An array of args to pass to the callback.
 */
function buddyc_options( $key, $args = null ) {
    $options = new Options( $key, $args );
    return $options->options;
}