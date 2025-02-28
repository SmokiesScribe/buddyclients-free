<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Start session only if not already started
if ( session_status() === PHP_SESSION_NONE ) {
    @session_start();
}