<?php
use BuddyClients\Components\Affiliate\Affiliate as Affiliate;
/**
 * Affiliate functions.
 * 
 * @since 0.1.0
 */
    
    /**
     * Retrieves the affiliate link for a user.
     * 
     * @since 0.1.0
     * 
     * @param   int     $user_id    The ID of the user.
     */
    function bc_affiliate_link( $user_id ) {
        return Affiliate::affiliate_link( $user_id );
    }
    
    /**
     * Retrieves the affiliate's email.
     * 
     * @since 0.1.0
     * 
     * @param   int     $user_id    The ID of the user.
     */
    function bc_affiliate_email( $user_id ) {
        $affiliate = new Affiliate( $user_id );
        return $affiliate->affiliate_email();
    }
    
    /**
     * Checks whether the user is an active affiliate.
     * 
     * @since 1.0.4
     * 
     * @param   int     $user_id    Optional. The ID of the user.
     *                              Defaults to the current user.
     */
    function bc_is_affiliate( $user_id = null ) {
        $affiliate = new Affiliate( $user_id );
        $status = $affiliate->user_data;
        return $status === 'current';
    }
    
    /**
     * Checks whether the user has ever been an active affiliate.
     * 
     * @since 1.0.4
     * 
     * @param   int     $user_id    The ID of the user.
     */
    function bc_was_affiliate( $user_id = null ) {
        $affiliate = new Affiliate( $user_id );
        $status = $affiliate->user_data;
        return $status !== false;
    }