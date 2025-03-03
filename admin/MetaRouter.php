<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Service\{
    MetaService,
    MetaServiceType,
    MetaAdjustment,
    MetaRateType,
    MetaRole,
    MetaFileUpload,
    MetaFilter
};
use BuddyClients\Components\Brief\{
    MetaBrief,
    MetaBriefField
};
use BuddyClients\Components\Legal\{
    MetaLegal,
    MetaLegalMod
};
use BuddyClients\Components\Email\MetaEmail;
use BuddyClients\Components\Quote\MetaQuote;
use BuddyClients\Components\Testimonial\MetaTestimonial;





/**
 * Directs to the correct meta class.
 * 
 * @since 1.0.29
 */
class MetaRouter {

    /**
     * Defines the meta classes.
     * 
     * @since 1.0.29
     */
    private static function classes() {
        $classes = [
            'buddyc_service'        => MetaService::class,
            'buddyc_service_type'   => MetaServiceType::class,
            'buddyc_adjustment'     => MetaAdjustment::class,
            'buddyc_rate_type'      => MetaRateType::class,
            'buddyc_role'           => MetaRole::class,
            'buddyc_email'          => MetaEmail::class,
            'buddyc_brief'          => MetaBrief::class,
            'buddyc_brief_field'    => MetaBriefField::class,
            'buddyc_legal'          => MetaLegal::class,
            'buddyc_legal_mod'      => MetaLegalMod::class,
            'buddyc_quote'          => MetaQuote::class,
            'buddyc_testimonial'    => MetaTestimonial::class,
            'buddyc_filter'         => MetaFilter::class,
            'buddyc_file_upload'    => MetaFileUpload::class
        ];
        
        /**
         * Filters the Meta classes.
         *
         * @since 1.0.25
         *
         * @param array  $callbacks An array of classes keyed by meta group.
         */
        $classes = apply_filters( 'buddyc_meta_classes', $classes );

        return $classes;
    }

    /**
     * Retrieves the class for a meta group.
     * 
     * @since 1.0.25
     * 
     * @param string $meta_group The name of the meta group.
     */
    private static function get_class( $meta_group ) {
        // Define classes
        $classes = self::classes();
        
        // Get class for meta group
        $class = $classes[$meta_group] ?? null;

        // Make sure the class exists
        if ( is_string( $class ) && class_exists( $class ) ) {
            return $class;
        }
    }

    /**
     * Retrieves the meta data for a meta group.
     * 
     * @since 1.0.25
     * 
     * @param string $meta_group The name of the meta group.
     * 
     * @return array The meta data.
     */
    public static function get_meta( $meta_group ) {
        return self::get_data( $meta_group, 'meta' );
    }

    /**
     * Retrieves the default meta data for a meta group.
     * 
     * @since 1.0.25
     * 
     * @param string $meta_group The name of the meta group.
     * 
     * @return array The meta data.
     */
    public static function get_defaults( $meta_group ) {
        return self::get_data( $meta_group, 'defaults' );
    }

    /**
     * Directs to the correct meta class based on the given group.
     * 
     * @since 1.0.25
     * 
     * @param   string      $meta_group     The name of the meta group.
     * @param   string      $method             The name of the static method to call.
     * 
     * @return  array       The meta data or default data for the group.
     */
    public static function get_data( $meta_group, $method ) {

        // Get the meta class
        $class = self::get_class( $meta_group );

        // Build the callable
        $callable = [$class, $method];

        // Retrieve the data        
        if ( is_callable( $callable ) ) {     
            return $callable();
        }
    }
}