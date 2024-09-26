<?php
use BuddyClients\Includes\ParamManager;

/**
 * Creates a new ParamManager instance.
 * 
 * @since 1.0.3
 * 
 * @param   string  $url    Optional. The url to modify.
 *                          Defaults to the current url.
 */
function bc_param_manager( $url = null ) {
    return new ParamManager( $url );
}