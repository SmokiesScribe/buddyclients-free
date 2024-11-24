<?php
/**
 * Checks for Freelancer Mode.
 * 
 * @since 0.1.0
 */
function buddyc_freelancer_mode() {
    $freelancer = new BuddyClients\Includes\Freelancer;
    return $freelancer->enabled;
}

/**
 * Retrieves the Freelancer ID.
 * 
 * @since 0.1.0
 */
function buddyc_freelancer_id() {
    $freelancer = new BuddyClients\Includes\Freelancer;
    return $freelancer->ID;
}