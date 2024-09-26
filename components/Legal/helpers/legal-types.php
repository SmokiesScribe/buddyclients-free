<?php
use BuddyClients\Admin\Settings as Settings;
/**
 * Retrieves the legal types.
 * 
 * @since 0.4.0
 */
function bc_legal_types() {
    return Settings::legal_types();
}