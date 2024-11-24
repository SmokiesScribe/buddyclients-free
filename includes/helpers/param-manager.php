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
function buddyc_param_manager( $url = null ) {
    return new ParamManager( $url );
}

/**
 * Retrieves the value of a url parameter.
 * 
 * @since 1.0.15
 * 
 * @param   string  $param    The key of the parameter to retrieve.
 * @param   string  $url    Optional. The url to modify.
 *                          Defaults to the current url.
 */
function buddyc_get_param( $param, $url = null ) {
    $param_manager = buddyc_param_manager( $url );
    return $param_manager->get( $param );
}

/**
 * Retrieves all url parameters.
 * 
 * @since 1.0.15
 * 
 * @param   string  $url    Optional. The url to modify.
 *                          Defaults to the current url.
 * @return  array   An associative array of all url parameters.
 */
function buddyc_get_all_params( $url = null ) {
    $param_manager = buddyc_param_manager( $url );
    return $param_manager->get_all_params();
}

/**
 * Retrieves all url parameters.
 * 
 * @since 1.0.17
 * 
 * @param   array   $params     An associative array of params and values.
 * @param   string  $url        Optional. The url to modify.
 *                              Defaults to the current url.
 * 
 * @return  string  The new url.
 */
function buddyc_add_params( $params, $url = null ) {
    $param_manager = buddyc_param_manager( $url );
    return $param_manager->add_params( $params, $url );
}

/**
 * Removes a url parameter.
 * 
 * @since 1.0.17
 * 
 * @param   string   $param     The param to remove.
 * @param   string  $url        Optional. The url to modify.
 *                              Defaults to the current url.
 * 
 * @return  string  The new url.
 */
function buddyc_remove_param( $param, $url = null ) {
    $param_manager = buddyc_param_manager( $url );
    return $param_manager->remove_param( $params, $url );
}

/**
 * Removes all url parameters.
 * 
 * @since 1.0.17
 * 
 * @param   string  $url        Optional. The url to modify.
 *                              Defaults to the current url.
 * 
 * @return  string  The new url.
 */
function buddyc_strip_params( $url = null ) {
    $param_manager = buddyc_param_manager( $url );
    return $param_manager->strip_params();
}