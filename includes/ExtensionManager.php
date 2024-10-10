<?php
namespace BuddyClients\Includes;

use BuddyClients\Includes\ProfileExtension;

use BuddyClients\Components\Booking\BookedService\BookedServiceList;
use BuddyClients\Components\Affiliate\AffiliateProfile;
use BuddyClients\Components\Legal\Legal;
use BuddyClients\Components\Legal\LegalForm;
use BuddyClients\Includes\UserFilesForm;
use BuddyClients\Components\Sales\SalesProfile;
use BuddyClients\Components\Availability\AvailabilityProfile;

/**
 * Manages group and profile extensions.
 * 
 * @since 1.0.4
 */
class ExtensionManager {
    
    /**
     * Constructor method.
     * 
     * @since 1.0.4
     */
    public function __construct() {
        self::extensions();
    }
    
    /**
     * Registers extensions.
     * 
     * @since 1.0.4
     */
    private static function extensions() {

        // Services
        self::build_extension( 'services', BookedServiceList::class );
        
        // Affiliate
        self::build_extension( 'affiliate', [AffiliateProfile::class, Legal::class] );
        
        // Availability
        self::build_extension( 'availability', [AvailabilityProfile::class] );
        
        // Team Agreement
        self::build_extension( 'team_agreement', [LegalForm::class] );
        
        // Sales Commission
        self::build_extension( 'sales', [SalesProfile::class] );
        
        // Files
        self::build_extension( 'files', [UserFilesForm::class] );
        
    }
    
    /**
     * Defines callbacks for extension args.
     * 
     * @since 1.0.4
     * 
     * @param   string  $slug   The slug of the extension.
     * @return  ?array  The array of args for the extension.
     *                  Null if the slug is not set.
     */
    private static function extension_args( $slug ) {
        $callbacks = [
            'services'          => [self::class, 'services_args'],
            'affiliate'         => [self::class, 'affiliate_args'],
            'availability'      => [self::class, 'availability_args'],
            'team_agreement'    => [self::class, 'team_agreement_args'],
            'sales'             => [self::class, 'sales_args'],
            'files'             => [self::class, 'files_args'],
        ];
        
        // Check if the callback exists for the given slug
        if ( isset( $callbacks[$slug] ) && is_callable( $callbacks[$slug] ) ) {
            // Call the callback and return its result
            return call_user_func( $callbacks[$slug] );
        }
        
        // Return null if no valid callback is found
        return null;
    }
    
    /**
     * Defines the services extension args.
     * 
     * @since 1.0.4
     */
    private static function services_args() {
        return [
            'type'              => 'profile',
            'content_callback'  => [new BookedServiceList, 'build'],
            'slug'              => 'services',
            'name'              => __( 'Services', 'buddyclients-free' ),
            'title'             => __( 'My Services', 'buddyclients-free' ),
            'private'           => true
        ];
    }
    
    /**
     * Defines the affiliate extension args.
     * 
     * @since 1.0.4
     */
    private static function affiliate_args() {
        return [
            'type'              => 'profile',
            'content_callback'  => [new AffiliateProfile, 'build'],
            'slug'              => 'affiliate',
            'name'              => __( 'Affiliate Program', 'buddyclients-free' ),
            'title'             => __( 'Affiliate Program', 'buddyclients-free' ),
            'private'           => true
        ];
    }
    
    /**
     * Defines the availability extension args.
     * 
     * @since 1.0.4
     */
    private static function availability_args() {
        return [
            'type'              => 'profile',
            'content_callback'  => [new AvailabilityProfile, 'build'],
            'slug'              => 'availability',
            'name'              => __( 'Availability', 'buddyclients-free' ),
            'title'             => __( 'Availability', 'buddyclients-free' ),
            'private'           => true,
            'member_type'       => 'team'
        ];
    }
    
    /**
     * Defines the team agreement extension args.
     * 
     * @since 1.0.4
     */
    private static function team_agreement_args() {
        return [
            'type'              => 'profile',
            'content_callback'  => [new LegalForm( 'team' ), 'echo_form'],
            'slug'              => 'team',
            'name'              => __( 'Team Agreement', 'buddyclients-free' ),
            'title'             => __( 'Team Agreement', 'buddyclients-free' ),
            'private'           => true,
            'member_type'       => 'team'
        ];
    }
    
    /**
     * Defines the sales extension args.
     * 
     * @since 1.0.4
     */
    private static function sales_args() {
        return [
            'type'              => 'profile',
            'content_callback'  => [new SalesProfile, 'build'],
            'slug'              => 'bc-sales',
            'name'              => __( 'Sales', 'buddyclients-free' ),
            'title'             => __( 'Sales Commission', 'buddyclients-free' ),
            'private'           => true,
            'member_type'       => 'sales'
        ];
    }
    
    /**
     * Defines the files extension args.
     * 
     * @since 1.0.4
     */
    private static function files_args() {
        return [
            'type'              => 'settings',
            'content_callback'  => [new UserFilesForm, 'build'],
            'slug'              => 'bc-files',
            'name'              => __( 'Files', 'buddyclients-free' ),
            'title'             => __( 'Manage Files', 'buddyclients-free' ),
            'private'           => true
        ];
    }
    
    /**
     * Checks whether classes exist.
     * 
     * @since 1.0.4
     * 
     * @param   array           $classes    An array of classes to check.
     * @return  bool            True if all classes exist, false if any do not.
     */
    private static function classes_exist( $classes ) {
        $classes = ( array ) $classes;
        
        // No classes provided
        if ( empty( $classes ) ) {
            return true;
        }
        
        // Loop through classes
        foreach ( $classes as $class ) {
            if ( ! class_exists( $class ) ) {
                // Return false if any class does not exist
                return false;
            }
        }
        return true;
    }
    
    /**
     * Builds and registers an extension.
     * 
     * @since 1.0.4
     * 
     * @param   string  $slug               The slug for the extension.
     * @param   array   $required_classes   Optional. An array of required classes.
     */
    private static function build_extension( $slug, $required_classes = [] ) {
        // Make sure classes exist
        if ( self::classes_exist( $required_classes ) ) {
            // Retrieve args
            $args = self::extension_args( $slug );
            // Register extension
            self::register_extension( $args );
        }
    }
    
    /**
     * Registers a profile extension.
     * 
     * @since 1.0.4
     * 
     * @param   array           $args
     *     The array of args to pass to the ProfileExtension. {
     * 
     *     @type    string      $type               Accepts 'profile' or 'settings'.
     *     @type    callable    $content_callback   The callback method to display the content.
     *     @type    string      $slug               The nav or subnav slug.
     *     @type    string      $name               The nav or subnav tab name.
     *     @type    string      $title              Optional. The title to display. Defaults to name.
     *     @type    int         $position           Optional. The nav or subnav position. Defaults to 30.
     *     @type    bool        $private            Optional. Whether to display the tab only for the profile owner. Defaults to false.
     *     @type    string      $member_type        Optional. Type of user to restrict to. Accepts 'team' and 'client'. Defaults to null.
     *     @type    string      $icon               Optional. The icon for account settings extensions.
     * }
     */
    private static function register_extension( $args ) {
        if ( $args ) {
            $ext_args = [
                'type'              => $args['type'] ?? 'profile',
                'content_callback'  => $args['content_callback'],
                'slug'              => $args['slug'],
                'name'              => $args['name'],
                'title'             => $args['title'] ?? null,
                'position'          => $args['position'] ?? null,
                'private'           => $args['private'] ?? null,
                'member_type'       => $args['member_type'] ?? null,
                'icon'              => $args['icon'] ?? null
            ];
            new ProfileExtension( $ext_args );
        }
    }
    
    
}