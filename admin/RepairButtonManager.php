<?php
namespace BuddyClients\Admin;

/**
 * Repair button manager.
 * 
 * Handles init of all repair buttons.
 *
 * @since 0.1.0
 */
class RepairButtonManager {
    
    /**
     * Defines all repair button args.
     * 
     * @since 0.1.0
     */
    private static function repair_buttons() {
        return [
            'email_templates' => [
                'post_type' => 'buddyc_email',
                'callback'  => ['BuddyClients\Components\Email\EmailTemplateManager', 'create']
            ]
        ];
    }
    
    /**
     * Generates all repair buttons.
     * 
     * @since 0.1.0
     */
    public static function run() {
        foreach ( self::repair_buttons() as $key => $args ) {
            new RepairButton( $key, $args );
        }
    }
    
}